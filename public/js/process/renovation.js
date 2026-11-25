var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + getUrl.pathname;
var currentRenovationId = null;

$(document).ready(function () {
    $('#tableRenovations').DataTable({
        order: [[0, 'desc']],
        autoWidth: false,
        language: {
            "sProcessing":   "Procesando...",
            "sLengthMenu":   "Mostrar _MENU_ registros",
            "sZeroRecords":  "No hay solicitudes de renovación",
            "sEmptyTable":   "No hay solicitudes de renovación",
            "sInfo":         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":    "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sSearch":       "Buscar:",
            "oPaginate":     { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
        }
    });

    cargarRenovaciones();
});

function cargarRenovaciones() {
    var showAll = $('#chkAllRenovations').is(':checked') ? 1 : 0;

    jQuery.ajax({
        url: baseUrl + '/getRequestsList',
        type: 'GET',
        data: { show_all: showAll },
        dataType: 'json',
        success: function (res) {
            var table = $('#tableRenovations').DataTable();
            table.clear();
            var pending = 0;

            if (res.requests && res.requests.length > 0) {
                res.requests.forEach(function (r) {
                    if (r.status == 1) pending++;

                    var badge = '<span class="badge badge-warning px-3 py-2" style="border-radius: 20px;">Pendiente</span>';
                    if (r.status == 2) badge = '<span class="badge badge-success px-3 py-2" style="border-radius: 20px;">Procesada</span>';
                    else if (r.status == 3) badge = '<span class="badge badge-danger px-3 py-2" style="border-radius: 20px;">Rechazada</span>';

                    var pago = r.payment_choice == 2
                        ? '<span class="badge badge-info">Cuenta Bancaria</span>'
                        : '<span class="badge badge-light border">Reinversión CAPORTA</span>';

                    var btn = '<button class="btn btn-primary btn-sm shadow-sm" onclick="verDetalleRenovacion(' + r.id + ')" style="border-radius: 6px;"><i class="fas fa-eye mr-1"></i> Revisar</button>';

                    table.row.add([
                        '<span class="font-weight-bold" style="font-family: monospace;">#' + r.id + '</span>',
                        '<span class="small text-muted">' + r.formatted_date + '</span>',
                        '<span class="small font-weight-bold text-muted">' + r.agent_name + '</span>',
                        '<span class="font-weight-bold text-dark">' + (r.client_name || '-') + '</span>',
                        '<span class="font-weight-bold text-primary">' + (r.contract_label || '-') + '</span>',
                        '<span class="font-weight-bold text-success">' + r.formatted_amount + '</span>',
                        pago,
                        badge,
                        btn
                    ]);
                });
            }

            if (showAll == 0) $('#badgePendingRenovations').text(pending);
            table.draw(false);
            table.columns.adjust();
        }
    });
}

function verDetalleRenovacion(id) {
    jQuery.ajax({
        url: baseUrl + '/getRequestDetail/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.status) return;
            var r = res.data;
            currentRenovationId = r.id;

            $('#ren_rev_folio').text('Renovación #' + r.id);
            $('#ren_agent').text(r.agent_name + ' (' + (r.agent_email || '') + ')');
            $('#ren_client').text(r.client_name || '-');
            $('#ren_rfc').text(r.rfc || '-');
            $('#ren_contract').text(r.contract_label || '-');
            $('#ren_amount').text('$' + parseFloat(r.amount).toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#ren_payment').text(r.payment_choice == 2 ? 'Depósito a cuenta bancaria' : 'Reinversión en fondo CAPORTA');

            $('#ren_email').text((r.client_email || '-') + (r.same_email == 1 ? ' (Mismo)' : ' (Nuevo)'));
            $('#ren_phone').text((r.client_phone || '-') + (r.same_phone == 1 ? ' (Mismo)' : ' (Nuevo)'));
            $('#ren_occupation').text(r.occupation || 'N/A');
            $('#ren_company').text(r.company_name || 'N/A');

            var benHtml = '';
            if (r.beneficiaries_qty == 0) {
                benHtml = '<span class="text-muted">Persona Moral — No aplica</span>';
            } else if (r.beneficiaries_qty == 3) {
                benHtml = '<div style="white-space: pre-wrap;">' + (r.beneficiaries_extra || '-') + '</div>';
            } else {
                benHtml += '<div><strong>1.</strong> ' + (r.beneficiary_1_name || '-') + ' (' + (r.beneficiary_1_dob || '') + ') — <strong>' + (r.beneficiary_1_pct || 0) + '%</strong></div>';
                if (r.beneficiaries_qty == 2) {
                    benHtml += '<div class="mt-1"><strong>2.</strong> ' + (r.beneficiary_2_name || '-') + ' (' + (r.beneficiary_2_dob || '') + ') — <strong>' + (r.beneficiary_2_pct || 0) + '%</strong></div>';
                }
            }
            $('#ren_beneficiaries_box').html(benHtml);

            var $docs = $('#ren_documents_container');
            $docs.empty();
            if (res.documents && res.documents.length > 0) {
                res.documents.forEach(function (d) {
                    $docs.append('<a href="' + window.location.origin + '/files/ticketfiles/' + d.name + '" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold shadow-sm"><i class="fas fa-file-pdf text-danger mr-1"></i> ' + d.doc_label + '</a>');
                });
            } else {
                $docs.html('<span class="text-muted small">Sin documentos adjuntos.</span>');
            }

            if (r.status == 2) {
                $('#ren_alert_notes').removeClass('alert-danger').addClass('alert-success').show();
                $('#ren_alert_title').text('Renovación Procesada:');
                $('#ren_alert_text').text(r.staff_notes || 'Atendida por operaciones.');
            } else if (r.status == 3) {
                $('#ren_alert_notes').removeClass('alert-success').addClass('alert-danger').show();
                $('#ren_alert_title').text('Solicitud Rechazada:');
                $('#ren_alert_text').text(r.staff_notes || 'Con observaciones.');
            } else {
                $('#ren_alert_notes').hide();
            }

            if (r.status == 1) $('#ren_staff_actions').show();
            else $('#ren_staff_actions').hide();

            $('#modalReviewRenovation').modal('show');
        }
    });
}

function procesarRenovacion() {
    if (!currentRenovationId) return;
    alertify.prompt('Procesar Renovación', 'Agrega una nota o folio de confirmación (opcional):', '', function (evt, value) {
        jQuery.ajax({
            url: baseUrl + '/processRequest/' + currentRenovationId,
            type: 'POST',
            data: { _token: $("meta[name='csrf-token']").attr("content"), staff_notes: value },
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    alertify.success(res.message);
                    $('#modalReviewRenovation').modal('hide');
                    cargarRenovaciones();
                }
            }
        });
    }, function () {});
}

function rechazarRenovacion() {
    if (!currentRenovationId) return;
    alertify.prompt('Rechazar Renovación', 'Indica el motivo del rechazo para el agente:', '', function (evt, value) {
        jQuery.ajax({
            url: baseUrl + '/rejectRequest/' + currentRenovationId,
            type: 'POST',
            data: { _token: $("meta[name='csrf-token']").attr("content"), staff_notes: value },
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    alertify.success(res.message);
                    $('#modalReviewRenovation').modal('hide');
                    cargarRenovaciones();
                }
            }
        });
    }, function () {});
}