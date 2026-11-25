var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + getUrl.pathname;

const mapperInversiones = {
    1: 'ABONO/ INCREMENTO',
    2: 'AGREGAR CUENTA BANCARIA',
    3: 'CAMBIO DE DATOS DEL BENEFICIARIO',
    4: 'CAMBIO DE DATOS DEL CONTRATANTE',
    5: 'CAMBIO DE FORMA DE PAGO DEL PREMIO',
    6: 'CAMBIO DE PLAN /MOV INTERNO',
    7: 'RETIRO PARCIAL',
    8: 'RETIRO TOTAL',
    9: 'VARIOS'
};

var idClientEditContext = 0;
var idNucEditContext = 0;
var idInsuranceEditContext = 0;
var fundTypeEditContext = '';
var busc = 0;

$(document).ready(function () {
    var $headerCells = $('#ticketsTable thead th');
    var totalCols = $headerCells.length;

    $headerCells.each(function (index) {
        if (index < totalCols - 1) {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" />');
        }
    });

    $('#ticketsTable').DataTable({
        processing: true,
        serverSide: true,
        orderable: false,
        language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sSearch":         "Buscar:",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        ajax: {
            url: baseUrl + '/GetTicketsList/0',
            type: 'GET',
            data: function (d) {
                const urlParams = new URLSearchParams(window.location.search);
                d.target_id     = urlParams.get('ticket_id');
                d.onlyMyTickets = $('#chkMyTickets').is(':checked') ? 1 : 0;
                d.active        = $('#chkActive').is(':checked') ? 1 : 0;
                d.users         = $('#selectUserFilt').val();
                d.agents        = $('#selectAgentFilt').val();
                d.insurances    = $('#selectInsuranceExcFilt').val();
                d.services      = $('#selectServiceFilt').val();
                d.status        = $('#selectStatusFilt').val();
            }
        },
        columns: [
            { data: 'folio', name: 'folio' },
            { data: 'client_name', name: 'client_name' },
            { data: 'policy_number', name: 'policy_number' },
            { 
                data: 'agent_name', 
                name: 'agent_name', 
                visible: (window.userProfile != 12) 
            },
            { data: 'tramite', name: 'tramite' },
            { data: 'insbranch', name: 'insbranch' },
            { data: 'fecha_registro', name: 'fecha_registro' },
            { data: 'status_btn', name: 'status_btn' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        initComplete: function () {
            this.api().columns().every(function () {
                var that = this;
                if (!this.settings()[0].aoColumns[this.index()].bSearchable) {
                    return;
                }

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
        },
        drawCallback: function () {
            const urlParams = new URLSearchParams(window.location.search);
            const targetId = urlParams.get('ticket_id');
            if (targetId) {
                var targetRow = $('button[onclick="verTicket(' + targetId + ')"]').closest('tr');
                if (targetRow.length > 0) {
                    targetRow.addClass('highlight-row');
                    targetRow.one('click', function () {
                        $(this).removeClass('highlight-row');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                }
            }
        },
        scrollX: true
    });

    $('#srcClient, #tableEmailLogs, #srcPolicy').DataTable({
        language: {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Sin información",
            "sSearch": "Buscar:"
        },
        scrollX: true
    });

    $('#modalEmailHistory').on('shown.bs.modal', function () {
        // Reajusta las columnas de la tabla de clientes
        $('#tableEmailLogs').DataTable().columns.adjust().responsive.recalc();
    });

    $('#modalSrcClient').on('shown.bs.modal', function () {
        // Reajusta las columnas de la tabla de clientes
        $('#srcClient').DataTable().columns.adjust().responsive.recalc();
    });

    $('#modalSrcPolicy').on('shown.bs.modal', function () {
        // Reajusta las columnas de la tabla de clientes
        $('#srcPolicy').DataTable().columns.adjust().responsive.recalc();
    });

    $('#selectStatus').on('change', function () {
        var statusId = $(this).val();
        $('.status-block').hide();
        $('.status-block').find('input, textarea').removeAttr('required');

        if (statusId == '51') {
            $('#status_51').slideDown();
            $('#status_field_detail').attr('required', true);
        } else if (statusId == '52') {
            $('#status_52').slideDown();
            $('#status_field_folio').attr('required', true);
            $('#status_field_original').attr('required', true);
            toggleFechasOriginalStatus();
        } else if (statusId == '53') {
            $('#status_53').slideDown();
            $('#status_field_comm_susp').attr('required', true);
        } else if (statusId == '54') {
            $('#status_54').slideDown();
            $('#status_field_comm_attend').attr('required', true);
        } else if (statusId == '55') {
            $('#status_55').slideDown();
            $('#status_field_motive').attr('required', true);
        }
    });

    ObtenerTicketsAlIniciar();
    iniciarAutoRefreshTickets();
});

function ObtenerTicketsAlIniciar() {
    if ($.fn.DataTable.isDataTable('#ticketsTable')) {
        $('#ticketsTable').DataTable().ajax.reload(null, false);
    }
}

var autoRefreshTicketsTimer = null;
function iniciarAutoRefreshTickets() {
    if (autoRefreshTicketsTimer) {
        clearInterval(autoRefreshTicketsTimer);
    }

    autoRefreshTicketsTimer = setInterval(function () {
        if (document.hidden) return;
        if ($('.modal.show').length > 0 || $('#modalChatTicket').is(':visible')) return;

        if (typeof ObtenerTicketsAlIniciar === 'function') {
            ObtenerTicketsAlIniciar();
        }
    }, 30000);
}

// =========================================================
// 🟢 CLIENTE Y CONTRATOS (GetNucByClient)
// =========================================================
function buscarclientes() {
    busc = 1;
    $("#modalSrcClient").modal("show");
}

function buscarpolizas() {
    $("#modalSrcPolicy").modal("show");
}

function obtenerid(id) {
    if (busc == 1) {
        idNucEditContext = 0;
        idInsuranceEditContext = 0;
        fundTypeEditContext = '';
        $("#e_policy_edit").val('');
        $("#e_fk_nuc").val('');
        $("#e_fk_insurance").val('');
        busc = 0;
    }

    idClientEditContext = id;
    var route = baseUrl + '/GetNucByClient/' + id;

    jQuery.ajax({
        url: route,
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            var table = $('#srcPolicy').DataTable();
            table.clear();

            if (result.data && result.data.length > 0) {
                result.data.forEach(function (valor) {
                    var rowData = [];
                    rowData.push('<span class="font-weight-bold" style="color: #1e293b; font-size: 0.95rem;">' + (valor.nuc || 'S/N') + '</span>');
                    rowData.push('<span style="font-weight: 600; color: #475569;">' + (valor.fund || 'Fondo') + '</span>');
                    rowData.push('<span class="badge badge-info" style="font-weight: 500;">' + (valor.fund_type || 'N/A') + '</span>');

                    var btnSelect = '<button type="button" class="btn btn-primary btn-sm shadow-sm" ' +
                                    'onclick="obteneridPoliza(' + valor.nucid + ', \'' + valor.nuc + '\', \'' + valor.fundid + '\', \'' + valor.fund_type + '\')" ' +
                                    'style="border-radius: 6px; padding: 5px 16px; font-weight: 600; border: none;">' +
                                    'Seleccionar' +
                                    '</button>';
                    rowData.push(btnSelect);
                    table.row.add(rowData);
                });
            }

            table.draw(false);
            setTimeout(function () { table.columns.adjust(); }, 10);

            if (result.client) {
                $("#e_client_name").val(result.client.name);
            }

            $("#modalSrcClient").modal("hide");
        },
        error: function (result, error, errorTrown) {
            alertify.error('Error al obtener contratos del cliente: ' + errorTrown);
        }
    });
}

function obteneridPoliza(nucid, nuc, fundid, fundtype) {
    $("#e_policy_edit").val(nuc);
    $("#e_fk_nuc").val(nucid);
    $("#e_fk_insurance").val(fundid);
    $("#e_nrpolicy").val('');

    idNucEditContext = nucid;
    idInsuranceEditContext = fundid;
    fundTypeEditContext = fundtype;

    $("#modalSrcPolicy").modal("hide");
}

function toggleModoContratoManual() {
    alertify.prompt('Contrato / NUC Manual', 'Ingresa el número de contrato o NUC no registrado:', '', function (evt, value) {
        if (value && value.trim() !== '') {
            var valLimpio = value.trim();
            $("#e_policy_edit").val(valLimpio + ' (Manual)');
            $("#e_nrpolicy").val(valLimpio);
            $("#e_fk_nuc").val('');
            idNucEditContext = 0;
            $("#modalSrcPolicy").modal("hide");
            alertify.success('Contrato manual asignado.');
        }
    }, function () {
        alertify.message('Cancelado');
    });
}

// =========================================================
// 🟢 VER Y EDITAR TICKET
// =========================================================
function verTicket(id) {
    jQuery.ajax({
        url: baseUrl + '/getTicketDetail/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                var item = response.data;
                var docs = response.documents;

                $('#v_ticket_folio').text(item.folio || '#' + item.id);
                $('#v_ticket_client').text(item.client_name || 'Sin Cliente Asociado');
                $('#v_ticket_policy').text(item.contract_number || 'Sin Contrato Asociado');
                $('#v_ticket_insurance').text(item.insurance_name ? item.insurance_name + ' (' + (item.fund_type || '') + ')' : 'Fondo General');
                $('#v_ticket_service_text').text(mapperInversiones[item.invertion] || mapperInversiones[item.service] || 'VARIOS');
                $('#v_ticket_agent_comm').text(item.agent_comm || 'Sin comentarios');

                var containerDocs = $('#v_ticket_archivos_container');
                containerDocs.empty();

                if (docs && docs.length > 0) {
                    docs.forEach(function (doc) {
                        var icono = obtenerIconoDocumento(doc.name);
                        var partes = doc.name.split('_');
                        var nombreVisual = partes.slice(2).join('_');

                        var badgeHTML = `
                            <a href="${window.location.origin}/files/ticketfiles/${doc.name}" target="_blank" class="d-inline-flex align-items-center bg-white border p-2 mr-2 mb-2 shadow-sm text-decoration-none" style="border-radius: 8px; color: #475569;">
                                <i class="fas ${icono} mr-2" style="font-size: 1rem;"></i>
                                <span class="font-weight-bold small" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${nombreVisual}</span>
                            </a>`;
                        containerDocs.append(badgeHTML);
                    });
                    $('#v_block_archivos').show();
                } else {
                    $('#v_block_archivos').hide();
                }

                $('#modalViewTicketFull').modal('show');
            }
        }
    });
}

var contenedorArchivosEdit = new DataTransfer();
var documentosEliminados = [];

function editarTicket(id) {
    busc = 0;
    documentosEliminados = [];
    contenedorArchivosEdit = new DataTransfer();
    $('#lista_archivos_container_edit').empty();

    jQuery.ajax({
        url: baseUrl + '/getTicketEditData/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                var item = response.ticket;
                var docs = response.documents;

                $('#e_ticket_id').val(item.id);
                $('#e_ticket_folio').text(item.folio || '#' + item.id);
                $('#e_client_name').val(item.client_name || (item.nrclient ? item.nrclient + ' (No Registrado)' : 'Sin Cliente'));
                $('#e_policy_edit').val(item.contract_number || 'Sin Contrato');
                $('#e_agent_comm').val(item.agent_comm);
                $('#fk_agent').val(item.fk_agent);
                $('#fk_user').val(item.fk_user);
                $('#e_inversiones').val(item.invertion || item.service);

                $('#e_fk_nuc').val(item.fk_nuc || '');
                $('#e_fk_insurance').val(item.fk_insurance || '');
                $('#e_nrpolicy').val(item.nrpolicy || '');

                idClientEditContext = item.fk_client || 0;
                idNucEditContext = item.fk_nuc || 0;
                idInsuranceEditContext = item.fk_insurance || 0;

                if (item.fk_client > 1) {
                    obtenerid(item.fk_client);
                }

                // Folio único y Falta de Original
                $('#e_folio').val(item.folio || '');
                $('#e_original').val(item.original || 'N/A');
                $('#e_original_delivery_date').val(item.original_delivery_date || '');
                $('#e_original_finestra_date').val(item.original_finestra_date || '');
                toggleFechasOriginalEdit();

                // Documentos existentes
                var containerExistentes = $('#e_ticket_archivos_existentes_container');
                containerExistentes.empty();

                if (docs && docs.length > 0) {
                    docs.forEach(function (doc) {
                        var icono = obtenerIconoDocumento(doc.name);
                        var partes = doc.name.split('_');
                        var nombreVisual = partes.slice(2).join('_');

                        var badgeHTML = `
                            <div id="old_doc_${doc.id}" class="d-inline-flex align-items-center bg-white border pl-3 pr-2 py-2 mr-2 mb-2 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                                <i class="fas ${icono} mr-2"></i>
                                <a href="${window.location.origin}/files/ticketfiles/${doc.name}" target="_blank" class="mr-3 font-weight-bold text-muted small text-decoration-none" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    ${nombreVisual}
                                </a>
                                <button type="button" onclick="removerArchivoExistente(${doc.id})" title="Eliminar archivo" style="background: transparent; border: none; padding: 0; color: #94a3b8; cursor: pointer;">
                                    <i class="fas fa-times" style="font-size: 11px;"></i>
                                </button>
                            </div>`;
                        containerExistentes.append(badgeHTML);
                    });
                    $('#e_block_archivos_existentes').show();
                } else {
                    $('#e_block_archivos_existentes').hide();
                }

                $('#modalEditTicketFull').modal('show');
            }
        }
    });
}

