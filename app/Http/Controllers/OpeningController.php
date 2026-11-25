<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\Nuc;
use App\Status;
use App\Currency;
use App\Insurance;
use App\Paymentform;
use App\Application;
use App\Charge;
use App\Opening;
use App\SixMonth_fund;
use App\Coupon;
use App\Charge_Moves;
use App\Documents;
use Carbon\Carbon;
use DB;
use DateTime;

class OpeningController extends Controller
{
    protected $staffUserId = 36; 

    public function index()
    {
        $profile = User::findProfile();
        $user = User::user_id();

        $agents = User::select('id', DB::raw('CONCAT(name," ",IFNULL(firstname,"")) AS name'))
            ->orderBy('name')
            ->where('fk_profile', '12')
            ->pluck('name', 'id');

        $currencies = Currency::pluck('name', 'id');
        $insurances = Insurance::orderBy('name')->where('active_fund', 1)->get();
        $paymentForms = Paymentform::whereNull('deleted_at')->pluck('name', 'id');
        $applications = Application::pluck('name', 'id');
        $charges = Charge::whereNull('deleted_at')->pluck('name', 'id');
        $clients = Client::get();

        $perm = Permission::permView($profile, 52);
        $perm_btn = Permission::permBtns($profile, 52);
        $nucs = Nuc::where('active_stat', 1)->pluck('nuc', 'id');

        $cmbStatus = Status::select('id', 'name')
            ->where('fk_section', '52')
            ->pluck('name', 'id');

        $cmbStatusMF = Status::select('id', 'name')
            ->where('fk_section', '19')
            ->pluck('name', 'id');

        if ($profile == 12) {
            $openings = DB::select('call aperturasAgente(?,?)', [$user, 1]);
        } else {
            $openings = DB::select('call aperturas(?)', [0]);
        }

        // Contador inicial de solicitudes pendientes
        $pendingRequestsCount = DB::table('Opening_Requests')
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('fk_agent', $user);
            })
            ->count();

        $pendingRenovationsCount = DB::table('Renewal_Requests')
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('fk_agent', $user);
            })
            ->count();

        $requestsMap = $this->getRequestsMap();
        $renewalsMap = $this->getRenewalsMap();

        if ($perm == 0) {
            return redirect()->route('home');
        } else {
            return view('process.opening.opening', compact(
                'profile', 'agents', 'perm_btn', 'cmbStatus', 'cmbStatusMF',
                'currencies', 'insurances', 'paymentForms', 'applications',
                'openings', 'clients', 'charges', 'nucs', 'pendingRequestsCount', 'requestsMap', 
                'pendingRenovationsCount', 'renewalsMap'
            ));
        }
    }

    // =========================================================
    // 🟢 SERVER-SIDE PESTAÑA 1: APERTURAS EN PROCESO
    // =========================================================
    public function GetOpeningsList(Request $request)
    {
        try {
            $profile = User::findProfile();
            $perm_btn = Permission::permBtns($profile, 52);
            $active = intval($request->input('active', 0));
            $targetId = intval($request->input('target_id', 0));

            $rawOpenings =$this->ReturnData($profile,$active);
            // dd($rawOpenings);

            // Si viene desde notificación (?opening_id=X) y no está en activas, buscar en historial completo
            if ($targetId > 0 && $active == 0) {$exists = collect($rawOpenings)->contains('oid',$targetId);
                if (!$exists) {$rawOpenings = $this->ReturnData($profile, 1);
                }
            }

            $requestsMap =$this->getRequestsMap();
            $renewalsMap =$this->getRenewalsMap();

            $collection = collect($rawOpenings)->map(function ($v) use ($requestsMap,$renewalsMap) {
                if (isset($requestsMap[$v->oid])) {$v->origen_text = 'Emisión';
                } elseif (isset($renewalsMap[$v->oid])) {$v->origen_text = 'Renovación';
                } else {
                    $v->origen_text = 'Alta Directa';
                }
                $v->estatus_interno_text = ($v->statid == 6 && empty($v->finestra_status)) ? 'Pend. Finestra' : ($v->name ?? '');
                return $v;
            });

            $recordsTotal =$collection->count();

            // 1. Buscador global
            $searchValue = mb_strtolower(trim($request->input('search.value', '')));
            if ($searchValue !== '') {
                $collection =$collection->filter(function ($v) use ($searchValue) {
                    $haystack = mb_strtolower(implode(' ', [$v->agent ?? '',
                        $v->cname ?? '',
                        $v->insurance ?? '',
                        $v->fund_type ?? '',
                        $v->nuc ?? '',
                        $v->limit_status ?? '',
                        $v->origen_text ?? '',
                        $v->name ?? '',
                        $v->estatus_interno_text ?? ''
                    ]));
                    return mb_strpos($haystack,$searchValue) !== false;
                });
            }

            // 2. Buscador por columna individual
            if ($request->has('columns')) {
                foreach ($request->input('columns') as$col) {
                    $colData =$col['data'] ?? null;
                    $colSearch = mb_strtolower(trim($col['search']['value'] ?? ''));
                    if ($colSearch !== '' &&$colData) {
                        $collection =$collection->filter(function ($v) use ($colData, $colSearch) {$fieldVal = '';
                            switch ($colData) {
                                case 'agent':           $fieldVal =$v->agent ?? ''; break;
                                case 'cname':           $fieldVal =$v->cname ?? ''; break;
                                case 'insurance':       $fieldVal =$v->insurance ?? ''; break;
                                case 'fund_type':       $fieldVal =$v->fund_type ?? ''; break;
                                case 'nuc':             $fieldVal =$v->nuc ?? ''; break;
                                case 'limit_status':    $fieldVal =$v->limit_status ?? ''; break;
                                case 'origen':          $fieldVal =$v->origen_text ?? ''; break;
                                case 'estatus_agente':  $fieldVal =$v->name ?? ''; break;
                                case 'estatus_interno': $fieldVal =$v->estatus_interno_text ?? ''; break;
                            }
                            return mb_strpos(mb_strtolower((string)$fieldVal),$colSearch) !== false;
                        });
                    }
                }
            }

            $recordsFiltered =$collection->count();

            // 3. Ordenamiento
            if ($request->has('order')) {$colIdx = $request->input('order.0.column');$colDir = $request->input('order.0.dir', 'asc');$colName = $request->input("columns.{$colIdx}.data");

                $sortMap = [
                    'agent'           => 'agent',
                    'cname'           => 'cname',
                    'insurance'       => 'insurance',
                    'fund_type'       => 'fund_type',
                    'nuc'             => 'nuc',
                    'limit_status'    => 'limit_status',
                    'origen'          => 'origen_text',
                    'estatus_agente'  => 'name',
                    'estatus_interno' => 'estatus_interno_text',
                ];

                if (isset($sortMap[$colName])) {
                    $prop = $sortMap[$colName];
                    $collection = $collection->sortBy(function ($item) use ($prop) {
                        return mb_strtolower((string) ($item->{$prop} ?? ''));
                    }, SORT_REGULAR, $colDir === 'desc');
                }
            }

            // 4. Si viene target_id de notificación, colocarlo primero
            if ($targetId > 0) {
                $collection = $collection->sortBy(function ($v) use ($targetId) {
                    return (intval($v->oid ?? 0) === $targetId) ? 0 : 1;
                });
            }

            $start = max(0, intval($request->input('start', 0)));
            $length = intval($request->input('length', 25));
            if ($length > 0) {
                $collection = $collection->slice($start, $length);
            } elseif ($length === 0) {
                $collection = collect();
            }

            // 5. Formatear filas HTML
            $dataFormatted =$collection->values()->map(function ($v) use ($profile, $perm_btn,$requestsMap, $renewalsMap) {
                $btnStatAg = '<button type="button" class="btn btn-sm" style="color:#' . $v->font_color . '; background:#' . $v->color . '; border-color: #' . $v->border_color . '; padding: 4px 12px; border-radius:20px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatusAgente(' . $v->oid . ')">' . e($v->name) . '</button>';
                if($profile != 12){   
                    $btnStat = ($v->statid == 6 && !$v->finestra_status)
                        ? '<button type="button" class="btn btn-sm text-white" style="background:#e98c46; border-radius:20px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatus(' . $v->oid . ')">Pend. Finestra</button>'
                        : '<button type="button" class="btn btn-sm" style="color:#' . $v->font_color . '; background:#' . $v->color . '; border-color: #' . $v->border_color . '; border-color: #e98c46; padding: 4px 12px; border-radius:20px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatus(' . $v->oid . ')">' . e($v->name) . '</button>';
                } else {
                    $btnStat = "";
                }

                $origenBadge = '<span class="badge badge-light border text-secondary px-2 py-1">Alta Directa</span>';
                $btnReq = '';

                if (isset($requestsMap[$v->oid])) {$origenBadge = '<span class="badge badge-light border text-primary px-2 py-1">Emisión</span>';
                    $btnReq = '<button type="button" class="btn btn-info btn-sm shadow-sm" onclick="verDetalleSolicitud(' . $requestsMap[$v->oid] . ')" title="Ver Solicitud de Emisión" style="border-radius:6px;"><i class="fas fa-file-alt"></i></button>';
                } elseif (isset($renewalsMap[$v->oid])) {$origenBadge = '<span class="badge badge-light border text-success px-2 py-1">Renovación</span>';
                    $btnReq = '<button type="button" class="btn btn-primary btn-sm shadow-sm text-white" onclick="verDetalleRenovacion(' . $renewalsMap[$v->oid] . ')" title="Ver Solicitud de Renovación" style="border-radius:6px;"><i class="fas fa-sync-alt"></i></button>';
                }

                $btnEdit = '<button type="button" class="btn btn-warning btn-sm" onclick="editarApertura(' . $v->oid . ')" style="border-radius:6px;"><i class="fas fa-edit"></i></button>';
                $btnTrash = (isset($perm_btn['erase']) &&$perm_btn['erase'] == 1)
                    ? '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarApertura(' . $v->oid . ')" style="border-radius:6px;"><i class="fa fa-trash"></i></button>'
                    : '';

                $opciones = '<div class="d-flex justify-content-center align-items-center" style="gap: 4px; white-space: nowrap;">' . $btnReq . $btnEdit .$btnTrash . '</div>';

                return [
                    'DT_RowId'        => (string)$v->oid,
                    'agent'           => e($v->agent),
                    'cname'           => '<span class="font-weight-bold text-dark">' . e($v->cname) . '</span>',
                    'insurance'       => e($v->insurance),
                    'fund_type'       => '<span class="badge badge-secondary">' . e($v->fund_type) . '</span>',
                    'nuc'             => '<span class="font-weight-bold">' . e($v->nuc) . '</span>',
                    'limit_status'    => e($v->limit_status ?? ''),
                    'origen'          => $origenBadge,
                    'estatus_agente'  => $btnStatAg,
                    'estatus_interno' => $btnStat,
                    'opciones'        => $opciones
                ];
            });

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $dataFormatted
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
                'file'            => basename($e->getFile()),
                'line'            => $e->getLine()
            ], 500);
        }
    }

    // =========================================================
    // 🟢 WIZARD DE NUEVA SOLICITUD DE APERTURA
    // =========================================================
    public function create()
    {
        $user = User::user_id();
        $profile = User::findProfile();
        $perm = Permission::permView($profile, 51);
        $perm_btn = Permission::permBtns($profile, 51);

        if ($perm == 0) {
            return redirect()->route('home');
        }

        $clients = Client::when($profile == 12, function ($q) use ($user) {
                $q->where('fk_agent', $user)
                  ->orWhereIn('id', function ($sub) use ($user) {
                      $sub->select('fk_client')->from('Nuc')->where('fk_agent', $user);
                  })
                  ->orWhereIn('id', function ($sub) use ($user) {
                      $sub->select('fk_client')->from('SixMonth_fund')->where('fk_agent', $user);
                  });
            })
            ->orderBy('name')
            ->get();

        $paymentForms = Paymentform::whereNull('deleted_at')->pluck('name', 'id');
        $charges = Charge::whereNull('deleted_at')->pluck('name', 'id');

        // NUCs de destino (Solo CP y MP)
        $nucsDestino = DB::table('Nuc')
            ->join('Insurance', 'Nuc.fk_insurance', '=', 'Insurance.id')
            ->leftJoin('Client', 'Nuc.fk_client', '=', 'Client.id')
            ->select(
                'Nuc.id',
                DB::raw("CONCAT(Nuc.nuc, ' (', Insurance.fund_type, ') - ', IFNULL(Client.name, ''), ' ', IFNULL(Client.firstname, '')) as label")
            )
            ->whereIn('Insurance.fund_type', ['CP', 'MP'])
            ->where('Nuc.active_stat', 1)
            ->whereNull('Nuc.deleted_at')
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('Nuc.fk_agent', $user);
            })
            ->orderBy('Nuc.nuc')
            ->get();

        // NUCs de origen para Movimiento Interno (CP, MP y LP con prefijo para evitar colisión de ID)
        $nucsCpMp = DB::table('Nuc')
            ->join('Insurance', 'Nuc.fk_insurance', '=', 'Insurance.id')
            ->leftJoin('Client', 'Nuc.fk_client', '=', 'Client.id')
            ->select(
                DB::raw("CONCAT('NUC_', Nuc.id) as composite_id"),
                DB::raw("CONCAT(Nuc.nuc, ' (', Insurance.fund_type, ') - ', IFNULL(Client.name, ''), ' ', IFNULL(Client.firstname, '')) as label")
            )
            ->whereIn('Insurance.fund_type', ['CP', 'MP'])
            ->where('Nuc.active_stat', 1)
            ->whereNull('Nuc.deleted_at')
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('Nuc.fk_agent', $user);
            });

        $nucsLp = DB::table('SixMonth_fund')
            ->join('Insurance', 'SixMonth_fund.fk_insurance', '=', 'Insurance.id')
            ->leftJoin('Client', 'SixMonth_fund.fk_client', '=', 'Client.id')
            ->select(
                DB::raw("CONCAT('SMF_', SixMonth_fund.id) as composite_id"),
                DB::raw("CONCAT(SixMonth_fund.nuc, ' (LP) - ', IFNULL(Client.name, ''), ' ', IFNULL(Client.firstname, '')) as label")
            )
            ->where('Insurance.fund_type', 'LP')
            ->where('SixMonth_fund.active_stat', 1)
            ->whereNull('SixMonth_fund.deleted_at')
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('SixMonth_fund.fk_agent', $user);
            });

        $nucsOrigen = $nucsCpMp->unionAll($nucsLp)->orderBy('label')->get();

        return view('process.opening.openingCreate', compact(
            'profile', 'perm_btn', 'clients', 'paymentForms', 'charges', 'nucsOrigen', 'nucsDestino'
        ));
    }

    public function storeRequest(Request $request)
    {
        try {
            $user_id = User::user_id();
            $fk_client = intval($request->input('fk_client', 0));

            $plazo = intval($request->input('plazo', 6));
            $currency = $request->input('currency', 'MXN');
            $fund_type = 'CP';
            if ($plazo === 12) $fund_type = 'MP';
            if ($plazo === 24) $fund_type = 'LP';

            $monto_limpio = (float) preg_replace('/[^0-9.]/', '', $request->input('amount', 0));

            // 1. Validación de montos mínimos por plazo y moneda
            $minimos = [
                6  => ['MXN' => 250000, 'USD' => 5000],
                12 => ['MXN' => 750000, 'USD' => 15000],
                24 => ['MXN' => 500000, 'USD' => 25000],
            ];
            $minRequerido = $minimos[$plazo][$currency] ?? 0;

            if ($monto_limpio < $minRequerido) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "El monto mínimo para {$plazo} meses en {$currency} es de $" . number_format($minRequerido, 2) . "."
                ], 422);
            }

            // 2. Validación de Origen del Recurso (Dinero Nuevo / Movimiento Interno / Ambos)
            $isNewMoney = $request->has('is_new_money') ? 1 : 0;
            $isInternalMove = $request->has('is_internal_move') ? 1 : 0;

            if (!$isNewMoney && !$isInternalMove) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Debes seleccionar al menos un origen del recurso (Dinero Nuevo o Movimiento Interno).'
                ], 422);
            }

            $montoNuevo = $isNewMoney ? (float) preg_replace('/[^0-9.]/', '', $request->input('new_money_amount', 0)) : 0;
            $montoInterno = $isInternalMove ? (float) preg_replace('/[^0-9.]/', '', $request->input('internal_move_amount', 0)) : 0;

            if (abs(($montoNuevo + $montoInterno) - $monto_limpio) > 0.01) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'La suma de Dinero Nuevo y Movimiento Interno ($' . number_format($montoNuevo + $montoInterno, 2) . ') debe coincidir con el Monto Total ($' . number_format($monto_limpio, 2) . ').'
                ], 422);
            }

            $openingType = 1;
            if ($isNewMoney && $isInternalMove) $openingType = 3;
            elseif ($isInternalMove) $openingType = 2;

            // 3. Validación de mayoría de edad en Beneficiarios 1 y 2
            $qtyBen = intval($request->input('beneficiaries_qty', 1));
            if ($qtyBen === 1 || $qtyBen === 2) {
                if ($request->filled('beneficiary_1_dob') && Carbon::parse($request->input('beneficiary_1_dob'))->age < 18) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'El Beneficiario 1 debe ser mayor de edad (18 años o más).'
                    ], 422);
                }
            }
            if ($qtyBen === 2) {
                if ($request->filled('beneficiary_2_dob') && Carbon::parse($request->input('beneficiary_2_dob'))->age < 18) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'El Beneficiario 2 debe ser mayor de edad (18 años o más).'
                    ], 422);
                }
            }

            DB::beginTransaction();

            // 4. Buscar el fondo sugerido en Insurance
            $payTypeQuery = ($fund_type === 'LP') ? 1 : $request->input('fk_payment_form');
            $insurance = Insurance::where('fund_type', $fund_type)
                ->where('fund_curr', $currency)
                ->where('pay_type', $payTypeQuery)
                ->where('active_fund', 1)
                ->first();

            if (!$insurance) {
                $insurance = Insurance::where('fund_type', $fund_type)
                    ->where('fund_curr', $currency)
                    ->where('active_fund', 1)
                    ->first();
            }

            // Descomponer NUC de origen si lleva Movimiento Interno
            $nucOriginType = null;
            $fkNucOrigin = null;
            if ($isInternalMove && $request->filled('fk_nuc_origin')) {
                $parts = explode('_', $request->input('fk_nuc_origin'));
                if (count($parts) === 2) {
                    $nucOriginType = $parts[0];
                    $fkNucOrigin = intval($parts[1]);
                }
            }

            $isReinvLp = ($fund_type === 'LP' && $request->input('fk_payment_form') == 4);
            $fkNucDest = $isReinvLp ? $request->input('fk_nuc_dest') : null;
            $esTransferencia = (intval($request->input('fk_payment_form')) === 2);

            $reqId = DB::table('Opening_Requests')->insertGetId([
                'fk_agent'             => $user_id,
                'fk_client'            => $fk_client,
                'client_type'          => intval($request->input('client_type', 0)),
                'new_name'             => $fk_client === 0 ? $request->input('new_name') : null,
                'new_firstname'        => ($fk_client === 0 && $request->input('client_type') == 0) ? $request->input('new_firstname') : null,
                'new_lastname'         => ($fk_client === 0 && $request->input('client_type') == 0) ? $request->input('new_lastname') : null,
                'new_birth_date'       => ($fk_client === 0 && $request->filled('new_birth_date')) ? $request->input('new_birth_date') : null,
                'new_rfc'              => $fk_client === 0 ? strtoupper(trim($request->input('new_rfc'))) : null,
                'new_curp'             => ($fk_client === 0 && $request->input('client_type') == 0) ? strtoupper(trim($request->input('new_curp'))) : null,
                'new_domicile'         => $fk_client === 0 ? $request->input('new_domicile') : null,
                'email'                => $request->input('email'),
                'cellphone'            => $request->input('cellphone'),
                'home_phone'           => $request->input('home_phone'),
                'nationality'          => $request->input('nationality'),
                'residence_country'    => $request->input('residence_country'),
                'marital_status'       => $request->input('client_type') == 0 ? $request->input('marital_status') : null,
                'activity'             => $request->input('activity'),
                'company_name'         => $request->input('company_name'),
                'legal_dictum'         => $request->input('client_type') == 1 ? intval($request->input('legal_dictum', 1)) : 0,
                'currency'             => $currency,
                'plazo'                => $plazo,
                'fund_type'            => $fund_type,
                'fk_insurance'         => $insurance ? $insurance->id : null,
                'fk_payment_form'      => $request->input('fk_payment_form'),
                'fk_charge'            => $request->input('fk_charge'),
                'amount'               => $monto_limpio,
                'opening_type'         => $openingType,
                'is_new_money'         => $isNewMoney,
                'new_money_amount'     => $montoNuevo,
                'is_internal_move'     => $isInternalMove,
                'internal_move_amount' => $montoInterno,
                'nuc_origin_type'      => $nucOriginType,
                'fk_nuc_origin'        => $fkNucOrigin,
                'nuc_origin_text'      => ($isInternalMove && !$fkNucOrigin) ? $request->input('nuc_origin_text') : null,
                'fk_nuc_dest'          => $fkNucDest,
                'nuc_dest_text'        => ($isReinvLp && !$fkNucDest) ? $request->input('nuc_dest_text') : null,
                'bank_account'         => $esTransferencia ? $request->input('bank_account') : null,
                'bank_clabe'           => $esTransferencia ? $request->input('bank_clabe') : null,
                'bank_name'            => $esTransferencia ? $request->input('bank_name') : null,
                'bank_aba'             => $esTransferencia ? $request->input('bank_aba') : null,
                'bank_swift'           => $esTransferencia ? $request->input('bank_swift') : null,
                'beneficiaries_qty'    => $qtyBen,
                'beneficiary_1_name'   => ($qtyBen === 1 || $qtyBen === 2) ? $request->input('beneficiary_1_name') : null,
                'beneficiary_1_dob'    => (($qtyBen === 1 || $qtyBen === 2) && $request->filled('beneficiary_1_dob')) ? $request->input('beneficiary_1_dob') : null,
                'beneficiary_1_pct'    => ($qtyBen === 1 || $qtyBen === 2) ? $request->input('beneficiary_1_pct') : null,
                'beneficiary_2_name'   => ($qtyBen === 2) ? $request->input('beneficiary_2_name') : null,
                'beneficiary_2_dob'    => ($qtyBen === 2 && $request->filled('beneficiary_2_dob')) ? $request->input('beneficiary_2_dob') : null,
                'beneficiary_2_pct'    => ($qtyBen === 2) ? $request->input('beneficiary_2_pct') : null,
                'beneficiaries_extra'  => ($qtyBen === 3) ? $request->input('beneficiaries_extra') : null,
                'status'               => 1,
                'created_at'           => Carbon::now(),
                'updated_at'           => Carbon::now()
            ]);

            // 5. Procesar ambos estados de cuenta y toda la documentación (Sección 52)
            $singleFiles = [
                'file_ine',
                'file_domicilio',
                'file_domicilio_moral',
                'file_csf',
                'file_estado_cuenta_doc', // Estado de cuenta opcional en Documentación
                'file_comprobante_pago',
                'file_estado_cuenta',     // Estado de cuenta obligatorio cuando es transferencia
                'file_acta',
                'file_poderes',
                'file_folio_mercantil',
                'file_rfc_moral'
            ];

            foreach ($singleFiles as $inputName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    if ($file->isValid()) {
                        $prefix = str_replace('file_', '', $inputName);
                        $filename = "sol_" . $prefix . "_31_" . $reqId . "_" . time() . "_" . uniqid() . "_" . $file->getClientOriginalName();

                        $doc = new Documents;
                        $doc->fk_key     = $reqId;
                        $doc->fk_section = 52;
                        $doc->name       = $filename;
                        $doc->save();

                        $file->move(public_path('files/openingfiles'), $filename);
                    }
                }
            }

            if ($request->hasFile('file_ine_rep')) {
                foreach ($request->file('file_ine_rep') as $file) {
                    if ($file->isValid()) {
                        $filename = "sol_ine_rep_31_" . $reqId . "_" . time() . "_" . uniqid() . "_" . $file->getClientOriginalName();

                        $doc = new Documents;
                        $doc->fk_key     = $reqId;
                        $doc->fk_section = 52;
                        $doc->name       = $filename;
                        $doc->save();

                        $file->move(public_path('files/openingfiles'), $filename);
                    }
                }
            }

            // Notificar al staff que hay una nueva solicitud de emisión pendiente

            if ($this->staffUserId && $this->staffUserId != $user_id) {
                $clientLabel = $fk_client > 0
                    ? trim((Client::find($fk_client)->name ?? '') . ' ' . (Client::find($fk_client)->firstname ?? ''))
                    : trim(($request->input('new_name') ?? '') . ' ' . ($request->input('new_firstname') ?? '') . ' (Nuevo)');

                $agentUser = User::find($user_id);
                $agentName = $agentUser ? trim(($agentUser->name ?? '') . ' ' . ($agentUser->firstname ?? '')) : 'Un agente';

                DB::table('notifications')->insert([
                    'fk_user'    => $this->staffUserId,
                    'type'       => 'new_opening_request',
                    'title'      => 'Nueva Solicitud de Emisión #' . $reqId,
                    'message'    => "{$agentName} envió una solicitud de {$fund_type} ({$currency}) por $" . number_format($monto_limpio, 2) . " para {$clientLabel}.",
                    'url'        => url('process/opening/opening') . '?req_id=' . $reqId,
                    'is_read'    => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Solicitud de apertura enviada correctamente (Folio #' . $reqId . ').'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al registrar solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================
    // 🟢 LISTADO Y REVISIÓN DE SOLICITUDES (PARA LA PESTAÑA 2)
    // =========================================================
    public function getRequestsList(Request $request)
    {
        try {
            $profile = User::findProfile();
            $user = User::user_id();
            $showAll = intval($request->input('show_all', 0));
            $targetId = intval($request->input('target_id', 0));

            $clientSql = "CASE 
                WHEN r.fk_client > 0 THEN CONCAT(IFNULL(c.name,''), ' ', IFNULL(c.firstname,''), ' ', IFNULL(c.lastname,''))
                ELSE CONCAT(IFNULL(r.new_name,''), ' ', IFNULL(r.new_firstname,''), ' ', IFNULL(r.new_lastname,''), ' (Nuevo)')
            END";

            $query = DB::table('Opening_Requests as r')
                ->leftJoin('users as u', 'r.fk_agent', '=', 'u.id')
                ->leftJoin('Client as c', 'r.fk_client', '=', 'c.id')
                ->leftJoin('Insurance as i', 'r.fk_insurance', '=', 'i.id')
                ->select(
                    'r.id', 'r.fk_client', 'r.client_type', 'r.currency', 'r.plazo',
                    'r.fund_type', 'r.amount', 'r.opening_type', 'r.status', 'r.created_at',
                    DB::raw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) as agent_name"),
                    DB::raw("{$clientSql} as client_name"),
                    'i.name as fund_name'
                )
                ->whereNull('r.deleted_at')
                ->when($profile == 12, function ($q) use ($user) {
                    $q->where('r.fk_agent', $user);
                })
                ->when($showAll == 0, function ($q) use ($targetId) {
                    $q->where(function ($sub) use ($targetId) {
                        $sub->where('r.status', 1);
                        if ($targetId > 0) {
                            $sub->orWhere('r.id', $targetId);
                        }
                    });
                });

            $recordsTotal = $query->count();

            // Buscador global
            $searchValue = trim($request->input('search.value', ''));
            if ($searchValue !== '') {
                $query->where(function ($q) use ($searchValue, $clientSql) {
                    $q->where('r.id', 'like', "%{$searchValue}%")
                      ->orWhereRaw("{$clientSql} LIKE ?", ["%{$searchValue}%"])
                      ->orWhereRaw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) LIKE ?", ["%{$searchValue}%"])
                      ->orWhere('i.name', 'like', "%{$searchValue}%")
                      ->orWhere('r.fund_type', 'like', "%{$searchValue}%")
                      ->orWhere('r.amount', 'like', "%{$searchValue}%");
                });
            }

            // Buscador por columnas individuales
            if ($request->has('columns')) {
                foreach ($request->input('columns') as $col) {
                    $colData = $col['data'] ?? null;
                    $colSearch = trim($col['search']['value'] ?? '');
                    if ($colSearch !== '' && $colData) {
                        if ($colData === 'folio') {
                            $query->where('r.id', 'like', "%{$colSearch}%");
                        } elseif ($colData === 'fecha') {
                            $query->where('r.created_at', 'like', "%{$colSearch}%");
                        } elseif ($colData === 'agente') {
                            $query->whereRaw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) LIKE ?", ["%{$colSearch}%"]);
                        } elseif ($colData === 'cliente') {
                            $query->whereRaw("{$clientSql} LIKE ?", ["%{$colSearch}%"]);
                        } elseif ($colData === 'fondo') {
                            $query->where(function ($q) use ($colSearch) {
                                $q->where('i.name', 'like', "%{$colSearch}%")->orWhere('r.fund_type', 'like', "%{$colSearch}%");
                            });
                        } elseif ($colData === 'monto') {
                            $query->where('r.amount', 'like', "%{$colSearch}%");
                        }
                    }
                }
            }

            $recordsFiltered = $query->count();

            if ($targetId > 0) {
                $query->orderByRaw("CASE WHEN r.id = ? THEN 0 ELSE 1 END", [$targetId]);
            }

            if ($request->has('order')) {
                $colIdx = $request->input('order.0.column');
                $colDir = $request->input('order.0.dir', 'desc');
                $colName = $request->input("columns.{$colIdx}.data");

                $orderMap = [
                    'folio'   => 'r.id',
                    'fecha'   => 'r.created_at',
                    'agente'  => 'agent_name',
                    'cliente' => 'client_name',
                    'fondo'   => 'i.name',
                    'monto'   => 'r.amount',
                    'estatus' => 'r.status'
                ];
                if (isset($orderMap[$colName])) {
                    $query->orderBy($orderMap[$colName], $colDir);
                } else {
                    $query->orderBy('r.id', 'desc');
                }
            } else {
                $query->orderBy('r.id', 'desc');
            }

            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 25));
            $rows = $query->skip($start)->take($length)->get();

            $pendingCount = DB::table('Opening_Requests')
                ->whereNull('deleted_at')
                ->where('status', 1)
                ->when($profile == 12, function ($q) use ($user) { $q->where('fk_agent', $user); })
                ->count();

            $dataFormatted = $rows->map(function ($r) {
                $badgeStatus = '<span class="badge badge-warning px-3 py-2" style="border-radius:20px;">Pendiente</span>';
                if ($r->status == 2) $badgeStatus = '<span class="badge badge-success px-3 py-2" style="border-radius:20px;">Procesada</span>';
                elseif ($r->status == 3) $badgeStatus = '<span class="badge badge-danger px-3 py-2" style="border-radius:20px;">Rechazada</span>';

                $origenBadge = '<span class="badge badge-light border">Dinero Nuevo</span>';
                if ($r->opening_type == 3) $origenBadge = '<span class="badge badge-primary">Mixto</span>';
                elseif ($r->opening_type == 2) $origenBadge = '<span class="badge badge-info">Mov. Interno</span>';

                return [
                    'DT_RowId' => 'req_' . $r->id,
                    'folio'    => '<span class="font-weight-bold" style="font-family:monospace;">#' . $r->id . '</span>',
                    'fecha'    => '<span class="small text-muted">' . Carbon::parse($r->created_at)->format('d/m/Y g:i A') . '</span>',
                    'agente'   => '<span class="small font-weight-bold text-muted">' . e($r->agent_name) . '</span>',
                    'cliente'  => '<span class="font-weight-bold text-dark">' . e($r->client_name) . '</span>',
                    'fondo'    => '<span class="font-weight-bold">' . e($r->fund_name ?? 'N/A') . '</span> <small class="badge badge-secondary">' . e($r->fund_type) . '</small>',
                    'monto'    => '<span class="font-weight-bold text-primary">$' . number_format($r->amount, 2) . ' ' . e($r->currency) . '</span>',
                    'origen'   => $origenBadge,
                    'estatus'  => $badgeStatus,
                    'acciones' => '<button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="verDetalleSolicitud(' . $r->id . ')" style="border-radius:6px;"><i class="fas fa-eye mr-1"></i> Revisar</button>'
                ];
            });

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'pendingCount'    => $pendingCount,
                'data'            => $dataFormatted
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ], 500);
        }
    }

    public function getRequestDetail($id)
    {
        $req = DB::table('Opening_Requests as r')
            ->leftJoin('users as u', 'r.fk_agent', '=', 'u.id')
            ->leftJoin('Client as c', 'r.fk_client', '=', 'c.id')
            ->leftJoin('Insurance as i', 'r.fk_insurance', '=', 'i.id')
            ->leftJoin('Payment_form as pf', 'r.fk_payment_form', '=', 'pf.id')
            ->leftJoin('Charge as ch', 'r.fk_charge', '=', 'ch.id')
            ->select(
                'r.*',
                DB::raw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) as agent_name"),
                DB::raw("CASE 
                    WHEN r.fk_client > 0 THEN CONCAT(IFNULL(c.name,''), ' ', IFNULL(c.firstname,''), ' ', IFNULL(c.lastname,''))
                    ELSE CONCAT(IFNULL(r.new_name,''), ' ', IFNULL(r.new_firstname,''), ' ', IFNULL(r.new_lastname,''))
                END as client_full_name"),
                'c.rfc as existing_rfc',
                'c.curp as existing_curp',
                'c.domicile as existing_domicile',
                'i.name as fund_name',
                'pf.name as payment_form_name',
                'ch.name as charge_name'
            )
            ->where('r.id', $id)
            ->first();

        if (!$req) {
            return response()->json(['status' => false, 'message' => 'Solicitud no encontrada'], 404);
        }

        if (!empty($req->nuc_origin_text)) {
            $req->nuc_origin_label = $req->nuc_origin_text;
        }
        if (!empty($req->nuc_dest_text)) {
            $req->nuc_dest_label = $req->nuc_dest_text;
        }

        // Resolver etiqueta de NUC Origen si incluye movimiento interno
        $req->nuc_origin_label = 'N/A';
        if (($req->is_internal_move == 1 || $req->opening_type == 2 || $req->opening_type == 3)) {
            if ($req->fk_nuc_origin) {
                if ($req->nuc_origin_type === 'SMF') {
                    $req->nuc_origin_label = DB::table('SixMonth_fund')->where('id', $req->fk_nuc_origin)->value('nuc') . ' (LP)';
                } else {
                    $req->nuc_origin_label = DB::table('Nuc')->where('id', $req->fk_nuc_origin)->value('nuc') . ' (CP/MP)';
                }
            } elseif (!empty($req->nuc_origin_text)) {
                $req->nuc_origin_label = $req->nuc_origin_text;
            }
        }

        // Resolver etiqueta de NUC Destino si es reinversión LP
        $req->nuc_dest_label = 'N/A';
        if ($req->fk_nuc_dest) {
            $req->nuc_dest_label = DB::table('Nuc')->where('id', $req->fk_nuc_dest)->value('nuc');
        } elseif (!empty($req->nuc_dest_text)) {
            $req->nuc_dest_label = $req->nuc_dest_text;
        }

        // Documentos asociados a esta solicitud (distinguiendo ambos estados de cuenta)
        $docs = Documents::where('fk_key', $id)
            ->where('fk_section', 52)
            ->where('name', 'like', 'sol_%')
            ->get()
            ->map(function ($d) {
                $label = 'Documento Adjunto';
                if (strpos($d->name, 'sol_ine_rep_') === 0) $label = 'INE Rep. Legal / Apoderado';
                elseif (strpos($d->name, 'sol_ine_') === 0) $label = 'Identificación Oficial (INE)';
                elseif (strpos($d->name, 'sol_domicilio') === 0) $label = 'Comprobante de Domicilio';
                elseif (strpos($d->name, 'sol_csf_') === 0) $label = 'Constancia de Situación Fiscal';
                elseif (strpos($d->name, 'sol_estado_cuenta_doc_') === 0) $label = 'Estado de Cuenta (Documentación)';
                elseif (strpos($d->name, 'sol_comprobante_pago_') === 0) $label = 'Comprobante de Pago';
                elseif (strpos($d->name, 'sol_estado_cuenta_') === 0) $label = 'Estado de Cuenta Bancario (Transferencia)';
                elseif (strpos($d->name, 'sol_acta_') === 0) $label = 'Acta Constitutiva';
                elseif (strpos($d->name, 'sol_poderes_') === 0) $label = 'Poderes Actualizados';
                elseif (strpos($d->name, 'sol_folio_mercantil_') === 0) $label = 'Folio Mercantil';
                elseif (strpos($d->name, 'sol_rfc_moral_') === 0) $label = 'Alta Hacienda RFC';

                $d->doc_label = $label;
                return $d;
            });

        return response()->json([
            'status'    => true,
            'data'      => $req,
            'documents' => $docs
        ]);
    }

    public function rejectRequest(Request $request, $id)
    {
        $profile = User::findProfile();
        if ($profile == 12) {
            return response()->json(['status' => false, 'message' => 'Sin permisos para rechazar solicitudes.'], 403);
        }

        DB::table('Opening_Requests')->where('id', $id)->update([
            'status'       => 3,
            'staff_notes'  => $request->input('staff_notes'),
            'processed_by' => User::user_id(),
            'processed_at' => Carbon::now(),
            'updated_at'   => Carbon::now()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'La solicitud fue marcada con observaciones / rechazada.'
        ]);
    }

    // =========================================================
    // 🔵 MÉTODOS ORIGINALES DE APERTURAS OFICIALES (INTACTOS)
    // =========================================================
    public function ReturnData($profile, $active)
    {
        // dd("entres");
        $user = User::user_id();
        if ($profile == 12) {
            $openings = DB::select('call aperturasAgente(?,?)', [$user, $active]);
        } else {
            $openings = DB::select('call aperturas(?)', [$active]);
        }
        return $openings;
    }

    public function GetActive($active)
    {
        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 52);
        $openings = $this->ReturnData($profile, $active);
        return response()->json([
            'status'       => true,
            'openings'     => $openings,
            'profile'      => $profile,
            'permission'   => $perm_btn,
            'requests_map' => $this->getRequestsMap(),
            'renewals_map' => $this->getRenewalsMap(),
        ]);
    }

    public function GetInfo($id)
    {
        $opening = Opening::where('id', $id)->first();
        $insurance = Insurance::where('id', $opening->fk_insurance)->first();
        $chargeMoves = null;

        if ($insurance->fund_type == "CP" || $insurance->fund_type == "MP") {
            $insurances = Insurance::whereIn('fund_type', ['CP', 'MP'])->get();
            $opening = DB::table('Opening')->select(
                DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS cname'),
                'Opening.fk_agent', 'fk_application', 'fk_payment_form', 'Opening.fk_insurance', 'nuc', 'currency', 'estatus', 'Client.id as clid', 'Opening.id as opid', 'fund_type',
                'fk_charge', 'yield', 'yield_usd', 'fund_curr', 'fst_scnd'
            )
                ->join('Client', 'Opening.fk_client', '=', 'Client.id')
                ->join('Insurance', 'Opening.fk_insurance', '=', 'Insurance.id')
                ->join('Nuc', 'Opening.fk_nuc', '=', 'Nuc.id')
                ->where('Opening.id', $id)->first();
        } else {
            $insurances = Insurance::where('fund_type', 'LP')->get();
            $opening = DB::table('Opening')->select(
                DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS cname'),
                'Opening.fk_agent', 'fk_application', 'fk_payment_form', 'Opening.fk_insurance', 'nuc', 'fk_nuc', 'currency', 'deposit_date', 'amount', 'Client.id as clid', 'Opening.id as opid',
                'fund_type', 'fk_charge', 'yield', 'yield_usd', 'fund_curr', 'pair_non', 'fk_cp'
            )
                ->join('Client', 'Opening.fk_client', '=', 'Client.id')
                ->join('Insurance', 'Opening.fk_insurance', '=', 'Insurance.id')
                ->join('SixMonth_fund', 'Opening.fk_nuc', '=', 'SixMonth_fund.id')
                ->where('Opening.id', $id)->first();

            $chargeMoves = DB::table('Charge_Moves')->select("*")
                ->join('Charge', 'Charge_Moves.fk_charge', 'Charge.id')
                ->where('fk_fund', $opening->fk_nuc)
                ->whereNull("Charge_Moves.deleted_at")->get();
        }

        $profile = User::findProfile();
        return response()->json(['status' => true, "data" => $opening, "insurances" => $insurances, "chargeMoves" => $chargeMoves, "profile" => $profile]);
    }

    public function GetinfoFund($id)
    {
        $opening = Insurance::where('id', $id)->first();
        return response()->json(['status' => true, "data" => $opening]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $fk_client = $request->fk_client;
        if ($fk_client == 0) {
            $client = new Client;
            $client->name = $request->name;
            $client->firstname = $request->firstname;
            $client->lastname = $request->lastname;
            $client->birth_date = $request->birth_date;
            $client->rfc = $request->rfc;
            $client->curp = $request->curp;
            $client->cellphone = $request->cellphone;
            $client->email = $request->email;
            $client->domicile = $request->domicile;
            $client->fk_agent = $request->fk_agent;
            $client->save();
            $fk_client = $client->id;
        }

        if ($request->fund_type == "CP" || $request->fund_type == "MP") {
            $nuc = new Nuc;
            $nuc->nuc = $request->nuc;
            $nuc->currency = $request->selectCurrency;
            $nuc->fk_client = $fk_client;
            $nuc->estatus = $request->estatus;
            $nuc->fk_application = $request->fk_application;
            $nuc->fk_payment_form = $request->fk_payment_form;
            $nuc->fk_charge = $request->fk_charge ?? 1;
            $nuc->fk_insurance = $request->fk_insurance;
            $nuc->fst_scnd = $request->fst_scnd;
            $nuc->fk_agent = $request->fk_agent;
            if ($request->filled('amount')) {
                $nuc->fst_amount = floatval($request->amount);
            }
            $nuc->save();
            $fk_nuc = $nuc->id;
        } else {
            $deposit_date = new DateTime($request->deposit_date);
            $initial_date = new DateTime($deposit_date->format('Y') . "-" . $deposit_date->format('m') . "-01");
            $initial = clone $initial_date;
            $initialflag = 0;
            $initialdiff = 0;

            if (intval($deposit_date->format('d')) <= 10) {
                $initial_date->modify('+2 month');
                $initialflag = 0;
                $initialdiff = $deposit_date->diff($initial)->days;
            } else {
                $initial_date->modify('+3 month');
                $initialflag = 1;
                $initial->modify('+1 month');
                $initialdiff = $deposit_date->diff($initial)->days;
            }

            $end_date = clone $deposit_date;
            $end_date->modify('+2 year');

            $nuc = new SixMonth_fund;
            $nuc->nuc = $request->nuc;
            $nuc->currency = $request->selectCurrency;
            $nuc->amount = $request->amount;
            $nuc->fk_client = $fk_client;
            $nuc->deposit_date = $deposit_date;
            $nuc->initial_date = $initial_date;
            $nuc->end_date = $end_date;
            $nuc->fk_application = $request->fk_application;
            $nuc->fk_payment_form = $request->fk_payment_form;
            $nuc->fk_charge = $request->fk_charge ?? 1;
            $nuc->fk_insurance = $request->fk_insurance;
            $nuc->fk_agent = $request->fk_agent;
            $nuc->pair_non = $request->pair_non;
            $nuc->fk_cp = $request->fk_cp;
            $nuc->save();
            $fk_nuc = $nuc->id;

            $date1 = clone $initial_date;
            $date2 = clone $deposit_date;
            $number = 1;
            for ($cont = 0; $cont < 22; $cont += 2) {
                $coupon = new Coupon;
                $coupon->number = $number;
                if ($cont != 0) $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                $coupon->amount = intval($diff) * (($nuc->amount * ($request->yield / 100)) / 360);
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $nuc->id;
                $coupon->save();
                $number++;
                $date2 = clone $date1;
            }
            $coupon = new Coupon;
            $coupon->number = $number;
            $date1->modify('+2 month');
            $diff = $date1->diff($date2)->days;
            $coupon->amount = intval($diff) * (($nuc->amount * ($request->yield / 100)) / 360);
            if ($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($request->yield / 100)) / 360);
            else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($request->yield / 100)) / 360);
            $coupon->pay_date = $date1;
            $coupon->fk_nuc = $nuc->id;
            $coupon->save();

            if ($request->charge_moves != null) {
                foreach ($request->charge_moves as $charge) {
                    $chargeMove = new Charge_Moves;
                    $chargeMove->amount = $charge["amount"];
                    $chargeMove->apply_date = $charge['apply_date'];
                    $chargeMove->fk_fund = $nuc->id;
                    $chargeMove->fk_charge = $charge['fk_charge'];
                    $chargeMove->save();
                }
            }
        }

        $opening = new Opening;
        $opening->fk_agent = $request->fk_agent;
        $opening->fk_client = $fk_client;
        $opening->fk_nuc = $fk_nuc;
        $opening->fk_insurance = $request->fk_insurance;
        $opening->save();

        // 🟢 Si proviene de una solicitud de agente, marcarla como Procesada y heredar documentos
        if ($request->filled('fk_opening_request') && intval($request->fk_opening_request) > 0) {
            $reqId = intval($request->fk_opening_request);
            DB::table('Opening_Requests')->where('id', $reqId)->update([
                'status'       => 2,
                'fk_opening'   => $opening->id,
                'processed_by' => User::user_id(),
                'processed_at' => Carbon::now(),
                'updated_at'   => Carbon::now()
            ]);

            $reqDocs = Documents::where('fk_key', $reqId)
                ->where('fk_section', 52)
                ->where('name', 'like', 'sol_%')
                ->get();

            foreach ($reqDocs as $rDoc) {
                $newDoc = new Documents;
                $newDoc->fk_key     = $opening->id;
                $newDoc->fk_section = 52;
                $newDoc->name       = $rDoc->name;
                $newDoc->save();
            }
        } elseif ($request->filled('fk_renewal_request') && intval($request->fk_renewal_request) > 0) {
            $renId = intval($request->fk_renewal_request);
            DB::table('Renewal_Requests')->where('id', $renId)->update([
                'status'       => 2,
                'fk_opening'   => $opening->id,
                'processed_by' => User::user_id(),
                'processed_at' => Carbon::now(),
                'updated_at'   => Carbon::now()
            ]);

            // Heredar documentos de la renovación (Sección 52) al nuevo contrato (Sección 52)
            $renDocs = Documents::where('fk_key', $renId)->where('fk_section', 52)->where('name', 'like', 'ren_%')->get();
            foreach ($renDocs as $rDoc) {
                $newDoc = new Documents;
                $newDoc->fk_key     = $opening->id;
                $newDoc->fk_section = 52;
                $newDoc->name       = $rDoc->name;
                $newDoc->save();
            }
        }

        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 52);
        $openings = $this->ReturnData($profile, $request->active);

        return response()->json([
            'status'       => true,
            'message'      => 'Apertura Creada Correctamente',
            'openings'     => $openings,
            'profile'      => $profile,
            'permission'   => $perm_btn,
            'requests_map' => $this->getRequestsMap(),
            'renewals_map' => $this->getRenewalsMap(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $opening = Opening::where('id', $request->id)->first();
        $fk_client = $request->fk_client;
        if ($fk_client == 0) {
            $client = new Client;
            $client->name = $request->name;
            $client->firstname = $request->firstname;
            $client->lastname = $request->lastname;
            $client->birth_date = $request->birth_date;
            $client->rfc = $request->rfc;
            $client->curp = $request->curp;
            $client->cellphone = $request->cellphone;
            $client->email = $request->email;
            $client->domicile = $request->domicile;
            $client->save();
            $fk_client = $client->id;
        }

        if ($request->fund_type == "CP" || $request->fund_type == "MP") {
            Nuc::where('id', $opening->fk_nuc)->update([
                'nuc' => $request->nuc, 'fk_client' => $request->fk_client, 'fk_application' => $request->fk_application,
                'fk_payment_form' => $request->fk_payment_form, 'fk_charge' => $request->fk_charge,
                'fk_insurance' => $request->fk_insurance, 'currency' => $request->currency,
                'fk_agent' => $request->fk_agent, 'estatus' => $request->estatus, 'fst_scnd' => $request->fst_scnd
            ]);
        } else {
            $deposit_date = new DateTime($request->deposit_date);
            $initial_date = new DateTime($deposit_date->format('Y') . "-" . $deposit_date->format('m') . "-01");
            $initial = clone $initial_date;
            $initialflag = 0;
            $initialdiff = 0;

            if (intval($deposit_date->format('d')) <= 10) {
                $initial_date->modify('+2 month');
                $initialflag = 0;
                $initialdiff = $deposit_date->diff($initial)->days;
            } else {
                $initial_date->modify('+3 month');
                $initialflag = 1;
                $initial->modify('+1 month');
                $initialdiff = $deposit_date->diff($initial)->days;
            }

            $end_date = clone $deposit_date;
            $end_date->modify('+2 year');

            $nuc = DB::table('SixMonth_fund')->select('*')
                ->join('Insurance', 'Insurance.id', '=', 'fk_insurance')
                ->where('SixMonth_fund.id', $opening->fk_nuc)->first();

            SixMonth_fund::where('id', $opening->fk_nuc)->update([
                'nuc' => $request->nuc, 'fk_client' => $request->fk_client, 'amount' => $request->amount,
                'currency' => $request->currency, 'deposit_date' => $deposit_date, 'initial_date' => $initial_date,
                'end_date' => $end_date, 'fk_application' => $request->fk_application,
                'fk_payment_form' => $request->fk_payment_form, 'fk_charge' => $request->fk_charge,
                'fk_insurance' => $request->fk_insurance, 'fk_agent' => $request->fk_agent,
                'pair_non' => $request->pair_non, 'fk_cp' => $request->fk_cp
            ]);

            if (floatval($request->amount) != floatval($nuc->amount) || $request->currency != $nuc->currency || $request->deposit_date != $nuc->deposit_date) {
                $coupons = Coupon::where("fk_nuc", $opening->fk_nuc)->get();
                foreach ($coupons as $coup) {
                    $coup->delete();
                }

                $date1 = clone $initial_date;
                $date2 = clone $deposit_date;
                $number = 1;
                for ($cont = 0; $cont < 22; $cont += 2) {
                    $coupon = new Coupon;
                    $coupon->number = $number;
                    if ($cont != 0) $date1->modify('+2 month');
                    $diff = $date1->diff($date2)->days;
                    $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield / 100)) / 360);
                    $coupon->pay_date = $date1;
                    $coupon->fk_nuc = $opening->fk_nuc;
                    $coupon->save();
                    $number++;
                    $date2 = clone $date1;
                }
                $coupon = new Coupon;
                $coupon->number = $number;
                $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield / 100)) / 360);
                if ($initialflag == 0) $coupon->amount += intval($initialdiff) * (($request->amount * ($nuc->yield / 100)) / 360);
                else $coupon->amount -= intval($initialdiff) * (($request->amount * ($nuc->yield / 100)) / 360);
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $opening->fk_nuc;
                $coupon->save();
            }

            $charges = Charge_Moves::where("fk_fund", $opening->fk_nuc)->get();
            foreach ($charges as $charge) {
                $charge->delete();
            }

            if ($request->charge_moves != null) {
                foreach ($request->charge_moves as $charge) {
                    $chargeMove = new Charge_Moves;
                    $chargeMove->amount = $charge["amount"];
                    $chargeMove->apply_date = $charge['apply_date'];
                    $chargeMove->fk_fund = $opening->fk_nuc;
                    $chargeMove->fk_charge = $charge['fk_charge'];
                    $chargeMove->save();
                }
            }
        }

        Opening::where('id', $request->id)->update([
            'fk_agent' => $request->fk_agent, 'fk_client' => $request->fk_client, 'fk_insurance' => $request->fk_insurance
        ]);

        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 52);
        $openings = $this->ReturnData($profile, $request->active);

        return response()->json([
            'status'       => true,
            'message'      => 'Apertura Actualizada',
            'openings'     => $openings,
            'profile'      => $profile,
            'permission'   => $perm_btn,
            'requests_map' => $this->getRequestsMap(),
            'renewals_map' => $this->getRenewalsMap(),
        ]);
    }

    public function destroy($id, Request $request)
    {
        $opening = DB::table('Opening')->join('Insurance', 'Opening.fk_insurance', '=', 'Insurance.id')->where('Opening.id', $id)->first();

        if ($opening->fund_type == "CP" || $opening->fund_type == "MP") {
            $fund = Nuc::find($opening->fk_nuc);
            if ($fund) $fund->delete();
        } else {
            $SixMonth_fund = SixMonth_fund::find($opening->fk_nuc);
            if ($SixMonth_fund) $SixMonth_fund->delete();
        }

        $opening = Opening::find($id);
        $opening->delete();

        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 52);
        $openings = $this->ReturnData($profile, $request->active);

        return response()->json([
            'status'       => true,
            'message'      => 'Apertura eliminada',
            'openings'     => $openings,
            'profile'      => $profile,
            'permission'   => $perm_btn,
            'requests_map' => $this->getRequestsMap(),
            'renewals_map' => $this->getRenewalsMap(),
        ]);
    }

    public function GetinfoStatus($id)
    {
        $opening = Opening::where('id', $id)->first();
        return response()->json(['status' => true, "data" => $opening]);
    }

    public function updateStatus(Request $request)
    {
        $status = Opening::where('id', $request->id)->first();
        if (!$status) {
            return response()->json(['status' => false, 'message' => 'Apertura no encontrada.'], 404);
        }

        $estatusAnterior = $status->fk_status;
        $agentStatusAnterior = $status->agent_status;

        $status->fk_status = $request->fk_status;
        $status->pick_status = $request->pick_status;
        $status->limit_status = $request->limit_status;
        $status->agent_status = $request->agent_status;
        $status->office_status = $request->office_status;
        $status->finestra_status = $request->finestra_status;
        $status->save();

        // Generar notificación si hubo cambio de estatus
        if ($estatusAnterior != $request->fk_status || $agentStatusAnterior != $request->agent_status) {
            $profile = User::findProfile();
            $currentUserId = User::user_id();

            $client = DB::table('Client')->where('id', $status->fk_client)->first();
            $clientName = $client
                ? trim(($client->name ?? '') . ' ' . ($client->firstname ?? '') . ' ' . ($client->lastname ?? ''))
                : 'Cliente';

            $insurance = Insurance::find($status->fk_insurance);
            $nucNumber = ($insurance && $insurance->fund_type === 'LP')
                ? DB::table('SixMonth_fund')->where('id', $status->fk_nuc)->value('nuc')
                : DB::table('Nuc')->where('id', $status->fk_nuc)->value('nuc');

            $statusName = DB::table('Status')->where('id', $request->fk_status)->value('name') ?? 'Actualizado';
            $targetUrl = url('process/opening/opening') . '?opening_id=' . $status->id;

            if ($profile == 12) {
                $agentUser = User::find($currentUserId);
                $agentName = $agentUser ? trim(($agentUser->name ?? '') . ' ' . ($agentUser->firstname ?? '')) : 'El agente';

                $recipientId = $this->staffUserId;
                $notifTitle  = 'Actualización en Apertura';
                $notifMsg    = "{$agentName} actualizó el estatus de {$clientName} (" . ($nucNumber ?: 'NUC') . ") a: {$statusName}";
            } else {
                $recipientId = $status->fk_agent;
                $notifTitle  = 'Cambio de estatus en Apertura';
                $notifMsg    = "La apertura de {$clientName} (" . ($nucNumber ?: 'PENDIENTE') . ") cambió a estatus: {$statusName}";
            }

            if ($recipientId && $recipientId != $currentUserId) {
                DB::table('notifications')->insert([
                    'fk_user'    => $recipientId,
                    'type'       => 'opening_status',
                    'title'      => $notifTitle,
                    'message'    => $notifMsg,
                    'url'        => $targetUrl,
                    'is_read'    => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 52);
        $openings = $this->ReturnData($profile, $request->active);

        return response()->json([
            'status'       => true,
            'message'      => 'Estatus Actualizado',
            'openings'     => $openings,
            'profile'      => $profile,
            'permission'   => $perm_btn,
            'requests_map' => $this->getRequestsMap(),
            'renewals_map' => $this->getRenewalsMap(),
        ]);
    }

    private function getRequestsMap()
    {
        return DB::table('Opening_Requests')
            ->whereNotNull('fk_opening')
            ->whereNull('deleted_at')
            ->pluck('id', 'fk_opening')
            ->toArray();
    }

    public function getClientContracts($clientId)
    {
        $nucsDestino = DB::table('Nuc')
            ->join('Insurance', 'Nuc.fk_insurance', '=', 'Insurance.id')
            ->select(
                'Nuc.id',
                DB::raw("CONCAT(Nuc.nuc, ' (', Insurance.fund_type, ' - ', Nuc.currency, ')') as label")
            )
            ->where('Nuc.fk_client', $clientId)
            ->whereIn('Insurance.fund_type', ['CP', 'MP'])
            ->where('Nuc.active_stat', 1)
            ->whereNull('Nuc.deleted_at')
            ->orderBy('Nuc.nuc')
            ->get();

        $nucsCpMp = DB::table('Nuc')
            ->join('Insurance', 'Nuc.fk_insurance', '=', 'Insurance.id')
            ->select(
                DB::raw("CONCAT('NUC_', Nuc.id) as composite_id"),
                DB::raw("CONCAT(Nuc.nuc, ' (', Insurance.fund_type, ' - ', Nuc.currency, ')') as label")
            )
            ->where('Nuc.fk_client', $clientId)
            ->whereIn('Insurance.fund_type', ['CP', 'MP'])
            ->where('Nuc.active_stat', 1)
            ->whereNull('Nuc.deleted_at');

        $nucsLp = DB::table('SixMonth_fund')
            ->join('Insurance', 'SixMonth_fund.fk_insurance', '=', 'Insurance.id')
            ->select(
                DB::raw("CONCAT('SMF_', SixMonth_fund.id) as composite_id"),
                DB::raw("CONCAT(SixMonth_fund.nuc, ' (LP - ', SixMonth_fund.currency, ')') as label")
            )
            ->where('SixMonth_fund.fk_client', $clientId)
            ->where('Insurance.fund_type', 'LP')
            ->where('SixMonth_fund.active_stat', 1)
            ->whereNull('SixMonth_fund.deleted_at');

        $nucsOrigen = $nucsCpMp->unionAll($nucsLp)->orderBy('label')->get();

        return response()->json([
            'status'      => true,
            'nucsDestino' => $nucsDestino,
            'nucsOrigen'  => $nucsOrigen
        ]);
    }

    // ========================================================= renovaicones ==================================================
    private function getRenewalsMap()
    {
        return DB::table('Renewal_Requests')
            ->whereNotNull('fk_opening')
            ->whereNull('deleted_at')
            ->pluck('id', 'fk_opening')
            ->toArray();
    }

    public function getRenovationsList(Request $request)
    {
        try {
            $profile = User::findProfile();
            $user = User::user_id();
            $showAll = intval($request->input('show_all', 0));
            $targetId = intval($request->input('target_id', 0));

            $query = DB::table('Renewal_Requests as r')
                ->leftJoin('users as u', 'r.fk_agent', '=', 'u.id')
                ->select(
                    'r.*',
                    DB::raw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) as agent_name")
                )
                ->whereNull('r.deleted_at')
                ->when($profile == 12, function ($q) use ($user) {
                    $q->where('r.fk_agent', $user);
                })
                ->when($showAll == 0, function ($q) use ($targetId) {
                    $q->where(function ($sub) use ($targetId) {
                        $sub->where('r.status', 1);
                        if ($targetId > 0) {
                            $sub->orWhere('r.id', $targetId);
                        }
                    });
                });

            $recordsTotal = $query->count();

            // Buscador global
            $searchValue = trim($request->input('search.value', ''));
            if ($searchValue !== '') {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('r.id', 'like', "%{$searchValue}%")
                      ->orWhere('r.client_name', 'like', "%{$searchValue}%")
                      ->orWhere('r.contract_label', 'like', "%{$searchValue}%")
                      ->orWhereRaw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) LIKE ?", ["%{$searchValue}%"])
                      ->orWhere('r.amount', 'like', "%{$searchValue}%");
                });
            }

            // Buscador por columna
            if ($request->has('columns')) {
                foreach ($request->input('columns') as $col) {
                    $colData = $col['data'] ?? null;
                    $colSearch = trim($col['search']['value'] ?? '');
                    if ($colSearch !== '' && $colData) {
                        if ($colData === 'folio') $query->where('r.id', 'like', "%{$colSearch}%");
                        elseif ($colData === 'fecha') $query->where('r.created_at', 'like', "%{$colSearch}%");
                        elseif ($colData === 'agente') $query->whereRaw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) LIKE ?", ["%{$colSearch}%"]);
                        elseif ($colData === 'cliente') $query->where('r.client_name', 'like', "%{$colSearch}%");
                        elseif ($colData === 'contrato') $query->where('r.contract_label', 'like', "%{$colSearch}%");
                        elseif ($colData === 'monto') $query->where('r.amount', 'like', "%{$colSearch}%");
                    }
                }
            }

            $recordsFiltered = $query->count();

            if ($targetId > 0) {
                $query->orderByRaw("CASE WHEN r.id = ? THEN 0 ELSE 1 END", [$targetId]);
            }

            if ($request->has('order')) {
                $colIdx = $request->input('order.0.column');
                $colDir = $request->input('order.0.dir', 'desc');
                $colName = $request->input("columns.{$colIdx}.data");

                $orderMap = [
                    'folio'    => 'r.id',
                    'fecha'    => 'r.created_at',
                    'agente'   => 'agent_name',
                    'cliente'  => 'r.client_name',
                    'contrato' => 'r.contract_label',
                    'monto'    => 'r.amount',
                    'estatus'  => 'r.status'
                ];
                if (isset($orderMap[$colName])) {
                    $query->orderBy($orderMap[$colName], $colDir);
                } else {
                    $query->orderBy('r.id', 'desc');
                }
            } else {
                $query->orderBy('r.id', 'desc');
            }

            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 25));
            $rows = $query->skip($start)->take($length)->get();

            $pendingCount = DB::table('Renewal_Requests')
                ->whereNull('deleted_at')
                ->where('status', 1)
                ->when($profile == 12, function ($q) use ($user) { $q->where('fk_agent', $user); })
                ->count();

            $dataFormatted = $rows->map(function ($r) {
                $badge = '<span class="badge badge-warning px-3 py-2" style="border-radius:20px;">Pendiente</span>';
                if ($r->status == 2) $badge = '<span class="badge badge-success px-3 py-2" style="border-radius:20px;">Procesada</span>';
                elseif ($r->status == 3) $badge = '<span class="badge badge-danger px-3 py-2" style="border-radius:20px;">Rechazada</span>';

                return [
                    'DT_RowId' => 'ren_' . $r->id,
                    'folio'    => '<span class="font-weight-bold" style="font-family:monospace;">#' . $r->id . '</span>',
                    'fecha'    => '<span class="small text-muted">' . Carbon::parse($r->created_at)->format('d/m/Y g:i A') . '</span>',
                    'agente'   => '<span class="small font-weight-bold text-muted">' . e($r->agent_name) . '</span>',
                    'cliente'  => '<span class="font-weight-bold text-dark">' . e($r->client_name) . '</span>',
                    'contrato' => '<span class="font-weight-bold text-primary">' . e($r->contract_label) . '</span>',
                    'monto'    => '<span class="text-success font-weight-bold">$' . number_format($r->amount, 2) . '</span>',
                    'estatus'  => $badge,
                    'acciones' => '<button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="verDetalleRenovacion(' . $r->id . ')" style="border-radius:6px;"><i class="fas fa-eye mr-1"></i> Revisar</button>'
                ];
            });

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'pendingCount'    => $pendingCount,
                'data'            => $dataFormatted
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ], 500);
        }
    }

    public function getRenovationDetail($id)
    {
        $req = DB::table('Renewal_Requests as r')
            ->leftJoin('users as u', 'r.fk_agent', '=', 'u.id')
            ->select('r.*', DB::raw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) as agent_name"), 'u.email as agent_email')
            ->where('r.id', $id)->first();

        $req->nuc_dest_label = 'N/A';
        if (!empty($req->fk_nuc_dest)) {
            $req->nuc_dest_label = DB::table('Nuc')->where('id', $req->fk_nuc_dest)->value('nuc') ?? 'N/A';
        }

        if (!$req) return response()->json(['status' => false, 'message' => 'No encontrada'], 404);

        $docs = Documents::where('fk_key', $id)->where('fk_section', 52)->where('name', 'like', 'ren_%')->get()
            ->map(function ($d) {
                $label = 'Documento';
                if (strpos($d->name, 'ren_ine_') === 0) $label = 'Identificación Oficial';
                elseif (strpos($d->name, 'ren_domicilio_') === 0) $label = 'Comprobante Domicilio';
                elseif (strpos($d->name, 'ren_estado_cuenta_') === 0) $label = 'Estado de Cuenta';
                elseif (strpos($d->name, 'ren_csf_') === 0) $label = 'Constancia Fiscal (CSF)';
                $d->doc_label = $label;
                return $d;
            });

        return response()->json(['status' => true, 'data' => $req, 'documents' => $docs]);
    }

    public function rejectRenovation(Request $request, $id)
    {
        if (User::findProfile() == 12) return response()->json(['status' => false], 403);
        DB::table('Renewal_Requests')->where('id', $id)->update([
            'status' => 3, 'staff_notes' => $request->input('staff_notes'),
            'processed_by' => User::user_id(), 'processed_at' => Carbon::now(), 'updated_at' => Carbon::now()
        ]);
        return response()->json(['status' => true, 'message' => 'Renovación observada/rechazada.']);
    }
}