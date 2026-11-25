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
use DB;
use Carbon\Carbon;
use App\Nuc;
use App\SixMonth_fund;

class CreateTicketController extends Controller
{
    protected $mapperServicios = [
        1 => ['nombre' => 'ACLARACION DE PAGOS', 'dias' => 5],
        2 => ['nombre' => 'ACLARACION DE RESCATE', 'dias' => 7],
        3 => ['nombre' => 'ACTUALIZACION DE DATOS', 'dias' => 5],
        4 => ['nombre' => 'ALTA /BAJA FIDEICOMISO', 'dias' => 10],
        5 => ['nombre' => 'ALTA CUENTAS', 'dias' => 5],
        6 => ['nombre' => 'ALTA/BAJA BIEN ASEGURADO', 'dias' => 3],
        7 => ['nombre' => 'ALTA/BAJA DE ASEGURADO', 'dias' => 5],
        8 => ['nombre' => 'APLICACIÓN PAGO', 'dias' => 5],
        9 => ['nombre' => 'AUMENTO /DISMINUSIÓN DE COBERTURAS', 'dias' => 5],
        10 => ['nombre' => 'AUMENTO /DISMINUSIÓN DE SUMA ASEGURADA', 'dias' => 10],
        11 => ['nombre' => 'CAMBIO AGENTE', 'dias' => 10],
        12 => ['nombre' => 'CAMBIO BENEFICIARIO', 'dias' => 5],
        13 => ['nombre' => 'CAMBIO DE CONDUCTO DE COBRO', 'dias' => 5],
        14 => ['nombre' => 'CAMBIO DE CONTRATANTE', 'dias' => 5],
        15 => ['nombre' => 'CAMBIO DE DEDUCIBLE /COASEGURO', 'dias' => 5],
        16 => ['nombre' => 'CAMBIO FORMA DE PAGO', 'dias' => 5],
        17 => ['nombre' => 'CAMBIO PLAN', 'dias' => 5],
        18 => ['nombre' => 'CANCELACION', 'dias' => 10],
        19 => ['nombre' => 'CONVERSIÓN POLIZA', 'dias' => 7],
        20 => ['nombre' => 'COTIZACIÓN', 'dias' => 5],
        21 => ['nombre' => 'DESCUENTO', 'dias' => 2],
        22 => ['nombre' => 'FACTURACION', 'dias' => 5],
        23 => ['nombre' => 'FALLECIMIENTO', 'dias' => 20],
        24 => ['nombre' => 'LIGAS DE PAGO', 'dias' => 2],
        25 => ['nombre' => 'MODIFICACIONES GENERALES', 'dias' => 5],
        26 => ['nombre' => 'PROGRAMACIÓN DE CIRUGIA', 'dias' => 7],
        27 => ['nombre' => 'RECONOCIMIENTO DE ANTIGÜEDAD', 'dias' => 5],
        28 => ['nombre' => 'RECONSIDERACION DE RIESGO', 'dias' => 5],
        29 => ['nombre' => 'RECONSIDERACION DE SINIESTRO', 'dias' => 5],
        30 => ['nombre' => 'REEMBOLSO', 'dias' => 7],
        31 => ['nombre' => 'REHABILITACIÓN', 'dias' => 5],
        32 => ['nombre' => 'REIMPRESIÓN PÓLIZA', 'dias' => 3],
        33 => ['nombre' => 'RENOVACIÓN', 'dias' => 3],
        34 => ['nombre' => 'RESCATE PARCIAL', 'dias' => 7],
        35 => ['nombre' => 'VARIOS', 'dias' => 7],
        36 => ['nombre' => 'VENCIMIENTO /RESCATE TOTAL', 'dias' => 10],
    ];