function actualizarCambiosTicketFull() {
    var form = $('#formEditTicketFull')[0];
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        alertify.warning('Completa los campos obligatorios (*).');
        return;
    }

    var $btn = $('#btnGuardarCambiosFull');
    var textoOriginalBtn = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...');

    var id = $('#e_ticket_id').val();
    var formData = new FormData(form);

    formData.append('fk_client', idClientEditContext);
    formData.append('fk_nuc', idNucEditContext);
    formData.append('fk_insurance', idInsuranceEditContext);
    formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    formData.delete('subir_archivo[]');
    if (contenedorArchivosEdit.files.length > 0) {
        Array.from(contenedorArchivosEdit.files).forEach(file => {
            formData.append('subir_archivo[]', file);
        });
    }

    if (documentosEliminados.length > 0) {
        documentosEliminados.forEach(idDoc => {
            formData.append('documentos_eliminados[]', idDoc);
        });
    }

    jQuery.ajax({
        url: baseUrl + '/UpdateTicketFullData/' + id,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            $btn.prop('disabled', false).html(textoOriginalBtn);
            if (result.status === 'success') {
                alertify.success(result.message);
                $('#modalEditTicketFull').modal('hide');
                ObtenerTicketsAlIniciar();
            } else {
                alertify.error(result.message);
            }
        },
        error: function () {
            $btn.prop('disabled', false).html(textoOriginalBtn);
            alertify.error('Error al procesar la actualización del ticket.');
        }
    });
}

