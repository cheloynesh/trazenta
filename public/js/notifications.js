var conteoNotificacionesPrevio = 0;

function consultarNotificacionesGlobales() {
    if (document.hidden) return;

    jQuery.ajax({
        // 🟢 Usamos la variable global
        url: window.AppRoutes.notificationsGet,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var count = response.unread_count;
                var $badge =$('#notif-badge');
                var $container =$('#notif-list-container');

                // 1. Actualizar Badge Rojo
                if (count > 0) {
                    $badge.text(count > 99 ? '99+' : count).show();
                    if (count > conteoNotificacionesPrevio && conteoNotificacionesPrevio !== 0) {
                        alertify.message('🔔 Tienes nuevas notificaciones.');
                    }
                } else {
                    $badge.hide();
                }
                conteoNotificacionesPrevio = count;

                // 2. Renderizar Lista
                $container.empty();
                
                if (response.notifications.length === 0) {
                    $container.html('<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-2x mb-3 d-block opacity-50"></i>No hay notificaciones recientes</div>');
                } else {
                    response.notifications.forEach(function(n) {
                        var isUnread = (n.is_read == 0);
                        var bgClass = isUnread ? 'bg-light border-left-primary' : 'bg-white';
                        var borderLeft = isUnread ? 'border-left: 4px solid #007bff !important;' : 'border-left: 4px solid transparent !important;';
                        var fontWeight = isUnread ? 'font-weight-bold text-dark' : 'text-secondary';
                        
                        var icon = 'fas fa-bell';
                        var iconColor = 'text-primary';

                        switch(n.type) {
                            case 'file_upload':
                                icon = 'fas fa-paperclip'; iconColor = 'text-info'; break;
                            case 'ticket_created':
                                icon = 'fas fa-ticket-alt'; iconColor = 'text-warning'; break;
                            case 'emition_created':
                                icon = 'fas fa-file-signature'; iconColor = 'text-success'; break;
                            case 'new_message':
                                icon = 'fas fa-comment-dots'; iconColor = 'text-primary'; break;
                        }

                        var html = `
                            <a href="javascript:void(0)" onclick="clickNotificacion(${n.id}, '${n.url}')" class="dropdown-item p-3 border-bottom d-flex align-items-start ${bgClass}" style="${borderLeft} white-space: normal; transition: background 0.2s;">
                                <div class="mr-3 mt-1 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #f8f9fa;">
                                    <i class="${icon} ${iconColor}"></i>
                                </div>
                                <div class="w-100" style="overflow: hidden;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="${fontWeight}" style="font-size: 0.85rem; line-height: 1.2;">${n.title}</span>
                                        <small class="text-muted text-nowrap ml-2" style="font-size: 0.65rem;">${n.formatted_time}</small>
                                    </div>
                                    <p class="mb-0 text-muted" style="font-size: 0.78rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        ${n.message}
                                    </p>
                                </div>
                            </a>`;
                        $container.append(html);
                    });
                }

                // 3. Botón de "Ver todas" al final
                $container.append(`
                    <a href="/notifications/history" class="dropdown-item text-center text-primary font-weight-bold py-2 bg-light" style="font-size: 0.85rem; border-top: 1px solid #e9ecef; position: sticky; bottom: 0;">
                        Ver todas las notificaciones <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                `);
            }
        }
    });
}

function clickNotificacion(id, url) {
    // 🟢 Usamos la variable global
    jQuery.post(window.AppRoutes.notificationsMarkRead + "/" + id, {
        _token: window.AppRoutes.csrfToken
    }, function() {
        if (url && url !== 'null' && url !== '') {
            window.location.href = url;
        } else {
            consultarNotificacionesGlobales();
        }
    });
}

function marcarTodasNotificacionesLeidas() {
    // 🟢 Usamos la variable global
    jQuery.post(window.AppRoutes.notificationsMarkAllRead, {
        _token: window.AppRoutes.csrfToken
    }, function() {
        consultarNotificacionesGlobales();
    });
}

$(document).ready(function() {
    consultarNotificacionesGlobales();
    setInterval(consultarNotificacionesGlobales, 20000);
});