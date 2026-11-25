@extends('home')
<head>
    <title>Nueva Apertura de Fondos | Trazenta</title>
</head>
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h2 style="font-weight: 700; color: #334155; margin: 0; font-size: 1.5rem;">Formulario de Apertura de Fondos</h2>
                <p class="text-muted small mb-0">Completa la información KYC, financiera y documental para aperturar la inversión.</p>
            </div>
            <a href="{{ url('process/opening/opening') }}" class="btn btn-light border shadow-sm font-weight-bold mt-2 mt-md-0">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Aperturas
            </a>
        </div>

        <div class="card shadow-sm" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="card-body p-0">
                <div class="row m-0">
                    {{-- Navegación Lateral (Wizard) --}}
                    <div class="col-md-3 p-4 bg-light" style="border-right: 1px solid #e2e8f0;">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active font-weight-bold mb-2" id="step1-tab" data-toggle="pill" href="#step1" role="tab">
                                <i class="fas fa-user-circle mr-2"></i> 1. Cliente
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="step2-tab" data-toggle="pill" href="#step2" role="tab">
                                <i class="fas fa-file mr-2"></i> 2. Documentos
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="step3-tab" data-toggle="pill" href="#step3" role="tab">
                                <i class="fas fa-chart-line mr-2"></i> 3. Datos Inversión
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="step4-tab" data-toggle="pill" href="#step4" role="tab">
                                <i class="fas fa-users mr-2"></i> 4. Beneficiarios
                            </a>
                        </div>
                    </div>

                    {{-- Contenido del Formulario --}}
                    <div class="col-md-9 p-4">
                        <form id="formApertura" class="needs-validation" novalidate enctype="multipart/form-data">
                            <div class="tab-content" id="v-pills-tabContent">

                                {{-- ========================================================= --}}
                                {{-- PASO 1: CLIENTE Y KYC                                     --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-4">Información del Cliente</h5>

                                    {{-- Selector de Cliente Registrado / Botón No Registrado --}}
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small text-muted text-uppercase">Cliente <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" id="client_edit" class="form-control font-weight-bold" disabled placeholder="Selecciona un cliente o registra uno nuevo">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary font-weight-bold px-3" onclick="buscarclientes()">
                                                    <i class="fas fa-search mr-1"></i> Buscar
                                                </button>
                                                <button type="button" class="btn btn-warning font-weight-bold px-3" onclick="activarClienteNoRegistrado()">
                                                    <i class="fas fa-user-plus mr-1"></i> Cliente no Registrado
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tipo de Cliente --}}
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small text-muted text-uppercase d-block">Tipo de Cliente <span class="text-danger">*</span></label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="rdFisica" name="client_type" class="custom-control-input" value="0" checked onchange="toggleTipoCliente()">
                                            <label class="custom-control-label font-weight-bold" for="rdFisica">Persona Física</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="rdMoral" name="client_type" class="custom-control-input" value="1" onchange="toggleTipoCliente()">
                                            <label class="custom-control-label font-weight-bold" for="rdMoral">Persona Moral</label>
                                        </div>
                                    </div>

                                    {{-- BLOQUE CLIENTE NO REGISTRADO (ALTA EN CLIENT) --}}
                                    <div id="bloque_nuevo_cliente" class="p-3 mb-4 rounded border" style="background-color: #f8fafc; display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-id-card mr-1 text-warning"></i> Datos de Registro del Nuevo Cliente</h6>
                                            <span class="badge badge-warning">Nuevo en Sistema</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="small font-weight-bold" id="lbl_new_name">Nombre(s) <span class="text-danger">*</span></label>
                                                <input type="text" name="new_name" id="new_name" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-3 col-new-fisica">
                                                <label class="small font-weight-bold">Apellido Paterno <span class="text-danger">*</span></label>
                                                <input type="text" name="new_firstname" id="new_firstname" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-3 col-new-fisica">
                                                <label class="small font-weight-bold">Apellido Materno</label>
                                                <input type="text" name="new_lastname" id="new_lastname" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="small font-weight-bold" id="lbl_new_birth">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                                <input type="date" name="new_birth_date" id="new_birth_date" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="small font-weight-bold">RFC <span class="text-danger">*</span></label>
                                                <input type="text" name="new_rfc" id="new_rfc" class="form-control text-uppercase" maxlength="13">
                                            </div>
                                            <div class="col-md-4 mb-3 col-new-fisica">
                                                <label class="small font-weight-bold">CURP <span class="text-danger">*</span></label>
                                                <input type="text" name="new_curp" id="new_curp" class="form-control text-uppercase" maxlength="18">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-0">
                                                <label class="small font-weight-bold">Domicilio Completo <span class="text-danger">*</span></label>
                                                <input type="text" name="new_domicile" id="new_domicile" class="form-control" placeholder="Calle, número, colonia, C.P., ciudad y estado">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DICTAMEN JURÍDICO (SOLO MORAL) --}}
                                    <div class="row mb-3" id="row_dictamen" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="p-3 bg-light rounded border">
                                                <label class="font-weight-bold small d-block mb-2">Dictamen jurídico no mayor a un año <span class="text-danger">*</span></label>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="dictamenSi" name="legal_dictum" class="custom-control-input" value="1" checked onchange="toggleDictamenJuridico()">
                                                    <label class="custom-control-label font-weight-bold" for="dictamenSi">Sí</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="dictamenNo" name="legal_dictum" class="custom-control-input" value="0" onchange="toggleDictamenJuridico()">
                                                    <label class="custom-control-label font-weight-bold" for="dictamenNo">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DATOS DE CONTACTO (FÍSICA Y MORAL) --}}
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Correo Electrónico <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="kyc_email" class="form-control" required placeholder="correo@ejemplo.com">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Celular <span class="text-danger">*</span></label>
                                            <input type="text" name="cellphone" id="kyc_cellphone" class="form-control" required placeholder="+52 ...">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Teléfono de casa</label>
                                            <input type="text" name="home_phone" id="kyc_home_phone" class="form-control" placeholder="+52 ...">
                                        </div>
                                    </div>

                                    {{-- CAMPOS KYC DE PERSONA FÍSICA --}}
                                    <div id="bloque_kyc_fisica">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="font-weight-bold small">Nacionalidad <span class="text-danger">*</span></label>
                                                <input type="text" name="nationality" id="kyc_nationality" class="form-control" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="font-weight-bold small">País de residencia <span class="text-danger">*</span></label>
                                                <input type="text" name="residence_country" id="kyc_residence_country" class="form-control" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="font-weight-bold small">Estado Civil <span class="text-danger">*</span></label>
                                                <select name="marital_status" id="marital_status" class="form-select form-control" required>
                                                    <option hidden selected value="">Selecciona una opción</option>
                                                    <option value="Soltero(a)">Soltero(a)</option>
                                                    <option value="Casado(a)">Casado(a)</option>
                                                    <option value="Divorciado(a)">Divorciado(a)</option>
                                                    <option value="Viudo(a)">Viudo(a)</option>
                                                    <option value="Unión Libre">Unión Libre</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Actividad que desempeña el cliente <span class="text-danger">*</span></label>
                                                <input type="text" name="activity" id="kyc_activity" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Nombre de la empresa</label>
                                                <input type="text" name="company_name" id="kyc_company_name" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right border-top pt-3 mt-3">
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="nextTab('step2-tab')">
                                            Siguiente <i class="fas fa-arrow-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 2: DOCUMENTOS                                        --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-4">Documentación Legal y Fiscal</h5>

                                    {{-- DOCUMENTOS COMPARTIDOS --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold small">Estado de Cuenta Bancario (Opcional)</label>
                                            <input type="file" name="file_estado_cuenta_doc" id="file_estado_cuenta_doc" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                        </div>
                                    </div>

                                    {{-- DOCUMENTOS PERSONA FÍSICA --}}
                                    <div id="docs_fisica">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Identificación Oficial (INE)</label>
                                                <input type="file" name="file_ine" id="file_ine" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Comprobante de domicilio <span class="text-danger">*</span></label>
                                                <input type="file" name="file_domicilio" id="file_domicilio" class="form-control-file border p-2 rounded bg-light" required accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Constancia de Situación Fiscal</label>
                                                <input type="file" name="file_csf" id="file_csf" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DOCUMENTOS PERSONA MORAL - DICTAMEN = SÍ --}}
                                    <div id="docs_moral_dictamen_si" style="display: none;">
                                        <div class="alert alert-info small">
                                            <i class="fas fa-info-circle mr-1"></i> Al contar con dictamen jurídico vigente (menor a 1 año), únicamente se requiere el comprobante de domicilio y constancia fiscal.
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Comprobante de domicilio</label>
                                                <input type="file" name="file_domicilio_moral" id="file_domicilio_moral_si" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Constancia de Situación Fiscal</label>
                                                <input type="file" name="file_csf" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DOCUMENTOS PERSONA MORAL - DICTAMEN = NO --}}
                                    <div id="docs_moral_dictamen_no" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Acta constitutiva <span class="text-danger">*</span></label>
                                                <input type="file" name="file_acta" id="file_acta" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Poderes actualizados (Actas asamblea) <span class="text-danger">*</span></label>
                                                <input type="file" name="file_poderes" id="file_poderes" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Folio mercantil <span class="text-danger">*</span></label>
                                                <input type="file" name="file_folio_mercantil" id="file_folio_mercantil" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Comprobante de domicilio <span class="text-danger">*</span></label>
                                                <input type="file" name="file_domicilio_moral" id="file_domicilio_moral_no" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">Alta de hacienda RFC <span class="text-danger">*</span></label>
                                                <input type="file" name="file_rfc_moral" id="file_rfc_moral" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold small">INE Rep. legal y apoderados <span class="text-danger">*</span></label>
                                                <input type="file" name="file_ine_rep[]" id="file_ine_rep" class="form-control-file border p-2 rounded bg-light" multiple accept=".pdf,image/*">
                                                <small class="text-muted">Puedes seleccionar varios archivos.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="nextTab('step1-tab')"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="nextTab('step3-tab')">Siguiente <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 3: DATOS INVERSIÓN Y LIQUIDACIÓN                     --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="step3" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-4">Estructura de la Inversión</h5>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Tipo de moneda <span class="text-danger">*</span></label>
                                            <select name="currency" id="currency" class="form-select form-control" onchange="validarMontoMinimo()" required>
                                                <option hidden selected value="">Selecciona</option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Plazo solicitado <span class="text-danger">*</span></label>
                                            <select name="plazo" id="plazo" class="form-select form-control" onchange="evaluarReinversionLp(); validarMontoMinimo();" required>
                                                <option hidden selected value="">Selecciona</option>
                                                <option value="6">6 meses (CP)</option>
                                                <option value="12">12 meses (MP)</option>
                                                <option value="24">24 meses (LP)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Monto total <span class="text-danger">*</span></label>
                                            <input type="text" name="amount" id="amount" class="form-control font-weight-bold" data-type="currency" onchange="validarMontoMinimo()" required placeholder="$0.00">
                                            <small id="hint_monto_minimo" class="text-muted d-block mt-1"></small>
                                            <div id="error_monto_minimo" class="invalid-feedback font-weight-bold"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold small">Forma de pago del premio <span class="text-danger">*</span></label>
                                            <select name="fk_payment_form" id="fk_payment_form" class="form-select form-control" onchange="evaluarReinversionLp()" required>
                                                <option hidden selected value="">Selecciona</option>
                                                @foreach($paymentForms as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold small">Ficha de depósito (Comprobante) <span class="text-danger">*</span></label>
                                            <select name="fk_charge" id="fk_charge" class="form-select form-control" required>
                                                <option hidden selected value="">Selecciona</option>
                                                @foreach($charges as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- BLOQUE CONDICIONAL: REINVERSIÓN DE LP A CP/MP --}}
                                    <div class="row mb-3 p-3 rounded border" id="box_reinversion_lp" style="background-color: #eff6ff; border-color: #bfdbfe !important; display: none;">
                                        <div class="col-md-12">
                                            <label class="font-weight-bold small text-primary">
                                                <i class="fas fa-sync-alt mr-1"></i> NUC de Destino para Reinversión (Solo Corto / Mediano Plazo) <span class="text-danger">*</span>
                                            </label>
                                            <div id="wrap_nuc_dest_select">
                                                <select name="fk_nuc_dest" id="fk_nuc_dest" class="form-control select2-buscable" style="width: 100%;">
                                                    <option value="">Selecciona un contrato del cliente...</option>
                                                </select>
                                            </div>
                                            <div id="wrap_nuc_dest_text" style="display: none;">
                                                <input type="text" name="nuc_dest_text" id="nuc_dest_text" class="form-control" placeholder="Escribe el NUC de destino o 'En trámite' si se apertura junto con este">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ORIGEN DEL RECURSO: DINERO NUEVO Y/O MOVIMIENTO INTERNO (CHECKBOXES) --}}
                                    <div class="row mb-3 p-3 bg-light rounded border mx-0">
                                        <div class="col-md-12 mb-3">
                                            <label class="font-weight-bold small text-dark d-block">Origen del Recurso (Puedes seleccionar ambos) <span class="text-danger">*</span></label>
                                            <div class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" id="opDineroNuevo" name="is_new_money" class="custom-control-input" value="1" checked onchange="toggleMovInterno()">
                                                <label class="custom-control-label font-weight-bold" for="opDineroNuevo">Dinero Nuevo</label>
                                            </div>
                                            <div class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" id="opMovInterno" name="is_internal_move" class="custom-control-input" value="1" onchange="toggleMovInterno()">
                                                <label class="custom-control-label font-weight-bold" for="opMovInterno">Movimiento Interno</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-2" id="div_monto_nuevo">
                                            <label class="font-weight-bold small">Monto Dinero Nuevo <span class="text-danger">*</span></label>
                                            <input type="text" name="new_money_amount" id="new_money_amount" class="form-control" data-type="currency" placeholder="$0.00">
                                        </div>
                                        
                                        <div class="col-md-4 mb-2" id="col_nuc_origen" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="font-weight-bold small mb-1">Contrato de Origen <span class="text-danger">*</span></label>
                                                <div class="custom-control custom-checkbox small" id="wrap_chk_otro_titular" style="display: none;">
                                                    <input type="checkbox" class="custom-control-input" id="chkOtroTitular" onchange="actualizarModoContratos()">
                                                    <label class="custom-control-label text-primary font-weight-bold" for="chkOtroTitular">Otro titular</label>
                                                </div>
                                            </div>
                                            <div id="wrap_nuc_origin_select">
                                                <select name="fk_nuc_origin" id="fk_nuc_origin" class="form-control select2-buscable" style="width: 100%;">
                                                    <option value="">Selecciona un contrato del cliente...</option>
                                                </select>
                                            </div>
                                            <div id="wrap_nuc_origin_text" style="display: none;">
                                                <input type="text" name="nuc_origin_text" id="nuc_origin_text" class="form-control" placeholder="Ej. NUC-0000 o En trámite">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-2" id="div_monto_interno" style="display: none;">
                                            <label class="font-weight-bold small">Monto Mov. Interno <span class="text-danger">*</span></label>
                                            <input type="text" name="internal_move_amount" id="internal_move_amount" class="form-control" data-type="currency" placeholder="$0.00">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold small">Comprobante de pago (Ficha de depósito) <span class="text-danger">*</span></label>
                                            <input type="file" name="file_comprobante_pago" id="file_comprobante_pago" class="form-control-file border p-2 rounded bg-light" required accept=".pdf,image/*">
                                        </div>
                                    </div>

                                    {{-- BLOQUE CONDICIONAL: CUENTA BANCARIA (SOLO TRANSFERENCIA / DEPÓSITO A CUENTA) --}}
                                    <div id="bloque_cuenta_bancaria" style="display: none;">
                                        <hr>
                                        <h6 class="text-muted text-uppercase small font-weight-bold mt-4">Cuenta de Depósito / Retiros</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-4 mb-2">
                                                <label class="small font-weight-bold">Número de cuenta <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_account" id="bank_account" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="small font-weight-bold">CLABE <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_clabe" id="bank_clabe" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="small font-weight-bold">Banco <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_name" id="bank_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-3 bg-light p-3 rounded mx-0">
                                            <div class="col-md-12 mb-2">
                                                <label class="small text-muted font-weight-bold">Si el banco es extranjero, llena los campos correspondientes:</label>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="small font-weight-bold">ABA (Estados Unidos)</label>
                                                <input type="text" name="bank_aba" id="bank_aba" class="form-control">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="small font-weight-bold">SWIFT (Europa / Internacional)</label>
                                                <input type="text" name="bank_swift" id="bank_swift" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label class="font-weight-bold small">Estado de cuenta bancario <span class="text-danger">*</span></label>
                                                <input type="file" name="file_estado_cuenta" id="file_estado_cuenta" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="nextTab('step2-tab')"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="nextTab('step4-tab')">Siguiente <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 4: BENEFICIARIOS                                     --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="step4" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-4">Designación de Beneficiarios</h5>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <label class="small font-weight-bold">¿Cuántos beneficiarios son? <span class="text-danger">*</span></label>
                                            <select name="beneficiaries_qty" id="beneficiaries_qty" class="form-select form-control" onchange="toggleBeneficiarios()" required>
                                                <option value="1" selected>1</option>
                                                <option value="2">2</option>
                                                <option value="3">3 o más</option>
                                                <option value="0">Soy persona moral, no aplica</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Beneficiario 1 --}}
                                    <div class="row mb-2 block-ben-1">
                                        <div class="col-md-5 mb-2">
                                            <label class="small font-weight-bold">Nombre completo del beneficiario 1 <span class="text-danger">*</span></label>
                                            <input type="text" name="beneficiary_1_name" id="ben1_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="small font-weight-bold">Fecha de nacimiento <span class="text-danger">*</span></label>
                                            <input type="date" name="beneficiary_1_dob" id="ben1_dob" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small font-weight-bold">Porcentaje (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="beneficiary_1_pct" id="ben1_pct" class="form-control" required max="100" min="1">
                                        </div>
                                    </div>

                                    {{-- Beneficiario 2 --}}
                                    <div class="row mb-2 block-ben-2" style="display:none;">
                                        <div class="col-md-5 mb-2">
                                            <label class="small font-weight-bold">Nombre completo del beneficiario 2 <span class="text-danger">*</span></label>
                                            <input type="text" name="beneficiary_2_name" id="ben2_name" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="small font-weight-bold">Fecha de nacimiento <span class="text-danger">*</span></label>
                                            <input type="date" name="beneficiary_2_dob" id="ben2_dob" class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small font-weight-bold">Porcentaje (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="beneficiary_2_pct" id="ben2_pct" class="form-control" max="100" min="1">
                                        </div>
                                    </div>

                                    {{-- 3 o más Beneficiarios (Oculta 1 y 2) --}}
                                    <div class="row mb-3 block-ben-3" style="display:none;">
                                        <div class="col-md-12">
                                            <label class="small font-weight-bold text-primary">Datos de los beneficiarios <span class="text-danger">*</span></label>
                                            <small class="d-block text-muted mb-1">
                                                Escribe a continuación por cada beneficiario: 1. Nombre completo (tal cual INE), 2. Fecha de nacimiento (mayor de edad), 3. Porcentaje asignado.
                                            </small>
                                            <textarea name="beneficiaries_extra" id="ben_extra" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-4 mt-4">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="nextTab('step3-tab')"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" id="btnGuardarApertura" onclick="guardarApertura()" class="btn btn-success shadow-sm font-weight-bold px-5" style="border-radius: 8px;">
                                            <i class="fas fa-save mr-2"></i> Finalizar y Enviar
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL BUSCAR CLIENTE --}}
    <div id="modalSrcClient" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                    <h4 class="modal-title" style="font-weight: 700; color: #334155;">Buscar Cliente Registrado</h4>
                    <button type="button" class="close" onclick="$('#modalSrcClient').modal('hide')" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" style="background-color: #f1f5f9;">
                    <div class="table-responsive bg-white p-3 rounded shadow-sm">
                        <table class="table table-hover text-center" style="width:100%" id="srcClient">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small text-uppercase font-weight-bold">Nombre / Razón Social</th>
                                    <th class="small text-uppercase font-weight-bold">RFC</th>
                                    <th class="small text-uppercase font-weight-bold">Correo</th>
                                    <th class="small text-uppercase font-weight-bold">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td class="align-middle font-weight-bold text-dark">{{$client->name}} {{$client->firstname}} {{$client->lastname}}</td>
                                        <td class="align-middle text-muted" style="font-family: monospace;">{{$client->rfc}}</td>
                                        <td class="align-middle text-muted">{{$client->email}}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-primary btn-sm px-3"
                                                onclick="obtenerIdCliente({{$client->id}}, '{{ addslashes(trim($client->name.' '.$client->firstname.' '.$client->lastname)) }}', '{{ $client->rfc }}', '{{ $client->email }}', '{{ $client->cellphone }}')">
                                                Seleccionar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-warning font-weight-bold" onclick="activarClienteNoRegistrado()">
                        <i class="fas fa-user-plus mr-1"></i> Cliente no Registrado
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{URL::asset('js/currencyformat.js')}}"></script>
@endsection

@push('head')
    <script src="{{URL::asset('js/general.js')}}" ></script>
    <script src="{{URL::asset('js/process/openingCreate.js')}}"></script>
    <style>
        @media (max-width: 767.98px) {
            .btn {
                white-space: nowrap;
            }

            .input-group {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .input-group > .form-control,
            .input-group > .input-group-prepend,
            .input-group > .input-group-append,
            .input-group > .btn {
                flex: 1 1 100%;
                width: 100%;
                border-radius: 6px !important;
            }

            .input-group-prepend .btn,
            .input-group-append .btn,
            .input-group > .btn {
                width: 100%;
                white-space: normal;
            }
        }

        .nav-pills .nav-link.active { background-color: #064e3b !important; color: white !important; border-radius: 8px; }
        .nav-pills .nav-link { color: #64748b; border-radius: 8px; }

        /* Homologar Select2 al 100% del ancho y centrar su flecha interna */
        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            box-sizing: border-box !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            padding-right: 32px !important;
            color: #334155 !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            width: 28px !important;
            position: absolute !important;
            top: 1px !important;
            right: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 22px !important;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('.select2-buscable').select2({
                width: '100%',
                placeholder: 'Selecciona un contrato...',
                allowClear: true
            });
        });
    </script>
@endpush