// =========================================================
// 🟢 GESTIÓN DE ARCHIVOS Y FOLIOS
// =========================================================
function manejarMultiplesArchivosEdit(input) {
    if (input.files) {
        Array.from(input.files).forEach(file => {
            contenedorArchivosEdit.items.add(file);
        });
        renderizarNuevosArchivosEdit();
    }
}

function renderizarNuevosArchivosEdit() {
    var container = $('#lista_archivos_container_edit');
    container.empty();
    document.getElementById('subir_archivo_edit').files = contenedorArchivosEdit.files;

    Array.from(contenedorArchivosEdit.files).forEach((file, index) => {
        var icono = obtenerIconoDocumento(file.name);
        var badgeHTML = `
            <div class="d-inline-flex align-items-center bg-white border pl-3 pr-2 py-2 mr-2 mb-2 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                <i class="fas ${icono} mr-2"></i>
                <span class="mr-3 font-weight-bold text-muted small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</span>
                <button type="button" onclick="quitarNuevoArchivoEdit(${index})" style="background: transparent; border: none; padding: 0; color: #94a3b8; cursor: pointer;">
                    <i class="fas fa-times" style="font-size: 11px;"></i>
                </button>
            </div>`;
        container.append(badgeHTML);
    });
}

function quitarNuevoArchivoEdit(index) {
    var dt = new DataTransfer();
    Array.from(contenedorArchivosEdit.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    contenedorArchivosEdit = dt;
    renderizarNuevosArchivosEdit();
}

function removerArchivoExistente(docId) {
    documentosEliminados.push(docId);
    $('#old_doc_' + docId).fadeOut(250, function () {
        $(this).remove();
        if ($('#e_ticket_archivos_existentes_container').children().length === 0) {
            $('#e_block_archivos_existentes').hide();
        }
    });
    alertify.message('Archivo marcado para eliminación');
}

function agregarParejaFolioFecha(folioVal = '', fechaVal = '') {
    var totalFilas = $('#container_parejas_folios .pareja-folio-row').length;
    var esElPrimero = totalFilas === 0;

    var filaHTML = `
        <div class="row no-gutters mb-2 pareja-folio-row p-2 bg-light border shadow-sm align-items-center" style="border-radius: 10px;">
            <div class="col-md-6 pr-md-2 mb-2 mb-md-0">
                <input type="text" name="folios[]" class="form-control font-weight-bold input-folio-val" value="${folioVal}" placeholder="Folio (Ej. INV-1001)" ${esElPrimero ? 'required' : ''}>
            </div>
            <div class="col-md-5 pr-md-2 mb-2 mb-md-0">
                <input type="date" name="fechas[]" class="form-control font-weight-bold input-fecha-val" value="${fechaVal}" ${esElPrimero ? 'required' : ''}>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn p-0 text-muted" onclick="eliminarParejaFolioFecha(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;
    $('#container_parejas_folios').append(filaHTML);
}

function eliminarParejaFolioFecha(btn) {
    var contenedor = $('#container_parejas_folios');
    if (contenedor.find('.pareja-folio-row').length <= 1) {
        contenedor.find('.input-folio-val, .input-fecha-val').val('');
        return;
    }
    $(btn).closest('.pareja-folio-row').remove();
}

function agregarParejaFolioFechaEdit(folioVal = '', fechaVal = '') {
    var filaHTML = `
        <div class="row no-gutters mb-2 e-pareja-folio-row p-2 bg-light border shadow-sm align-items-center" style="border-radius: 10px;">
            <div class="col-md-6 pr-md-2 mb-2 mb-md-0">
                <input type="text" name="folios_edit[]" class="form-control font-weight-bold" value="${folioVal}" placeholder="Folio (Ej. INV-1001)">
            </div>
            <div class="col-md-5 pr-md-2 mb-2 mb-md-0">
                <input type="date" name="fechas_edit[]" class="form-control font-weight-bold" value="${fechaVal}">
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn p-0 text-muted" onclick="$(this).closest('.e-pareja-folio-row').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;
    $('#e_container_parejas_folios').append(filaHTML);
}

// =========================================================
// 🟢 ESTATUS, CHAT, CORREOS Y ACCIONES
// =========================================================
var idTicket = 0;
var contenedorArchivosFaltantes = new DataTransfer();

function opcionesEstatus(ticketId, statusId) {
    idTicket = ticketId;
    $('#estatus_ticket_id').val(statusId);
    $('#formEstatusModal').removeClass('was-validated');
    contenedorArchivosFaltantes = new DataTransfer();
    $('#lista_faltantes_51, #lista_faltantes_53').empty();

    jQuery.ajax({
        url: baseUrl + '/GetTicketStatusFields/' + ticketId,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                var tk = response.ticket;

                $('#status_field_folio').val(tk.folio || '');
                $('#status_field_original').val(tk.original || 'N/A');
                $('#status_field_original_delivery_date').val(tk.original_delivery_date || '');
                $('#status_field_original_finestra_date').val(tk.original_finestra_date || '');

                $('#status_field_detail').val(tk.detail);
                $('#status_field_comm_susp').val(tk.comm_susp);
                $('#status_field_comm_attend').val(tk.comm_attend);
                $('#status_field_motive').val(tk.motive);

                renderizarArchivosExistentes(tk.link_miss, 'file_existente_link_miss');
                renderizarArchivosExistentes(tk.link_susp, 'file_existente_link_susp');
                renderizarArchivosExistentes(tk.link_attend, 'file_existente_link_attend');

                $('#selectStatus').val(statusId).trigger('change');

                if (response.perm_btn['modify'] != 1) {
                    $('#formEstatusModal input, #formEstatusModal textarea, #formEstatusModal select').prop('disabled', true);
                    $('#btnGuardarEstatus').hide();
                    $('#chkbxestatus').hide();
                    if (statusId == '51' || statusId == '53') {
                        $('#subir_faltantes_51, #subir_faltantes_53').prop('disabled', false);
                        $('#btnSubirFaltantesAgente').show();
                    }
                } else {
                    $('#formEstatusModal input, #formEstatusModal textarea, #formEstatusModal select').prop('disabled', false);
                    $('#btnSubirFaltantesAgente').hide();
                    $('#btnGuardarEstatus').show();
                    $('#chkbxestatus').show();
                }

                $('#myEstatusModal').modal('show');
            }
        }
    });
}

