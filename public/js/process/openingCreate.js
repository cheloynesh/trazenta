var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + getUrl.pathname.replace('/create', '');

let idClient = 0;
let modoCliente = 'ninguno';

$(document).ready(function () {
    $('#srcClient').DataTable({
        language: {
            "sZeroRecords": "No se encontraron resultados",
            "sSearch": "Buscar:",
            "sInfo": "Mostrando _START_ al _END_ de _TOTAL_ registros"
        },
        scrollX: true
    });

    $('#modalSrcClient').on('shown.bs.modal', function () {
        $('#srcClient').DataTable().columns.adjust();
    });

    // Inicialización visual de pestañas y dependencias
    toggleTipoCliente();
    toggleMovInterno();
    evaluarReinversionLp();
    toggleCuentaBancaria();
    inicializarPreviewArchivos('#formApertura');
});

const MONTOS_MINIMOS = {
    '6':  { 'MXN': 250000, 'USD': 5000 },
    '12': { 'MXN': 750000, 'USD': 15000 },
    '24': { 'MXN': 500000, 'USD': 25000 }
};

let allNucsDestino = [];
let allNucsOrigen = [];

function validarMontoMinimo(mostrarAlerta = true) {
    var plazo = $('#plazo').val();
    var currency = $('#currency').val();
    var rawAmount = $('#amount').val().replace(/[^0-9.]/g, '');
    var monto = parseFloat(rawAmount) || 0;

    var $inputAmount = $('#amount');
    var $hint = $('#hint_monto_minimo');
    var $feedback = $('#error_monto_minimo');

    // Si aún no se selecciona plazo o moneda, limpiar mensajes
    if (!plazo || !currency || !MONTOS_MINIMOS[plazo] || !MONTOS_MINIMOS[plazo][currency]) {
        $hint.text('');
        $inputAmount.removeClass('is-invalid');
        return true;
    }

    var minimo = MONTOS_MINIMOS[plazo][currency];
    var minimoFormateado = '$' + minimo.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ' + currency;

    // Mostrar siempre al usuario cuál es el mínimo aplicable
    $hint.html('Mínimo requerido: <strong>' + minimoFormateado + '</strong>');

    // Si todavía no captura monto, no marcar error hasta que escriba o intente guardar
    if (rawAmount === '') {
        $inputAmount.removeClass('is-invalid');
        return false;
    }

    if (monto < minimo) {
        var msg = 'El monto mínimo para ' + plazo + ' meses en ' + currency + ' es de ' + minimoFormateado + '.';
        $inputAmount.addClass('is-invalid');
        $feedback.text(msg);
        if (mostrarAlerta) {
            alertify.warning(msg);
        }
        return false;
    }

    $inputAmount.removeClass('is-invalid');
    $feedback.text('');
    return true;
}

function nextTab(tabId) {
    $('#' + tabId).tab('show');
}

function buscarclientes() {
    $("#modalSrcClient").modal("show");
}

function obtenerIdCliente(id, name, rfc, email, cellphone) {
    idClient = id;
    modoCliente = 'registrado';

    cargarContratosPorCliente(id);
    actualizarModoContratos();

    $('#client_edit').val(name);
    $('#bloque_nuevo_cliente').slideUp();
    $('#new_name, #new_firstname, #new_birth_date, #new_rfc, #new_curp, #new_domicile').removeAttr('required');

    if (email) $('#kyc_email').val(email);
    if (cellphone) $('#kyc_cellphone').val(cellphone);

    if (rfc && rfc.trim().length === 12) {
        $('#rdMoral').prop('checked', true);
    } else {
        $('#rdFisica').prop('checked', true);
    }
    toggleTipoCliente();

    $("#modalSrcClient").modal("hide");
}

function activarClienteNoRegistrado() {
    idClient = 0;
    modoCliente = 'no_registrado';

    actualizarModoContratos();

    $("#modalSrcClient").modal("hide");
    $('#client_edit').val('CLIENTE NO REGISTRADO (Captura Manual)');
    $('#kyc_email, #kyc_cellphone').val('');

    $('#bloque_nuevo_cliente').slideDown();
    toggleTipoCliente();
    alertify.message('Captura los datos del nuevo cliente para registrarlo.');
}

