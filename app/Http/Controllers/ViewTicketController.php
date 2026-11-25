<?php

namespace App\Http\Controllers;

use App\Mail\TicketStatusUpdatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Profile;
use App\User;
use App\Permission;
use App\Client;
use App\Insurance;
use App\Ticket;
use App\Documents;
use App\Status;
use App\Ticket_Folios;
use App\Currency;
use App\Paymentform;
use App\Charge;
use DB;
use Carbon\Carbon;

class ViewTicketController extends Controller
{
    protected $mapperInversiones = [
        1 => ['nombre' => 'ABONO/ INCREMENTO', 'dias' => 3],
        2 => ['nombre' => 'AGREGAR CUENTA BANCARIA', 'dias' => 3],
        3 => ['nombre' => 'CAMBIO DE DATOS DEL BENEFICIARIO', 'dias' => 5],
        4 => ['nombre' => 'CAMBIO DE DATOS DEL CONTRATANTE', 'dias' => 5],
        5 => ['nombre' => 'CAMBIO DE FORMA DE PAGO DEL PREMIO', 'dias' => 5],
        6 => ['nombre' => 'CAMBIO DE PLAN /MOV INTERNO', 'dias' => 5],
        7 => ['nombre' => 'RETIRO PARCIAL', 'dias' => 10],
        8 => ['nombre' => 'RETIRO TOTAL', 'dias' => 20],
        9 => ['nombre' => 'VARIOS', 'dias' => 5],
    ];