function actualizarEstatus() {
    var form = $('#formEstatusModal')[0];
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        alertify.warning('Llena los campos obligatorios del estatus.');
        return;
    }

    var $btn = $('#btnGuardarEstatus');
    var textoOriginalBtn = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Actualizando...');

    var formData = new FormData(form);
    formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    jQuery.ajax({
        url: baseUrl + '/UpdateTicketStatus/' + idTicket,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            $btn.prop('disabled', false).html(textoOriginalBtn);
            if (result.status === 'success') {
                alertify.success(result.message);
                $('#myEstatusModal').modal('hide');
                ObtenerTicketsAlIniciar();
            } else {
                alertify.error(result.message);
            }
        },
        error: function () {
            $btn.prop('disabled', false).html(textoOriginalBtn);
            alertify.error('Error al actualizar el estatus.');
        }
    });
}

function manejarArchivosFaltantes(input, containerId) {
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            contenedorArchivosFaltantes.items.add(file);
        });
        renderizarListaFaltantes(containerId);
        input.value = '';
    }
}

function renderizarListaFaltantes(containerId) {
    var container = $('#' + containerId);
    container.empty();
    Array.from(contenedorArchivosFaltantes.files).forEach((file, index) => {
        var badgeHTML = `
            <span class="badge badge-light border text-dark p-2 d-inline-flex align-items-center mr-2 mb-2 shadow-sm">
                <i class="fas fa-file-pdf text-danger mr-2"></i>
                <span class="text-truncate" style="max-width: 160px;">${file.name}</span>
                <button type="button" class="btn p-0 ml-2 text-muted" onclick="removerArchivoFaltante(${index}, '${containerId}')">
                    <i class="fas fa-times"></i>
                </button>
            </span>`;
        container.append(badgeHTML);
    });
}