function toggleTipoCliente() {
    var esMoral = $('#rdMoral').is(':checked');

    if (modoCliente === 'no_registrado') {
        $('#new_name, #new_birth_date, #new_rfc, #new_domicile').attr('required', true);
        if (esMoral) {
            $('.col-new-fisica').hide();
            $('#new_firstname, #new_curp').removeAttr('required');
            $('#lbl_new_name').html('Razón Social <span class="text-danger">*</span>');
            $('#lbl_new_birth').html('Fecha de Constitución <span class="text-danger">*</span>');
        } else {
            $('.col-new-fisica').show();
            $('#new_firstname, #new_curp').attr('required', true);
            $('#lbl_new_name').html('Nombre(s) <span class="text-danger">*</span>');
            $('#lbl_new_birth').html('Fecha de Nacimiento <span class="text-danger">*</span>');
        }
    }

    if (esMoral) {
        $('#bloque_kyc_fisica').hide();
        $('#kyc_nationality, #kyc_residence_country, #marital_status, #kyc_activity').removeAttr('required');

        $('#row_dictamen').slideDown();
        $('#docs_fisica').hide();
        $('#file_domicilio').removeAttr('required');

        toggleDictamenJuridico();
    } else {
        $('#row_dictamen').hide();
        $('#docs_moral_dictamen_si, #docs_moral_dictamen_no').hide();
        $('#file_acta, #file_poderes, #file_folio_mercantil, #file_domicilio_moral_no, #file_rfc_moral, #file_ine_rep').removeAttr('required');

        $('#bloque_kyc_fisica').slideDown();
        $('#kyc_nationality, #kyc_residence_country, #marital_status, #kyc_activity').attr('required', true);

        $('#docs_fisica').slideDown();
        $('#file_domicilio').attr('required', true);
    }
}

function toggleDictamenJuridico() {
    if (!$('#rdMoral').is(':checked')) return;

    var tieneDictamen = $('#dictamenSi').is(':checked');

    if (tieneDictamen) {
        $('#docs_moral_dictamen_no').hide();
        $('#file_acta, #file_poderes, #file_folio_mercantil, #file_domicilio_moral_no, #file_rfc_moral, #file_ine_rep').removeAttr('required');
        $('#docs_moral_dictamen_si').slideDown();
    } else {
        $('#docs_moral_dictamen_si').hide();
        $('#docs_moral_dictamen_no').slideDown();
        $('#file_acta, #file_poderes, #file_folio_mercantil, #file_domicilio_moral_no, #file_rfc_moral, #file_ine_rep').attr('required', true);
    }
}

// Oculta y Muestra Montos e Inputs dependiendo de los Checkboxes
function toggleMovInterno() {
    var checkNew = $('#opDineroNuevo').is(':checked');
    var checkInt = $('#opMovInterno').is(':checked');

    if (checkInt) {
        $('#col_nuc_origen').slideDown(200, function () {
            $('#fk_nuc_origin').select2({ width: '100%', placeholder: 'Selecciona un contrato...', allowClear: true });
        });
        $('#fk_nuc_origin').attr('required', true);
        
        $('#div_monto_interno').slideDown();
        $('#internal_move_amount').attr('required', true);
    } else {
        $('#col_nuc_origen').hide();
        $('#fk_nuc_origin').removeAttr('required').val('').trigger('change');
        
        $('#div_monto_interno').hide();
        $('#internal_move_amount').removeAttr('required').val('');
    }

    if (checkNew) {
        $('#div_monto_nuevo').slideDown();
        $('#new_money_amount').attr('required', true);
    } else {
        $('#div_monto_nuevo').hide();
        $('#new_money_amount').removeAttr('required').val('');
    }
}

function evaluarReinversionLp() {
    var plazo = $('#plazo').val();
    var payForm = $('#fk_payment_form').val();

    if (plazo == '24' && payForm == '4') {
        $('#box_reinversion_lp').slideDown(200, function () {
            $('#fk_nuc_dest').select2({ width: '100%', placeholder: 'Selecciona un contrato...', allowClear: true });
        });
        $('#fk_nuc_dest').attr('required', true);
    } else {
        $('#box_reinversion_lp').hide();
        $('#fk_nuc_dest').removeAttr('required').val('').trigger('change');
    }

    toggleCuentaBancaria();
}

