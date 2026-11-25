@extends('home')
<head>
    <title>Formulario de Renovaciones | Trazenta</title>
</head>
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h2 style="font-weight: 700; color: #334155; margin: 0; font-size: 1.5rem;">Formulario de Renovaciones</h2>
                <p class="text-muted small mb-0">Completa la información del cliente, el contrato a renovar, documentación y beneficiarios.</p>
            </div>
            <a href="{{ url('process/opening/opening') }}" class="btn btn-light border shadow-sm font-weight-bold mt-2 mt-md-0">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Aperturas
            </a>
        </div>

        <div class="card shadow-sm" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="card-body p-0">
                <div class="row m-0">
                    {{-- Navegación Lateral (5 Pasos) --}}
                    {{-- Navegación Lateral (5 Pasos) --}}
                    <div class="col-md-3 p-4 bg-light" style="border-right: 1px solid #e2e8f0;">
                        <div class="nav flex-column nav-pills" id="ren-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active font-weight-bold mb-2" id="rstep1-tab" data-toggle="pill" href="#rstep1" role="tab">
                                <i class="fas fa-user-circle mr-2"></i> 1. Cliente
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="rstep2-tab" data-toggle="pill" href="#rstep2" role="tab">
                                <i class="fas fa-file mr-2"></i> 2. Obligación a Vencer
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="rstep3-tab" data-toggle="pill" href="#rstep3" role="tab">
                                <i class="fas fa-hand-holding-usd mr-2"></i> 3. Pago del Premio
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="rstep4-tab" data-toggle="pill" href="#rstep4" role="tab">
                                <i class="fas fa-file-alt mr-2"></i> 4. CSF Actualizada
                            </a>
                            <a class="nav-link font-weight-bold mb-2" id="rstep5-tab" data-toggle="pill" href="#rstep5" role="tab">
                                <i class="fas fa-users mr-2"></i> 5. Beneficiarios
                            </a>
                        </div>
                    </div>

                    {{-- Contenido del Formulario --}}
                    <div class="col-md-9 p-4">
                        <form id="formRenovacion" class="needs-validation" novalidate enctype="multipart/form-data">
                            <div class="tab-content">

                                {{-- ========================================================= --}}
                                {{-- PASO 1: INFORMACIÓN COMPLETA DEL CLIENTE                  --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade show active" id="rstep1" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-3">Información del Cliente</h5>

                                    {{-- Buscador de Cliente --}}
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small">Nombre completo del cliente <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" id="ren_client_name" name="client_name" class="form-control font-weight-bold" readonly placeholder="Haz clic en Buscar para seleccionar al cliente..." required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="$('#modalSrcClientRen').modal('show')">
                                                    <i class="fas fa-search mr-1"></i> Buscar Cliente
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-light border small text-muted mb-4">
                                        <i class="fas fa-folder-open text-primary mr-1"></i>
                                        Puedes encontrar el correo y teléfono del cliente en el documento <strong>"RESUMEN EJECUTIVO"</strong>, ubicado en la carpeta Drive de tu cliente.
                                    </div>

                                    {{-- Correo y Teléfono --}}
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-2">
                                            <label class="font-weight-bold small d-block">Correo del cliente:</label>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="chkMismoCorreo" name="same_email" value="1" checked onchange="toggleContactoRen()">
                                                <label class="custom-control-label font-weight-bold" for="chkMismoCorreo">Mismo correo <span id="lbl_registered_email" class="text-muted small"></span></label>
                                            </div>
                                            <div id="div_nuevo_correo" style="display: none;">
                                                <input type="email" name="client_email" id="ren_client_email" class="form-control" placeholder="Nuevo correo del cliente">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <label class="font-weight-bold small d-block">Teléfono del cliente:</label>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="chkMismoTelefono" name="same_phone" value="1" checked onchange="toggleContactoRen()">
                                                <label class="custom-control-label font-weight-bold" for="chkMismoTelefono">Mismo teléfono <span id="lbl_registered_phone" class="text-muted small"></span></label>
                                            </div>
                                            <div id="div_nuevo_telefono" style="display: none;">
                                                <input type="text" name="client_phone" id="ren_client_phone" class="form-control" placeholder="+52 ...">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Datos Complementarios y RFC --}}
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Ocupación del cliente</label>
                                            <input type="text" name="occupation" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">Nombre de la empresa</label>
                                            <input type="text" name="company_name" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold small">RFC <span class="text-danger">*</span></label>
                                            <input type="text" name="rfc" id="ren_rfc" class="form-control text-uppercase" maxlength="13" required>
                                        </div>
                                    </div>

                                    {{-- Documentación del Cliente --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold small">Anexa identificación oficial de tu cliente <strong>(100% legible)</strong> <span class="text-danger">*</span></label>
                                            <input type="file" name="file_ine" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold small">Anexa comprobante de domicilio, no mayor a 90 días <strong>(100% legible)</strong> <span class="text-danger">*</span></label>
                                            <input type="file" name="file_domicilio" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*" required>
                                        </div>
                                    </div>

                                    <div class="text-right border-top pt-3 mt-3">
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="irPasoRenovacion(2)">
                                            Siguiente <i class="fas fa-arrow-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 2: OBLIGACIONES A VENCER                             --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="rstep2" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-1">Obligaciones a vencer</h5>
                                    <p class="text-muted small mb-4">*Se llena un formulario por contrato.</p>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Obligación por vencer <span class="text-danger">*</span></label>
                                        <small class="d-block text-muted mb-1">Ejemplo: OBDENA USD SS00 000</small>
                                        <select name="contract_composite_id" id="ren_contract_select" class="form-control select2-buscable" style="width: 100%;" onchange="onSelectContratoRen()" required>
                                            <option value="">Primero selecciona un cliente en el Paso 1...</option>
                                        </select>
                                        <input type="hidden" name="contract_label" id="ren_contract_label">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small">Monto a renovar <span class="text-danger">*</span></label>
                                        <input type="text" name="amount" id="ren_amount" class="form-control font-weight-bold" data-type="currency" placeholder="$0.00" required>
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-3 mt-4">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="irPasoRenovacion(1)"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="irPasoRenovacion(3)">Siguiente <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 3: FORMA DE PAGO DEL PREMIO                          --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="rstep3" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-4">Forma de pago del premio</h5>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small d-block mb-2">El cliente quiere que: <span class="text-danger">*</span></label>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="payCaporta" name="payment_choice" class="custom-control-input" value="1" checked onchange="toggleFormaPagoRen()">
                                            <label class="custom-control-label font-weight-bold" for="payCaporta">Se reinvierta en fondo de CAPORTA</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="payBanco" name="payment_choice" class="custom-control-input" value="2" onchange="toggleFormaPagoRen()">
                                            <label class="custom-control-label font-weight-bold" for="payBanco">Se deposite a cuenta bancaria</label>
                                        </div>
                                    </div>

                                    {{-- BLOQUE CONDICIONAL: REINVERSIÓN EN FONDO (NUC DESTINO) --}}
                                    <div id="div_reinversion_ren" class="p-3 mb-3 rounded border" style="background-color: #eff6ff; border-color: #bfdbfe !important;">
                                        <label class="font-weight-bold small text-primary">
                                            <i class="fas fa-sync-alt mr-1"></i> Contrato de Destino para Reinversión (CP / MP) <span class="text-danger">*</span>
                                        </label>
                                        <select name="fk_nuc_dest" id="ren_fk_nuc_dest" class="form-control select2-buscable" style="width: 100%;">
                                            <option value="">Primero selecciona un cliente en el Paso 1...</option>
                                        </select>
                                    </div>

                                    {{-- BLOQUE CONDICIONAL: ESTADO DE CUENTA (DEPÓSITO BANCARIO) --}}
                                    <div id="div_estado_cuenta_ren" class="p-3 bg-light rounded border mb-3" style="display: none;">
                                        <label class="font-weight-bold small">Anexa el estado de cuenta, no mayor a 90 días <strong>(100% legible)</strong> <span class="text-danger">*</span></label>
                                        <input type="file" name="file_estado_cuenta" id="ren_file_estado_cuenta" class="form-control-file border p-2 rounded bg-white" accept=".pdf,image/*">
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-3 mt-4">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="irPasoRenovacion(2)"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="irPasoRenovacion(4)">Siguiente <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 4: CSF ACTUALIZADA                                   --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="rstep4" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-4">Constancia de Situación Fiscal</h5>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Anexa la CSF actualizada</label>
                                        <input type="file" name="file_csf" class="form-control-file border p-2 rounded bg-light" accept=".pdf,image/*">
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-3 mt-4">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="irPasoRenovacion(3)"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="irPasoRenovacion(5)">Siguiente <i class="fas fa-arrow-right ml-1"></i></button>
                                    </div>
                                </div>

                                {{-- ========================================================= --}}
                                {{-- PASO 5: BENEFICIARIOS                                     --}}
                                {{-- ========================================================= --}}
                                <div class="tab-pane fade" id="rstep5" role="tabpanel">
                                    <h5 class="font-weight-bold text-primary mb-2">Beneficiarios</h5>
                                    <p class="small text-muted mb-1">Encuentra la información de los beneficiarios en el documento <strong>"RESUMEN EJECUTIVO"</strong>, ubicado en la carpeta Drive de tu cliente.</p>
                                    <p class="small text-danger font-weight-bold mb-3">*Los beneficiarios deben ser mayor de edad</p>

                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <label class="small font-weight-bold">¿Cuántos beneficiarios son? <span class="text-danger">*</span></label>
                                            <select name="beneficiaries_qty" id="ren_beneficiaries_qty" class="form-select form-control" onchange="toggleBeneficiariosRen()" required>
                                                <option value="1" selected>1</option>
                                                <option value="2">2</option>
                                                <option value="3">3 o más</option>
                                                <option value="0">Soy persona moral, no aplica</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Beneficiario 1 --}}
                                    <div class="row mb-2 rblock-ben-1">
                                        <div class="col-md-5 mb-2">
                                            <label class="small font-weight-bold">Nombre completo del beneficiario 1 <span class="text-danger">*</span></label>
                                            <input type="text" name="beneficiary_1_name" id="rben1_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small font-weight-bold">Porcentaje (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="beneficiary_1_pct" id="rben1_pct" class="form-control" required max="100" min="1">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="small font-weight-bold">Fecha de nacimiento beneficiario 1 <span class="text-danger">*</span></label>
                                            <input type="date" name="beneficiary_1_dob" id="rben1_dob" class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- Beneficiario 2 --}}
                                    <div class="row mb-2 rblock-ben-2" style="display:none;">
                                        <div class="col-md-5 mb-2">
                                            <label class="small font-weight-bold">Nombre completo del beneficiario 2 <span class="text-danger">*</span></label>
                                            <input type="text" name="beneficiary_2_name" id="rben2_name" class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small font-weight-bold">Porcentaje (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="beneficiary_2_pct" id="rben2_pct" class="form-control" max="100" min="1">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="small font-weight-bold">Fecha de nacimiento beneficiario 2 <span class="text-danger">*</span></label>
                                            <input type="date" name="beneficiary_2_dob" id="rben2_dob" class="form-control">
                                        </div>
                                    </div>

                                    {{-- 3 o más Beneficiarios --}}
                                    <div class="row mb-3 rblock-ben-3" style="display:none;">
                                        <div class="col-md-12">
                                            <label class="small font-weight-bold text-primary">Datos de los beneficiarios <span class="text-danger">*</span></label>
                                            <small class="d-block text-muted mb-1">
                                                Escribe por cada beneficiario: 1. Nombre completo (tal cual INE), 2. Porcentaje asignado, 3. Fecha de nacimiento (mayor de edad).
                                            </small>
                                            <textarea name="beneficiaries_extra" id="rben_extra" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-top pt-4 mt-4">
                                        <button type="button" class="btn btn-light border font-weight-bold" onclick="irPasoRenovacion(4)"><i class="fas fa-arrow-left mr-1"></i> Anterior</button>
                                        <button type="button" id="btnGuardarRenovacion" onclick="guardarRenovacion()" class="btn btn-success shadow-sm font-weight-bold px-5" style="border-radius: 8px;">
                                            <i class="fas fa-paper-plane mr-2"></i> Enviar Renovación
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
    <div id="modalSrcClientRen" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header bg-light">
                    <h4 class="modal-title font-weight-bold">Seleccionar Cliente</h4>
                    <button type="button" class="close" onclick="$('#modalSrcClientRen').modal('hide')"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover text-center" style="width:100%" id="srcClientRen">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nombre / Razón Social</th>
                                    <th>RFC</th>
                                    <th>Correo</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td class="font-weight-bold">{{$client->name}} {{$client->firstname}} {{$client->lastname}}</td>
                                        <td style="font-family: monospace;">{{$client->rfc}}</td>
                                        <td>{{$client->email}}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm px-3"
                                                onclick="seleccionarClienteRen({{$client->id}}, '{{ addslashes(trim($client->name.' '.$client->firstname.' '.$client->lastname)) }}', '{{ $client->rfc }}', '{{ $client->email }}', '{{ $client->cellphone }}')">
                                                Seleccionar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{URL::asset('js/currencyformat.js')}}"></script>
@endsection

@push('head')
    <script src="{{URL::asset('js/process/renovationCreate.js')}}"></script>
    <script src="{{URL::asset('js/general.js')}}" ></script>
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
        .select2-container { width: 100% !important; display: block !important; }
        .select2-container .select2-selection--single {
            height: 38px !important; border: 1px solid #ced4da !important; border-radius: 6px !important;
            display: flex !important; align-items: center !important; background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important; top: 1px !important; right: 6px !important;
        }
    </style>
@endpush