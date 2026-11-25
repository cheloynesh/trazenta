var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + getUrl.pathname.replace('/create', '');
let renClientId = 0;

$(document).ready(function () {
    $('#srcClientRen').DataTable({
        language: { "sZeroRecords": "No se encontraron resultados", "sSearch": "Buscar:" }
    });

    $('#ren_contract_select').select2({
        width: '100%',
        placeholder: 'Selecciona la obligación por vencer...',
        allowClear: true
    });
    inicializarPreviewArchivos('#formRenovacion');
});

function irPasoRenovacion(step) {
    $('#rstep' + step + '-tab').tab('show');
}

function updateProgress(step) {
    var pct = (step / 5) * 100;
    $('#renProgressLabel').text(step + '/5');
    $('#renProgressBar').css('width', pct + '%');
}

function seleccionarClienteRen(id, name, rfc, email, cellphone) {
    renClientId = id;
    $('#ren_client_name').val(name);
    $('#ren_rfc').val(rfc || '');
    $('#lbl_registered_email').text(email ? '(' + email + ')' : '');
    $('#lbl_registered_phone').text(cellphone ? '(' + cellphone + ')' : '');

    // Detectar si RFC es Persona Moral (12 caracteres)
    if (rfc && rfc.trim().length === 12) {
        $('#ren_beneficiaries_qty').val('0').trigger('change');
    }

    $('#modalSrcClientRen').modal('hide');

    // Cargar contratos exclusivos de este cliente
    jQuery.ajax({
        url: baseUrl + '/getClientContracts/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            var $sel = $('#ren_contract_select');
            $sel.empty().append('<option value="">Selecciona una obligación...</option>');

            if (res.contracts && res.contracts.length > 0) {
                res.contracts.forEach(function (c) {
                    $sel.append(
                        $('<option></option>')
                            .val(c.composite_id)
                            .text(c.label)
                            .attr('data-amount', c.amount)
                    );
                });
            } else {
                $sel.append('<option value="" disabled>Este cliente no tiene contratos activos</option>');
            }
            $sel.trigger('change');

            var $dest = $('#ren_fk_nuc_dest');
            $dest.empty().append('<option value="">Selecciona el contrato destino...</option>');
            if (res.nucsDestino && res.nucsDestino.length > 0) {
                res.nucsDestino.forEach(function (n) {
                    $dest.append($('<option></option>').val(n.id).text(n.label));
                });
            } else {
                $dest.append('<option value="" disabled>Sin contratos CP/MP activos</option>');
            }
            $dest.trigger('change');
        }
    });
}

function onSelectContratoRen() {
    var $opt = $('#ren_contract_select option:selected');
    $('#ren_contract_label').val($opt.val() ? $opt.text() : '');

    var amt = parseFloat($opt.attr('data-amount') || 0);
    if (amt > 0) {
        $('#ren_amount').val('$' + amt.toLocaleString('en-US', { minimumFractionDigits: 2 }));
    }
}

function toggleContactoRen() {
    if ($('#chkMismoCorreo').is(':checked')) {
        $('#div_nuevo_correo').hide();
        $('#ren_client_email').removeAttr('required').val('');
    } else {
        $('#div_nuevo_correo').slideDown();
        $('#ren_client_email').attr('required', true);
    }

    if ($('#chkMismoTelefono').is(':checked')) {
        $('#div_nuevo_telefono').hide();
        $('#ren_client_phone').removeAttr('required').val('');
    } else {
        $('#div_nuevo_telefono').slideDown();
        $('#ren_client_phone').attr('required', true);
    }
}

function toggleFormaPagoRen() {
    var esBanco = $('#payBanco').is(':checked');
    var esFondo = $('#payCaporta').is(':checked');

    if (esBanco) {
        $('#div_estado_cuenta_ren').slideDown();
        $('#ren_file_estado_cuenta').attr('required', true);
        
        $('#div_reinversion_ren').hide();
        $('#ren_fk_nuc_dest').removeAttr('required').val('').trigger('change');
    } else if (esFondo) {
        $('#div_estado_cuenta_ren').hide();
        $('#ren_file_estado_cuenta').removeAttr('required').val('');
        
        $('#div_reinversion_ren').slideDown(200, function() {
            $('#ren_fk_nuc_dest').select2({ width: '100%', placeholder: 'Selecciona el contrato destino...', allowClear: true });
        });
        $('#ren_fk_nuc_dest').attr('required', true);
    }
}

function toggleBeneficiariosRen() {
    var qty = $('#ren_beneficiaries_qty').val();

    $('#rben1_name, #rben1_dob, #rben1_pct').removeAttr('required');
    $('#rben2_name, #rben2_dob, #rben2_pct').removeAttr('required');
    $('#rben_extra').removeAttr('required');
    $('.rblock-ben-1, .rblock-ben-2, .rblock-ben-3').hide();

    if (qty === '1') {
        $('.rblock-ben-1').slideDown();
        $('#rben1_name, #rben1_dob, #rben1_pct').attr('required', true);
    } else if (qty === '2') {
        $('.rblock-ben-1, .rblock-ben-2').slideDown();
        $('#rben1_name, #rben1_dob, #rben1_pct').attr('required', true);
        $('#rben2_name, #rben2_dob, #rben2_pct').attr('required', true);
    } else if (qty === '3') {
        $('.rblock-ben-3').slideDown();
        $('#rben_extra').attr('required', true);
    }
}

function esMayorDeEdadRen(fechaString) {
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

function guardarRenovacion() {
    var form = $('#formRenovacion')[0];

    if (renClientId <= 0) {
        alertify.warning('Debes seleccionar un cliente en el Paso 1.');
        irPasoRenovacion(1);
        return;
    }

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        alertify.warning('Faltan campos obligatorios (*). Revisa los pasos del formulario.');
        return;
    }

    // Validar mayoría de edad de beneficiarios (Paso 5)
    var qtyBen = $('#ren_beneficiaries_qty').val();
    if (qtyBen == '1' || qtyBen == '2') {
        if (!esMayorDeEdadRen($('#rben1_dob').val())) {
            alertify.error('El Beneficiario 1 debe ser mayor de edad (18 años o más).');
            irPasoRenovacion(5);
            $('#rben1_dob').addClass('is-invalid');
            return;
        }
        $('#rben1_dob').removeClass('is-invalid');
    }
    if (qtyBen == '2') {
        if (!esMayorDeEdadRen($('#rben2_dob').val())) {
            alertify.error('El Beneficiario 2 debe ser mayor de edad (18 años o más).');
            irPasoRenovacion(5);
            $('#rben2_dob').addClass('is-invalid');
            return;
        }
        $('#rben2_dob').removeClass('is-invalid');
    }

    var $btn = $('#btnGuardarRenovacion');
    var txtBtn = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Enviando...');

    var formData = new FormData(form);
    formData.append('fk_client', renClientId);
    formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    jQuery.ajax({
        url: baseUrl + '/storeRequest',
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.status === 'success') {
                alertify.success(res.message);
                setTimeout(function () {
                    window.location.href = window.location.protocol + "//" + window.location.host + "/process/opening/opening";
                }, 1500);
            } else {
                $btn.prop('disabled', false).html(txtBtn);
                alertify.error(res.message);
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(txtBtn);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alertify.error(xhr.responseJSON.message);
            } else {
                alertify.error('Error al guardar la solicitud de renovación.');
            }
        }
    });
}