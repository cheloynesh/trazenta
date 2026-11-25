@extends('home') {{-- Reemplaza con el nombre real de tu layout principal --}}
<head>
    <title>Historial de Notificaciones | ELAN</title>
</head>
@section('content')
<div class="container-fluid py-4">
    
    {{-- Encabezado de la página --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fas fa-history mr-2 text-primary"></i> Historial de Notificaciones</h3>
            <p class="text-muted small mb-0 mt-1">Consulta y busca todas las notificaciones recibidas en tu cuenta.</p>
        </div>
        <div>
            <button onclick="marcarTodasNotificacionesLeidas(); $('#notificationsHistoryTable').DataTable().ajax.reload();" class="btn btn-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                <i class="fas fa-check-double mr-2"></i> Marcar todas como leídas
            </button>
        </div>
    </div>

    {{-- Contenedor de la Tabla --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            <table id="notificationsHistoryTable" class="table table-hover table-borderless w-100" style="border-collapse: separate; border-spacing: 0 8px;">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="text-center text-uppercase text-muted" style="border-radius: 8px 0 0 8px; font-size: 0.8rem; font-weight: 700;">Tipo</th>
                        <th class="text-uppercase text-muted" style="font-size: 0.8rem; font-weight: 700;">Título</th>
                        <th class="text-uppercase text-muted" style="font-size: 0.8rem; font-weight: 700;">Mensaje</th>
                        <th class="text-uppercase text-muted" style="font-size: 0.8rem; font-weight: 700;">Fecha</th>
                        <th class="text-uppercase text-muted" style="font-size: 0.8rem; font-weight: 700;">Estado</th>
                        <th class="text-center text-uppercase text-muted" style="border-radius: 0 8px 8px 0; font-size: 0.8rem; font-weight: 700;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Llenado por DataTables --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('head')
<script>
    $(document).ready(function() {
        $('#notificationsHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('notifications.historyData') }}",
                type: 'GET'
            },
            columns: [
                { data: 'type', name: 'type', orderable: false, searchable: false, width: "5%" },
                { data: 'title', name: 'title', width: "20%" },
                { data: 'message', name: 'message', width: "40%" },
                { data: 'created_at', name: 'created_at', width: "15%" },
                { data: 'is_read', name: 'is_read', width: "10%" },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, width: "10%" }
            ],
            order: [[3, 'desc']], // Ordenar por fecha descendente por defecto
            language: {
                "sProcessing":     "<div class='spinner-border text-primary' role='status'><span class='sr-only'>Procesando...</span></div> Cargando...",
                "sLengthMenu":     "Mostrar _MENU_ notificaciones",
                "sZeroRecords":    "No se encontraron notificaciones",
                "sEmptyTable":     "No tienes notificaciones registradas",
                "sInfo":           "Mostrando _START_ al _END_ de _TOTAL_ notificaciones",
                "sInfoEmpty":      "Mostrando 0 notificaciones",
                "sInfoFiltered":   "(filtrado de _MAX_ totales)",
                "sSearch":         "<i class='fas fa-search text-muted'></i> Buscar:",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "<i class='fas fa-chevron-right'></i>",
                    "sPrevious": "<i class='fas fa-chevron-left'></i>"
                }
            },
            drawCallback: function(settings) {
                // Estilos opcionales a las filas
                $('#notificationsHistoryTable tbody tr').css({
                    'background-color': '#fff',
                    'box-shadow': '0 2px 4px rgba(0,0,0,0.02)',
                    'border-radius': '8px'
                });
                $('#notificationsHistoryTable tbody td').css({
                    'vertical-align': 'middle',
                    'border-top': '1px solid #f1f5f9',
                    'border-bottom': '1px solid #f1f5f9'
                });
                $('#notificationsHistoryTable tbody td:first-child').css('border-left', '1px solid #f1f5f9').css('border-radius', '8px 0 0 8px');
                $('#notificationsHistoryTable tbody td:last-child').css('border-right', '1px solid #f1f5f9').css('border-radius', '0 8px 8px 0');
            }
        });

        // Darle algo de estilo al input de búsqueda nativo de DataTables
        $('.dataTables_filter input').addClass('form-control form-control-sm border-0 bg-light').css('border-radius', '20px').css('padding', '6px 15px');
    });
</script>
@endpush