    public function index()
    {
        $user = User::user_id();

        $profiles = Profile::get();
        $profile = User::findProfile();
        $users = User::select('id', DB::raw('CONCAT(name," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
            ->where('fk_profile', '!=', 12)
            ->orderBy("name")
            ->pluck('name', 'id');

        $agents = User::select('id', DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
            ->where('fk_profile', '=', 12)
            ->orderBy('name')
            ->pluck('name', 'id');

        $currencies = Currency::pluck('name', 'id');
        $insurances = Insurance::orderBy('name')->pluck('name', 'id');
        $paymentForms = Paymentform::pluck('name', 'id');
        $charges = Charge::pluck('name', 'id');

        $cmbStatus = Status::select('id', 'name')
            ->where("fk_section", "49")
            ->pluck('name', 'id');

        $perm = Permission::permView($profile, 49);
        $perm_btn = Permission::permBtns($profile, 49);
        $tickets = [];

        // 🟢 Clientes con contratos en Nuc (CP/MP) O SixMonth_fund (LP)
        $clients = Client::select('Client.*')
            ->where(function ($query) use ($user, $profile) {
                $query->whereExists(function ($sub) use ($user, $profile) {
                    $sub->select(DB::raw(1))
                        ->from('Nuc')
                        ->whereColumn('Nuc.fk_client', 'Client.id')
                        ->whereNull('Nuc.deleted_at')
                        ->when($profile == 12, function ($q) use ($user) {
                            $q->where('Nuc.fk_agent', $user);
                        });
                })->orWhereExists(function ($sub) use ($user, $profile) {
                    $sub->select(DB::raw(1))
                        ->from('SixMonth_fund')
                        ->whereColumn('SixMonth_fund.fk_client', 'Client.id')
                        ->whereNull('SixMonth_fund.deleted_at')
                        ->when($profile == 12, function ($q) use ($user) {
                            $q->where('SixMonth_fund.fk_agent', $user);
                        });
                });
            })
            ->orderBy('Client.name')
            ->get();

        if ($perm == 0) {
            return redirect()->route('home');
        } else {
            return view('tickets.viewticket', compact(
                'profiles', 'perm_btn', 'clients', 'insurances', 'tickets', 
                'cmbStatus', 'agents', 'profile', 'users', 'currencies', 
                'paymentForms', 'charges'
            ));
        }
    }

    public function markChatAsRead($id)
    {
        try {
            $user = User::user_id();
            $now = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('Chat_Reads')->updateOrInsert(
                [
                    'fk_key' => $id,
                    'fk_section' => 49,
                    'fk_user'   => $user
                ],
                [
                    'last_read_at' => $now
                ]
            );

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 🟢 OBTENER NUCs POR CLIENTE (LLAMA AL STORED PROCEDURE)
    public function GetNucByClient($id)
    {
        $client = DB::table('Client')
            ->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
            ->where('id', $id)
            ->first();

        $nucs = DB::select('call getAllNucs(?)', [$id]);

        return response()->json(['status' => true, 'data' => $nucs, 'client' => $client]);
    }

    public function GetTicketsList($check, Request $request)
    {
        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 49);
        $user = User::user_id();

        $onlyMyTickets = $request->input('onlyMyTickets', $check);

        $targetId = $request->filled('target_id') ? (int)$request->input('target_id') : null;

        try {
            // Cliente dinámico
            $clientSql = "CONVERT(CASE 
                WHEN Tickets.fk_client = 0 AND (Tickets.nrclient IS NOT NULL AND Tickets.nrclient != '') THEN Tickets.nrclient 
                WHEN Tickets.fk_client = 0 THEN 'SERVICIO PROPIO / N/A' 
                WHEN Client.id IS NOT NULL AND Client.name IS NOT NULL AND Client.name != '' THEN CONCAT(Client.name, ' ', IFNULL(Client.firstname, ''), ' ', IFNULL(Client.lastname, '')) 
                WHEN Tickets.nrclient IS NOT NULL AND Tickets.nrclient != '' THEN Tickets.nrclient 
                ELSE 'N/A' 
            END USING utf8mb4) COLLATE utf8mb4_unicode_ci";

            // NUC / Contrato dinámico evaluando SixMonth_fund (LP) o Nuc (CP/MP) o valor manual
            $policySql = "CONVERT(CASE 
                WHEN Insurance.fund_type = 'LP' AND SixMonth_fund.nuc IS NOT NULL AND SixMonth_fund.nuc != '' THEN SixMonth_fund.nuc 
                WHEN Insurance.fund_type IN ('CP', 'MP') AND Nuc.nuc IS NOT NULL AND Nuc.nuc != '' THEN Nuc.nuc 
                WHEN SixMonth_fund.nuc IS NOT NULL AND SixMonth_fund.nuc != '' THEN SixMonth_fund.nuc 
                WHEN Nuc.nuc IS NOT NULL AND Nuc.nuc != '' THEN Nuc.nuc 
                WHEN Tickets.nrpolicy IS NOT NULL AND Tickets.nrpolicy != '' THEN CONCAT(Tickets.nrpolicy, ' (Manual)') 
                ELSE 'Sin contrato' 
            END USING utf8mb4) COLLATE utf8mb4_unicode_ci";

            // 1. Consulta base uniendo fk_nuc
            $query = DB::table('Tickets')
                ->leftJoin('Client', 'Tickets.fk_client', '=', 'Client.id')
                ->leftJoin('users', 'Tickets.fk_agent', '=', 'users.id')
                ->leftJoin('Insurance', 'Tickets.fk_insurance', '=', 'Insurance.id')
                ->leftJoin('Nuc', function ($join) {
                    $join->on(DB::raw('IFNULL(Tickets.fk_nuc, Tickets.fk_policy)'), '=', 'Nuc.id')
                         ->whereRaw("Insurance.fund_type IN ('CP', 'MP')");
                })
                ->leftJoin('SixMonth_fund', function ($join) {
                    $join->on(DB::raw('IFNULL(Tickets.fk_nuc, Tickets.fk_policy)'), '=', 'SixMonth_fund.id')
                         ->whereRaw("Insurance.fund_type = 'LP'");
                })
                ->join('Status', 'Tickets.fk_status', '=', 'Status.id')
                ->select(
                    'Tickets.id',
                    'Tickets.ins_seg',
                    'Tickets.service',
                    'Tickets.invertion',
                    'Tickets.fk_nuc',
                    'Tickets.fk_policy',
                    'Tickets.nrpolicy',
                    'Tickets.folio',
                    'Tickets.created_at',
                    'Insurance.name as insurance_name',
                    'Insurance.fund_type',
                    DB::raw("{$policySql} as policy_number"),
                    DB::raw("{$clientSql} as client_complete_name"),
                    DB::raw("IFNULL(CONCAT(users.name, ' ', IFNULL(users.firstname, ''), ' ', IFNULL(users.lastname, '')), 'N/A') as agent_name"),
                    'Status.id as status_id',
                    'Status.name as status_name',
                    'Status.color as status_color'
                )
                ->addSelect(['unread_chat_count' => DB::table('Messages')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('Messages.fk_key', 'Tickets.id')
                    ->where('Messages.fk_user', '!=', $user)
                    ->where('Messages.fk_section', 49)
                    ->whereRaw("Messages.created_at > IFNULL((SELECT last_read_at FROM Chat_Reads WHERE fk_key = Tickets.id AND fk_user = ? LIMIT 1), '2000-01-01')", [$user])
                    ->when($profile == 12, function ($q) {
                        return $q->where('Messages.is_internal', 0);
                    })
                ])
                ->whereNull('Tickets.deleted_at')
                ->when($profile == 12, function ($q) use ($user) {
                    $q->where('Tickets.fk_agent', $user);
                })
                ->when($onlyMyTickets == 1, function ($q) use ($user, $targetId) {
                    $q->where(function ($sub) use ($user, $targetId) {
                        $sub->where('Tickets.fk_user', $user);
                        if ($targetId) {
                            $sub->orWhere('Tickets.id', $targetId);
                        }
                    });
                });

            // 2. Filtros dinámicos
            if (!empty($request->users)) {
                $query->whereIn('Tickets.fk_user', $request->users);
            }
            if (!empty($request->agents)) {
                $query->whereIn('Tickets.fk_agent', $request->agents);
            }
            if (!empty($request->insurances)) {
                $query->whereIn('Tickets.fk_insurance', $request->insurances);
            }
            if (!empty($request->services)) {
                $query->where(function ($q) use ($request) {
                    $q->whereIn('Tickets.invertion', $request->services)
                      ->orWhereIn('Tickets.service', $request->services);
                });
            }
            if (!empty($request->status)) {
                $query->whereIn('Tickets.fk_status', $request->status);
            } elseif (intval($request->active) != 1) {
                $query->where(function ($sub) use ($targetId) {
                    $sub->whereNotIn('Tickets.fk_status', [56, 57]);
                    if ($targetId) {
                        $sub->orWhere('Tickets.id', $targetId);
                    }
                });
            }

            $totalRecords = $query->count();

            // 3. Buscador global
            $searchValue = trim($request->input('search.value', ''));
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue, $clientSql, $policySql) {
                    $q->where('Tickets.folio', 'like', "%{$searchValue}%")
                      ->orWhere('Tickets.id', 'like', "%{$searchValue}%")
                      ->orWhere(function ($subClient) use ($searchValue, $clientSql) {
                          $words = array_filter(explode(' ', preg_replace('/\s+/', ' ', $searchValue)));
                          if (!empty($words)) {
                              foreach ($words as $word) {
                                  $subClient->whereRaw("{$clientSql} LIKE ?", ["%{$word}%"]);
                              }
                          }
                      })
                      ->orWhereRaw("{$policySql} LIKE ?", ["%{$searchValue}%"])
                      ->orWhere('users.name', 'like', "%{$searchValue}%")
                      ->orWhere('Insurance.name', 'like', "%{$searchValue}%")
                      ->orWhere('Status.name', 'like', "%{$searchValue}%");

                    $invIds = [];
                    foreach ($this->mapperInversiones as $k => $item) {
                        if (isset($item['nombre']) && mb_stripos($item['nombre'], $searchValue) !== false) {
                            $invIds[] = $k;
                        }
                    }
                    if (!empty($invIds)) {
                        $q->orWhere(function ($subQ) use ($invIds) {
                            $subQ->whereIn('Tickets.invertion', $invIds)
                                 ->orWhereIn('Tickets.service', $invIds);
                        });
                    }
                });
            }

            // 3.1 Buscador por columnas individuales
            if ($request->has('columns')) {
                $columns = $request->input('columns');

                $columnMap = [
                    'folio'          => 'Tickets.folio',
                    'agent_name'     => DB::raw("CONCAT(IFNULL(users.name, ''), ' ', IFNULL(users.firstname, ''), ' ', IFNULL(users.lastname, ''))"),
                    'insbranch'      => DB::raw("IFNULL(Insurance.name, '')"),
                    'fecha_registro' => 'Tickets.created_at',
                    'status_btn'     => 'Status.name'
                ];

                foreach ($columns as $col) {
                    $colData = $col['data'] ?? null;
                    $colSearchVal = trim($col['search']['value'] ?? '');

                    if ($colSearchVal !== '') {
                        if ($colData === 'client_name') {
                            $words = array_filter(explode(' ', preg_replace('/\s+/', ' ', $colSearchVal)));
                            if (!empty($words)) {
                                $query->where(function ($q) use ($words, $clientSql) {
                                    foreach ($words as $word) {
                                        $q->whereRaw("{$clientSql} LIKE ?", ["%{$word}%"]);
                                    }
                                });
                            }
                        } elseif ($colData === 'policy_number') {
                            $query->whereRaw("{$policySql} LIKE ?", ["%{$colSearchVal}%"]);
                        } elseif ($colData === 'tramite') {
                            $invIds = [];
                            foreach ($this->mapperInversiones as $k => $item) {
                                if (isset($item['nombre']) && mb_stripos($item['nombre'], $colSearchVal) !== false) {
                                    $invIds[] = $k;
                                }
                            }
                            if (!empty($invIds)) {
                                $query->where(function ($subQ) use ($invIds) {
                                    $subQ->whereIn('Tickets.invertion', $invIds)
                                         ->orWhereIn('Tickets.service', $invIds);
                                });
                            }
                        } elseif (isset($columnMap[$colData])) {
                            $query->where($columnMap[$colData], 'like', "%{$colSearchVal}%");
                        }
                    }
                }
            }

            $recordsFiltered = $query->count();

            // 4. Paginación y ordenamiento
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($request->filled('target_id')) {
                $targetId = (int)$request->input('target_id');
                $query->orderByRaw("CASE WHEN Tickets.id = ? THEN 0 ELSE 1 END", [$targetId]);
            }

            if ($request->has('order')) {
                $columnIndex = $request->input('order.0.column');
                $columnDir   = $request->input('order.0.dir', 'asc');
                $columnData  = $request->input("columns.{$columnIndex}.data");

                if ($columnData === 'client_name') {
                    $query->orderByRaw("{$clientSql} {$columnDir}");
                } elseif ($columnData === 'policy_number') {
                    $query->orderByRaw("{$policySql} {$columnDir}");
                } elseif (isset($columnMap[$columnData])) {
                    $query->orderBy($columnMap[$columnData], $columnDir);
                } else {
                    $query->orderBy('Tickets.id', 'desc');
                }
            } else {
                $query->orderBy('Tickets.id', 'desc');
            }

            $tickets = $query->skip($start)->take($length)->get();

            // 5. Formatear salida para DataTables
            $dataFormatted = $tickets->map(function ($ticket) use ($profile, $perm_btn) {
                // Folio
                $rawFolio = $ticket->folio ? $ticket->folio : '-';
                $foliosArray = explode(' // ', $rawFolio);
                $badgesFolios = [];
                foreach ($foliosArray as $singleFolio) {
                    $badgesFolios[] = '<span class="badge badge-light border text-secondary font-weight-bold my-1" style="padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 0.8rem; display: inline-block;">' . e(trim($singleFolio)) . '</span>';
                }
                $folioHTML = '<div class="d-flex flex-column align-items-center justify-content-center gap-1">' . implode('', $badgesFolios) . '</div>';

                // Cliente
                $clientHTML = '<span class="font-weight-bold" style="color: #1e293b; font-size: 0.9rem;">' . e($ticket->client_complete_name) . '</span>';

                // NUC / Contrato
                $policyHTML = '<span class="font-weight-bold" style="color: #1e293b; font-size: 0.9rem;">' . e($ticket->policy_number) . '</span>';

                // Agente
                $agentName = !empty($ticket->agent_name) ? e($ticket->agent_name) : 'Sin asignar';
                $agentHTML = '<span class="text-muted font-weight-bold" style="font-size: 0.85rem;"><i class="fas fa-user mr-1 text-primary" style="opacity: 0.7;"></i> ' . $agentName . '</span>';

                // Trámite (Inversiones)
                $servicioTexto = $this->mapperInversiones[$ticket->invertion]['nombre'] ?? $this->mapperInversiones[$ticket->service]['nombre'] ?? 'VARIOS';
                $tramiteHTML = '<div class="d-flex flex-column align-items-center">' .
                    '<span class="font-weight-bold text-dark">Inversión</span>' .
                    '<small class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">' . e($servicioTexto) . '</small>' .
                    '</div>';

                // Fondo / Plazo
                $fondoNombre = $ticket->insurance_name ?? 'Fondo General';
                $tipoPlazo = $ticket->fund_type ?? 'N/A';
                $insbranchHTML = '<div class="d-flex flex-column align-items-center">' .
                    '<span class="font-weight-bold text-dark">' . e($fondoNombre) . '</span>' .
                    '<small class="badge badge-secondary" style="font-size: 0.7rem;">' . e($tipoPlazo) . '</small>' .
                    '</div>';

                // Fecha
                $fechaHTML = '<span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">' . Carbon::parse($ticket->created_at)->format('d/m/Y g:i A') . '</span>';

                // Estatus
                $statusColor = $ticket->status_color ?? 'e2e8f0';
                $statusName = $ticket->status_name ?? 'Sin Estatus';
                $statusId = $ticket->status_id ?? 50;
                $statusHTML = '<button class="btn btn-sm shadow-sm" style="background-color: #' . $statusColor . '; border-color: #' . $statusColor . '; color: white; border-radius: 20px; padding: 4px 12px; font-weight: 600; font-size: 0.7rem; border: none; min-width: 90px;" onclick="opcionesEstatus(' . $ticket->id . ',' . $statusId . ')">' . e($statusName) . '</button>';

                // Acciones
                $badgeChatHTML = '';
                if ($ticket->unread_chat_count > 0) {
                    $badgeChatHTML = '<span class="badge badge-danger shadow" style="position: absolute; top: -6px; right: -6px; font-size: 0.65rem; border-radius: 50%; padding: 3px 6px; border: 2px solid white; z-index: 10; animation: pulse 2s infinite;">' . $ticket->unread_chat_count . '</span>';
                }

                $btnChatClass = ($ticket->unread_chat_count > 0) ? 'btn-success' : 'btn-info';
                $btnFolioText = $ticket->folio ? $ticket->folio : '#' . $ticket->id;

                $btnChat = '<div style="position: relative; display: inline-block;">' .
                    '<button class="btn shadow-sm mx-1 ' . $btnChatClass . '" ' .
                    'onclick="abrirChatTicket(' . $ticket->id . ', \'' . e($btnFolioText) . '\')" ' .
                    'title="Abrir Chat" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none;">' .
                    '<i class="fas fa-comments text-white" style="font-size: 0.85rem;"></i>' .
                    '</button>' . $badgeChatHTML . '</div>';

                $btnView = '<button class="btn btn-primary shadow-sm mx-1" onclick="verTicket(' . $ticket->id . ')" title="Ver detalles" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; background-color: #E1F3E7;"><i class="fas fa-eye" style="font-size: 0.85rem; color: #000"></i></button>';

                $btnHistory = '';
                $btnEdit = '';
                $btnTrash = '';

                if (isset($perm_btn["modify"]) && $perm_btn["modify"] == 1) {
                    $btnEdit = '<button class="btn btn-warning shadow-sm mx-1" onclick="editarTicket(' . $ticket->id . ')" title="Editar" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; color: #000;"><i class="fa fa-edit" style="font-size: 0.85rem;"></i></button>';
                    $btnHistory = '<button class="btn btn-info shadow-sm mx-1" onclick="verHistorial(' . $ticket->id . ')" title="Ver Historial de correo" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; background-color: #04AA4F;"><i class="fa fa-history" style="font-size: 0.85rem;"></i></button>';
                }

                if (isset($perm_btn["erase"]) && $perm_btn["erase"] == 1) {
                    $btnTrash = '<button class="btn btn-danger shadow-sm mx-1" onclick="eliminarTicket(' . $ticket->id . ')" title="Eliminar" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none;"><i class="fa fa-trash" style="font-size: 0.85rem;"></i></button>';
                }

                $accionesHTML = '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnChat . $btnView . $btnHistory . $btnEdit . $btnTrash . '</div>';

                return [
                    'folio'          => $folioHTML,
                    'client_name'    => $clientHTML,
                    'policy_number'  => $policyHTML,
                    'agent_name'     => $agentHTML,
                    'tramite'        => $tramiteHTML,
                    'insbranch'      => $insbranchHTML,
                    'fecha_registro' => $fechaHTML,
                    'status_btn'     => $statusHTML,
                    'actions'        => $accionesHTML
                ];
            });

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
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

    public function getTicketDetail($id)
    {
        $ticket = Ticket::leftJoin('Client', 'Tickets.fk_client', '=', 'Client.id')
            ->leftJoin('Insurance', 'Tickets.fk_insurance', '=', 'Insurance.id')
            ->leftJoin('Nuc', function ($join) {
                $join->on(DB::raw('IFNULL(Tickets.fk_nuc, Tickets.fk_policy)'), '=', 'Nuc.id')
                     ->whereRaw("Insurance.fund_type IN ('CP', 'MP')");
            })
            ->leftJoin('SixMonth_fund', function ($join) {
                $join->on(DB::raw('IFNULL(Tickets.fk_nuc, Tickets.fk_policy)'), '=', 'SixMonth_fund.id')
                     ->whereRaw("Insurance.fund_type = 'LP'");
            })
            ->select(
                'Tickets.*',
                DB::raw("CONCAT(Client.name, ' ', IFNULL(Client.firstname, ''), ' ', IFNULL(Client.lastname, '')) as client_name"),
                DB::raw("COALESCE(SixMonth_fund.nuc, Nuc.nuc, Tickets.nrpolicy, 'Sin contrato') as contract_number"),
                'Insurance.name as insurance_name',
                'Insurance.fund_type'
            )
            ->where('Tickets.id', $id)
            ->first();

        $documents = Documents::where('fk_key', $id)
            ->where('fk_section', 49)
            ->get();

        return response()->json([
            'status'    => 'success',
            'data'      => $ticket,
            'documents' => $documents
        ], 200);
    }

    public function getTicketEditData($id)
    {
        $ticket = Ticket::leftJoin('Client', 'Tickets.fk_client', '=', 'Client.id')
            ->leftJoin('Insurance', 'Tickets.fk_insurance', '=', 'Insurance.id')
            ->leftJoin('Nuc', function ($join) {
                $join->on(DB::raw('IFNULL(Tickets.fk_nuc, Tickets.fk_policy)'), '=', 'Nuc.id')
                     ->whereRaw("Insurance.fund_type IN ('CP', 'MP')");
            })
            ->leftJoin('SixMonth_fund', function ($join) {
                $join->on(DB::raw('IFNULL(Tickets.fk_nuc, Tickets.fk_policy)'), '=', 'SixMonth_fund.id')
                     ->whereRaw("Insurance.fund_type = 'LP'");
            })
            ->select(
                'Tickets.*',
                DB::raw("CONCAT(Client.name, ' ', IFNULL(Client.firstname, ''), ' ', IFNULL(Client.lastname, '')) as client_name"),
                DB::raw("COALESCE(SixMonth_fund.nuc, Nuc.nuc, Tickets.nrpolicy, '') as contract_number"),
                'Insurance.name as insurance_name',
                'Insurance.fund_type'
            )
            ->where('Tickets.id', $id)
            ->first();

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket no encontrado.'], 404);
        }

        $documents = Documents::where('fk_key', $id)
            ->where('fk_section', 49)
            ->get();

        return response()->json([
            'status'    => 'success',
            'ticket'    => $ticket,
            'documents' => $documents
        ], 200);
    }

