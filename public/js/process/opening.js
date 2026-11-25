var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + getUrl.pathname;

var idClient = 0;
var fund_type = "";
var yieldr = 0;
var yield_usd = 0;
var charge_moves = [];
var currentRequestId = null;
var currentRequestData = null;
var currentRenovationId = null;
var currentRenovationData = null;

// $(document).ready(function () {
//     $('#example thead th').each(function () {
//         var title = $(this).text().trim();
//         if (!title || title === "null") title = "Columna";
//         $(this).empty().html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" />');
//     });

//     $('#example').DataTable({
//         order: [[5, 'asc']],
//         language: {
//             "sProcessing": "Procesando...",
//             "sLengthMenu": "Mostrar _MENU_ registros",
//             "sZeroRecords": "No se encontraron resultados",
//             "sEmptyTable": "Ningún dato disponible en esta tabla",
//             "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
//             "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
//             "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
//             "sSearch": "Buscar:",
//             "oPaginate": { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
//         },
//         aLengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
//         initComplete: function () {
//             this.api().columns().every(function () {
//                 var that = this;
//                 $('input', this.header()).on('keyup change input', function (e) {
//                     if (e.keyCode === 27) $(this).val('');
//                     if (that.search() !== this.value) that.search(this.value).draw();
//                 });
//                 $('input', this.header()).on('click', function (e) { e.stopPropagation(); });
//             });
//         }
//     });

//     $('#tableRequests').DataTable({
//         order: [[0, 'desc']],
//         autoWidth: false,
//         language: {
//             "sProcessing":     "Procesando...",
//             "sLengthMenu":     "Mostrar _MENU_ registros",
//             "sZeroRecords":    "No hay solicitudes pendientes",
//             "sEmptyTable":     "No hay solicitudes por procesar",
//             "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
//             "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
//             "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
//             "sSearch":         "Buscar:",
//             "oPaginate": {
//                 "sFirst":    "Primero",
//                 "sLast":     "Último",
//                 "sNext":     "Siguiente",
//                 "sPrevious": "Anterior"
//             }
//         }
//     });

//     // Recalcular ancho de columnas cuando la pestaña ya terminó de mostrarse
//     $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
//         $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
//     });

//     $('#selectNucs').select2({ dropdownParent: $('#myModal'), placeholder: 'Selecciona una opción', allowClear: true, width: '100%' });
//     $('#selectNucs1').select2({ dropdownParent: $('#myModaledit'), placeholder: 'Selecciona una opción', allowClear: true, width: '100%' });
//     $('#srcClient, #tbProf1').DataTable({
//         language: { "sZeroRecords": "No se encontraron resultados", "sSearch": "Buscar:" }
//     });


//     $('#tableRenovations').DataTable({ order: [[0, 'desc']] });
//     $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
//         $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
//     });
// });

var autoRefreshOpeningsTimer = null;