function toggleCuentaBancaria() {
    var payFormVal = $('#fk_payment_form').val();
    var payFormText = ($('#fk_payment_form option:selected').text() || '').toUpperCase();

    // 2 = DEPOSITO A CUENTA
    var esTransferencia = (payFormVal === '2' || payFormText.includes('DEPOSITO') || payFormText.includes('TRANSFERENCIA'));

    if (esTransferencia) {
        $('#bloque_cuenta_bancaria').slideDown();
        $('#bank_account, #bank_clabe, #bank_name, #file_estado_cuenta').attr('required', true);
    } else {
        $('#bloque_cuenta_bancaria').hide();
        $('#bank_account, #bank_clabe, #bank_name, #file_estado_cuenta').removeAttr('required');
    }
}

function toggleBeneficiarios() {
    var qty = $('#beneficiaries_qty').val();

    $('#ben1_name, #ben1_dob, #ben1_pct').removeAttr('required');
    $('#ben2_name, #ben2_dob, #ben2_pct').removeAttr('required');
    $('#ben_extra').removeAttr('required');
    $('.block-ben-1, .block-ben-2, .block-ben-3').hide();

    if (qty === '1') {
        $('.block-ben-1').slideDown();
        $('#ben1_name, #ben1_dob, #ben1_pct').attr('required', true);
    } else if (qty === '2') {
        $('.block-ben-1, .block-ben-2').slideDown();
        $('#ben1_name, #ben1_dob, #ben1_pct').attr('required', true);
        $('#ben2_name, #ben2_dob, #ben2_pct').attr('required', true);
    } else if (qty === '3') {
        $('.block-ben-3').slideDown();
        $('#ben_extra').attr('required', true);
    }
}

// Verifica si la persona es mayor o igual a 18 años a la fecha actual
function esMayorDeEdad(fechaString) {
    if (!fechaString) return false;
    var hoy = new Date();
    var cumple = new Date(fechaString);
    var edad = hoy.getFullYear() - cumple.getFullYear();
    var m = hoy.getMonth() - cumple.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < cumple.getDate())) {
        edad--;
    }
    return edad >= 18;
}

function guardarApertura() {
    var form = $('#formApertura')[0];

    if (modoCliente === 'ninguno') {
        alertify.warning('Debes seleccionar un cliente registrado o activar "Cliente no Registrado".');
        nextTab('step1-tab');
        return;
    }

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        alertify.warning('Faltan campos obligatorios (*). Revisa las pestañas marcadas.');
        return;
    }

    if (!validarMontoMinimo(true)) {
        nextTab('step2-tab');
        $('#amount').focus();
        return;
    }

    // Validación de Edad de Beneficiarios
    var qtyBen = $('#beneficiaries_qty').val();
    if (qtyBen == '1' || qtyBen == '2') {
        var dob1 = $('#ben1_dob').val();
        if (!esMayorDeEdad(dob1)) {
            alertify.error('El Beneficiario 1 debe ser mayor de edad.');
            nextTab('step4-tab');
            $('#ben1_dob').addClass('is-invalid');
            return;
        } else {
            $('#ben1_dob').removeClass('is-invalid');
        }
    }
    if (qtyBen == '2') {
        var dob2 = $('#ben2_dob').val();
        if (!esMayorDeEdad(dob2)) {
            alertify.error('El Beneficiario 2 debe ser mayor de edad.');
            nextTab('step4-tab');
            $('#ben2_dob').addClass('is-invalid');
            return;
        } else {
            $('#ben2_dob').removeClass('is-invalid');
        }
    }

    // Validación de Orígenes del Recurso vs. Monto Total
    var totalAmount = parseFloat($('#amount').val().replace(/[^0-9.]/g, '') || 0);
    var isNew = $('#opDineroNuevo').is(':checked');
    var isInt = $('#opMovInterno').is(':checked');

    if (!isNew && !isInt) {
        alertify.error('Debes seleccionar al menos un origen del recurso.');
        nextTab('step3-tab');
        return;
    }

    var valNew = isNew ? parseFloat($('#new_money_amount').val().replace(/[^0-9.]/g, '') || 0) : 0;
    var valInt = isInt ? parseFloat($('#internal_move_amount').val().replace(/[^0-9.]/g, '') || 0) : 0;

    if (Math.abs((valNew + valInt) - totalAmount) > 0.05) {
        alertify.error('La suma de Dinero Nuevo ($' + valNew.toLocaleString('en-US') + ') y Mov. Interno ($' + valInt.toLocaleString('en-US') + ') no coincide con el Monto Total ($' + totalAmount.toLocaleString('en-US') + ').');
        nextTab('step3-tab');
        return;
    }

    var $btn = $('#btnGuardarApertura');
    var txtBtn = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...');

    var formData = new FormData(form);
    formData.append('fk_client', idClient);
    formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    jQuery.ajax({
        url: baseUrl + '/storeRequest',
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function (result) {
            if (result.status === 'success') {
                alertify.success(result.message);
                setTimeout(function () {
                    window.location.href = baseUrl;
                }, 1800);
            } else {
                $btn.prop('disabled', false).html(txtBtn);
                alertify.error(result.message);
            }
        },
        error: function (xhr, status, error) {
            $btn.prop('disabled', false).html(txtBtn);
            
            // Si Laravel arrojó un error 422 de validación de montos mínimos en Controller
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alertify.error(xhr.responseJSON.message);
            } else {
                alertify.error('Error en el servidor: ' + error);
            }
        }
    });
}