    public function UpdateTicketFullData(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);
            if (!$ticket) {
                return response()->json(['status' => 'error', 'message' => 'Ticket no encontrado.'], 404);
            }

            // 1. Eliminar documentos marcados
            if ($request->has('documentos_eliminados')) {
                foreach ($request->input('documentos_eliminados') as $docId) {
                    $document = Documents::find($docId);
                    if ($document) {
                        $fileOldPath = public_path('files/ticketfiles/' . $document->name);
                        if (file_exists($fileOldPath)) {
                            unlink($fileOldPath);
                        }
                        $document->delete();
                    }
                }
            }

            // 2. Subida de nuevos documentos
            if ($request->hasFile('subir_archivo')) {
                foreach ($request->file('subir_archivo') as $file) {
                    if ($file->isValid()) {
                        $filename = "ticket_" . $ticket->id . "_" . $file->getClientOriginalName();
                        $doc = new Documents;
                        $doc->fk_key = $ticket->id;
                        $doc->fk_section = 49;
                        $doc->name = $filename;
                        $doc->save();

                        $file->move(public_path('files/ticketfiles'), $filename);
                    }
                }
            }

            // 3. Actualizar campos generales
            $ticket->fk_client   = $request->fk_client;
            $ticket->agent_comm  = $request->agent_comm;
            $ticket->fk_agent    = $request->fk_agent;
            $ticket->fk_user     = $request->fk_user;
            $ticket->ins_seg     = 'inv';
            $ticket->invertion   = $request->service_inv;
            $ticket->service     = $request->service_inv;

