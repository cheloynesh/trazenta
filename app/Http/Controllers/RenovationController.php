<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Documents;
use Carbon\Carbon;
use DB;

class RenovationController extends Controller
{
    protected $staffUserId = 36; 

    public function index()
    {
        $profile = User::findProfile();
        $user = User::user_id();

        $pendingCount = DB::table('Renewal_Requests')
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('fk_agent', $user);
            })
            ->count();

        return view('process.renovation.renovation', compact('profile', 'pendingCount'));
    }

    public function create()
    {
        $userId = User::user_id();
        $profile = User::findProfile();

        $clients = Client::when($profile == 12, function ($q) use ($userId) {
                $q->where('fk_agent', $userId)
                  ->orWhereIn('id', function ($sub) use ($userId) {
                      $sub->select('fk_client')->from('Nuc')->where('fk_agent', $userId);
                  })
                  ->orWhereIn('id', function ($sub) use ($userId) {
                      $sub->select('fk_client')->from('SixMonth_fund')->where('fk_agent', $userId);
                  });
            })
            ->orderBy('name')
            ->get();

        return view('process.renovation.renovationCreate', compact('profile', 'clients'));
    }

    // Obtener contratos activos del cliente seleccionado (CP, MP y LP)
    public function getClientContracts($clientId)
    {
        // 1. Contratos Destino para Reinversión (Solo CP y MP)
        $nucsDestino = DB::table('Nuc')
            ->join('Insurance', 'Nuc.fk_insurance', '=', 'Insurance.id')
            ->select('Nuc.id', DB::raw("CONCAT(Nuc.nuc, ' (', Insurance.fund_type, ' - ', Nuc.currency, ')') as label"))
            ->where('Nuc.fk_client', $clientId)
            ->whereIn('Insurance.fund_type', ['CP', 'MP'])
            ->where('Nuc.active_stat', 1)
            ->whereNull('Nuc.deleted_at')
            ->orderBy('Nuc.nuc')
            ->get();

        // 2. Obligaciones a Vencer (Todos: CP, MP y LP)
        $nucsCpMp = DB::table('Nuc')
            ->join('Insurance', 'Nuc.fk_insurance', '=', 'Insurance.id')
            ->select(
                DB::raw("CONCAT('NUC_', Nuc.id) as composite_id"),
                DB::raw("CONCAT(Nuc.nuc, ' (', Insurance.fund_type, ' - ', Nuc.currency, ')') as label"),
                DB::raw("IFNULL(Nuc.fst_amount, 0) as amount")
            )
            ->where('Nuc.fk_client', $clientId)
            ->where('Nuc.active_stat', 1)
            ->whereNull('Nuc.deleted_at');

        $nucsLp = DB::table('SixMonth_fund')
            ->join('Insurance', 'SixMonth_fund.fk_insurance', '=', 'Insurance.id')
            ->select(
                DB::raw("CONCAT('SMF_', SixMonth_fund.id) as composite_id"),
                DB::raw("CONCAT(SixMonth_fund.nuc, ' (LP - ', SixMonth_fund.currency, ')') as label"),
                DB::raw("IFNULL(SixMonth_fund.amount, 0) as amount")
            )
            ->where('SixMonth_fund.fk_client', $clientId)
            ->where('SixMonth_fund.active_stat', 1)
            ->whereNull('SixMonth_fund.deleted_at');

        $contracts = $nucsCpMp->unionAll($nucsLp)->orderBy('label')->get();

        return response()->json([
            'status'      => true,
            'contracts'   => $contracts,
            'nucsDestino' => $nucsDestino
        ]);
    }

    public function storeRequest(Request $request)
    {
        try {
            $userId = User::user_id();
            $fkClient = intval($request->input('fk_client', 0));

            if ($fkClient <= 0) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Debes seleccionar un cliente registrado.'
                ], 422);
            }

            // Validar mayoría de edad en beneficiarios 1 y 2
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

            $client = Client::find($fkClient);
            $clientFullName = $client
                ? trim(($client->name ?? '') . ' ' . ($client->firstname ?? '') . ' ' . ($client->lastname ?? ''))
                : $request->input('client_name');

            // Descomponer obligación seleccionada (NUC_10 o SMF_5)
            $contractType = null;
            $fkNuc = null;
            if ($request->filled('contract_composite_id')) {
                $parts = explode('_', $request->input('contract_composite_id'));
                if (count($parts) === 2) {
                    $contractType = $parts[0];
                    $fkNuc = intval($parts[1]);
                }
            }

            $sameEmail = $request->has('same_email') ? 1 : 0;
            $samePhone = $request->has('same_phone') ? 1 : 0;

            $clientEmail = $sameEmail ? ($client->email ?? null) : $request->input('client_email');
            $clientPhone = $samePhone ? ($client->cellphone ?? null) : $request->input('client_phone');

            $montoLimpio = (float) preg_replace('/[^0-9.]/', '', $request->input('amount', 0));

            $paymentChoice = intval($request->input('payment_choice', 1));
            $fkNucDest = ($paymentChoice === 1 && $request->filled('fk_nuc_dest')) ? $request->input('fk_nuc_dest') : null;

            $reqId = DB::table('Renewal_Requests')->insertGetId([
                'fk_agent'           => $userId,
                'fk_client'          => $fkClient,
                'client_name'        => $clientFullName,
                'contract_type'      => $contractType,
                'fk_nuc'             => $fkNuc,
                'contract_label'     => $request->input('contract_label'),
                'amount'             => $montoLimpio,
                'same_email'         => $sameEmail,
                'client_email'       => $clientEmail,
                'same_phone'         => $samePhone,
                'client_phone'       => $clientPhone,
                'occupation'         => $request->input('occupation'),
                'company_name'       => $request->input('company_name'),
                'rfc'                => strtoupper(trim($request->input('rfc'))),
                'payment_choice'     => $paymentChoice,
                'fk_nuc_dest'        => $fkNucDest,
                'beneficiaries_qty'  => $qtyBen,
                'beneficiary_1_name' => ($qtyBen === 1 || $qtyBen === 2) ? $request->input('beneficiary_1_name') : null,
                'beneficiary_1_dob'  => (($qtyBen === 1 || $qtyBen === 2) && $request->filled('beneficiary_1_dob')) ? $request->input('beneficiary_1_dob') : null,
                'beneficiary_1_pct'  => ($qtyBen === 1 || $qtyBen === 2) ? $request->input('beneficiary_1_pct') : null,
                'beneficiary_2_name' => ($qtyBen === 2) ? $request->input('beneficiary_2_name') : null,
                'beneficiary_2_dob'  => ($qtyBen === 2 && $request->filled('beneficiary_2_dob')) ? $request->input('beneficiary_2_dob') : null,
                'beneficiary_2_pct'  => ($qtyBen === 2) ? $request->input('beneficiary_2_pct') : null,
                'beneficiaries_extra'=> ($qtyBen === 3) ? $request->input('beneficiaries_extra') : null,
                'status'             => 1,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now()
            ]);

            // Guardar documentos con prefijo ren_ en fk_section = 52
            $filesMap = [
                'file_ine',
                'file_domicilio',
                'file_estado_cuenta',
                'file_csf'
            ];

            foreach ($filesMap as $inputName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    if ($file->isValid()) {
                        $prefix = str_replace('file_', '', $inputName);
                        $filename = "ren_" . $prefix . "_52_" . $reqId . "_" . time() . "_" . uniqid() . "_" . $file->getClientOriginalName();

                        $doc = new Documents;
                        $doc->fk_key     = $reqId;
                        $doc->fk_section = 52;
                        $doc->name       = $filename;
                        $doc->save();

                        $file->move(public_path('files/openingfiles'), $filename);
                    }
                }
            }

            // Notificar al usuario de staff (ID fijo) que hay una nueva solicitud de renovación

            if ($this->staffUserId && $this->staffUserId != $userId) {
                $agentUser = User::find($userId);
                $agentName = $agentUser ? trim(($agentUser->name ?? '') . ' ' . ($agentUser->firstname ?? '')) : 'Un agente';

                $contratoNotif = $contract_label ?? $request->input('contract_label', 'Contrato');
                $clienteNotif  = $client_name ?? $request->input('client_name', 'Cliente');
                $montoNotif    = (float) preg_replace('/[^0-9.]/', '', $request->input('amount', 0));

                DB::table('notifications')->insert([
                    'fk_user'    => $this->staffUserId,
                    'type'       => 'new_renewal_request',
                    'title'      => 'Nueva Solicitud de Renovación #' . $reqId,
                    'message'    => "{$agentName} solicitó renovar {$contratoNotif} por $" . number_format($montoNotif, 2) . " ({$clienteNotif}).",
                    'url'        => url('process/opening/opening') . '?ren_id=' . $reqId,
                    'is_read'    => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Solicitud de renovación enviada correctamente (Folio #' . $reqId . ').'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al registrar renovación: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRequestsList(Request $request)
    {
        $profile = User::findProfile();
        $user = User::user_id();
        $showAll = intval($request->input('show_all', 0));

        $requests = DB::table('Renewal_Requests as r')
            ->leftJoin('users as u', 'r.fk_agent', '=', 'u.id')
            ->select(
                'r.*',
                DB::raw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) as agent_name")
            )
            ->whereNull('r.deleted_at')
            ->when($profile == 12, function ($q) use ($user) {
                $q->where('r.fk_agent', $user);
            })
            ->when($showAll == 0, function ($q) {
                $q->where('r.status', 1);
            })
            ->orderBy('r.id', 'desc')
            ->get()
            ->map(function ($item) {
                $item->formatted_date = Carbon::parse($item->created_at)->format('d/m/Y g:i A');
                $item->formatted_amount = '$' . number_format($item->amount, 2);
                return $item;
            });

        return response()->json([
            'status'   => true,
            'requests' => $requests,
            'profile'  => $profile
        ]);
    }

    public function getRequestDetail($id)
    {
        $req = DB::table('Renewal_Requests as r')
            ->leftJoin('users as u', 'r.fk_agent', '=', 'u.id')
            ->select(
                'r.*',
                DB::raw("CONCAT(IFNULL(u.name,''), ' ', IFNULL(u.firstname,'')) as agent_name"),
                'u.email as agent_email'
            )
            ->where('r.id', $id)
            ->first();

        if (!$req) {
            return response()->json(['status' => false, 'message' => 'Solicitud no encontrada'], 404);
        }

        $docs = Documents::where('fk_key', $id)
            ->where('fk_section', 52)
            ->where('name', 'like', 'ren_%')
            ->get()
            ->map(function ($d) {
                $label = 'Documento Adjunto';
                if (strpos($d->name, 'ren_ine_') === 0) $label = 'Identificación Oficial (INE)';
                elseif (strpos($d->name, 'ren_domicilio_') === 0) $label = 'Comprobante de Domicilio (< 90 días)';
                elseif (strpos($d->name, 'ren_estado_cuenta_') === 0) $label = 'Estado de Cuenta (< 90 días)';
                elseif (strpos($d->name, 'ren_csf_') === 0) $label = 'Constancia de Situación Fiscal (CSF)';
                $d->doc_label = $label;
                return $d;
            });

        $req->nuc_dest_label = 'N/A';
        if ($req->fk_nuc_dest) {
            $req->nuc_dest_label = DB::table('Nuc')->where('id', $req->fk_nuc_dest)->value('nuc');
        }

        return response()->json([
            'status'    => true,
            'data'      => $req,
            'documents' => $docs
        ]);
    }

    public function processRequest(Request $request, $id)
    {
        $profile = User::findProfile();
        if ($profile == 12) {
            return response()->json(['status' => false, 'message' => 'Sin permisos.'], 403);
        }

        DB::table('Renewal_Requests')->where('id', $id)->update([
            'status'       => 2,
            'staff_notes'  => $request->input('staff_notes'),
            'processed_by' => User::user_id(),
            'processed_at' => Carbon::now(),
            'updated_at'   => Carbon::now()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Renovación marcada como procesada.'
        ]);
    }

    public function rejectRequest(Request $request, $id)
    {
        $profile = User::findProfile();
        if ($profile == 12) {
            return response()->json(['status' => false, 'message' => 'Sin permisos.'], 403);
        }

        DB::table('Renewal_Requests')->where('id', $id)->update([
            'status'       => 3,
            'staff_notes'  => $request->input('staff_notes'),
            'processed_by' => User::user_id(),
            'processed_at' => Carbon::now(),
            'updated_at'   => Carbon::now()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Renovación rechazada / con observaciones.'
        ]);
    }
}