function removerArchivoFaltante(index, containerId) {
    var dt = new DataTransfer();
    Array.from(contenedorArchivosFaltantes.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    contenedorArchivosFaltantes = dt;
    renderizarListaFaltantes(containerId);
}

function subirFaltantesAgente() {
    if (contenedorArchivosFaltantes.files.length === 0) {
        alertify.warning('Selecciona al menos un archivo.');
        return;
    }

    var $btn = $('#btnSubirFaltantesAgente');
    var textoOriginalBtn = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Subiendo...');

    var formData = new FormData();
    formData.append('_token', $("meta[name='csrf-token']").attr("content"));
    Array.from(contenedorArchivosFaltantes.files).forEach(file => {
        formData.append('archivos_faltantes[]', file);
    });

    jQuery.ajax({
        url: baseUrl + '/UploadMissingFiles/' + idTicket,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            $btn.prop('disabled', false).html(textoOriginalBtn);
            if (result.status === 'success') {
                alertify.success(result.message);
                $('#myEstatusModal').modal('hide');
                ObtenerTicketsAlIniciar();
            } else {
                alertify.error(result.message);
            }
        },
        error: function () {
            $btn.prop('disabled', false).html(textoOriginalBtn);
            alertify.error('Error al subir los archivos.');
        }
    });
}