function cargarContratosPorCliente(clientId) {
    jQuery.ajax({
        url: baseUrl + '/getClientContracts/' + clientId,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            var $dest = $('#fk_nuc_dest');
            $dest.empty().append('<option value="">Selecciona un contrato del cliente...</option>');
            if (res.nucsDestino && res.nucsDestino.length > 0) {
                res.nucsDestino.forEach(function (n) {
                    $dest.append($('<option></option>').val(n.id).text(n.label));
                });
            } else {
                $dest.append('<option value="" disabled>Sin contratos CP/MP activos para este cliente</option>');
            }

            var $orig = $('#fk_nuc_origin');
            $orig.empty().append('<option value="">Selecciona un contrato del cliente...</option>');
            if (res.nucsOrigen && res.nucsOrigen.length > 0) {
                res.nucsOrigen.forEach(function (n) {
                    $orig.append($('<option></option>').val(n.composite_id).text(n.label));
                });
            } else {
                $orig.append('<option value="" disabled>Sin contratos activos para este cliente</option>');
            }
        }
    });
}

function actualizarModoContratos() {
    var esNoRegistrado = (modoCliente === 'no_registrado');
    var otroTitular = $('#chkOtroTitular').is(':checked');

    // Mostrar check "Otro titular" solo cuando es cliente no registrado
    if (esNoRegistrado) {
        $('#wrap_chk_otro_titular').show();
    } else {
        $('#wrap_chk_otro_titular').hide();
        $('#chkOtroTitular').prop('checked', false);
    }

    // 1. NUC Destino Reinversión LP
    if (esNoRegistrado) {
        $('#wrap_nuc_dest_select').hide();
        $('#fk_nuc_dest').removeAttr('required').val('').trigger('change');
        $('#wrap_nuc_dest_text').show();
        if ($('#box_reinversion_lp').is(':visible')) $('#nuc_dest_text').attr('required', true);
    } else {
        $('#wrap_nuc_dest_text').hide();
        $('#nuc_dest_text').removeAttr('required').val('');
        $('#wrap_nuc_dest_select').show();
        if ($('#box_reinversion_lp').is(':visible')) $('#fk_nuc_dest').attr('required', true);
    }

    // 2. Contrato de Origen (Movimiento Interno)
    if (esNoRegistrado && !otroTitular) {
        $('#wrap_nuc_origin_select').hide();
        $('#fk_nuc_origin').removeAttr('required').val('').trigger('change');
        $('#wrap_nuc_origin_text').show();
        if ($('#col_nuc_origen').is(':visible')) $('#nuc_origin_text').attr('required', true);
    } else {
        $('#wrap_nuc_origin_text').hide();
        $('#nuc_origin_text').removeAttr('required').val('');
        $('#wrap_nuc_origin_select').show();
        if ($('#col_nuc_origen').is(':visible')) $('#fk_nuc_origin').attr('required', true);
    }
}