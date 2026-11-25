var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;

$(document).ready( function () {
    $('#srcClient').DataTable({
        language : {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
              "sFirst":    "Primero",
              "sLast":     "Último",
              "sNext":     "Siguiente",
              "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "scrollX": true, // Activa el scroll horizontal interno
        
    });

    $('#srcPolicy').DataTable({
        language : {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
              "sFirst":    "Primero",
              "sLast":     "Último",
              "sNext":     "Siguiente",
              "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "scrollX": true, // Activa el scroll horizontal interno
        
    });

    $('#modalSrcClient').on('shown.bs.modal', function () {
        // Reajusta las columnas de la tabla de clientes
        $('#srcClient').DataTable().columns.adjust().responsive.recalc();
    });

    $('#modalSrcPolicy').on('shown.bs.modal', function () {
        // Reajusta las columnas de la tabla de clientes
        $('#srcPolicy').DataTable().columns.adjust().responsive.recalc();
    });

    $('#inversion_seguro').on('change', function() {
        var value = $(this).val();
        
        if (value === 'seg') {
            $('#bloque_inversion').slideUp();
            $('#bloque_seguro').slideDown();
            
            // Manejo de obligatorios principales
            $('#inversiones, #fk_nuc').removeAttr('required');
            $('#compania, #ramo, #tipo_servicio').attr('required', true);
        } 
        else if (value === 'inv') {
            $('#bloque_seguro').slideUp();
            $('#bloque_inversion').slideDown();
            
            // Manejo de obligatorios principales
            $('#compania, #ramo, #tipo_servicio, #servicios_agentes, #servicios').removeAttr('required');
            $('#inversiones, #fk_nuc').attr('required', true);
            
            // Resetear el sub-select de seguros
            $('#tipo_servicio').val('').trigger('change');
        } 
        else {
            $('#bloque_inversion').slideUp();
            $('#bloque_seguro').slideUp();
            $('#inversiones, #compania, #ramo, #tipo_servicio').removeAttr('required');
        }
    });

    // 2. Cambio secundario: Tipo de Servicio (Dentro de Seguro) -> ¡CORREGIDO!
    $('#tipo_servicio').on('change', function() {
        var value = $(this).val();
        
        if (value === '1') {
            // Muestra Agentes, oculta Servicios
            $('#col_servicios').slideUp();
            $('#col_servicios_agentes').slideDown();
            
            $('#servicios').removeAttr('required');
            $('#servicios_agentes').attr('required', true);
        } 
        else if (value === '2') {
            // Muestra Servicios, oculta Agentes
            $('#col_servicios_agentes').slideUp(); // <-- Aquí estaba el detalle, ahora sí lo oculta
            $('#col_servicios').slideDown();
            
            $('#servicios_agentes').removeAttr('required');
            $('#servicios').attr('required', true);
        } 
        else {
            // Si no hay nada seleccionado, oculta ambos
            $('#col_servicios_agentes').slideUp();
            $('#col_servicios').slideUp();
            $('#servicios_agentes, #servicios').removeAttr('required');
        }
    });

    $('#compania').on('change', function() {
        var insuranceId = $(this).val();
        var $targetClaveSelect = $('#fk_agent_key');

        if (!insuranceId) {
            $targetClaveSelect.val('0').trigger('change');
            return;
        }

        // 🟢 Busca si el agente tiene una clave registrada con ese data-insurance
        var $matchingOption = $targetClaveSelect.find(`option[data-insurance-id="${insuranceId}"]`);

        if ($matchingOption.length > 0) {
            // Encontró clave propia del agente
            $targetClaveSelect.val($matchingOption.val()).trigger('change');
        } else {
            // No tiene clave en esa aseguradora -> Asignar Clave de ELAN (0)
            $targetClaveSelect.val('0').trigger('change');
            alertify.warning('Se asignó Clave de ELAN. Si cuentas con clave propia, por favor contáctanos para registrarla.');
        }
    });
} );

idClient = 0;
idPolicy = 0;
idInsurance = 0;
fund_type = '';
var modoClienteActual = 'registrado';

busc = 0;
function buscarclientes(){
    busc = 1;
    $("#modalSrcClient").modal("show");
}

function buscarpolizas(edit){
    editsearch = edit;
    $("#modalSrcPolicy").modal("show");
}

function ocultar(modalId){
    $(modalId).modal("hide");
}

function toggleModoCliente(modo) {
    modoClienteActual = modo;
    
    // Cerramos el modal usando tu función nativa
    $("#modalSrcClient").modal("hide");

    var $inputClient = $('#client_edit');
    var $btnBuscarClient = $('#btn_buscar_cliente');
    var $colPolicy = $('#col_polizas');

    if (modo === 'propio') {
        idClient = 0;
        idPolicy = 0;

        // 1. Configurar campo de cliente para Servicio Propio
        $('#client_edit').removeClass('is-invalid');
        $inputClient.val('SERVICIO PROPIO (AGENTE)')
                    .prop('disabled', true);
        $('#policy_edit').prop('disabled', true).val('').attr('placeholder', 'Sin contrato/NUC seleccionado');
        $colPolicy.hide();

        alertify.message('Modo: Servicio Propio activado');

    } else if (modo === 'no_registrado') {
        idClient = 1;
        idPolicy = 1;

        // 1. Habilitar campo de texto libre para el Cliente
        $inputClient.val('')
            .prop('disabled', false)
            .attr('placeholder', 'Escribe el nombre completo del cliente...')
            .focus();

        // 2. Habilitar campo de texto libre para la Póliza
        $('#policy_edit').val('')
                    .prop('disabled', false)
                    .attr('placeholder', 'Escribe el número de contrato/NUC...');
        
        $('#btn_buscar_poliza').prop('disabled', true).hide(); 

        $colPolicy.show();

        alertify.warning('Modo: Cliente No Registrado. Ingresa los datos manualmente.');
    }
}

function toggleModoPoliza() {
    var $colPolicy = $('#col_polizas');

    idPolicy = 1;

    // 2. Habilitar campo de texto libre para la Póliza
    $('#policy_edit').val('')
        .prop('disabled', false)
        .attr('placeholder', 'Escribe el número de contrato/NUC...')
        .focus();

    $colPolicy.show();
    $("#modalSrcPolicy").modal("hide");

    alertify.warning('Modo: Póliza No Registrada. Ingresa los datos manualmente.');
}

function obtenerid(id){
    if(busc == 1) {
        modoClienteActual = 'registrado';
        $('#client_edit').prop('disabled', true);
        $('#policy_edit').prop('disabled', true).val('').attr('placeholder', 'Sin póliza seleccionada');
        $('#btn_buscar_poliza').show();
        $('#col_polizas').show();
        idPolicy = 0;
        idInsurance = 0;
        fund_type = '';
        $("#policy_edit").val('');
        $('#client_edit').prop('disabled', true);
        busc = 0;
    }
    idClient = id;
    $('#client_edit').removeClass('is-invalid');
    var route = baseUrl + '/GetNucByClient/'+ id;

    jQuery.ajax({
        url: route,
        type: 'get',
        dataType: 'json',
        success: function(result)
        {
            var table = $('#srcPolicy').DataTable(); 
            table.clear();

            result.data.forEach(function(valor, indice, array) {
                var rowData = [];
                
                // 1. Columna: Póliza (Texto fuerte y destacado)
                rowData.push('<span class="font-weight-bold" style="color: #1e293b; font-size: 0.95rem;">' + valor.nuc + '</span>');
                
                // 2. Columna: Aseguradora (Gris elegante)
                rowData.push('<span style="font-weight: 600; color: #475569;">' + valor.fund + '</span>');
                
                // 3. Columna: Ramo y Plan (Estilo secundario limpio)
                rowData.push('<span style="font-weight: 500; color: #64748b;">' + valor.fund_type + '</span>');

                // 5. Columna: Acción (Botón Seleccionar Premium con degradado)
                var btnSelect = '<button type="button" class="btn btn-primary btn-sm shadow-sm" ' +
                                'onclick="obteneridPoliza(' + valor.nucid + ', \'' + valor.nuc + '\',\'' + valor.fundid + '\',\''+ valor.fund_type + '\')" ' +
                                'style="border-radius: 6px; padding: 5px 16px; font-weight: 600; border: none;">' +
                                'Seleccionar' +
                                '</button>';
                rowData.push(btnSelect);

                // Agregamos la fila estructurada al DataTables
                table.row.add(rowData);
            });

            table.draw(false);

            // Ajuste automático de columnas para asegurar alineación perfecta tras renderizar
            setTimeout(function() {
                table.columns.adjust();
            }, 10);

            // Llenamos el input del cliente
            $("#client_edit").val(result.client.name);
            
            // Habilitamos el botón de póliza aplicando los estilos activos que definimos antes
            $("#btn_buscar_poliza").prop("disabled", false).css({
                'opacity': '1',
                'cursor': 'pointer'
            });

            // Cerramos el modal de clientes de manera segura
            $("#modalSrcClient").modal("hide");
        },
        error: function(result, error, errorTrown)
        {
            alertify.error(errorTrown);
        }
    });
}

function obteneridPoliza(id, policy, fundid, fundtype){
    $("#policy_edit").val(policy);
    idPolicy = id;
    idInsurance = fundid;
    fund_type = fundtype;
    $('#client_edit').prop('disabled', true);

    // $("#compania").val(fk_insurance).trigger('change');
    // llenarRamos();
    // setTimeout(function() {
    //     $("#ramo").val(fk_branch);
    // }, 100);

    $("#modalSrcPolicy").modal("hide");
}

// Variable global en tu JS para acumular los archivos de forma segura
let contenedorArchivos = new DataTransfer();

function manejarMultiplesArchivos(input) {
    if (input.files) {
        // Agregamos los nuevos archivos seleccionados al acumulador global
        Array.from(input.files).forEach(file => {
            contenedorArchivos.items.add(file);
        });
        
        // Sincronizamos e imprimimos en pantalla
        renderizarListaDocumentos();
    }
}

function renderizarListaDocumentos() {
    const contenedorVisual = $('#lista_archivos_container');
    contenedorVisual.empty();
    
    // Forzamos al input HTML real a tener exactamente los archivos del acumulador
    document.getElementById('subir_archivo').files = contenedorArchivos.files;

    // Dibujamos las píldoras decorativas para cada archivo
    Array.from(contenedorArchivos.files).forEach((file, index) => {
        // Detectamos si es PDF o Imagen para cambiar el icono dinámicamente
        let icono = file.type === 'application/pdf' ? 'fa-file-pdf text-danger' : 'fa-file-image text-info';
        
        let badgeHTML = `
            <div class="d-inline-flex align-items-center bg-white border p-2 mr-2 mb-2 shadow-sm" style="border-radius: 8px; line-height: 1;">
                <i class="fas ${icono} mr-2" style="font-size: 1rem;"></i>
                <span class="mr-2 font-weight-bold text-muted small" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    ${file.name}
                </span>
                <button type="button" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" 
                        onclick="quitarArchivoDeLista(${index})" 
                        style="width: 20px; height: 20px; min-width: 20px; min-height: 20px; border-radius: 50%; padding: 0 !important; border: none;">
                    <i class="fas fa-times" style="font-size: 9px;"></i>
                </button>
            </div>
        `;
        contenedorVisual.append(badgeHTML);
    });
}

function quitarArchivoDeLista(indexAEliminar) {
    const nuevoDataTransfer = new DataTransfer();
    const archivosActuales = contenedorArchivos.files;
    
    // Reconstruimos el contenedor metiendo todos los archivos excepto el que se eliminó
    for (let i = 0; i < archivosActuales.length; i++) {
        if (i !== indexAEliminar) {
            nuevoDataTransfer.items.add(archivosActuales[i]);
        }
    }
    
    contenedorArchivos = nuevoDataTransfer;
    renderizarListaDocumentos();
    alertify.message('Documento removido');
}

function guardarSolicitud() 
{
    // 1. Validar el formulario con Bootstrap antes de enviar
    var form = $('#validationSolicitud')[0];

    if (modoClienteActual === 'registrado' && idClient == 0) {
        alertify.warning('Debes seleccionar un cliente registrado.');
        $('#client_edit').addClass('is-invalid');
        return;
    } else {
        $('#client_edit').removeClass('is-invalid');
    }
    
    if (!form.checkValidity()) {
        // Si falta algún campo obligatorio, activamos los mensajes visuales de error
        form.classList.add('was-validated');
        alertify.warning('Por favor, completa todos los campos obligatorios (*).');
        return; // Detenemos la ejecución aquí
    }

    // Identificamos el botón de guardado
    var $btn = $('#btnGuardarSolicitud'); 
    var textoOriginalBtn = $btn.html();

    // 2. Bloqueamos el botón y mostramos estado inicial de carga
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...');

    var route = baseUrl + '/storeSolicitud'; 
    var formData = new FormData(form);

    formData.delete('subir_archivo[]');

    if (typeof contenedorArchivos !== 'undefined' && contenedorArchivos.files.length > 0) {
        Array.from(contenedorArchivos.files).forEach(file => {
            formData.append('subir_archivo[]', file); // Aquí viajan al controlador
        });
    }

    formData.append('fk_client', idClient);
    formData.append('fk_nuc', idPolicy);
    formData.append('fk_insurance', idInsurance);
    formData.append('fund_type', fund_type);
    formData.append('fk_agent_key', $("#fk_agent_key").val());
    formData.append('nrclient', $("#client_edit").val());
    formData.append('nrpolicy', $("#policy_edit").val());
    formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    jQuery.ajax({
        url: route,
        type: 'post',
        data: formData,
        dataType: 'json',
        cache: false,        // Requerido para el envío de archivos
        contentType: false,  // Requerido: Dile a jQuery que no configure el Content-Type header
        processData: false,  // Requerido: Dile a jQuery que no convierta los archivos en strings

        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var porcentaje = Math.round((e.loaded / e.total) * 100);
                        if (porcentaje < 100) {
                            $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Subiendo ' + porcentaje + '%...');
                        } else {
                            $btn.html('<i class="fas fa-sync-alt fa-spin mr-2"></i> Registrando en servidor...');
                        }
                    }
                }, false);
            }
            return myXhr;
        },

        success: function(result)
        {
            if(result.status === 'success' || result.success) {
                alertify.success(result.message || 'Solicitud guardada con éxito.');
                
                // Efecto de desvanecimiento: Oculta formulario y muestra pantalla de agradecimiento
                $('#validationSolicitud').fadeOut(300, function() {
                    $('#pantalla_agradecimiento').fadeIn(300);
                    $btn.prop('disabled', false).html(textoOriginalBtn);
                });
            } else {
                alertify.error(result.message || 'Ocurrió un problema al guardar.');
            }
        },
        error: function(result, error, errorTrown)
        {
            // Manejo de errores de validación del servidor (ej. campos vacíos desde Laravel)
            if (result.responseJSON && result.responseJSON.errors) {
                var errores = result.responseJSON.errors;
                // Mostramos el primer error que encuentre el servidor
                var primerError = Object.values(errores)[0][0];
                $btn.prop('disabled', false).html(textoOriginalBtn);
                alertify.error(primerError);
            } else {
                $btn.prop('disabled', false).html(textoOriginalBtn);
                alertify.error('Error del servidor: ' + errorTrown);
            }
        }
    });
}