function eliminarTicket(id) {
    alertify.confirm(
        'Confirmar Eliminación',
        '¿Estás seguro de que deseas eliminar este ticket? Esta acción no se puede deshacer.',
        function () {
            jQuery.ajax({
                url: baseUrl + '/destroyTicket/' + id,
                type: 'POST',
                data: { _token: $("meta[name='csrf-token']").attr("content") },
                dataType: 'json',
                success: function (result) {
                    if (result.status === 'success') {
                        alertify.success(result.message);
                        ObtenerTicketsAlIniciar();
                    } else {
                        alertify.error(result.message);
                    }
                },
                error: function () {
                    alertify.error('Error al intentar borrar el ticket.');
                }
            });
        },
        function () { alertify.message('Cancelado'); }
    ).set('labels', { ok: 'Sí, Eliminar', cancel: 'Cancelar' });
}

var currentChatTicketId = null;
var chatInterval = null;

function abrirChatTicket(ticketId, folio) {
    currentChatTicketId = ticketId;
    $('#chat_modal_title').text('Chat - Folio ' + folio);
    $('#chat_modal_subtitle').text('Cargando mensajes...');
    $('#input_chat_message').val('');
    $('#checkNotaInterna').prop('checked', false);

    if (window.permModify == 1) {
        $('#wrapper_nota_interna').show();
    } else {
        $('#wrapper_nota_interna').hide();
    }

    cargarMensajesChat(true);
    $('#modalChatTicket').modal('show');

    jQuery.ajax({
        url: baseUrl + '/markChatAsRead/' + currentChatTicketId,
        type: 'POST',
        data: { _token: $("meta[name='csrf-token']").attr("content") },
        success: function () {
            if (typeof ObtenerTicketsAlIniciar === 'function') {
                ObtenerTicketsAlIniciar();
            }
        }
    });

    clearInterval(chatInterval);
    chatInterval = setInterval(function () {
        if ($('#modalChatTicket').is(':visible')) {
            cargarMensajesChat(false);
        }
    }, 5000);
}

