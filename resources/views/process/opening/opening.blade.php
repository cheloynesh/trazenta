@extends('home')
<head>
    <title>Aperturas y Emisiones | Trazenta</title>
</head>
@section('content')
    <div class="container-fluid">
        {{-- Encabezado Moderno --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div class="mb-3 mb-md-0">
                <h2 style="font-weight: 700; color: #334155; margin: 0; font-size: 1.5rem;">Control de Aperturas y Emisiones</h2>
                <p class="text-muted small mb-0">Gestiona las solicitudes enviadas por agentes y el seguimiento operativo de contratos.</p>
            </div>
            <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                @if ($perm_btn['addition'] == 1)
                    <a href="{{ route('opening.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center font-weight-bold" style="border-radius: 8px; padding: 0.55rem 1.3rem;">
                        <i class="fas fa-th-list mr-2"></i> Nueva Solicitud
                    </a>
                    <a href="{{ route('renovation.create') }}" class="btn btn-success shadow-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-sync-alt mr-1"></i> Nueva Renovación
                    </a>
                    @if ($profile != 12)
                        <button type="button" class="btn btn-outline-primary shadow-sm d-flex align-items-center font-weight-bold" onclick="nuevaApertura()" style="border-radius: 8px; padding: 0.55rem 1.2rem;">
                            <i class="fas fa-plus mr-2"></i> Alta Directa
                        </button>
                    @endif
                @endif
            </div>
        </div>

        {{-- Card Principal con Pestañas --}}
        <div class="card shadow-sm" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="card-header bg-white pt-3 px-4 pb-0" style="border-bottom: 1px solid #e2e8f0;">
                <ul class="nav nav-tabs border-0" id="openingTabs" role="tablist">
                    <li class="nav-item mr-2">
                        <a class="nav-link active font-weight-bold py-2 px-3" data-toggle="tab" href="#panelEnProceso">
                            <i class="fas fa-tasks mr-2" style="color: #d97706;"></i> Aperturas en Proceso
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link font-weight-bold py-2 px-3 d-flex align-items-center" data-toggle="tab" href="#panelSolicitudes" onclick="cargarSolicitudes()">
                            <i class="fas fa-inbox mr-2" style="color: #106a6a;"></i> Solicitudes Emisión
                            <span id="badgePendingRequests" class="badge ml-2 text-white" style="background-color: #106a6a;">{{ $pendingRequestsCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-2 px-3 d-flex align-items-center" data-toggle="tab" href="#panelRenovaciones" onclick="cargarRenovaciones()">
                            <i class="fas fa-sync-alt mr-2 text-success"></i> Solicitudes Renovación
                            <span id="badgePendingRenovations" class="badge badge-success ml-2 text-white">{{ $pendingRenovationsCount }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4" style="background-color: #ffffff;">
                <div class="tab-content" id="openingTabsContent">

                    {{-- ========================================================= --}}
                    {{-- PESTAÑA 1: APERTURAS EN PROCESO (ESTRUCTURA CLÁSICA)      --}}
                    {{-- ========================================================= --}}
                    <div class="tab-pane fade show active" id="panelEnProceso" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkActive" onclick="chkActive()">
                                <label class="custom-control-label font-weight-bold text-secondary small" for="chkActive" style="cursor: pointer; user-select: none;">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Mostrar Recibidos / Terminados
                                </label>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover text-center align-middle" style="width:100%" id="example">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #edf2f7;">
                                    <tr>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Agente</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Cliente</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Fondo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Tipo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Contrato</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Fecha Límite</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Origen</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Estatus</th>
                                        @if ($profile != 12)
                                            <th class="text-muted small text-uppercase font-weight-bold text-center">Interno</th>
                                        @endif
                                        <th class="text-muted small text-uppercase font-weight-bold text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- PESTAÑA 2: SOLICITUDES POR PROCESAR (FORMULARIO AGENTE)   --}}
                    {{-- ========================================================= --}}
                    <div class="tab-pane fade" id="panelSolicitudes" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkAllRequests" onclick="cargarSolicitudes()">
                                <label class="custom-control-label font-weight-bold text-secondary small" for="chkAllRequests" style="cursor: pointer; user-select: none;">
                                    <i class="fas fa-history text-primary mr-1"></i> Mostrar historial (Procesadas y Rechazadas)
                                </label>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover text-center align-middle" style="width:100%" id="tableRequests">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #edf2f7;">
                                    <tr>
                                        <th class="text-muted small text-uppercase font-weight-bold">Folio</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Fecha</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Agente</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Cliente</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Plazo / Fondo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Monto</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Origen</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Estatus</th>
                                        <th class="text-muted small text-uppercase font-weight-bold">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- PESTAÑA 3: SOLICITUDES POR PROCESAR (FORMULARIO RENOVACIONES)   --}}
                    {{-- ========================================================= --}}
                    <div class="tab-pane fade" id="panelRenovaciones">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="chkAllRen" onclick="cargarRenovaciones()">
                            <label class="custom-control-label font-weight-bold text-secondary small" for="chkAllRen"><i class="fas fa-history text-primary mr-1"></i> Mostrar historial (Procesadas y Rechazadas)</label>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover text-center align-middle" style="width:100%" id="tableRenovations">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="small font-weight-bold">Folio</th>
                                        <th class="small font-weight-bold">Fecha</th>
                                        <th class="small font-weight-bold">Agente</th>
                                        <th class="small font-weight-bold">Cliente</th>
                                        <th class="small font-weight-bold">Contrato Base</th>
                                        <th class="small font-weight-bold">Monto Renovar</th>
                                        <th class="small font-weight-bold">Estatus</th>
                                        <th class="small font-weight-bold">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL REVISIÓN DE SOLICITUD (KYC, DOCS Y PROCESAMIENTO)   --}}
    {{-- ========================================================= --}}
    <div id="modalReviewRequest" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.18);">
                <div class="modal-header align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0; padding: 1.25rem 1.5rem;">
                    <div>
                        <span id="rev_folio" class="badge badge-warning font-weight-bold mb-1" style="font-size: 0.85rem;">Solicitud #0</span>
                        <h4 class="modal-title" style="font-weight: 700; color: #1e293b;">Revisión de Expediente de Apertura</h4>
                    </div>
                    <button type="button" class="close" onclick="$('#modalReviewRequest').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto; background-color: #ffffff;">
                    {{-- Alerta si fue rechazada --}}
                    <div id="rev_alert_rejected" class="alert alert-danger" style="display: none;">
                        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Solicitud con Observaciones / Rechazada:</strong>
                        <span id="rev_staff_notes_text"></span>
                    </div>
                    <div id="rev_alert_processed" class="alert alert-success" style="display: none;">
                        <strong><i class="fas fa-check-circle mr-1"></i> Solicitud Procesada:</strong>
                        <span id="rev_processed_text"></span>
                    </div>

                    {{-- 1. Datos del Cliente y KYC --}}
                    <h6 class="text-uppercase text-primary small font-weight-bold mb-3"><i class="fas fa-user-check mr-1"></i> 1. Datos del Cliente y KYC</h6>
                    <div class="row bg-light p-3 rounded border mb-4 mx-0">
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Cliente:</small><span id="rev_client_name" class="font-weight-bold text-dark">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Tipo Persona:</small><span id="rev_client_type">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">RFC / CURP:</small><span id="rev_rfc_curp" style="font-family: monospace;">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Correo / Celular:</small><span id="rev_contact">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Nacionalidad / Residencia:</small><span id="rev_nationality">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Estado Civil / Actividad:</small><span id="rev_activity">-</span></div>
                        <div class="col-md-6 mb-2"><small class="text-muted d-block font-weight-bold">Empresa / Dictamen Jurídico:</small><span id="rev_company_dictum">-</span></div>
                        <div class="col-md-6 mb-2"><small class="text-muted d-block font-weight-bold">Domicilio:</small><span id="rev_domicile">-</span></div>
                    </div>

                    {{-- 2. Condiciones de Inversión --}}
                    <h6 class="text-uppercase text-primary small font-weight-bold mb-3"><i class="fas fa-chart-pie mr-1"></i> 2. Estructura de Inversión</h6>
                    <div class="row bg-light p-3 rounded border mb-4 mx-0">
                        <div class="col-md-3 mb-2"><small class="text-muted d-block font-weight-bold">Plazo / Fondo:</small><span id="rev_plazo_fund" class="font-weight-bold text-dark">-</span></div>
                        <div class="col-md-3 mb-2"><small class="text-muted d-block font-weight-bold">Monto y Moneda:</small><span id="rev_amount" class="font-weight-bold text-success">-</span></div>
                        <div class="col-md-3 mb-2"><small class="text-muted d-block font-weight-bold">Pago del Premio:</small><span id="rev_payment_form">-</span></div>
                        <div class="col-md-3 mb-2"><small class="text-muted d-block font-weight-bold">Ficha de Depósito:</small><span id="rev_charge">-</span></div>
                        <div class="col-md-6 mb-0"><small class="text-muted d-block font-weight-bold">Origen del Recurso:</small><span id="rev_origin">-</span></div>
                        <div class="col-md-6 mb-0"><small class="text-muted d-block font-weight-bold">NUC Destino Reinversión (LP):</small><span id="rev_dest">-</span></div>
                    </div>

                    {{-- 3. Datos Bancarios y Beneficiarios --}}
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex flex-column mb-3 mb-md-0">
                            <h6 class="text-uppercase text-primary small font-weight-bold mb-2"><i class="fas fa-university mr-1"></i> 3. Cuenta Bancaria</h6>
                            <div class="p-3 bg-light rounded border flex-grow-1">
                                <div class="mb-1"><small class="text-muted font-weight-bold">Banco:</small> <span id="rev_bank_name">-</span></div>
                                <div class="mb-1"><small class="text-muted font-weight-bold">Cuenta:</small> <span id="rev_bank_account" style="font-family: monospace;">-</span></div>
                                <div class="mb-1"><small class="text-muted font-weight-bold">CLABE:</small> <span id="rev_bank_clabe" style="font-family: monospace;">-</span></div>
                                <div class="mb-0"><small class="text-muted font-weight-bold">ABA / SWIFT:</small> <span id="rev_bank_intl">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <h6 class="text-uppercase text-primary small font-weight-bold mb-2"><i class="fas fa-users mr-1"></i> 4. Beneficiarios</h6>
                            <div id="rev_beneficiaries_box" class="p-3 bg-light rounded border flex-grow-1 small">-</div>
                        </div>
                    </div>

                    {{-- 4. Expediente Documental --}}
                    <h6 class="text-uppercase text-primary small font-weight-bold mb-3"><i class="fas fa-folder-open mr-1"></i> 5. Documentación Adjunta</h6>
                    <div id="rev_documents_container" class="d-flex flex-wrap align-items-start bg-light rounded border pt-3 px-3 pb-2"></div>
                </div>

                <div class="modal-footer justify-content-between" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary font-weight-bold" onclick="$('#modalReviewRequest').modal('hide')">Cerrar</button>
                    @if ($profile != 12)
                        <div id="rev_staff_actions">
                            <button type="button" class="btn btn-outline-danger font-weight-bold mr-2" onclick="rechazarSolicitudActual()">
                                <i class="fas fa-times-circle mr-1"></i> Rechazar / Observar
                            </button>
                            <button type="button" class="btn btn-success font-weight-bold px-4 shadow-sm" onclick="prepararAperturaDesdeSolicitud()">
                                <i class="fas fa-check-circle mr-1"></i> Generar Apertura Oficial
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL ALTA DE APERTURA OFICIAL (STAFF)                    --}}
    {{-- ========================================================= --}}
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header" style="background-color: #f8fafc;">
                    <div>
                        <h4 class="modal-title font-weight-bold" id="gridModalLabek">Registro de Apertura Oficial</h4>
                        <span id="badgeFromRequest" class="badge badge-info" style="display: none;">Precargado desde Solicitud #0</span>
                    </div>
                    <button type="button" class="close" onclick="cerrarApertura()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Agente:</label>
                                    <select name="selectAgent" id="selectAgent" class="form-select form-control">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($agents as $id => $agent)
                                            <option value='{{ $id }}'>{{ $agent }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Cliente</label>
                                    <input type="text" id="client_edit" class="form-control" disabled placeholder="Cliente">
                                </div>
                            </div>
                            <div class="col-md-4">
                                @if ($perm_btn['modify'] == 1)
                                    <label for="">Cambiar Cliente</label>
                                    <button type="button" class="btn btn-primary btn-block" onclick="buscarclientes(0)">Buscar</button>
                                @endif
                            </div>
                        </div>

                        <div id="fisica" style="display: none;">
                            <div class="row">
                                <div class="col-md-4"><div class="form-group"><label>Nombre</label><input type="text" id="name" name="name" class="form-control" placeholder="Nombre"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>Apellido paterno</label><input type="text" id="firstname" name="firstname" class="form-control" placeholder="Apellido"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>Apellido materno</label><input type="text" id="lastname" name="lastname" class="form-control" placeholder="Apellido"></div></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><div class="form-group"><label>Fecha de nacimiento</label><input type="date" id="birth_date" name="birth_date" class="form-control"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>RFC</label><input type="text" id="rfc" name="rfc" class="form-control" placeholder="RFC"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>CURP</label><input type="text" id="curp" name="curp" class="form-control" placeholder="CURP"></div></div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4"><div class="form-group"><label>Celular</label><input type="text" id="cellphone" name="cellphone" class="form-control" placeholder="Celular"></div></div>
                                <div class="col-lg-4"><div class="form-group"><label>Correo</label><input type="text" id="email" name="email" class="form-control" placeholder="Correo"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>Domicilio Fiscal</label><input type="text" id="address" name="address" class="form-control"></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo Solicitud:</label>
                                    <select name="selectAppli" id="selectAppli" class="form-select form-control">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($applications as $id => $appli)
                                            <option value='{{ $id }}'>{{ $appli }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Forma de Pago del Premio:</label>
                                    <select name="selectPaymentform" id="selectPaymentform" class="form-select form-control" onchange="fundchange(0)">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($paymentForms as $id => $payment_form)
                                            <option value='{{ $id }}'>{{ $payment_form }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Moneda</label>
                                    <select name="selectCurrency" id="selectCurrency" class="form-select form-control" disabled>
                                        <option hidden selected>Selecciona una opción</option>
                                        <option value="MXN">MXN</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fondo / Compañía:</label>
                                    <select name="selectInsurance" id="selectInsurance" class="form-select form-control" onchange="fundchange(0)">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($insurances as $insurance)
                                            <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>NUC/Obligación</label>
                                    <input type="text" id="nuc" name="nuc" value="PENDIENTE" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div id="divLP" style="display: none;">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Monto:</label>
                                        <input type="text" id="amount" name="amount" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha de aplicación</label>
                                        <input type="date" id="initial_date" name="initial_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-block" onclick="OpenCharge()">Abrir conductos de apertura</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mes de pago:</label>
                                        <select name="selectPairNon" id="selectPairNon" class="form-select form-control">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            <option value=1>Non</option>
                                            <option value=2>Par</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="divNucs" style="display: none;">
                                    <div class="form-group">
                                        <label>Nuc de destino:</label>
                                        <select name="selectNucs" id="selectNucs" class="form-control select2-buscable" style="width: 100%">
                                            <option value=""></option>
                                            @foreach($nucs as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="divCP" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quincena:</label>
                                    <select name="selectFstscnd" id="selectFstscnd" class="form-select form-control">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        <option value=1>Primera</option>
                                        <option value=2>Segunda</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estatus:</label>&nbsp;&nbsp;
                                    <input id="onoff" type="checkbox" checked data-toggle="toggle" data-on="Reinversión" data-off="Depósito" data-width="120" data-offstyle="secondary">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cerrarApertura()">Cancelar</button>
                    <button type="button" onclick="guardarApertura()" class="btn btn-primary">Guardar Apertura</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modales existentes de Edición, Estatus Staff, Estatus Agente, Conductos y Búsqueda de Clientes --}}
    @include('process.opening.openingEdit')

    {{-- Modal Estatus Staff --}}
    <div id="myModalStatus" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estatus Interno</h4>
                    <button type="button" class="close" onclick="closeStatusInt()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="font-weight: bold;">Estatus:</label>
                                <select name="selectStatus" id="selectStatus" class="form-select form-control">
                                    <option hidden selected value="">Selecciona una opción</option>
                                    @foreach ($cmbStatus as $id => $status)
                                        <option value='{{ $id }}'>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Recogido:</label>
                                <select name="selectStatusPick" id="selectStatusPick" class="form-select form-control" onchange="showDate('Pick')">
                                    <option hidden selected value="0">Selecciona una opción</option>
                                    <option value="1">PENDIENTE</option>
                                    <option value="2">FECHA</option>
                                </select>
                            </div>
                            <div class="form-group" id="divDatePick">
                                <label>Fecha</label>
                                <input type="date" id="authPick" name="authPick" class="form-control" onchange="changeLimit()">
                            </div>
                            <div class="form-group" id="divDateLimit">
                                <label>Fecha Límite</label>
                                <input type="date" id="authLimit" name="authLimit" class="form-control" style="border: 1px solid red">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Entregado a Agente:</label>
                                <select name="selectStatusAgent" id="selectStatusAgent" class="form-select form-control" onchange="showDate('Agent')">
                                    <option hidden selected value="0">Selecciona una opción</option>
                                    <option value="1">PENDIENTE</option>
                                    <option value="2">FECHA</option>
                                </select>
                            </div>
                            <div class="form-group" id="divDateAgent">
                                <label>Fecha</label>
                                <input type="date" id="authAgent" name="authAgent" class="form-control">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Oficina:</label>
                                <select name="selectStatusOffice" id="selectStatusOffice" class="form-select form-control" onchange="showDate('Office')">
                                    <option hidden selected value="0">Selecciona una opción</option>
                                    <option value="1">PENDIENTE</option>
                                    <option value="2">FECHA</option>
                                </select>
                            </div>
                            <div class="form-group" id="divDateOffice">
                                <label>Fecha</label>
                                <input type="date" id="authOffice" name="authOffice" class="form-control">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Entregado a Finestra:</label>
                                <select name="selectStatusFinestra" id="selectStatusFinestra" class="form-select form-control" onchange="showDate('Finestra')">
                                    <option hidden selected value="0">Selecciona una opción</option>
                                    <option value="1">PENDIENTE</option>
                                    <option value="2">FECHA</option>
                                </select>
                            </div>
                            <div class="form-group" id="divDateFinestra">
                                <label>Fecha</label>
                                <input type="date" id="authFinestra" name="authFinestra" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeStatusInt()">Cancelar</button>
                    <button type="button" onclick="save()" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Estatus Agente --}}
    <div id="myModalStatusAgent" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estatus</h4>
                    <button type="button" class="close" onclick="closeStatusAg()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Estatus:</label>
                                <select name="selectStatus1" id="selectStatus1" class="form-select form-control" disabled>
                                    <option hidden selected value="">Selecciona una opción</option>
                                    @foreach ($cmbStatus as $id => $status)
                                        <option value='{{ $id }}'>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Pendiente de entrega</label><input type="text" disabled id="authPick1" name="authPick1" class="form-control"></div>
                            <div class="form-group" id="divLimit"><label>Fecha Límite</label><input type="text" disabled id="authLimit1" name="authLimit1" class="form-control" style="border: 1px solid red;"></div>
                            <div class="form-group"><label>Entregado</label><input type="text" disabled id="authAgent1" name="authAgent1" class="form-control"></div>
                            <div class="form-group"><label>Recibido</label><input type="text" disabled id="authOffice1" name="authOffice1" class="form-control"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeStatusAg()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Conducto de Cobro --}}
    <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Conductos de Apertura</h4>
                    <button type="button" class="close" aria-label="Close" onclick="cancelar('#myModal2')"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-lg-12">
                            @if ($profile != 12)
                                <div class="row align-items-end">
                                    <div class="col-lg-3"><div class="form-group"><label>Monto</label><input type="text" id="camount" name="camount" class="form-control" data-type="currency"></div></div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Conducto de Apertura:</label>
                                            <select name="selectCharge" id="selectCharge" class="form-select form-control">
                                                <option hidden selected value=0>Selecciona una opción</option>
                                                @foreach ($charges as $id => $charge)
                                                    <option value='{{ $id }}'>{{ $charge }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3"><div class="form-group"><label>Fecha de aplicación</label><input type="date" id="apply_date" name="apply_date" class="form-control"></div></div>
                                    <div class="col-lg-3"><div class="form-group"><button type="button" class="btn btn-primary btn-block" onclick="guardarConducto()">Agregar</button></div></div>
                                </div>
                            @endif
                            <br>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover text-center" id="tbProf1">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Monto</th>
                                            <th class="text-center">Conducto</th>
                                            <th class="text-center">Fecha</th>
                                            @if ($profile != 12)
                                                <th class="text-center">Opciones</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cancelar('#myModal2')">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Editar Conducto --}}
    <div id="myModalEditCharge" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Conducto</h4>
                    <button type="button" class="close" aria-label="Close" onclick="cancelar('#myModalEditCharge')"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-end">
                        <div class="col-lg-3"><div class="form-group"><label>Monto</label><input type="text" id="camount1" name="camount1" class="form-control" data-type="currency"></div></div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Conducto de Apertura:</label>
                                <select name="selectCharge1" id="selectCharge1" class="form-select form-control">
                                    <option hidden selected value=0>Selecciona una opción</option>
                                    @foreach ($charges as $id => $charge)
                                        <option value='{{ $id }}'>{{ $charge }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3"><div class="form-group"><label>Fecha de aplicación</label><input type="date" id="apply_date1" name="apply_date1" class="form-control"></div></div>
                        <div class="col-lg-3"><div class="form-group"><button type="button" class="btn btn-primary btn-block" onclick="actualizarConducto()">Actualizar</button></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL PARA REVISAR RENOVACIONES (HOMOLOGADO CON EMISIONES) --}}
    {{-- ========================================================= --}}
    <div id="modalReviewRenovation" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.18);">
                <div class="modal-header align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0; padding: 1.25rem 1.5rem;">
                    <div>
                        <span id="ren_rev_folio" class="badge badge-success font-weight-bold mb-1" style="font-size: 0.85rem;">Renovación #0</span>
                        <h4 class="modal-title" style="font-weight: 700; color: #1e293b;">Revisión de Expediente de Renovación</h4>
                    </div>
                    <button type="button" class="close" onclick="$('#modalReviewRenovation').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto; background-color: #ffffff;">
                    {{-- Alerta si fue rechazada --}}
                    <div id="ren_alert_rejected" class="alert alert-danger" style="display: none;">
                        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Solicitud con Observaciones / Rechazada:</strong>
                        <span id="ren_notes_text"></span>
                    </div>

                    {{-- 1. Datos de la Renovación --}}
                    <h6 class="text-uppercase text-primary small font-weight-bold mb-3"><i class="fas fa-sync-alt mr-1"></i> 1. Datos de la Renovación</h6>
                    <div class="row bg-light p-3 rounded border mb-4 mx-0">
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Cliente:</small><span id="ren_client" class="font-weight-bold text-dark">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Obligación Base:</small><span id="ren_contract" class="font-weight-bold text-primary">-</span></div>
                        <div class="col-md-4 mb-2"><small class="text-muted d-block font-weight-bold">Monto a Renovar:</small><span id="ren_amount" class="font-weight-bold text-success">-</span></div>
                        
                        <div class="col-md-4 mb-0"><small class="text-muted d-block font-weight-bold">Forma de Pago:</small><span id="ren_payment">-</span></div>
                        <div class="col-md-8 mb-0"><small class="text-muted d-block font-weight-bold">Contrato Destino (Reinversión):</small><span id="ren_dest" class="font-weight-bold text-info">-</span></div>
                    </div>

                    {{-- 2. Beneficiarios y Expediente Documental --}}
                    <div class="row mb-2">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="text-uppercase text-primary small font-weight-bold mb-2"><i class="fas fa-users mr-1"></i> 2. Beneficiarios</h6>
                            <div id="ren_beneficiaries_box" class="p-3 bg-light rounded border h-100 small">-</div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-primary small font-weight-bold mb-2"><i class="fas fa-folder-open mr-1"></i> 3. Documentación Adjunta</h6>
                            <div id="ren_documents_container" class="d-flex flex-wrap p-3 bg-light rounded border h-100" style="gap: 0.75rem;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary font-weight-bold" onclick="$('#modalReviewRenovation').modal('hide')">Cerrar</button>
                    @if ($profile != 12)
                        <div id="ren_staff_actions">
                            <button type="button" class="btn btn-outline-danger font-weight-bold mr-2" onclick="rechazarRenovacionActual()">
                                <i class="fas fa-times-circle mr-1"></i> Rechazar / Observar
                            </button>
                            <button type="button" class="btn btn-success font-weight-bold px-4 shadow-sm" onclick="prepararAperturaDesdeRenovacion()">
                                <i class="fas fa-check-circle mr-1"></i> Generar Apertura Oficial
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('searchclient')
    <script src="{{URL::asset('js/currencyformat.js')}}"></script>
    <script src="{{URL::asset('js/general.js')}}" ></script>