            $ticket->fk_nuc      = $request->filled('fk_nuc') ? $request->fk_nuc : ($request->filled('e_fk_nuc') ? $request->e_fk_nuc : $ticket->fk_nuc);
            $ticket->nrpolicy    = $request->filled('nrpolicy') ? $request->nrpolicy : ($request->filled('e_nrpolicy') ? $request->e_nrpolicy : $ticket->nrpolicy);

            if ($request->filled('fk_insurance') && $request->fk_insurance > 0) {
                $ticket->fk_insurance = $request->fk_insurance;
            } elseif ($ticket->fk_nuc) {
                $fundId = DB::table('Nuc')->where('id', $ticket->fk_nuc)->value('fk_insurance')
                       ?? DB::table('SixMonth_fund')->where('id', $ticket->fk_nuc)->value('fk_insurance');
                if ($fundId) {
                    $ticket->fk_insurance = $fundId;
                }
            }

            // 4. Folio único y estado de Falta de Original
            $ticket->folio = $request->filled('folio') ? trim($request->input('folio')) : null;

            $original = $request->input('original', 'N/A');
            $ticket->original = $original;
            $ticket->original_delivery_date = (in_array($original, ['Entregado', 'Finestra']) && $request->filled('original_delivery_date'))
                ? $request->input('original_delivery_date')
                : null;
            $ticket->original_finestra_date = ($original === 'Finestra' && $request->filled('original_finestra_date'))
                ? $request->input('original_finestra_date')
                : null;