$('#modalChatTicket').on('hidden.bs.modal', function () {
    clearInterval(chatInterval);
    currentChatTicketId = null;
});

function cargarMensajesChat(scrollBottom = false) {
    if (!currentChatTicketId) return;

    jQuery.ajax({
        url: baseUrl + '/getTicketMessages/' + currentChatTicketId,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                var container = $('#chat_body_container');
                container.empty();
                var mensajes = response.messages;
                var currentUserId = response.current_user_id;

                $('#chat_modal_subtitle').text(mensajes.length + ' mensaje(s)');

                if (mensajes.length === 0) {
                    container.html('<div class="text-center my-auto text-muted small"><i class="fas fa-comments fa-2x mb-2 d-block opacity-50"></i>Inicia la conversación sobre este ticket...</div>');
                } else {
                    mensajes.forEach(function (m) {
                        var esMio = (m.fk_user == currentUserId);
                        var esInterno = (m.is_internal == 1);
                        var htmlBurbuja = '';

                        if (esInterno) {
                            htmlBurbuja = `
                                <div class="w-100 my-2 text-center">
                                    <div class="d-inline-block p-2 px-3 text-dark text-left shadow-sm" style="background-color: #fef08a; border: 1px solid #fde047; border-radius: 10px; max-width: 90%; font-size: 0.82rem;">
                                        <div class="font-weight-bold text-amber-900 mb-1"><i class="fas fa-lock mr-1"></i> Nota Interna - ${m.sender_name}</div>
                                        <div>${m.message}</div>
                                        <div class="text-right text-muted mt-1" style="font-size: 0.7rem;">${m.formatted_time}</div>
                                    </div>
                                </div>`;
                        } else if (esMio) {
                            htmlBurbuja = `
                                <div class="d-flex flex-column align-items-end my-1">
                                    <div class="bg-primary text-white p-2 px-3 shadow-sm" style="border-radius: 15px 15px 0px 15px; max-width: 80%; font-size: 0.88rem; background-color: #00a859 !important;">
                                        <div>${m.message}</div>
                                    </div>
                                    <small class="text-muted mr-1" style="font-size: 0.7rem;">${m.formatted_time}</small>
                                </div>`;
                        } else {
                            htmlBurbuja = `
                                <div class="d-flex flex-column align-items-start my-1">
                                    <small class="text-muted font-weight-bold ml-1 mb-1" style="font-size: 0.75rem;">${m.sender_name}</small>
                                    <div class="bg-white border text-dark p-2 px-3 shadow-sm" style="border-radius: 15px 15px 15px 0px; max-width: 80%; font-size: 0.88rem;">
                                        <div>${m.message}</div>
                                    </div>
                                    <small class="text-muted ml-1" style="font-size: 0.7rem;">${m.formatted_time}</small>
                                </div>`;
                        }
                        container.append(htmlBurbuja);
                    });
                }

                if (scrollBottom) {
                    container.scrollTop(container[0].scrollHeight);
                }
            }
        }
    });
}

function enviarMensajeChat() {
    var txt = $('#input_chat_message').val().trim();
    if (txt === '') return;

    var isInternal = $('#checkNotaInterna').is(':checked') ? 1 : 0;
    var $btn = $('#btnEnviarMensaje');
    $btn.prop('disabled', true);

    jQuery.ajax({
        url: baseUrl + '/sendTicketMessage',
        type: 'POST',
        data: {
            ticket_id: currentChatTicketId,
            message: txt,
            is_internal: isInternal,
            _token: $("meta[name='csrf-token']").attr("content")
        },
        dataType: 'json',
        success: function (response) {
            $btn.prop('disabled', false);
            if (response.status === 'success') {
                $('#input_chat_message').val('');
                $('#checkNotaInterna').prop('checked', false);
                cargarMensajesChat(true);
            } else {
                alertify.error(response.message);
            }
        },
        error: function () {
            $btn.prop('disabled', false);
            alertify.error('Error al enviar el mensaje.');
        }
    });
}