$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const targetOpeningId = urlParams.get('opening_id');
    const targetReqId = urlParams.get('req_id');
    const targetRenId = urlParams.get('ren_id');

    // Si la notificación apunta a Emisiones o Renovaciones, activar esa pestaña al entrar
    if (targetReqId) {
        $('a[href="#panelSolicitudes"]').tab('show');
    } else if (targetRenId) {
        $('a[href="#panelRenovaciones"]').tab('show');
    }

    // 1. Preparar inputs de búsqueda en cada columna (excepto la última de Opciones/Acciones)
    ['#example', '#tableRequests', '#tableRenovations'].forEach(function (tableSelector) {
        var $ths = $(tableSelector + ' thead th');
        var total = $ths.length;
        $ths.each(function (idx) {
            if (idx < total - 1) {
                var title = $(this).text().trim();
                $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" />');
            }
        });
    });

    // 2. Columnas dinámicas para Pestaña 1 (#example)
    var colsExample = [
        { data: 'agent', name: 'agent' },
        { data: 'cname', name: 'cname' },
        { data: 'insurance', name: 'insurance' },
        { data: 'fund_type', name: 'fund_type' },
        { data: 'nuc', name: 'nuc' },
        { data: 'limit_status', name: 'limit_status' },
        { data: 'origen', name: 'origen' },
        { data: 'estatus_agente', name: 'estatus_agente' }
    ];
    if (window.userProfile != 12) {
        colsExample.push({ data: 'estatus_interno', name: 'estatus_interno' });
    }
    colsExample.push({ data: 'opciones', name: 'opciones', orderable: false, searchable: false });

    // Inicializar DataTable Server-Side: Pestaña 1 (Aperturas en Proceso)
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        language: {
            "sProcessing":   "Procesando...",
            "sLengthMenu":   "Mostrar _MENU_ registros",
            "sZeroRecords":  "No se encontraron resultados",
            "sEmptyTable":   "Ningún dato disponible en esta tabla",
            "sInfo":         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":    "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch":       "Buscar:",
            "oPaginate":     { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
        },
        ajax: {
            url: baseUrl + '/GetOpeningsList',
            type: 'GET',
            data: function (d) {
                const params = new URLSearchParams(window.location.search);
                d.target_id = params.get('opening_id');
                d.active    = $('#chkActive').is(':checked') ? 1 : 0;
            }
        },
        columns: colsExample,
        initComplete: function () {
            vincularBuscadoresColumna(this.api());
        },
        drawCallback: function () {
            const params = new URLSearchParams(window.location.search);
            const tId = params.get('opening_id');
            if (tId) {
                var $row = $('#example tbody tr#' + tId);
                if ($row.length > 0) {
                    $row.addClass('highlight-row');
                    $row.one('click', function () {
                        $(this).removeClass('highlight-row');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                }
            }
        },
        scrollX: true,
    });

    // Inicializar DataTable Server-Side: Pestaña 2 (Solicitudes de Emisión)
    $('#tableRequests').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        pageLength: 25,
        order: [[0, 'desc']],
        language: {
            "sProcessing":   "Procesando...",
            "sLengthMenu":   "Mostrar _MENU_ registros",
            "sZeroRecords":  "No se encontraron solicitudes de emisión",
            "sEmptyTable":   "No hay solicitudes de emisión",
            "sInfo":         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":    "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sSearch":       "Buscar:",
            "oPaginate":     { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
        },
        ajax: {
            url: baseUrl + '/getRequestsList',
            type: 'GET',
            data: function (d) {
                const params = new URLSearchParams(window.location.search);
                d.target_id = params.get('req_id');
                d.show_all  = $('#chkAllRequests').is(':checked') ? 1 : 0;
            },
            dataSrc: function (json) {
                if (typeof json.pendingCount !== 'undefined') {
                    $('#badgePendingRequests').text(json.pendingCount);
                }
                return json.data;
            }
        },
        columns: [
            { data: 'folio', name: 'folio' },
            { data: 'fecha', name: 'fecha' },
            { data: 'agente', name: 'agente' },
            { data: 'cliente', name: 'cliente' },
            { data: 'fondo', name: 'fondo' },
            { data: 'monto', name: 'monto' },
            { data: 'origen', name: 'origen', orderable: false },
            { data: 'estatus', name: 'estatus' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        initComplete: function () {
            vincularBuscadoresColumna(this.api());
        },
        drawCallback: function () {
            const params = new URLSearchParams(window.location.search);
            const rId = params.get('req_id');
            if (rId) {
                var $row = $('#tableRequests tbody tr#req_' + rId);
                if ($row.length > 0) {
                    $row.addClass('highlight-row');
                    $row.one('click', function () {
                        $(this).removeClass('highlight-row');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                }
            }
        },
        scrollX: true,
    });

    // Inicializar DataTable Server-Side: Pestaña 3 (Solicitudes de Renovación)
    $('#tableRenovations').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        pageLength: 25,
        order: [[0, 'desc']],
        language: {
            "sProcessing":   "Procesando...",
            "sLengthMenu":   "Mostrar _MENU_ registros",
            "sZeroRecords":  "No se encontraron solicitudes de renovación",
            "sEmptyTable":   "No hay solicitudes de renovación",
            "sInfo":         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":    "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sSearch":       "Buscar:",
            "oPaginate":     { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
        },
        ajax: {
            url: baseUrl + '/getRenovationsList',
            type: 'GET',
            data: function (d) {
                const params = new URLSearchParams(window.location.search);
                d.target_id = params.get('ren_id');
                d.show_all  = $('#chkAllRen').is(':checked') ? 1 : 0;
            },
            dataSrc: function (json) {
                if (typeof json.pendingCount !== 'undefined') {
                    $('#badgePendingRenovations').text(json.pendingCount);
                }
                return json.data;
            }
        },
        columns: [
            { data: 'folio', name: 'folio' },
            { data: 'fecha', name: 'fecha' },
            { data: 'agente', name: 'agente' },
            { data: 'cliente', name: 'cliente' },
            { data: 'contrato', name: 'contrato' },
            { data: 'monto', name: 'monto' },
            { data: 'estatus', name: 'estatus' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        initComplete: function () {
            vincularBuscadoresColumna(this.api());
        },
        drawCallback: function () {
            const params = new URLSearchParams(window.location.search);
            const renId = params.get('ren_id');
            if (renId) {
                var $row = $('#tableRenovations tbody tr#ren_' + renId);
                if ($row.length > 0) {
                    $row.addClass('highlight-row');
                    $row.one('click', function () {
                        $(this).removeClass('highlight-row');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                }
            }
        },
        scrollX: true,
    });


    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        setTimeout(function () {
            sincronizarEncabezadoScroll('#tableRequests');
            sincronizarEncabezadoScroll('#tableRenovations');
        }, 60);
    });

    $(window).on('resize', function () {
        setTimeout(function () {
            sincronizarEncabezadoScroll('#tableRequests');
            sincronizarEncabezadoScroll('#tableRenovations');
        }, 120);
    });


    iniciarAutoRefreshAperturas();

    $('#selectNucs').select2({ dropdownParent: $('#myModal'), placeholder: 'Selecciona una opción', allowClear: true, width: '100%' });
    $('#selectNucs1').select2({ dropdownParent: $('#myModaledit'), placeholder: 'Selecciona una opción', allowClear: true, width: '100%' });
    $('#srcClient, #tbProf1').DataTable({
        language: { "sZeroRecords": "No se encontraron resultados", "sSearch": "Buscar:" }
    });
});

function sincronizarEncabezadoScroll(tableId) {
    var $wrapper = $(tableId + '_wrapper');
    var $headTable = $wrapper.find('.dataTables_scrollHead table');
    var $bodyTable = $wrapper.find('.dataTables_scrollBody table');

    if (!$headTable.length || !$bodyTable.length) return;

    if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().columns.adjust();
    }

    var bodyWidth = $bodyTable.outerWidth();
    if (bodyWidth > 0) {
        $headTable.css({ width: bodyWidth + 'px', minWidth: bodyWidth + 'px' });
        $wrapper.find('.dataTables_scrollHeadInner').css({ width: bodyWidth + 'px' });
    }
}

function vincularBuscadoresColumna(api) {
    api.columns().every(function () {
        var that = this;
        if (!this.settings()[0].aoColumns[this.index()].bSearchable) return;

        $('input', this.header()).on('click', function (e) {
            e.stopPropagation();
        });

        $('input', this.header()).on('keyup change clear', function (e) {
            e.stopPropagation();
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });
}

function recargarTablasAperturas() {
    if ($.fn.DataTable.isDataTable('#example')) $('#example').DataTable().ajax.reload(null, false);
    if ($.fn.DataTable.isDataTable('#tableRequests')) $('#tableRequests').DataTable().ajax.reload(null, false);
    if ($.fn.DataTable.isDataTable('#tableRenovations')) $('#tableRenovations').DataTable().ajax.reload(null, false);
}

function cargarSolicitudes() {
    if ($.fn.DataTable.isDataTable('#tableRequests')) {
        $('#tableRequests').DataTable().ajax.reload(null, false);
    }
}

function cargarRenovaciones() {
    if ($.fn.DataTable.isDataTable('#tableRenovations')) {
        $('#tableRenovations').DataTable().ajax.reload(null, false);
    }
}

function iniciarAutoRefreshAperturas() {
    if (autoRefreshOpeningsTimer) clearInterval(autoRefreshOpeningsTimer);

    autoRefreshOpeningsTimer = setInterval(function () {
        if (document.hidden) return;
        if ($('.modal.show').length > 0) return;
        recargarTablasAperturas();
    }, 30000);
}

// =========================================================
// 🟢 GESTIÓN DE SOLICITUDES (PESTAÑA 2)
// =========================================================

function verDetalleSolicitud(id) {
    jQuery.ajax({
        url: baseUrl + '/getRequestDetail/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.status) return;
            var r = res.data;
            currentRequestData = r;

            $('#rev_folio').text('Solicitud #' + r.id);
            $('#rev_client_name').text(r.client_full_name);
            $('#rev_client_type').text(r.client_type == 1 ? 'Persona Moral' : 'Persona Física');

            var rfc = r.fk_client > 0 ? (r.existing_rfc || 'N/A') : (r.new_rfc || 'N/A');
            var curp = r.fk_client > 0 ? (r.existing_curp || 'N/A') : (r.new_curp || 'N/A');
            $('#rev_rfc_curp').text(rfc + ' / ' + curp);

            $('#rev_contact').text((r.email || 'S/C') + ' | ' + (r.cellphone || 'S/T'));
            $('#rev_nationality').text((r.nationality || 'N/A') + ' / ' + (r.residence_country || 'N/A'));
            $('#rev_activity').text((r.marital_status || 'N/A') + ' | ' + (r.activity || 'N/A'));
            $('#rev_company_dictum').text((r.company_name || 'N/A') + (r.client_type == 1 ? ' (Dictamen: ' + (r.legal_dictum == 1 ? 'Sí' : 'No') + ')' : ''));
            $('#rev_domicile').text(r.fk_client > 0 ? (r.existing_domicile || 'N/A') : (r.new_domicile || 'N/A'));

            $('#rev_plazo_fund').text(r.plazo + ' meses (' + r.fund_type + ') - ' + (r.fund_name || 'Sin fondo'));
            $('#rev_amount').text('$' + parseFloat(r.amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ' + r.currency);
            $('#rev_payment_form').text(r.payment_form_name || 'N/A');
            $('#rev_charge').text(r.charge_name || 'N/A');
            var origenText = '';
            if (r.opening_type == 3) {
                origenText = 'Mixto — Dinero Nuevo: $' + parseFloat(r.new_money_amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) +
                            ' | Mov. Interno: $' + parseFloat(r.internal_move_amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) +
                            ' (Contrato: ' + r.nuc_origin_label + ')';
            } else if (r.opening_type == 2 || r.is_internal_move == 1) {
                origenText = 'Movimiento Interno: $' + parseFloat(r.internal_move_amount || r.amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) +
                            ' (Contrato: ' + r.nuc_origin_label + ')';
            } else {
                origenText = 'Dinero Nuevo: $' + parseFloat(r.new_money_amount || r.amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
            }
            $('#rev_origin').text(origenText);
            $('#rev_dest').text(r.nuc_dest_label);

            $('#rev_bank_name').text(r.bank_name || '-');
            $('#rev_bank_account').text(r.bank_account || '-');
            $('#rev_bank_clabe').text(r.bank_clabe || '-');
            $('#rev_bank_intl').text((r.bank_aba || 'N/A') + ' / ' + (r.bank_swift || 'N/A'));

            // Beneficiarios
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
            $('#rev_beneficiaries_box').html(benHtml);

            // Documentos
            var $docsContainer = $('#rev_documents_container');
            $docsContainer.empty();
            if (res.documents && res.documents.length > 0) {
                res.documents.forEach(function (doc) {
                    var icono = obtenerIconoDocumento(doc.name);
                    var badgeHTML = `
                        <a href="${window.location.origin}/files/openingfiles/${doc.name}" target="_blank" title="${doc.name}"
                           class="d-inline-flex align-items-center bg-white border pl-3 pr-3 py-2 mr-2 mb-2 shadow-sm text-decoration-none"
                           style="border-radius: 8px; border: 1px solid #e2e8f0;">
                            <i class="fas ${icono} mr-2"></i>
                            <span class="mr-2 font-weight-bold text-muted small" style="max-width: 190px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                ${doc.doc_label}
                            </span>
                            <i class="fas fa-external-link-alt text-muted" style="font-size: 11px;"></i>
                        </a>`;
                    $docsContainer.append(badgeHTML);
                });
            } else {
                $docsContainer.html('<span class="text-muted small">Sin documentos adjuntos.</span>');
            }

            if (r.status == 3) {
                $('#rev_staff_notes_text').text(r.staff_notes || 'Sin comentarios');
                $('#rev_alert_rejected').show();
            } else {
                $('#rev_alert_rejected').hide();
            }

            if (r.status == 2) {
                $('#rev_processed_text').text('Esta solicitud ya fue atendida y generó la Apertura #' + (r.fk_opening || ''));
                $('#rev_alert_processed').show();
            } else {
                $('#rev_alert_processed').hide();
            }

            if (r.status == 1) {
                $('#rev_staff_actions').show();
            } else {
                $('#rev_staff_actions').hide();
            }

            $('#modalReviewRequest').modal('show');
        }
    });
}

function rechazarSolicitudActual() {
    if (!currentRequestData) return;

    alertify.prompt('Rechazar / Observar Solicitud', 'Escribe el motivo del rechazo o corrección para el agente:', '', function (evt, value) {
        jQuery.ajax({
            url: baseUrl + '/rejectRequest/' + currentRequestData.id,
            type: 'POST',
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                staff_notes: value
            },
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    alertify.success(res.message);
                    $('#modalReviewRequest').modal('hide');
                    cargarSolicitudes();
                }
            }
        });
    }, function () {
        alertify.message('Cancelado');
    });
}

function prepararAperturaDesdeSolicitud() {
    if (!currentRequestData) return;
    var r = currentRequestData;

    $('#modalReviewRequest').modal('hide');

    // Abrir modal oficial de Apertura y precargar todos los campos
    currentRequestId = r.id;
    $('#badgeFromRequest').text('Precargado desde Solicitud #' + r.id).show();

    $('#selectAgent').val(r.fk_agent);
    $('#nuc').val('PENDIENTE');

    if (r.fk_client > 0) {
        idClient = r.fk_client;
        $('#client_edit').val(r.client_full_name);
        $('#fisica').hide();
    } else {
        idClient = 0;
        $('#client_edit').val('CLIENTE NUEVO (Precargado)');
        $('#fisica').show();
        $('#name').val(r.new_name);
        $('#firstname').val(r.new_firstname);
        $('#lastname').val(r.new_lastname);
        $('#birth_date').val(r.new_birth_date);
        $('#rfc').val(r.new_rfc);
        $('#curp').val(r.new_curp);
        $('#cellphone').val(r.cellphone);
        $('#email').val(r.email);
        $('#address').val(r.new_domicile);
    }

    $('#selectPaymentform').val(r.fk_payment_form);
    if (r.fk_insurance) {
        $('#selectInsurance').val(r.fk_insurance);
        fundchange(0);
    }

    // Precargar monto y conducto
    charge_moves = [];
    if (r.fund_type === 'LP') {
        $('#amount').val(parseFloat(r.amount).toLocaleString('en-US'));
        if (r.fk_nuc_dest) {
            $('#selectNucs').val(r.fk_nuc_dest).trigger('change');
        }
        if (r.fk_charge) {
            charge_moves.push({
                id: 1,
                amount: r.amount,
                fk_charge: r.fk_charge,
                chargeName: r.charge_name || 'Conducto Solicitud',
                apply_date: ''
            });
        }
    } else {
        $('#amount').val(r.amount);
    }

    var table = $('#tbProf1').DataTable();
    table.clear();
    refreshTable(charge_moves);

    $('#myModal').modal('show');
}

// =========================================================
// 🔵 FUNCIONES ORIGINALES DE APERTURAS OFICIALES (INTACTAS)
// =========================================================
function FillTable(data, profile, permission, requestsMap, renewalsMap) {
    if (requestsMap) window.requestsMap = requestsMap;
    if (renewalsMap) window.renewalsMap = renewalsMap;

    var mapReq = window.requestsMap || {};
    var mapRen = window.renewalsMap || {};
    var table = $('#example').DataTable();
    table.clear();

    data.forEach(function (v) {
        var btnStatAg = '<button class="btn btn-sm" style="color:#' + v.font_color + '; background:#' + v.color + '; border-color: #' + v.border_color + '; border-radius:20px; padding: 4px 12px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatusAgente(' + v.oid + ')">' + v.name + '</button>';
        var btnStat = (v.statid == 6 && !v.finestra_status)
            ? '<button class="btn btn-sm text-white" style="background:#e98c46; border-color: #e98c46; border-radius:20px; padding: 4px 12px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatus(' + v.oid + ')">Pend. Finestra</button>'
            : '<button class="btn btn-sm" style="color:#' + v.font_color + '; background:#' + v.color + '; border-color: #' + v.border_color + '; border-radius:20px; padding: 4px 12px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatus(' + v.oid + ')">' + v.name + '</button>';

        var origenBadge = '<span class="badge badge-light border text-secondary px-2 py-1">Alta Directa</span>';
        var btnReq = '';

        if (mapReq[v.oid]) {
            origenBadge = '<span class="badge badge-light border text-primary px-2 py-1">Emisión</span>';
            btnReq = '<button type="button" class="btn btn-info btn-sm shadow-sm" onclick="verDetalleSolicitud(' + mapReq[v.oid] + ')" title="Ver Solicitud de Emisión" style="border-radius:6px;"><i class="fas fa-file-alt"></i></button>';
        } else if (mapRen[v.oid]) {
            origenBadge = '<span class="badge badge-light border text-success px-2 py-1">Renovación</span>';
            btnReq = '<button type="button" class="btn btn-success btn-sm shadow-sm text-white" onclick="verDetalleRenovacion(' + mapRen[v.oid] + ')" title="Ver Solicitud de Renovación" style="border-radius:6px;"><i class="fas fa-sync-alt"></i></button>';
        }

        var btnEdit = '<button type="button" class="btn btn-warning btn-sm" onclick="editarApertura(' + v.oid + ')" style="border-radius:6px;"><i class="fas fa-edit"></i></button>';
        var btnTrash = permission["erase"] == 1
            ? '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarApertura(' + v.oid + ')" style="border-radius:6px;"><i class="fa fa-trash"></i></button>'
            : '';

        var opciones = '<div class="d-flex justify-content-center align-items-center" style="gap: 4px; white-space: nowrap;">' + btnReq + btnEdit + btnTrash + '</div>';

        if (profile != 12) {
            table.row.add([v.agent, v.cname, v.insurance, v.fund_type, v.nuc, v.limit_status, origenBadge, btnStatAg, btnStat, opciones]).node().id = v.oid;
        } else {
            table.row.add([v.agent, v.cname, v.insurance, v.fund_type, v.nuc, v.limit_status, origenBadge, btnStatAg, opciones]).node().id = v.oid;
        }
    });

    table.draw(false);
    table.columns.adjust();
}

function chkActive() {
    if ($.fn.DataTable.isDataTable('#example')) {
        $('#example').DataTable().ajax.reload();
    }
}

function nuevaApertura() {
    idClient = 0;
    currentRequestId = null;
    $('#badgeFromRequest').hide();
    document.getElementById("divCP").style.display = "none";
    document.getElementById("divLP").style.display = "none";
    document.getElementById("fisica").style.display = "none";
    $("#client_edit").val("");
    var table = $('#tbProf1').DataTable();
    table.clear();
    table.draw(false);
    charge_moves = [];
    $("#myModal").modal('show');
}

function guardarApertura() {
    var active = document.getElementById('chkActive').checked ? 1 : 0;
    var fk_agent = $("#selectAgent").val();
    var name = $("#name").val();
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var birth_date = $("#birth_date").val();
    var rfc = $("#rfc").val();
    var curp = $("#curp").val();
    var cellphone = $("#cellphone").val();
    var email = $("#email").val();
    var fk_insurance = $("#selectInsurance").val();
    var fk_currency = $("#selectCurrency").val();
    var fk_application = $("#selectAppli").val();
    var fk_payment_form = $("#selectPaymentform").val();
    var domicile = $("#address").val();
    var nuc = $("#nuc").val();
    var amount = $("#amount").val().replace(/[^0-9.]/g, '');
    var initial_date = $("#initial_date").val();
    var fst_scnd = $("#selectFstscnd").val();
    var pair_non = $("#selectPairNon").val();
    var fk_cp = $("#selectNucs").val();
    var reinvest = document.getElementById("onoff").checked ? 1 : 2;

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_agent': fk_agent,
        'fk_client': idClient,
        'fk_opening_request': currentRequestId,
        'fk_renewal_request': currentRenovationId,
        'name': name,
        'firstname': firstname,
        'lastname': lastname,
        'birth_date': birth_date,
        'rfc': rfc,
        'curp': curp,
        'cellphone': cellphone,
        'email': email,
        'fk_insurance': fk_insurance,
        'selectCurrency': fk_currency,
        'fk_application': fk_application,
        'fk_payment_form': fk_payment_form,
        'pair_non': pair_non,
        'fk_cp': fk_cp,
        'domicile': domicile,
        'amount': amount,
        'nuc': nuc,
        'deposit_date': initial_date,
        'fst_scnd': fst_scnd,
        'fund_type': fund_type,
        'estatus': reinvest,
        'yield': yieldr,
        'yield_usd': yield_usd,
        'charge_moves': charge_moves,
        'active': active
    };

    jQuery.ajax({
        url: baseUrl,
        type: "post",
        data: data,
        dataType: 'json',
        success: function (result) {
            alertify.success(result.message);
            recargarTablasAperturas();
            currentRequestId = null;
            currentRenovationId = null;
            $("#myModal").modal('hide');
        }
    });
}

var idupdate = 0;
function editarApertura(id) {
    idupdate = id;
    var cp = document.getElementById("divCP1");
    var lp = document.getElementById("divLP1");

    jQuery.ajax({
        url: baseUrl + '/GetInfo/' + id,
        type: 'get',
        dataType: 'json',
        success: function (result) {
            idClient = result.data.clid;
            $("#selectAgent1").val(result.data.fk_agent);
            $("#client_edit1").val(result.data.cname);
            $("#selectAppli1").val(result.data.fk_application);
            $("#selectPaymentform1").val(result.data.fk_payment_form);
            $("#selectCharge1").val(result.data.fk_charge);
            $("#selectCurrency1").val(result.data.currency);

            var assignPlan = $("#selectInsurance1");
            $("#selectInsurance1").empty();
            assignPlan.append('<option selected hidden value="0">Seleccione una opción</option>');
            result.insurances.forEach(function (valor) {
                if (valor.active_fund == 1)
                    assignPlan.append("<option value='" + valor.id + "'>" + valor.name + "</option>");
                else
                    assignPlan.append("<option hidden value='" + valor.id + "'>" + valor.name + "</option>");
            });

            $("#selectInsurance1").val(result.data.fk_insurance);
            $("#nuc1").val(result.data.nuc);

            if (result.data.fund_type == "CP" || result.data.fund_type == "MP") {
                $("#selectStatusMF").val(result.data.estatus);
                $("#selectFstscnd1").val(result.data.fst_scnd);
                cp.style.display = "";
                lp.style.display = "none";
            } else {
                $("#amount1").val(parseFloat(result.data.amount).toLocaleString('en-US'));
                $("#selectPairNon1").val(result.data.pair_non);
                $("#initial_date1").val(result.data.deposit_date);
                $("#selectNucs1").val(result.data.fk_cp);
                cp.style.display = "none";
                lp.style.display = "";

                if ($("#selectPaymentform1").val() == 4) {
                    document.getElementById("divNucs1").style.display = "";
                    $("#selectNucs1").val(result.data.fk_cp).trigger('change');
                } else {
                    document.getElementById("divNucs1").style.display = "none";
                    $("#selectNucs1").val(result.data.fk_cp).trigger('change');
                }

                charge_moves = [];
                result.chargeMoves.forEach(function (valor) {
                    charge_moves.push({
                        'id': charge_moves.length + 1,
                        'amount': valor.amount,
                        'fk_charge': valor.fk_charge,
                        'chargeName': valor.name,
                        'apply_date': valor.apply_date
                    });
                });
                if (result.profile != 12) refreshTable(charge_moves);
                else refreshTableAgent(charge_moves);
            }
            fund_type = result.data.fund_type;
            $("#selectCurrency1").val(result.data.fund_curr);
            yieldr = result.data.yield;
            yield_usd = result.data.yield_usd;

            $("#myModaledit").modal('show');
        }
    });
}

function cancelEdit() { $("#myModaledit").modal('hide'); }

function actualizarApertura() {
    var active = document.getElementById('chkActive').checked ? 1 : 0;
    var data = {
        'id': idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_agent': $("#selectAgent1").val(),
        'fk_client': idClient,
        'name': $("#name1").val(),
        'firstname': $("#firstname1").val(),
        'lastname': $("#lastname1").val(),
        'birth_date': $("#birth_date1").val(),
        'rfc': $("#rfc1").val(),
        'curp': $("#curp1").val(),
        'cellphone': $("#cellphone1").val(),
        'email': $("#email1").val(),
        'fk_insurance': $("#selectInsurance1").val(),
        'currency': $("#selectCurrency1").val(),
        'fk_application': $("#selectAppli1").val(),
        'fk_payment_form': $("#selectPaymentform1").val(),
        'fst_scnd': $("#selectFstscnd1").val(),
        'pair_non': $("#selectPairNon1").val(),
        'domicile': $("#address1").val(),
        'amount': $("#amount1").val().replace(/[^0-9.]/g, ''),
        'nuc': $("#nuc1").val(),
        'fk_cp': $("#selectNucs1").val(),
        'deposit_date': $("#initial_date1").val(),
        'fund_type': fund_type,
        'estatus': $("#selectStatusMF").val(),
        'yield': yieldr,
        'yield_usd': yield_usd,
        'charge_moves': charge_moves,
        'active': active
    };

    jQuery.ajax({
        url: baseUrl + "/" + idupdate,
        type: 'put',
        data: data,
        dataType: 'json',
        success: function (result) {
            alertify.success(result.message);
            recargarTablasAperturas();
            $("#myModaledit").modal('hide');
        }
    });
}

function eliminarApertura(id) {
    var active = document.getElementById('chkActive').checked ? 1 : 0;
    alertify.confirm("Eliminar Apertura", "¿Desea borrar la Apertura?",
        function () {
            jQuery.ajax({
                url: baseUrl + "/" + id,
                data: { 'id': id, "_token": $("meta[name='csrf-token']").attr("content"), 'active': active },
                type: 'delete',
                dataType: 'json',
                success: function (result) {
                    recargarTablasAperturas();
                    alertify.success('Eliminado');
                }
            });
        },
        function () { alertify.error('Cancelado'); }
    );
}

var idStatus = 0;
function opcionesEstatus(id) {
    idStatus = id;
    jQuery.ajax({
        url: baseUrl + '/GetinfoStatus/' + idStatus,
        type: 'get',
        dataType: 'json',
        success: function (result) {
            $("#selectStatus").val(result.data.fk_status);
            fillStatusDates(result.data.pick_status, "Pick");
            fillStatusDates(result.data.limit_status, "Limit");
            fillStatusDates(result.data.agent_status, "Agent");
            fillStatusDates(result.data.office_status, "Office");
            fillStatusDates(result.data.finestra_status, "Finestra");
            $("#myModalStatus").modal('show');
        }
    });
}

function fillStatusDates(date, status) {
    if (date == null) {
        if (status != "Limit") $("#selectStatus" + status).val(1);
        document.getElementById("divDate" + status).style.display = "none";
    } else {
        if (status != "Limit") $("#selectStatus" + status).val(2);
        document.getElementById("divDate" + status).style.display = "";
    }
    $("#auth" + status).val(date);
}

function changeLimit() {
    var date = $("#authPick").val().split('-');
    var dateplus = new Date(date[0], date[1] - 1, date[2], 0, 0, 0);
    dateplus = addWorkDays(dateplus, 10);
    var day = dateplus.toLocaleString("default", { day: "2-digit" });
    var year = dateplus.toLocaleString("default", { year: "numeric" });
    var month = dateplus.toLocaleString("default", { month: "2-digit" });
    $("#authLimit").val(year + "-" + month + "-" + day);
}

function save() {
    var active = document.getElementById('chkActive').checked ? 1 : 0;
    var data = {
        'id': idStatus,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_status': $("#selectStatus").val(),
        'pick_status': $("#authPick").val(),
        'limit_status': $("#authLimit").val(),
        'agent_status': $("#authAgent").val(),
        'office_status': $("#authOffice").val(),
        'finestra_status': $("#authFinestra").val(),
        'active': active
    };
    jQuery.ajax({
        url: baseUrl + "/updateStatus",
        type: 'post',
        data: data,
        dataType: 'json',
        success: function (result) {
            alertify.success(result.message);
            recargarTablasAperturas();
            $("#myModalStatus").modal('hide');
        }
    });
}

function closeStatusInt() { $("#myModalStatus").modal('hide'); }

function showDate(div) {
    if ($("#selectStatus" + div).val() == 2) {
        document.getElementById("divDate" + div).style.display = "";
        if (div == "Pick") document.getElementById("divDateLimit").style.display = "";
    } else {
        document.getElementById("divDate" + div).style.display = "none";
        $("#auth" + div).val(null);
        if (div == "Pick") {
            document.getElementById("divDateLimit").style.display = "none";
            $("#authLimit").val(null);
        }
    }
}

function opcionesEstatusAgente(id) {
    idStatus = id;
    jQuery.ajax({
        url: baseUrl + '/GetinfoStatus/' + idStatus,
        type: 'get',
        dataType: 'json',
        success: function (result) {
            $("#selectStatus1").val(result.data.fk_status);
            fillStatusDatesAgent(result.data.pick_status, "Pick1");
            fillStatusDatesAgent(result.data.limit_status, "Limit1");
            fillStatusDatesAgent(result.data.agent_status, "Agent1");
            fillStatusDatesAgent(result.data.office_status, "Office1");
            $("#myModalStatusAgent").modal('show');
        }
    });
}

function fillStatusDatesAgent(date, status) {
    if (date == null) {
        $("#auth" + status).val("PENDIENTE");
        if (status == "Pick1") document.getElementById("divLimit").style.display = "none";
    } else {
        $("#auth" + status).val(date);
        if (status == "Pick1") document.getElementById("divLimit").style.display = "";
    }
}

function closeStatusAg() { $("#myModalStatusAgent").modal('hide'); }

function addWorkDays(startDate, days) {
    var daysToAdd = parseInt(days);
    var i = 0;
    while (i < daysToAdd) {
        startDate.setTime(startDate.getTime() + 24 * 60 * 60 * 1000);
        if (startDate.getDay() != 6 && startDate.getDay() != 0) i++;
    }
    return startDate;
}

var clienttext = "";
function buscarclientes(flag) {
    clienttext = (flag == 0) ? "#client_edit" : "#client_edit1";
    $("#modalSrcClient").modal("show");
}

function ocultar() { $("#modalSrcClient").modal("hide"); }

function obtenerid(id, name) {
    idClient = id;
    document.getElementById("fisica").style.display = "none";
    $(clienttext).val(name);
    $("#modalSrcClient").modal("hide");
}

function noRegistrado() {
    idClient = 0;
    document.getElementById("fisica").style.display = "";
    $(clienttext).val("");
    $("#modalSrcClient").modal("hide");
}

function fundchange(flag) {
    var route = (flag == 0)
        ? baseUrl + '/GetinfoFund/' + $("#selectInsurance").val()
        : baseUrl + '/GetinfoFund/' + $("#selectInsurance1").val();
    var cp = (flag == 0) ? document.getElementById("divCP") : document.getElementById("divCP1");
    var lp = (flag == 0) ? document.getElementById("divLP") : document.getElementById("divLP1");

    if (!$("#selectInsurance").val() && flag == 0) return;

    jQuery.ajax({
        url: route,
        type: 'get',
        dataType: 'json',
        success: function (result) {
            if (!result.data) return;
            if (result.data.fund_type == "CP" || result.data.fund_type == "MP") {
                cp.style.display = "";
                lp.style.display = "none";
            } else {
                cp.style.display = "none";
                lp.style.display = "";
            }
            if (flag == 0) {
                $("#selectCurrency").val(result.data.fund_curr);
                document.getElementById("divNucs").style.display = ($("#selectPaymentform").val() == 4) ? "" : "none";
            } else {
                $("#selectCurrency1").val(result.data.fund_curr);
                document.getElementById("divNucs1").style.display = ($("#selectPaymentform1").val() == 4) ? "" : "none";
            }
            fund_type = result.data.fund_type;
            yieldr = result.data.yield;
            yield_usd = result.data.yield_usd;
        }
    });
}

function cerrarApertura() { $("#myModal").modal("hide"); }
function OpenCharge() { $("#myModal2").modal("show"); }
function cancelar(modal) { $(modal).modal("hide"); }

function refreshTable(charges) {
    var table = $('#tbProf1').DataTable();
    table.clear();
    charges.forEach(function (valor) {
        var btnEdit = '<button class="btn btn-warning btn-sm" onclick="editarConducto(' + valor.id + ')"><i class="fas fa-edit"></i></button>';
        var btnTrash = '<button class="btn btn-danger btn-sm" onclick="eliminarConducto(' + valor.id + ')"><i class="fa fa-trash"></i></button>';
        table.row.add([parseFloat(valor.amount).toLocaleString('en-US'), valor.chargeName, valor.apply_date, btnEdit + " " + btnTrash]);
    });
    table.draw(false);
}

function refreshTableAgent(charges) {
    var table = $('#tbProf1').DataTable();
    table.clear();
    charges.forEach(function (valor) {
        table.row.add([parseFloat(valor.amount).toLocaleString('en-US'), valor.chargeName, valor.apply_date]);
    });
    table.draw(false);
}

function guardarConducto() {
    var amount = $("#camount").val().replace(/[^0-9.]/g, '');
    var fk_charge = $("#selectCharge").val();
    var chargeName = $("#selectCharge option:selected").text();
    var apply_date = $("#apply_date").val();

    if (!amount || !apply_date || fk_charge == 0) {
        alertify.warning("Los campos no deben quedar vacíos.");
        return;
    }
    charge_moves.push({ 'id': charge_moves.length + 1, 'amount': amount, 'fk_charge': fk_charge, 'chargeName': chargeName, 'apply_date': apply_date });
    refreshTable(charge_moves);
}

function eliminarConducto(id) {
    var index = charge_moves.findIndex(c => c.id == id);
    if (index > -1) charge_moves.splice(index, 1);
    refreshTable(charge_moves);
}

var chargeId = 0;
function editarConducto(id) {
    var found = charge_moves.find(c => c.id == id);
    if (found) {
        chargeId = id;
        $("#camount1").val(parseFloat(found.amount).toLocaleString('en-US'));
        $("#selectCharge1").val(found.fk_charge);
        $("#apply_date1").val(found.apply_date);
        $("#myModalEditCharge").modal("show");
    }
}

function actualizarConducto() {
    var found = charge_moves.find(c => c.id == chargeId);
    if (found) {
        found.amount = $("#camount1").val().replace(/[^0-9.]/g, '');
        found.fk_charge = $("#selectCharge1").val();
        found.chargeName = $("#selectCharge1 option:selected").text();
        found.apply_date = $("#apply_date1").val();
        refreshTable(charge_moves);
        $("#myModalEditCharge").modal("hide");
    }
}

var currentRenovationId = null;
var currentRenovationData = null;

// Modificar FillTable para detectar el Origen dinámicamente y renderizar el botón
function FillTable(data, profile, permission, requestsMap, renewalsMap) {
    if (requestsMap) window.requestsMap = requestsMap;
    if (renewalsMap) window.renewalsMap = renewalsMap;
    
    var mapReq = window.requestsMap || {};
    var mapRen = window.renewalsMap || {};
    var table = $('#example').DataTable();
    table.clear();

    data.forEach(function (v) {
        var btnStatAg = '<button class="btn btn-sm" style="color:#'+v.font_color+'; background:#'+v.color+'; border-color: #'+v.border_color+'; border-radius:20px; padding: 4px 12px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatusAgente('+v.oid+')">'+v.name+'</button>';
        var btnStat = (v.statid == 6 && !v.finestra_status) 
            ? '<button class="btn btn-sm text-white" style="background:#e98c46; border-color: #e98c46; border-radius:20px; padding: 4px 12px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatus('+v.oid+')">Pend. Finestra</button>'
            : '<button class="btn btn-sm" style="color:#'+v.font_color+'; background:#'+v.color+'; border-color: #'+v.border_color+'; border-radius:20px; padding: 4px 12px; font-weight:600; font-size:0.75rem;" onclick="opcionesEstatus('+v.oid+')">'+v.name+'</button>';
        
        var btnEdit = '<button class="btn btn-warning btn-sm mx-1" onclick="editarApertura('+v.oid+')" style="border-radius:6px;"><i class="fas fa-edit"></i></button>';
        var btnTrash = permission["erase"] == 1 ? '<button class="btn btn-danger btn-sm" onclick="eliminarApertura('+v.oid+')" style="border-radius:6px;"><i class="fa fa-trash"></i></button>' : '';

        // Determinación de Origen (Reemplaza la columna y añade el botón al grupo de opciones)
        var origenBadge = '';
        var btnReq = '';
        if (mapReq[v.oid]) {
            origenBadge = '<span class="badge badge-light border text-primary">Emisión (N)</span>';
            btnReq = '<button class="btn btn-info btn-sm shadow-sm" onclick="verDetalleSolicitud(' + mapReq[v.oid] + ')" title="Ver Expediente Emisión" style="border-radius:6px;"><i class="fas fa-file-alt"></i></button>';
        } else if (mapRen[v.oid]) {
            origenBadge = '<span class="badge badge-light border text-success">Renovación</span>';
            btnReq = '<button class="btn btn-primary btn-sm shadow-sm text-white" onclick="verDetalleRenovacion(' + mapRen[v.oid] + ')" title="Ver Expediente Renovación" style="border-radius:6px;"><i class="fas fa-sync-alt"></i></button>';
        } else {
            origenBadge = '<span class="badge badge-light border text-secondary">Alta Directa</span>';
        }

        var opciones = btnReq + btnEdit + btnTrash;

        if (profile != 12) {
            table.row.add([v.agent, v.cname, v.insurance, v.fund_type, v.nuc, v.limit_status, origenBadge, btnStatAg, btnStat, opciones]).node().id = v.oid;
        } else {
            table.row.add([v.agent, v.cname, v.insurance, v.fund_type, v.nuc, v.limit_status, origenBadge, btnStatAg, opciones]).node().id = v.oid;
        }
    });
    table.draw(false);
}

// 🟢 MÉTODOS DE LA PESTAÑA RENOVACIONES

function verDetalleRenovacion(id) {
    jQuery.ajax({
        url: baseUrl + '/getRenovationDetail/' + id,
        type: 'GET',
        success: function (res) {
            var r = res.data;
            currentRenovationData = r;
            $('#ren_rev_folio').text('Renovación #' + r.id);
            $('#ren_client').text(r.client_name);
            $('#ren_contract').text(r.contract_label);
            $('#ren_amount').text('$' + parseFloat(r.amount).toLocaleString('en-US'));
            $('#ren_payment').text(r.payment_choice == 2 ? 'Cuenta Bancaria' : 'Fondo CAPORTA');
            $('#ren_dest').text(r.payment_choice == 1 ? r.nuc_dest_label : 'N/A');
            
            // Beneficiarios
            var ben = '';
            if (r.beneficiaries_qty == 0) ben = 'Moral / No Aplica';
            else if (r.beneficiaries_qty == 3) ben = r.beneficiaries_extra;
            else {
                ben += '1. ' + r.beneficiary_1_name + ' (' + r.beneficiary_1_pct + '%)<br>';
                if (r.beneficiaries_qty == 2) ben += '2. ' + r.beneficiary_2_name + ' (' + r.beneficiary_2_pct + '%)';
            }
            $('#ren_beneficiaries_box').html(ben);

            // Documentos
            var $renDocsContainer = $('#ren_documents_container');
            $renDocsContainer.empty();
            if (res.documents && res.documents.length > 0) {
                res.documents.forEach(function (d) {
                    var icono = obtenerIconoDocumento(d.name);
                    var badgeHTML = `
                        <a href="${window.location.origin}/files/openingfiles/${d.name}" target="_blank" title="${d.name}"
                           class="d-inline-flex align-items-center bg-white border pl-3 pr-3 py-2 mr-2 mb-2 shadow-sm text-decoration-none"
                           style="border-radius: 8px; border: 1px solid #e2e8f0;">
                            <i class="fas ${icono} mr-2"></i>
                            <span class="mr-2 font-weight-bold text-muted small" style="max-width: 190px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                ${d.doc_label}
                            </span>
                            <i class="fas fa-external-link-alt text-muted" style="font-size: 11px;"></i>
                        </a>`;
                    $renDocsContainer.append(badgeHTML);
                });
            } else {
                $renDocsContainer.html('<span class="text-muted small">Sin documentos adjuntos.</span>');
            }

            if (r.status == 1) { $('#ren_staff_actions').show(); $('#ren_alert_rejected').hide(); }
            else { 
                $('#ren_staff_actions').hide(); 
                if (r.status == 3) { $('#ren_notes_text').text(r.staff_notes); $('#ren_alert_rejected').show(); }
                else $('#ren_alert_rejected').hide();
            }
            $('#modalReviewRenovation').modal('show');
        }
    });
}

function rechazarRenovacionActual() {
    alertify.prompt('Rechazar Renovación', 'Motivo para el agente:', '', function (e, val) {
        jQuery.ajax({
            url: baseUrl + '/rejectRenovation/' + currentRenovationData.id, type: 'POST',
            data: { _token: $("meta[name='csrf-token']").attr("content"), staff_notes: val },
            success: function (res) { if (res.status) { alertify.success(res.message); $('#modalReviewRenovation').modal('hide'); cargarRenovaciones(); } }
        });
    }, function(){});
}

function prepararAperturaDesdeRenovacion() {
    var r = currentRenovationData;
    $('#modalReviewRenovation').modal('hide');

    currentRequestId = null; // Limpiar emisión por si acaso
    currentRenovationId = r.id; // Almacenar renovacion
    
    $('#badgeFromRequest').removeClass('badge-info').addClass('badge-success').text('Renovación Precargada #' + r.id).show();

    $('#selectAgent').val(r.fk_agent);
    $('#nuc').val('PENDIENTE');
    idClient = r.fk_client;
    $('#client_edit').val(r.client_name);
    $('#fisica').hide(); // El cliente obligatoriamente ya está registrado
    
    $('#amount').val(parseFloat(r.amount).toLocaleString('en-US'));

    // Limpiar conductos (porque es renovación, usualmente no hay conducto bancario o se carga manual)
    charge_moves = []; refreshTable(charge_moves);
    $('#myModal').modal('show');
}