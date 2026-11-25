<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Profile;
use App\User;
use App\Permission;

class NotificationController extends Controller
{
    // Retornar la vista principal del historial
    public function historyView()
    {
        return view('notifications.history'); // Asegúrate de crear esta vista
    }

    // Proveedor de datos para DataTables (Server-Side)
    public function getHistoryData(Request $request)
    {
        $userId = User::user_id();
        $userProfile = User::findProfile();

        $query = DB::table('notifications');

        // 1. Permisos: Agente vs Staff
        if ($userProfile == 12) {
            $query->where('fk_user', $userId);
        } else {
            $query->where(function($q) use ($userId) {
                $q->whereNull('fk_user')
                  ->orWhere('fk_user', $userId);
            });
        }

        $totalRecords = $query->count();

        // 2. Buscador global
        $searchValue = trim($request->input('search.value', ''));
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                  ->orWhere('message', 'like', "%{$searchValue}%")
                  ->orWhere('type', 'like', "%{$searchValue}%");
            });
        }

        $recordsFiltered = $query->count();

        // 3. Paginación y Ordenamiento
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);

        if ($request->has('order')) {
            $columnIndex = $request->input('order.0.column');
            $columnDir   = $request->input('order.0.dir', 'desc');

            $columnsMap = [
                0 => 'type',
                1 => 'title',
                2 => 'message',
                3 => 'created_at',
                4 => 'is_read'
            ];

            if (isset($columnsMap[$columnIndex])) {
                $query->orderBy($columnsMap[$columnIndex], $columnDir);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $notifications = $query->skip($start)->take($length)->get();

        // 4. Formatear HTML para la tabla
        $formattedData = $notifications->map(function($n) {
            // Icono según tipo
            $icon = 'fas fa-bell text-secondary';
            $typeText = 'General';

            switch($n->type) {
                case 'file_upload': 
                    $icon = 'fas fa-paperclip text-info'; 
                    $typeText = 'Archivo'; 
                    break;
                case 'ticket_created': 
                    $icon = 'fas fa-ticket-alt text-warning'; 
                    $typeText = 'Ticket'; 
                    break;
                case 'emition_created': 
                    $icon = 'fas fa-file-signature text-success'; 
                    $typeText = 'Emisión'; 
                    break;
                case 'new_message': 
                    $icon = 'fas fa-comment-dots text-primary'; 
                    $typeText = 'Mensaje'; 
                    break;
            }

            $typeHTML = '<div class="text-center" title="' . $typeText . '"><i class="' . $icon . '" style="font-size: 1.2rem;"></i></div>';

            // Estatus Leída / No leída
            if ($n->is_read == 1) {
                $statusHTML = '<span class="badge badge-light border text-muted font-weight-bold" style="border-radius: 6px;"><i class="fas fa-check mr-1"></i> Leída</span>';
                $titleHTML = '<span class="text-secondary">' . e($n->title) . '</span>';
            } else {
                $statusHTML = '<span class="badge badge-primary shadow-sm font-weight-bold" style="border-radius: 6px;"><i class="fas fa-circle mr-1" style="font-size: 0.5rem; position: relative;"></i> Nueva</span>';
                $titleHTML = '<strong class="text-dark">' . e($n->title) . '</strong>';
            }

            // Fecha
            $dateHTML = '<span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">' . \Carbon\Carbon::parse($n->created_at)->format('d/m/Y g:i A') . '</span>';

            // Acciones (Ir a la notificación)
            // Se usa la función clickNotificacion que ya tienes de forma global en tu JS
            $btnAction = '<button class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 6px;" onclick="clickNotificacion(' . $n->id . ', \'' . $n->url . '\')"><i class="fas fa-external-link-alt mr-1"></i> Abrir</button>';

            return [
                'type'       => $typeHTML,
                'title'      => $titleHTML,
                'message'    => '<span class="text-muted small" style="line-height: 1.2; display: block;">' . e($n->message) . '</span>',
                'created_at' => $dateHTML,
                'is_read'    => $statusHTML,
                'actions'    => '<div class="text-center">' . $btnAction . '</div>'
            ];
        });

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $formattedData
        ]);
    }
    // Obtener notificaciones para la campanita
    public function getNotifications()
    {
        $userId = User::user_id();$userProfile = User::findProfile();

        $query = DB::table('notifications');

        if ($userProfile == 12) {
            $query->where('fk_user',$userId);
        } else {
            $query->where(function($q) use ($userId) {$q->whereNull('fk_user')
                  ->orWhere('fk_user', $userId);
            });
        }

        $unreadCount = (clone$query)->where('is_read', 0)->count();

        // 🟢 Aumentamos a 15 notificaciones para el menú rápido
        $notifications = (clone $query)
            ->orderBy('created_at', 'desc')
            ->take(15) 
            ->get();

        $formatted = $notifications->map(function($n) {
            // Se usa diffForHumans pero en español si tu app() está en 'es'
            $n->formatted_time = Carbon::parse($n->created_at)->diffForHumans();
            return $n;
        });

        return response()->json([
            'status'        => 'success',
            'unread_count'  => $unreadCount,
            'notifications' => $formatted
        ]);
    }

    // Marcar una como leída
    public function markAsRead($id)
    {
        DB::table('notifications')->where('id', $id)->update(['is_read' => 1]);
        return response()->json(['status' => 'success']);
    }

    // Marcar todas como leídas
    public function markAllAsRead()
    {
        $userId = Auth::id();
        $userProfile = Auth::user()->profile_id ?? 12;

        $query = DB::table('notifications');
        if ($userProfile == 12) {
            $query->where('fk_user', $userId);
        } else {
            $query->where(function($q) use ($userId) {
                $q->whereNull('fk_user')->orWhere('fk_user', $userId);
            });
        }

        $query->where('is_read', 0)->update(['is_read' => 1]);

        return response()->json(['status' => 'success']);
    }
}