function verHistorial(ticketId) {
    $('#eh_ticket_subtitle').text('Ticket #' + String(ticketId).padStart(5, '0'));
    var $tbody = $('#tbodyEmailLogs');
    $tbody.html('<tr><td colspan="6" class="py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando historial...</td></tr>');
    $('#modalEmailHistory').modal('show');

    jQuery.ajax({
        url: baseUrl + '/getEmailLogs/' + ticketId,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $tbody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(function (log) {
                    var payload = {};
                    try {
                        payload = typeof log.payload === 'string' ? JSON.parse(log.payload) : (log.payload || {});
                    } catch (e) { payload = {}; }

                    var deliveryBadge = (log.delivery_status === 'SENT')
                        ? '<span class="badge badge-success px-3 py-2">Enviado</span>'
                        : '<span class="badge badge-danger px-3 py-2">Falló</span>';

                    var detalleHTML = '<div class="text-left font-weight-bold text-dark small mb-1">' + (log.subject || 'Sin Asunto') + '</div>';
                    if (payload.comentario) {
                        detalleHTML += '<div class="text-left text-muted small bg-light p-2 rounded mb-1">' + payload.comentario + '</div>';
                    }

                    var row = `
                        <tr>
                            <td class="align-middle text-muted small font-weight-bold">${log.formatted_date}</td>
                            <td class="align-middle"><span class="badge badge-light border text-uppercase">${log.status_name || 'N/A'}</span></td>
                            <td class="align-middle font-weight-bold text-dark small">${log.recipient_email}</td>
                            <td class="align-middle" style="max-width: 320px;">${detalleHTML}</td>
                            <td class="align-middle">${deliveryBadge}</td>
                            <td class="align-middle text-muted small">${log.sender_name || 'Sistema'}</td>
                        </tr>`;
                    $tbody.append(row);
                });
            } else {
                $tbody.html('<tr><td colspan="6" class="py-4 text-muted">No hay registros de correos enviados.</td></tr>');
            }
        },
        error: function () {
            $tbody.html('<tr><td colspan="6" class="py-4 text-danger">Error al cargar historial.</td></tr>');
        }
    });
}

function clearFilters() {
    $('#selectUserFilt, #selectAgentFilt, #selectInsuranceExcFilt, #selectServiceFilt, #selectStatusFilt').val(null).trigger('change');
    if (typeof alertify !== 'undefined') {
        alertify.message('Filtros restablecidos');
    }
}

function ocultar(modalId) {
    $(modalId).modal("hide");
}

function renderizarArchivosExistentes(campoDb, contenedorId) {
    var $contenedor = $('#' + contenedorId);
    $contenedor.empty();
    if (!campoDb) return;

    var archivos = [];
    try {
        var parsed = JSON.parse(campoDb);
        archivos = Array.isArray(parsed) ? parsed : [campoDb];
    } catch (e) {
        archivos = [campoDb];
    }

    if (archivos.length === 0) return;

    var html = '<div class="d-flex flex-wrap gap-2 mt-1">';
    archivos.forEach(function (archivo) {
        var esPdf = archivo.toLowerCase().endsWith('.pdf');
        var icono = esPdf ? 'fa-file-pdf text-danger' : 'fa-file-image text-success';
        html += `<a href="${window.location.origin}/files/ticketfiles/${archivo}" target="_blank" class="btn btn-sm btn-outline-secondary mr-1 mb-1 font-weight-bold d-inline-flex align-items-center">
            <i class="fas ${icono} mr-1"></i> Ver Archivo
        </a>`;
    });
    html += '</div>';
    $contenedor.html(html);
}

function toggleFechasOriginalStatus() {
    var val = $('#status_field_original').val();

    $('#div_status_original_delivery, #div_status_original_finestra').hide();
    $('#status_field_original_delivery_date, #status_field_original_finestra_date').removeAttr('required');

    if (val === 'Entregado') {
        $('#div_status_original_delivery').slideDown();
        $('#status_field_original_delivery_date').attr('required', true);
    } else if (val === 'Finestra') {
        $('#div_status_original_delivery').slideDown();
        $('#div_status_original_finestra').slideDown();
        $('#status_field_original_delivery_date').attr('required', true);
        $('#status_field_original_finestra_date').attr('required', true);
    }
}

function toggleFechasOriginalEdit() {
    var val = $('#e_original').val();

    $('#div_edit_original_delivery, #div_edit_original_finestra').hide();
    $('#e_original_delivery_date, #e_original_finestra_date').removeAttr('required');

    if (val === 'Entregado') {
        $('#div_edit_original_delivery').slideDown();
        $('#e_original_delivery_date').attr('required', true);
    } else if (val === 'Finestra') {
        $('#div_edit_original_delivery').slideDown();
        $('#div_edit_original_finestra').slideDown();
        $('#e_original_delivery_date').attr('required', true);
        $('#e_original_finestra_date').attr('required', true);
    }
}