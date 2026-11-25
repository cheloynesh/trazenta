@extends('home')
<head>
    <title>Historial de Tickets de Inversión | Trazenta</title>
</head>
@section('content')
    <div class="container-fluid">
        {{-- Encabezado Responsivo --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="mb-3 mb-md-0">
                <h2 style="font-weight: 700; color: #334155; margin: 0; font-size: 1.5rem;">Control de Tickets de Inversión</h2>
                <p class="text-muted small mb-0">Monitorea y gestiona los trámites e incidencias de contratos y fondos de inversión.</p>
            </div>
            <div>
                <a href="{{ url('tickets/create') }}" class="btn btn-primary shadow-sm d-flex align-items-center" 
                   style="border-radius: 8px; padding: 0.6rem 1.6rem; font-weight: 600; border: none;">
                    <i class="fas fa-plus mr-2" style="font-size: 0.85rem;"></i> Nuevo Ticket
                </a>
            </div>
        </div>

        {{-- Contenedor Principal (Card) --}}
        <div class="card shadow-sm" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="card-body p-4" style="background-color: #ffffff;">
                <div class="container-fluid mb-4">
                    <div id="accordionFilters">
                        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                            {{-- Encabezado Filtros --}}
                            <div class="card-header bg-white p-0" id="headingFilters">
                                <button class="btn btn-block text-left p-3 d-flex justify-content-between align-items-center" 
                                        type="button" data-toggle="collapse" data-target="#collapseFilters" 
                                        style="background-color: #f8fafc; border: none; outline: none; box-shadow: none;">
                                    <span class="h5 mb-0 font-weight-bold" style="color: #334155;">
                                        <i class="fas fa-filter mr-2"></i> Filtros Avanzados
                                    </span>
                                    <i class="fas fa-chevron-down text-muted"></i>
                                </button>
                            </div>

                            <div id="collapseFilters" class="collapse" aria-labelledby="headingFilters">
                                <div class="card-body p-4">
                                    {{-- Fila 1: Asignado y Agente --}}
                                    <div class="row">
                                        @if($profile != 12)
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold small text-muted text-uppercase">Asignado a</label>
                                                    <select id="selectUserFilt" name="selectUserFilt[]" class="form-select select2" multiple style="width: 100%;" onchange="ObtenerTicketsAlIniciar()">
                                                        @foreach($users as $id => $name)
                                                            <option value="{{ $id }}">{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold small text-muted text-uppercase">Agente</label>
                                                    <select id="selectAgentFilt" name="selectAgentFilt[]" class="form-select select2" multiple style="width: 100%;" onchange="ObtenerTicketsAlIniciar()">
                                                        @foreach($agents as $id => $name)
                                                            <option value="{{ $id }}">{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Fila 2: Fondo, Trámite y Estatus --}}
                                    <div class="row">
                                        <div class="col-12 col-md-4 mb-3">
                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold small text-muted text-uppercase">Fondo de Inversión</label>
                                                <select id="selectInsuranceExcFilt" name="selectInsuranceExcFilt[]" class="form-select select2" multiple onchange="ObtenerTicketsAlIniciar()">
                                                    @foreach($insurances as $id => $insurance)
                                                        <option value="{{ $id }}">{{ $insurance }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 mb-3">
                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold small text-muted text-uppercase">Trámite de Inversión</label>
                                                <select name="selectServiceFilt[]" id="selectServiceFilt" class="form-select select2" style="border-radius: 8px;" multiple onchange="ObtenerTicketsAlIniciar()">
                                                    <option value="1">ABONO/ INCREMENTO</option>
                                                    <option value="2">AGREGAR CUENTA BANCARIA</option>
                                                    <option value="3">CAMBIO DE DATOS DEL BENEFICIARIO</option>
                                                    <option value="4">CAMBIO DE DATOS DEL CONTRATANTE</option>
                                                    <option value="5">CAMBIO DE FORMA DE PAGO DEL PREMIO</option>
                                                    <option value="6">CAMBIO DE PLAN /MOV INTERNO</option>
                                                    <option value="7">RETIRO PARCIAL</option>
                                                    <option value="8">RETIRO TOTAL</option>
                                                    <option value="9">VARIOS</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 mb-3">
                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold small text-muted text-uppercase">Estatus</label>
                                                <select name="selectStatusFilt[]" id="selectStatusFilt" class="form-select select2" style="border-radius: 8px;" multiple onchange="ObtenerTicketsAlIniciar()">
                                                    @foreach ($cmbStatus as $id => $status)
                                                        <option value='{{ $id }}'>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Botonera --}}
                                    <div class="row align-items-end justify-content-end mt-2">
                                        <div class="col-md-4 mb-0 d-flex align-items-end justify-content-end">
                                            <button type="button" class="btn btn-link text-muted mr-3" onclick="clearFilters()" style="font-weight: 600; text-decoration: none;">
                                                Limpiar Filtros
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($profile != 12)
                    <div class="custom-control custom-checkbox d-inline-block mr-3 mb-2">
                        <input type="checkbox" class="custom-control-input" id="chkMyTickets" onchange="ObtenerTicketsAlIniciar()" @if($profile == 2) checked @endif>
                        <label class="custom-control-label font-weight-bold text-secondary small" for="chkMyTickets" style="cursor: pointer; user-select: none;">
                            <i class="fas fa-user-tag text-primary mr-1"></i> Ver solo mis tickets asignados
                        </label>
                    </div>
                @endif
                <div class="custom-control custom-checkbox d-inline-block mr-3 mb-2">
                    <input type="checkbox" class="custom-control-input" id="chkActive" onchange="ObtenerTicketsAlIniciar()">
                    <label class="custom-control-label font-weight-bold text-secondary small" for="chkActive" style="cursor: pointer; user-select: none;">
                        <i class="fas fa-check text-success mr-1"></i> Ver terminados
                    </label>
                </div>
                
                {{-- TABLA DE TICKETS --}}
                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle" style="width:100%" id="ticketsTable">
                        <thead style="background-color: #f8fafc; border-bottom: 2px solid #edf2f7;">
                            <tr>
                                <th class="text-muted small text-uppercase font-weight-bold">Folio</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Cliente</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Contrato / NUC</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Agente</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Trámite</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Fondo / Plazo</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Fecha Registro</th>
                                <th class="text-muted small text-uppercase font-weight-bold">Estatus</th>
                                <th class="text-muted small text-uppercase font-weight-bold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL DE DETALLES --}}
    <div id="modalViewTicketFull" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0; padding: 1.25rem 1.5rem;">
                    <div>
                        <span id="v_ticket_folio" class="badge badge-light border text-secondary font-weight-bold" style="padding: 5px 10px; border-radius: 6px; font-family: monospace; font-size: 0.85rem;">#00000</span>
                        <h4 class="modal-title" style="font-weight: 700; color: #1e293b; line-height: 1.2;">Información Completa de la Solicitud</h4>
                    </div>
                    <button type="button" class="close" onclick="ocultar('#modalViewTicketFull')" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" style="background-color: #ffffff; max-height: 75vh; overflow-y: auto;">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase text-muted small font-weight-bold d-block mb-1"><i class="fas fa-user mr-1 text-primary"></i> Cliente</label>
                            <span id="v_ticket_client" class="font-weight-bold text-dark" style="font-size: 1rem;">-</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase text-muted small font-weight-bold d-block mb-1"><i class="fas fa-file-contract mr-1 text-primary"></i> Contrato / NUC</label>
                            <span id="v_ticket_policy" class="font-weight-bold text-dark" style="font-size: 1rem;">-</span>
                        </div>
                    </div>
                    <div class="row mb-4 border-top pt-3">
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase text-muted small font-weight-bold d-block mb-1">Fondo de Inversión</label>
                            <span id="v_ticket_insurance" class="font-weight-bold text-secondary">-</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase text-muted small font-weight-bold d-block mb-1">Trámite Solicitado</label>
                            <span id="v_ticket_service_text" class="font-weight-bold text-secondary">-</span>
                        </div>
                    </div>
                    <div class="row mb-3 border-top pt-3">
                        <div class="col-md-12">
                            <label class="text-uppercase text-muted small font-weight-bold d-block mb-1">Detalles del Trámite / Comentarios</label>
                            <div id="v_ticket_agent_comm" class="p-3 bg-light text-muted small" style="border-radius: 8px; border-left: 4px solid #3b82f6; white-space: pre-wrap; line-height: 1.5;">-</div>
                        </div>
                    </div>
                    <div id="v_block_archivos" class="row mb-3 border-top pt-3" style="display: none;">
                        <div class="col-md-12">
                            <label class="text-uppercase text-muted small font-weight-bold d-block mb-2">
                                <i class="fas fa-paperclip mr-1 text-primary"></i> Documentos Adjuntos
                            </label>
                            <div id="v_ticket_archivos_container" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div> 
                </div>
                <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary font-weight-bold" onclick="ocultar('#modalViewTicketFull')" style="border-radius: 8px;">Cerrar Visualización</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE EDICIÓN DEL TICKET --}}
    <div id="modalEditTicketFull" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <form id="formEditTicketFull" class="needs-validation" novalidate>
                    <input type="hidden" id="e_ticket_id" name="ticket_id">
                    <input type="hidden" id="e_fk_nuc" name="e_fk_nuc">
                    <input type="hidden" id="e_fk_insurance" name="fk_insurance">
                    <input type="hidden" id="e_nrpolicy" name="nrpolicy">
                    
                    <div class="modal-header align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0; padding: 1.25rem 1.5rem;">
                        <div>
                            <span id="e_ticket_folio" class="badge badge-light border text-secondary font-weight-bold monospace mb-1" style="padding: 5px 10px; border-radius: 6px; font-size: 0.85rem;">#00000</span>
                            <h4 class="modal-title" style="font-weight: 700; color: #1e293b; line-height: 1.2;">Modificar Datos del Ticket</h4>
                        </div>
                        <button type="button" class="close" onclick="ocultar('#modalEditTicketFull')" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body p-4" style="background-color: #ffffff; max-height: 70vh; overflow-y: auto;">
                        
                        {{-- Cliente y Contrato / NUC --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold small text-muted text-uppercase">Cliente</label>
                                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0" style="background-color: #f1f5f9; color: #94a3b8;"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" id="e_client_name" class="form-control font-weight-bold border-left-0" disabled style="background-color: #f1f5f9; color: #334155; height: 42px;">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" onclick="buscarclientes()" style="font-weight: 600; height: 42px;"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div id="wrapper_btn_register_client" class="mt-2" style="display: none;">
                                        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold w-100" id="btn_register_client_action" style="border-radius: 6px; padding: 6px 12px;">
                                            <i class="fas fa-user-plus mr-1"></i> Registrar Cliente Completo en Sistema
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold small text-muted text-uppercase">Contrato / NUC Asociado</label>
                                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0" style="background-color: #f1f5f9; color: #94a3b8;"><i class="fas fa-file-contract"></i></span>
                                        </div>
                                        <input type="text" id="e_policy_edit" class="form-control font-weight-bold border-left-0" disabled placeholder="Sin contrato" style="background-color: #f1f5f9; color: #334155; height: 42px;">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="e_btn_buscar_poliza" onclick="buscarpolizas()" style="font-weight: 600; height: 42px;"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Asignaciones y Trámite --}}
                        <div class="row mb-3 border-top pt-3">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Agente <span class="text-danger">*</span></label>
                                    <select name="fk_agent" id="fk_agent" class="form-select form-control" style="border-radius: 8px; height: 42px;" required>
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @if(isset($agents))
                                            @foreach($agents as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Asignado a <span class="text-danger">*</span></label>
                                    <select name="fk_user" id="fk_user" class="form-select form-control" style="border-radius: 8px; height: 42px;" required>
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @if(isset($users))
                                            @foreach($users as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Trámite de Inversión <span class="text-danger">*</span></label>
                                    <select name="service_inv" id="e_inversiones" class="form-select form-control" style="border-radius: 8px; height: 42px;" required>
                                        <option hidden selected value="">Selecciona una opción</option>
                                        <option value="1">ABONO/ INCREMENTO</option>
                                        <option value="2">AGREGAR CUENTA BANCARIA</option>
                                        <option value="3">CAMBIO DE DATOS DEL BENEFICIARIO</option>
                                        <option value="4">CAMBIO DE DATOS DEL CONTRATANTE</option>
                                        <option value="5">CAMBIO DE FORMA DE PAGO DEL PREMIO</option>
                                        <option value="6">CAMBIO DE PLAN /MOV INTERNO</option>
                                        <option value="7">RETIRO PARCIAL</option>
                                        <option value="8">RETIRO TOTAL</option>
                                        <option value="9">VARIOS</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Documentos Existentes --}}
                        <div id="e_block_archivos_existentes" class="row mb-3 border-top pt-3" style="display: none;">
                            <div class="col-md-12">
                                <label class="font-weight-bold small text-muted text-uppercase mb-2">Documentos Actuales (Clic en × para eliminar)</label>
                                <div id="e_ticket_archivos_existentes_container" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        {{-- Agregar Nuevos Archivos --}}
                        <div class="row mb-3 border-top pt-3">
                            <div class="col-md-12">
                                <div class="form-group mb-0 p-3" style="background-color: #f8f9fa; border-radius: 12px; border: 1px solid #eee;">
                                    <label for="subir_archivo_edit" class="font-weight-bold small text-muted text-uppercase">Agregar nuevos archivos</label>
                                    <input type="file" id="subir_archivo_edit" name="subir_archivo[]" class="form-control-file d-block mb-1" accept="image/*,.pdf,.zip,.rar,.xml" multiple onchange="manejarMultiplesArchivosEdit(this)">
                                    <small class="text-muted">Los archivos seleccionados se sumarán a los documentos actuales del ticket.</small>
                                    <div id="lista_archivos_container_edit" class="mt-3 d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Folios de Trámite --}}
                        {{-- <div class="row mb-3 border-top pt-3">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <label class="font-weight-bold small text-muted text-uppercase mb-0">Folios de Trámite Asociados</label>
                                            <small class="d-block text-muted">Agrega o modifica los folios y sus fechas límite.</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-primary font-weight-bold" onclick="agregarParejaFolioFechaEdit()" style="font-size: 0.82rem; text-decoration: none;">
                                            <i class="fas fa-plus-circle mr-1"></i> Agregar Folio
                                        </button>
                                    </div>
                                    <div id="e_container_parejas_folios"></div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- Folio y Falta de Original --}}
                        <div class="row mb-3 border-top pt-3">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Folio de Trámite</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" name="folio" id="e_folio" class="form-control font-weight-bold" placeholder="Ej. INV-1001" style="border-radius: 0 8px 8px 0; height: 42px;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Falta de Original</label>
                                <select name="original" id="e_original" class="form-select form-control" onchange="toggleFechasOriginalEdit()" style="border-radius: 8px; height: 42px;">
                                    <option value="N/A">N/A</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Entregado">Entregado</option>
                                    <option value="Finestra">Finestra</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2" id="div_edit_original_delivery" style="display: none;">
                                <label class="font-weight-bold small text-muted text-uppercase">Fecha de Entrega <span class="text-danger">*</span></label>
                                <input type="date" name="original_delivery_date" id="e_original_delivery_date" class="form-control font-weight-bold" style="border-radius: 8px; height: 42px;">
                            </div>
                            <div class="col-md-6 mb-2" id="div_edit_original_finestra" style="display: none;">
                                <label class="font-weight-bold small text-muted text-uppercase">Fecha Entrega a Finestra <span class="text-danger">*</span></label>
                                <input type="date" name="original_finestra_date" id="e_original_finestra_date" class="form-control font-weight-bold" style="border-radius: 8px; height: 42px;">
                            </div>
                        </div>

                        {{-- Comentarios --}}
                        <div class="row mb-0 border-top pt-3">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold small text-muted text-uppercase">Detalles del Servicio / Comentarios <span class="text-danger">*</span></label>
                                    <textarea name="agent_comm" id="e_agent_comm" class="form-control" rows="4" style="border-radius: 8px; resize: none;" required></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-link text-muted font-weight-bold" onclick="ocultar('#modalEditTicketFull')" style="text-decoration: none;">Cancelar</button>
                        <button type="button" id="btnGuardarCambiosFull" onclick="actualizarCambiosTicketFull()" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="border-radius: 8px; border: none;">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL BUSCAR CLIENTE --}}
    <div id="modalSrcClient" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                    <h4 class="modal-title" style="font-weight: 700; color: #334155;">Buscar Clientes con Inversión</h4>
                    <button type="button" class="close" onclick="ocultar('#modalSrcClient')" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" style="background-color: #f1f5f9;"> 
                    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover text-center" style="width:100%" id="srcClient">
                                    <thead style="background-color: #f8fafc;">
                                        <tr>
                                            <th class="text-muted small text-uppercase font-weight-bold">Nombre</th>
                                            <th class="text-muted small text-uppercase font-weight-bold">RFC</th>
                                            <th class="text-muted small text-uppercase font-weight-bold">Tipo</th>
                                            <th class="text-muted small text-uppercase font-weight-bold">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clients as $client)
                                            <tr>
                                                <td class="align-middle font-weight-bold text-dark">{{$client->name}} {{$client->firstname}} {{$client->lastname}}</td>
                                                <td class="align-middle text-muted" style="font-family: monospace;">{{$client->rfc}}</td>
                                                <td class="align-middle">
                                                    <span class="badge badge-light border">{{ $client->status == 0 ? 'Física' : 'Moral' }}</span>
                                                </td>
                                                <td class="align-middle text-right">
                                                    <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="obtenerid({{$client->id}})" style="border-radius: 6px; padding: 5px 15px; font-weight: 600;">
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
        </div>
    </div>

    {{-- MODAL BUSCAR CONTRATO / NUC --}}
    <div id="modalSrcPolicy" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                    <h4 class="modal-title" style="font-weight: 700; color: #334155;">Seleccionar Contrato / NUC</h4>
                    <button type="button" class="close" onclick="ocultar('#modalSrcPolicy')" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" style="background-color: #fff;">
                    <div class="container-fluid">
                        <div class="table-responsive">
                            <table class="table table-hover text-center" style="width:100%; border-collapse: collapse;" id="srcPolicy">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #edf2f7;">
                                    <tr>
                                        <th class="text-muted small text-uppercase font-weight-bold" style="padding: 12px 20px;">Contrato / NUC</th>
                                        <th class="text-muted small text-uppercase font-weight-bold" style="padding: 12px 20px;">Fondo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold" style="padding: 12px 20px;">Tipo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center" style="padding: 12px 20px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #eee; border-radius: 0 0 15px 15px; padding: 1.25rem;">
                    <button type="button" onclick="ocultar('#modalSrcPolicy')" class="btn btn-link text-muted" style="text-decoration: none;">Cerrar</button>
                    <button type="button" class="btn btn-warning" id="btnPólizaNoRegistrada" onclick="toggleModoContratoManual()">
                        <i class="fas fa-file-alt mr-1"></i> Contrato no Registrado
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ESTATUS --}}
    <div id="myEstatusModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <form id="formEstatusModal" class="needs-validation" novalidate>
                    <input type="hidden" id="estatus_ticket_id" name="ticket_id">
                    
                    <div class="modal-header align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0; padding: 1.25rem 1.5rem;">
                        <div>
                            <h4 class="modal-title" style="font-weight: 700; color: #1e293b;">Actualización de Estatus</h4>
                            <p class="text-muted small mb-0">Modifica el estado actual del trámite y adjunta los requerimientos necesarios.</p>
                        </div>
                        <button type="button" class="close" onclick="ocultar('#myEstatusModal')" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4" style="background-color: #ffffff; max-height: 70vh; overflow-y: auto;">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold small text-muted text-uppercase">Selecciona el Nuevo Estatus <span class="text-danger">*</span></label>
                            <select name="fk_status" id="selectStatus" class="form-select form-control" style="border-radius: 8px; height: 42px;" required>
                                <option hidden selected value="">Selecciona una opción</option>
                                @foreach ($cmbStatus as $id => $status)
                                    <option value='{{ $id }}'>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr style="border-top: 1px solid #f1f5f9; margin: 1.5rem 0;">

                        {{-- 51: Falta de información --}}
                        <div id="status_51" class="status-block" style="display: none;">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Detalle de Falta de Información <span class="text-danger">*</span></label>
                                <textarea name="detail" id="status_field_detail" class="form-control" rows="3" style="border-radius: 8px; resize: none;" placeholder="Especifica qué información o documentos hacen falta..."></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-muted text-uppercase">PDF de Falta de Información</label>
                                @if($profile != 12)
                                    <input type="file" name="link_miss[]" id="status_field_link_miss" class="form-control-file d-block mb-1" accept="image/*,.pdf,.zip,.rar,.xml" multiple>
                                @endif
                                <div id="file_existente_link_miss" class="mt-2"></div>
                            </div>
                            @if($profile == 12)
                                <div class="form-group mb-0 p-3" style="background-color: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;margin-top: 1rem !important;">
                                    <label class="font-weight-bold small text-muted text-uppercase mb-1">Adjuntar Archivos / Documentos Faltantes</label>
                                    <input type="file" id="subir_faltantes_51" class="form-control-file d-block mb-1" accept="image/*,.pdf,.zip,.rar,.xml" multiple onchange="manejarArchivosFaltantes(this, 'lista_faltantes_51')">
                                    <div id="lista_faltantes_51" class="mt-2 d-flex flex-wrap gap-2"></div>
                                </div>
                            @endif
                        </div>

                        {{-- 52: En trámite --}}
                        <div id="status_52" class="status-block" style="display: none;">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Folio de Trámite <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="text" name="folio" id="status_field_folio" class="form-control font-weight-bold" placeholder="Ej. INV-1001" style="border-radius: 0 8px 8px 0; height: 42px;">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Falta de Original <span class="text-danger">*</span></label>
                                <select name="original" id="status_field_original" class="form-select form-control" onchange="toggleFechasOriginalStatus()" style="border-radius: 8px; height: 42px;">
                                    <option value="N/A">N/A</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Entregado">Entregado</option>
                                    <option value="Finestra">Finestra</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2" id="div_status_original_delivery" style="display: none;">
                                    <label class="font-weight-bold small text-muted text-uppercase">Fecha de Entrega <span class="text-danger">*</span></label>
                                    <input type="date" name="original_delivery_date" id="status_field_original_delivery_date" class="form-control font-weight-bold" style="border-radius: 8px; height: 42px;">
                                </div>
                                <div class="col-md-6 mb-2" id="div_status_original_finestra" style="display: none;">
                                    <label class="font-weight-bold small text-muted text-uppercase">Fecha Entrega a Finestra <span class="text-danger">*</span></label>
                                    <input type="date" name="original_finestra_date" id="status_field_original_finestra_date" class="form-control font-weight-bold" style="border-radius: 8px; height: 42px;">
                                </div>
                            </div>
                        </div>

                        {{-- 53: Suspensión --}}
                        <div id="status_53" class="status-block" style="display: none;">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Comentarios de Suspensión <span class="text-danger">*</span></label>
                                <textarea name="comm_susp" id="status_field_comm_susp" class="form-control" rows="2" style="border-radius: 8px; resize: none;" placeholder="Motivos de la suspensión del servicio..."></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-muted text-uppercase">PDF de Suspensión</label>
                                @if($profile != 12)
                                    <input type="file" name="link_susp[]" id="status_field_link_susp" class="form-control-file d-block mb-1" accept="image/*,.pdf,.zip,.rar,.xml" multiple>
                                @endif
                                <div id="file_existente_link_susp" class="mt-2"></div>
                            </div>
                            @if($profile == 12)
                                <div class="form-group mb-0 p-3" style="background-color: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; margin-top: 1rem !important;">
                                    <label class="font-weight-bold small text-muted text-uppercase mb-1">Adjuntar Archivos / Documentos de Suspensión</label>
                                    <input type="file" id="subir_faltantes_53" class="form-control-file d-block mb-1" accept="image/*,.pdf,.zip,.rar,.xml" multiple onchange="manejarArchivosFaltantes(this, 'lista_faltantes_53')">
                                    <div id="lista_faltantes_53" class="mt-2 d-flex flex-wrap gap-2"></div>
                                </div>
                            @endif
                        </div>

                        {{-- 54: Atendido --}}
                        <div id="status_54" class="status-block" style="display: none;">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Comentarios de Atención <span class="text-danger">*</span></label>
                                <textarea name="comm_attend" id="status_field_comm_attend" class="form-control" rows="2" style="border-radius: 8px; resize: none;" placeholder="Detalles de la resolución o cierre del trámite..."></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-muted text-uppercase">PDF de Evidencia / Cierre</label>
                                @if($profile != 12)
                                    <input type="file" name="link_attend[]" id="status_field_link_attend" class="form-control-file d-block mb-1" accept="image/*,.pdf,.zip,.rar,.xml" multiple>
                                @endif
                                <div id="file_existente_link_attend" class="mt-2"></div>
                            </div>
                        </div>

                        {{-- 55: Rechazado --}}
                        <div id="status_55" class="status-block" style="display: none;">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold small text-muted text-uppercase">Motivo de Rechazo <span class="text-danger">*</span></label>
                                <textarea name="motive" id="status_field_motive" class="form-control" rows="3" style="border-radius: 8px; resize: none;" placeholder="Explica detalladamente la causa del rechazo..."></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px; padding: 1rem 1.5rem;">
                        <div class="custom-control custom-checkbox text-left" id="chkbxestatus">
                            <input type="checkbox" class="custom-control-input" id="checkNotificarCorreo" name="notificar_correo" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-muted small" style="cursor: pointer; user-select: none;" for="checkNotificarCorreo">
                                <i class="fas fa-envelope mr-1 text-primary"></i> Notificar cambio por correo al cliente
                            </label>
                        </div>
                        <div>
                            <button type="button" onclick="ocultar('#myEstatusModal')" class="btn btn-link text-muted font-weight-bold mr-2" style="text-decoration: none;">Cancelar</button>
                            <button type="button" id="btnGuardarEstatus" onclick="actualizarEstatus()" class="btn btn-primary shadow-sm font-weight-bold px-4" style="border-radius: 8px; border: none; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: none;">
                                <i class="fas fa-sync-alt mr-2"></i> Actualizar Estatus
                            </button>
                            <button type="button" id="btnSubirFaltantesAgente" onclick="subirFaltantesAgente()" class="btn btn-success shadow-sm font-weight-bold px-4" style="border-radius: 8px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: none;">
                                <i class="fas fa-cloud-upload-alt mr-2"></i> Enviar Archivos Faltantes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CHAT --}}
    <div id="modalChatTicket" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header align-items-center" style="background-color: #014235; color: #ffffff; border-radius: 15px 15px 0 0; padding: 1rem 1.25rem;">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 38px; height: 38px;">
                            <i class="fas fa-comments text-success" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" style="font-weight: 700; font-size: 1.05rem;" id="chat_modal_title">Chat del Ticket</h5>
                            <small style="color: #a7f3d0;" id="chat_modal_subtitle">Cargando...</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white" onclick="ocultar('#modalChatTicket')" aria-label="Close" style="outline: none; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3" id="chat_body_container" style="background-color: #f8fafc; height: 380px; overflow-y: auto; display: flex; flex-direction: column;"></div>
                <div class="modal-footer d-block p-2" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    {{-- <div id="wrapper_nota_interna" class="custom-control custom-checkbox mb-2 ml-2" style="display: none;">
                        <input type="checkbox" class="custom-control-input" id="checkNotaInterna" value="1">
                        <label class="custom-control-label small font-weight-bold text-warning" for="checkNotaInterna" style="cursor: pointer;">
                            🔒 Nota privada interna (Solo visible para staff)
                        </label>
                    </div> --}}
                    <div class="input-group">
                        <input type="text" id="input_chat_message" class="form-control border-right-0" placeholder="Escribe un mensaje..." style="border-radius: 20px 0 0 20px; height: 40px; font-size: 0.9rem;" onkeypress="if(event.keyCode == 13) enviarMensajeChat()">
                        <div class="input-group-append">
                            <button type="button" id="btnEnviarMensaje" onclick="enviarMensajeChat()" class="btn btn-primary px-3" style="border-radius: 0 20px 20px 0; background-color: #00a859; border: none;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HISTORIAL DE CORREOS --}}
    <div id="modalEmailHistory" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0; padding: 1.25rem 1.5rem;">
                    <div>
                        <h5 class="modal-title mb-0" style="font-weight: 700; color: #1e293b;">Historial de Correos Enviados</h5>
                        <small class="text-muted font-weight-bold" id="eh_ticket_subtitle">Cargando...</small>
                    </div>
                    <button type="button" class="close" onclick="ocultar('#modalEmailHistory')" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" style="background-color: #ffffff;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center" style="width:100%" id="tableEmailLogs">
                            <thead style="background-color: #f1f5f9;">
                                <tr>
                                    <th class="text-muted small text-uppercase font-weight-bold">Fecha / Hora</th>
                                    <th class="text-muted small text-uppercase font-weight-bold">Estatus</th>
                                    <th class="text-muted small text-uppercase font-weight-bold">Destinatario</th>
                                    <th class="text-muted small text-uppercase font-weight-bold">Asunto / Contenido</th>
                                    <th class="text-muted small text-uppercase font-weight-bold">Resultado</th>
                                    <th class="text-muted small text-uppercase font-weight-bold">Enviado Por</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyEmailLogs"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary px-4 font-weight-bold" onclick="ocultar('#modalEmailHistory')" style="border-radius: 8px;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <script>
        window.userProfile = {{ $profile }};
        window.permModify = {{ $perm_btn['modify'] ?? 0 }};
    </script>
    <script src="{{URL::asset('js/tickets/viewticket.js')}}"></script>
    <script src="{{URL::asset('js/general.js')}}" ></script>
    <style>
        .highlight-row { background-color: #fef08a !important; cursor: pointer; }
        .table th, .table td { vertical-align: middle !important; }
        .gap-1 { gap: 0.25rem !important; }
        .gap-2 { gap: 0.5rem !important; }
        .gap-3 { gap: 1rem !important; }
    </style>
    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%', closeOnSelect: false, placeholder: 'Selecciona una o más opciones', allowClear: true });
        });
    </script>
@endpush