@endsection

@push('head')
    <script>
        window.requestsMap = @json($requestsMap ?? []);
        window.renewalsMap = @json($renewalsMap ?? []);
        window.userProfile = {{ $profile }};
    </script>
    <script src="{{URL::asset('js/process/opening.js')}}"></script>
    <style>
        #panelSolicitudes .dataTables_scrollHeadInner,
        #panelSolicitudes .dataTables_scrollHeadInner table,
        #panelRenovaciones .dataTables_scrollHeadInner,
        #panelRenovaciones .dataTables_scrollHeadInner table {
            width: 100% !important;
            min-width: 100% !important;
        }

        #tableRequests thead th,
        #tableRenovations thead th {
            white-space: nowrap;
        }

        #tableRequests thead input,
        #tableRenovations thead input {
            width: 115px !important;
            min-width: 115px !important;
        }

        @media (max-width: 767.98px) {
            #tableRequests_wrapper .dataTables_scrollHead table,
            #tableRequests_wrapper .dataTables_scrollBody table,
            #tableRenovations_wrapper .dataTables_scrollHead table,
            #tableRenovations_wrapper .dataTables_scrollBody table {
                width: max-content !important;
                min-width: 100% !important;
            }

            #tableRequests_wrapper th,
            #tableRequests_wrapper td,
            #tableRenovations_wrapper th,
            #tableRenovations_wrapper td {
                min-width: 130px !important;
                white-space: nowrap !important;
            }

            #tableRequests_wrapper thead input,
            #tableRenovations_wrapper thead input {
                width: 100% !important;
                min-width: 110px !important;
            }
        }

        #tableRequests .dataTables_scrollHead input,
        #tableRenovations .dataTables_scrollHead input,
        #tableRequests thead input,
        #tableRenovations thead input {
            min-width: 120px !important;
            width: 120px !important;
            max-width: none !important;
            box-sizing: border-box !important;
        }

        .highlight-row { background-color: #fef08a !important; cursor: pointer; }
        .table th, .table td { vertical-align: middle !important; }
        .nav-tabs .nav-link { color: #64748b; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #1e293b; background-color: #f8fafc; border-bottom: 3px solid #3b82f6; }
        .select2-container .select2-selection--single {
            height: 38px !important; border: 1px solid #ced4da !important; border-radius: 6px !important;
            display: flex !important; align-items: center !important; background-color: #ffffff !important;
        }
    </style>
@endpush