    protected $mapperAgentes = [
        1 => ['nombre' => 'ACLARACIÓN COMISIONES', 'dias' => 5],
        2 => ['nombre' => 'ACLARACIÓN DE BONOS', 'dias' => 5],
        3 => ['nombre' => 'ACTUALIZACIÓN CEDULA', 'dias' => 5],
        4 => ['nombre' => 'ACTUALIZACIÓN RC', 'dias' => 3],
        5 => ['nombre' => 'ALTA AGENTE', 'dias' => 5],
        6 => ['nombre' => 'CAMBIO CUENTA BANCARIA', 'dias' => 3],
        7 => ['nombre' => 'PAGO DE COMISIONES', 'dias' => 5],
        8 => ['nombre' => 'VARIOS', 'dias' => 5],
    ];

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

    public function index(){
        $user = User::user_id();

        $profiles = Profile::get();
        $profile = User::findProfile();
        $insurances = Insurance::orderBy("name")->where('name','!=',"FINESTRA")->pluck('name','id');

        $perm = Permission::permView($profile,48);
        $perm_btn =Permission::permBtns($profile,48);

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
        // dd($perm_btn);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('tickets.createticket', compact('profiles','perm_btn','clients','insurances'));
        }
    }

    public function GetNucByClient($id)
    {
        $user = User::user_id();
        $profile = User::findProfile();

        $client = DB::table('Client')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where('id',$id)->first();
        $nucs = DB::select('call getAllNucs(?)',[$id]);
        return response()->json(['status'=>true, "data"=>$nucs, "client"=>$client]);
    }

    public function storeSolicitud(Request $request)
    {
        // dd($request->all());
        $user = User::user_id();
        $ticket = new Ticket;
        $ticket->fk_agent = $user;
        $ticket->fk_agent_key = 0;
        $ticket->fk_client = $request->fk_client;
        $ticket->fk_policy = $request->fk_policy;
        $ticket->fk_nuc = $request->fk_nuc;
        $ticket->ins_seg = $request->inversion_seguro;
        $ticket->invertion = $request->inversiones;
        $ticket->fk_insurance = $request->fk_insurance;
        $ticket->fk_branch = $request->ramo;
        $ticket->serv_type = $request->tipo_servicio;
        $ticket->service = $request->servicios;
        $ticket->agent_comm = $request->comentarios;
        $ticket->serv_agent = $request->servicios_agentes;
        $ticket->nrclient = $request->nrclient;
        $ticket->nrpolicy = $request->nrpolicy;
        $ticket->fk_status = 50;

        if($request->inversion_seguro == 'inv') {
            $ticket->fk_user  = 36;
        } else {
            if($request->tipo_servicio == 1) {
                $ticket->fk_user  = 19;
            } else if($request->tipo_servicio == 3) {
                if($ticket->fk_agent_key == 0) {
                    $ticket->fk_user  = 521;
                } else {
                    $ticket->fk_user  = 543;
                }
            } else {
                // 1. Buscamos si existe un usuario asignado para el ramo específico
                $assignedUser = DB::table('Branch_User_Assignments')
                    ->where('fk_branch', intval($request->ramo))
                    ->value('fk_user');

                // 2. Si no hay usuario asignado a ese ramo, tomamos el usuario "Default" (fk_branch NULL)
                if (!$assignedUser) {
                    $assignedUser = DB::table('Branch_User_Assignments')
                        ->whereNull('fk_branch')
                        ->value('fk_user') ?? 13; // Fallback extremo por seguridad
                }

                $ticket->fk_user = $assignedUser;
            }
        }

        $ticket->save();

        // Justo después de guardar el nuevo $ticket:
        \DB::table('Status_Logs')->insert([
            'fk_key'  => $ticket->id,
            'fk_section'  => 49,
            'fk_status'  => $ticket->fk_status, // O el ID del estatus inicial (ej. 50)
            'fk_user'    => \Auth::id(),
            'created_at' => Carbon::now()
        ]);

        if ($request->hasFile('subir_archivo')) {
            // dd("entre");
            foreach ($request->file('subir_archivo') as $file) {
                
                $filename = "ticket_".$ticket->id."_".time()."_".uniqid()."_".$file->getClientOriginalName();

                $doc = new Documents;
                $doc->fk_key = $ticket->id;
                $doc->fk_section = 49;
                $doc->name = $filename;
                $doc->save();

                $file->move(public_path('files/ticketfiles'), $filename);
            }
        }

        $users = User::find($ticket->fk_agent);
        $client = \DB::table('Client')->where('id', $ticket->fk_client)->first();
        $agentName = trim(($users->name ?? '') . ' ' . ($users->firstname ?? '') . ' ' . ($users->lastname ?? ''));
        
        $policyNumber = $ticket->fk_policy ? $request->nrpolicy : 'N/A';
        $statusName = \DB::table('Status')->where('id', 50)->value('name') ?? 'ACTUALIZADO';

        $assignedStaffUser = User::find($ticket->fk_user);
        $signaturePath = null;

        if ($assignedStaffUser && !empty($assignedStaffUser->signature)) {
            $fullPath = public_path('files/signatures/' . $assignedStaffUser->signature);
            if (file_exists($fullPath)) {
                $signaturePath = $fullPath;
            }
        }

        // Preparamos el arreglo de datos para el Mailable
        $mailData = [
            'folio'       => $ticket->folio ?? 'N/A',
            'fecha'       => Carbon::parse($ticket->created_at)->format('d/m/Y g:i A'),
            'cliente'     => $request->nrclient,
            'agente'     => $agentName,
            'poliza'      => $policyNumber,
            'status_name' => $statusName,
            'comentario'  => $ticket->motive,
            'ticket'      => $ticket,
            'signature_path' => $signaturePath
        ];
        
        $policyNumber = $ticket->fk_policy ? $request->nrpolicy : 'N/A';
        if($ticket->ins_seg == 'inv') {
            $tramite = $this->mapperInversiones[$ticket->invertion]['nombre'];
        } elseif ($ticket->serv_type == 3) {
            $tramite = 'COTIZACIÓN';
        } else {
            $tramite = $this->mapperServicios[$ticket->service]['nombre'] ?? $this->mapperAgentes[$ticket->serv_agent]['nombre'];
        }

        $ticketName = trim($tramite. ' - ' . ($request->nrclient ?? ''));

        // Crear la notificación para el Staff
        \DB::table('notifications')->insert([
            'fk_user'    => $ticket->fk_user, // null = visible para todo el Staff
            'type'       => 'ticket_created',
            'title'      => 'Nuevo ticket creado',
            'message'    => 'El agente '. $agentName .' ha creado el ticket: ' . $ticketName,
            'url'        => route('view.index', [
                    'ticket_id' => $ticket->id
                ]),
            'is_read'    => 0,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        // Se envía al correo registrado del cliente (o al correo del agente según tu flujo)
        try {
            // Consultamos los datos de cliente y póliza para armar el correo
            if (!empty($users->email)) {
                Mail::to($users->email)->send(new TicketStatusUpdatedMail($mailData));
            }

        } catch (\Exception $mailEx) {
            // Si el servidor SMTP falla, guardamos el ticket y retornamos una advertencia leve sin tirar la app
            return response()->json([
                'status'  => 'success',
                'message' => 'El estatus se actualizó, pero no se pudo enviar la notificación por correo: ' . $mailEx->getMessage()
            ], 200);
        }

        return response()->json(['status'=>'success', "message"=>"Ticket creado correctamente"]);
    }

    public function getBranches($id)
    {
        $assignedBranches = DB::table('Branch_assign')->select('fk_branch AS id','name')
            ->join('Branch','fk_branch','=','Branch.id')
            ->where('fk_insurance',$id)
            ->orderBy('name')
            ->whereNull('Branch_assign.deleted_at')->get();
        // dd($id);

        return response()->json(['status'=>true, "branches" => $assignedBranches]);
    }
}