            $ticket->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Ticket y documentación actualizados correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTicketStatusFields($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Ticket no encontrado.'], 404);
        }
        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 49);
        return response()->json([
            'status'   => 'success',
            'ticket'   => $ticket,
            'perm_btn' => $perm_btn
        ], 200);
    }

    public function updateTicketStatus(Request $request, $id)
    {
        $comentarioEnvio = '';
        $profile = User::findProfile();
        $perm_btn = Permission::permBtns($profile, 49);

        if ($perm_btn['modify'] != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes privilegios para realizar modificaciones en este módulo.'
            ], 403);
        }

        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'El ticket no existe.'], 404);
        }

        $nuevoEstatus = $request->input('fk_status');
        $rutasArchivosAdjuntos = [];
        $estatusanterior = $ticket->fk_status;
        $ticket->fk_status = $nuevoEstatus;

        $procesarArchivosEstatus = function ($campoRequest, $campoDb) use ($request, $ticket, &$rutasArchivosAdjuntos) {
            $existentesRaw = $ticket->$campoDb;
            $existentesArr = [];

            if (!empty($existentesRaw)) {
                $parsed = json_decode($existentesRaw, true);
                $existentesArr = is_array($parsed) ? $parsed : [$existentesRaw];
            }

            if ($request->hasFile($campoRequest)) {
                foreach ($existentesArr as $oldFile) {
                    $oldPath = public_path('files/ticketfiles/' . $oldFile);
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $files = $request->file($campoRequest);
                if (!is_array($files)) { $files = [$files]; }
                $nuevosFilenames = [];

                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $filename = "ticket_" . $campoRequest . "_" . $ticket->id . "_" . time() . "_" . uniqid() . "_" . $file->getClientOriginalName();
                        $file->move(public_path('files/ticketfiles'), $filename);
                        $nuevosFilenames[] = $filename;
                        $rutasArchivosAdjuntos[] = public_path('files/ticketfiles/' . $filename);
                    }
                }

                if (!empty($nuevosFilenames)) {
                    $ticket->$campoDb = json_encode($nuevosFilenames, JSON_UNESCAPED_UNICODE);
                }
            } else {
                foreach ($existentesArr as $existente) {
                    $pathExistente = public_path('files/ticketfiles/' . $existente);
                    if (file_exists($pathExistente)) {
                        $rutasArchivosAdjuntos[] = $pathExistente;
                    }
                }
            }
        };

        switch ($nuevoEstatus) {
            case 51:
                $ticket->detail = $request->input('detail');
                $comentarioEnvio = $ticket->detail;
                $procesarArchivosEstatus('link_miss', 'link_miss');
                break;

            case 52: // En trámite: Folio único + Falta de original + Fechas condicionales
                $ticket->folio = $request->filled('folio') ? trim($request->input('folio')) : null;

                $original = $request->input('original', 'N/A');
                $ticket->original = $original;
                $ticket->original_delivery_date = (in_array($original, ['Entregado', 'Finestra']) && $request->filled('original_delivery_date'))
                    ? $request->input('original_delivery_date')
                    : null;
                $ticket->original_finestra_date = ($original === 'Finestra' && $request->filled('original_finestra_date'))
                    ? $request->input('original_finestra_date')
                    : null;

                $comentarioEnvio = 'Folio de trámite asignado: ' . ($ticket->folio ?? 'N/A') . ' | Original: ' . $original;
                break;

            case 53:
                $ticket->comm_susp = $request->input('comm_susp');
                $comentarioEnvio = $ticket->comm_susp;
                $procesarArchivosEstatus('link_susp', 'link_susp');
                break;

            case 54:
                $ticket->comm_attend = $request->input('comm_attend');
                $comentarioEnvio = $ticket->comm_attend;
                $procesarArchivosEstatus('link_attend', 'link_attend');
                break;

            case 55:
                $ticket->motive = $request->input('motive');
                $comentarioEnvio = $ticket->motive;
                break;
        }

        $ticket->updated_at = Carbon::now();
        $ticket->save();

        if ($estatusanterior != $nuevoEstatus) {
            DB::table('Status_Logs')->insert([
                'fk_key'     => $id,
                'fk_section' => 49,
                'fk_status'  => $nuevoEstatus,
                'fk_user'    => \Auth::id(),
                'created_at' => Carbon::now()
            ]);
        }

        if ($request->has('notificar_correo') && $request->input('notificar_correo') == 1) {
            $users = User::find($ticket->fk_agent);
            $client = DB::table('Client')->where('id', $ticket->fk_client)->first();
            $clientName = $client ? trim(($client->name ?? '') . ' ' . ($client->firstname ?? '') . ' ' . ($client->lastname ?? '')) : ($ticket->nrclient ?? 'N/A');
            $agentName = trim(($users->name ?? '') . ' ' . ($users->firstname ?? '') . ' ' . ($users->lastname ?? ''));

            $contractNumber = $ticket->fk_nuc ? (DB::table('Nuc')->where('id', $ticket->fk_nuc)->value('nuc') ?? DB::table('SixMonth_fund')->where('id', $ticket->fk_nuc)->value('nuc')) : ($ticket->nrpolicy ?? 'N/A');

            $ticketName = trim(($this->mapperInversiones[$ticket->invertion]['nombre'] ?? 'Inversión') . ' - ' . ($clientName ?? ''));
            $diasAtencion = $this->mapperInversiones[$ticket->invertion]['dias'] ?? 3;
            $statusName = DB::table('Status')->where('id', $nuevoEstatus)->value('name') ?? 'ACTUALIZADO';

            $asuntoCorreo = 'Actualización de Estatus: ' . $statusName . ' - Ticket #' . $id;
            switch ($nuevoEstatus) {
                case 50: $asuntoCorreo = 'Hemos recibido el trámite de ' . $clientName . '.'; break;
                case 51: $asuntoCorreo = 'Solicitamos información faltante de tu trámite ' . $ticketName; break;
                case 52: $asuntoCorreo = 'Tu solicitud del cliente ' . $clientName . ' se encuentra en trámite con el folio ' . ($ticket->folio ?? 'N/A') . '.'; break;
                case 53: $asuntoCorreo = 'Te informamos que tú trámite ' . $ticketName . ', se encuentra suspendido.'; break;
                case 54: $asuntoCorreo = 'Te informamos que tú trámite fue ATENDIDO ' . $ticketName . '.'; break;
                case 55: $asuntoCorreo = 'Te informamos que tú trámite fue RECHAZADO ' . $ticketName . '.'; break;
            }

            $assignedStaffUser = User::find($ticket->fk_user);
            $signaturePath = null;
            if ($assignedStaffUser && !empty($assignedStaffUser->signature)) {
                $fullPath = public_path('files/signatures/' . $assignedStaffUser->signature);
                if (file_exists($fullPath)) {
                    $signaturePath = $fullPath;
                }
            }

            $mailData = [
                'folio'             => $ticket->folio ?? 'N/A',
                'fecha'             => Carbon::parse($ticket->created_at)->format('d/m/Y g:i A'),
                'cliente'           => $clientName,
                'poliza'            => $contractNumber,
                'status_name'       => $statusName,
                'comentario'        => $comentarioEnvio,
                'agente'            => $agentName,
                'ticketname'        => $ticketName,
                'ticket'            => $ticket,
                'days'              => $diasAtencion,
                'archivos_adjuntos' => $rutasArchivosAdjuntos,
                'archivo_adjunto'   => !empty($rutasArchivosAdjuntos) ? $rutasArchivosAdjuntos[0] : null,
                'signature_path'    => $signaturePath
            ];

            $payloadAudit = json_encode([
                'estatus_id'        => $nuevoEstatus,
                'estatus_nombre'    => $statusName,
                'folio'             => $ticket->folio ?? 'N/A',
                'original'          => $ticket->original ?? 'N/A',
                'comentario'        => $comentarioEnvio,
                'cliente'           => $clientName,
                'poliza'            => $contractNumber,
                'ticket_name'       => $ticketName,
                'dias_atencion'     => $diasAtencion,
                'archivos_adjuntos' => array_map('basename', $rutasArchivosAdjuntos),
            ], JSON_UNESCAPED_UNICODE);

            $destinatario = $users->email ?? null;

            if (!empty($destinatario)) {
                try {
                    Mail::to($destinatario)->send(new TicketStatusUpdatedMail($mailData));

                    DB::table('Email_Logs')->insert([
                        'fk_key'          => $id,
                        'fk_section'      => 49,
                        'fk_user_sent'    => \Auth::id(),
                        'recipient_email' => $destinatario,
                        'status_name'     => $statusName,
                        'subject'         => $asuntoCorreo,
                        'payload'         => $payloadAudit,
                        'delivery_status' => 'SENT',
                        'error_message'   => null,
                        'created_at'      => Carbon::now()
                    ]);
                } catch (\Exception $mailEx) {
                    DB::table('Email_Logs')->insert([
                        'fk_key'          => $id,
                        'fk_section'      => 49,
                        'fk_user_sent'    => \Auth::id(),
                        'recipient_email' => $destinatario,
                        'status_name'     => $statusName,
                        'subject'         => $asuntoCorreo,
                        'payload'         => $payloadAudit,
                        'delivery_status' => 'FAILED',
                        'error_message'   => $mailEx->getMessage(),
                        'created_at'      => Carbon::now()
                    ]);
                }
            }
        }

        return response()->json([
            'status'    => 'success',
            'message'   => 'El estatus del ticket ha sido actualizado correctamente.',
            'fk_nuc'    => $ticket->fk_nuc,
            'fk_client' => $ticket->fk_client
        ], 200);
    }

    public function uploadMissingFiles(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);
            if (!$ticket) {
                return response()->json(['status' => 'error', 'message' => 'El ticket no existe.'], 404);
            }

            if (!$request->hasFile('archivos_faltantes')) {
                return response()->json(['status' => 'error', 'message' => 'No se seleccionó ningún archivo para subir.'], 400);
            }

            foreach ($request->file('archivos_faltantes') as $file) {
                if ($file->isValid()) {
                    $filename = "ticket_" . $ticket->id . "_" . time() . "_" . uniqid() . "_" . $file->getClientOriginalName();
                    $doc = new Documents;
                    $doc->fk_key     = $ticket->id;
                    $doc->fk_section = 49;
                    $doc->name       = $filename;
                    $doc->save();

                    $file->move(public_path('files/ticketfiles'), $filename);
                }
            }

            $ticket->updated_at = Carbon::now();
            $ticket->save();

            $users = User::find($ticket->fk_agent);
            $client = DB::table('Client')->where('id', $ticket->fk_client)->first();
            $clientName = $client ? trim(($client->name ?? '') . ' ' . ($client->firstname ?? '') . ' ' . ($client->lastname ?? '')) : 'N/A';
            $agentName = trim(($users->name ?? '') . ' ' . ($users->firstname ?? '') . ' ' . ($users->lastname ?? ''));

            $ticketName = trim(($this->mapperInversiones[$ticket->invertion]['nombre'] ?? 'Inversión') . ' - ' . ($clientName ?? ''));

            DB::table('notifications')->insert([
                'fk_user'    => $ticket->fk_user,
                'type'       => 'file_upload',
                'title'      => 'Archivos faltantes recibidos',
                'message'    => 'El agente ' . $agentName . ' adjuntó nuevos documentos en el Ticket: ' . $ticketName,
                'url'        => route('view.index', ['ticket_id' => $ticket->id]),
                'is_read'    => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Documentación faltante adjuntada correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al subir los archivos: ' . $e->getMessage()], 500);
        }
    }

    public function destroyTicket($id)
    {
        try {
            $ticket = Ticket::find($id);
            if (!$ticket) {
                return response()->json(['status' => 'error', 'message' => 'El ticket no existe o ya fue eliminado.'], 404);
            }

            $documents = Documents::where('fk_key', $id)->where('fk_section', 49)->get();
            foreach ($documents as $doc) {
                $filePath = public_path('files/ticketfiles/' . $doc->name);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $doc->delete();
            }

            if (!empty($ticket->link_susp)) {
                $pathSusp = public_path('files/ticketfiles/' . $ticket->link_susp);
                if (file_exists($pathSusp)) { @unlink($pathSusp); }
            }

            if (!empty($ticket->link_attend)) {
                $pathAttend = public_path('files/ticketfiles/' . $ticket->link_attend);
                if (file_exists($pathAttend)) { @unlink($pathAttend); }
            }

            DB::table('Ticket_Folios')->where('fk_ticket', $id)->delete();
            $ticket->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'El ticket y todos sus archivos asociados fueron eliminados correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar el ticket: ' . $e->getMessage()], 500);
        }
    }

    public function getTicketMessages($id)
    {
        $currentUserId = \Auth::id() ?? 1;

        $messages = DB::table('Messages')
            ->leftJoin('users', 'Messages.fk_user', '=', 'users.id')
            ->select(
                'Messages.*',
                DB::raw("CONCAT(IFNULL(users.name, ''), ' ', IFNULL(users.firstname, '')) as sender_name")
            )
            ->where('fk_key', $id)
            ->where('fk_section', 49)
            ->orderBy('Messages.created_at', 'asc')
            ->get();

        $formattedMessages = $messages->map(function ($m) {
            $m->sender_name = trim($m->sender_name) ?: 'Usuario';
            $m->formatted_time = Carbon::parse($m->created_at)->format('d/m g:i A');
            return $m;
        });

        return response()->json([
            'status'          => 'success',
            'current_user_id' => $currentUserId,
            'messages'        => $formattedMessages
        ], 200);
    }

    public function sendTicketMessage(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $messageText = trim($request->input('message'));
        $isInternal = $request->input('is_internal', 0);
        $user = User::user_id();

        if (empty($messageText)) {
            return response()->json(['status' => 'error', 'message' => 'El mensaje no puede estar vacío.'], 400);
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('Messages')->insert([
            'fk_key'      => $ticketId,
            'fk_section'  => 49,
            'fk_user'     => $user,
            'message'     => $messageText,
            'is_internal' => $isInternal,
            'created_at'  => $now,
            'updated_at'  => $now
        ]);

        $ticket = Ticket::find($ticketId);
        $users = User::find($ticket->fk_agent);
        $client = DB::table('Client')->where('id', $ticket->fk_client)->first();
        $clientName = $client ? trim(($client->name ?? '') . ' ' . ($client->firstname ?? '') . ' ' . ($client->lastname ?? '')) : 'N/A';
        $agentName = trim(($users->name ?? '') . ' ' . ($users->firstname ?? '') . ' ' . ($users->lastname ?? ''));

        $ticketName = trim(($this->mapperInversiones[$ticket->invertion]['nombre'] ?? 'Inversión') . ' - ' . ($clientName ?? ''));

        $profile = User::findProfile();
        if ($profile == 12) {
            $recipientId = $ticket->fk_user;
            $msg = 'El agente ' . $agentName . ' mandó un mensaje en el Ticket: ' . $ticketName;
        } else {
            $recipientId = $ticket->fk_agent;
            $msg = 'Recibiste una respuesta en el Ticket: ' . $ticketName;
        }

        DB::table('notifications')->insert([
            'fk_user'    => $recipientId,
            'type'       => 'new_message',
            'title'      => 'Nuevo mensaje en Tickets',
            'message'    => $msg,
            'url'        => route('view.index', ['ticket_id' => $ticket->id]),
            'is_read'    => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('Tickets')->where('id', $ticketId)->update(['updated_at' => $now]);

        return response()->json(['status' => 'success'], 200);
    }

    public function getEmailLogs($id)
    {
        try {
            $logs = DB::table('Email_Logs')
                ->leftJoin('users', 'Email_Logs.fk_user_sent', '=', 'users.id')
                ->select(
                    'Email_Logs.id',
                    'Email_Logs.fk_key',
                    'Email_Logs.recipient_email',
                    'Email_Logs.status_name',
                    'Email_Logs.subject',
                    'Email_Logs.payload',
                    'Email_Logs.delivery_status',
                    'Email_Logs.error_message',
                    'Email_Logs.created_at',
                    DB::raw("CONCAT(IFNULL(users.name,''), ' ', IFNULL(users.firstname,'')) as sender_name")
                )
                ->where('Email_Logs.fk_key', $id)
                ->where('Email_Logs.fk_section', 49)
                ->orderBy('Email_Logs.created_at', 'desc')
                ->get();

            $formattedLogs = $logs->map(function ($log) {
                $log->formatted_date = Carbon::parse($log->created_at)->format('d/m/Y g:i A');
                return $log;
            });

            return response()->json(['status' => 'success', 'data' => $formattedLogs], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Ocurrió un error al obtener el historial de correos: ' . $e->getMessage()], 500);
        }
    }

    public function linkRegisteredEntity(Request $request)
    {
        try {
            $ticketId = $request->input('ticket_id');
            $ticket = Ticket::findOrFail($ticketId);

            if ($request->has('fk_client')) {
                $ticket->fk_client = $request->input('fk_client');
            }

            if ($request->has('fk_nuc')) {
                $ticket->fk_nuc = $request->input('fk_nuc');
            }

            if ($request->has('fk_insurance')) {
                $ticket->fk_insurance = $request->input('fk_insurance');
            }

            $ticket->updated_at = Carbon::now();
            $ticket->save();

            return response()->json(['status' => 'success', 'message' => 'El ticket ha sido vinculado correctamente.'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al vincular: ' . $e->getMessage()], 500);
        }
    }
}