function reiniciarFormulario() {
    modoClienteActual = 'registrado';
    var form = $('#validationSolicitud')[0];
    $('#col_polizas').show();
    
    // 1. Limpiar inputs nativos del formulario (Selects, textareas, etc)
    form.reset();
    
    // 2. Remover el estado de validación visual de Bootstrap (Bordes rojos/verdes)
    $('#validationSolicitud').removeClass('was-validated');
    
    // 3. Resetear las variables globales de control
    idClient = 0;
    idPolicy = 0;
    
    // 4. Vaciar por completo el acumulador de documentos y su contenedor visual
    contenedorArchivos = new DataTransfer();
    $('#lista_archivos_container').empty();
    
    // 5. Limpiar textos de los campos deshabilitados
    $('#client_edit').val('').prop('disabled', true).attr('placeholder', 'Cliente');
    $('#policy_edit').prop('disabled', true).val('').attr('placeholder', 'Sin póliza seleccionada');
    
    // 6. Volver a bloquear el botón de búsqueda de pólizas por seguridad
    $('#btn_buscar_poliza').prop('disabled', true).css({
        'opacity': '0.6',
        'cursor': 'not-allowed'
    });
    
    // 7. Ocultar todos los sub-bloques condicionales que se abrieron
    // $('#bloque_inversion').hide();
    // $('#bloque_seguro').hide();
    $('#col_servicios_agentes').hide();
    $('#col_servicios').hide();
    
    // 8. Remover atributos de obligación dinámicos para evitar que se queden trabados
    $('#compania, #ramo, #tipo_servicio, #servicios_agentes, #servicios').removeAttr('required');

    $('#ramo').empty();
    $('#ramo').append('<option selected  value="">Seleccione una opción</option>');

    // 9. Intercambiar las pantallas para volver a mostrar el formulario limpio
    $('#pantalla_agradecimiento').fadeOut(300, function() {
        $('#validationSolicitud').fadeIn(300);
    });
}

function llenarRamos(){
    var insurance = $("#compania").val();

    var route = baseUrl + '/getBranches/'+ insurance;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            actualizarSelect(result.branches,"#ramo");
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function actualizarSelect(result, select)
{
    var assignPlan = $(select);

    $(select).empty();
    if(result.length == 0 || result == null) assignPlan.append('<option selected  value="">Seleccione una opción</option>');
    else assignPlan.append('<option selected hidden value="">Seleccione una opción</option>');
    result.forEach( function(valor, indice, array) {
        assignPlan.append("<option value='" + valor.id + "'>" + valor.name + "</option>");
    });
}