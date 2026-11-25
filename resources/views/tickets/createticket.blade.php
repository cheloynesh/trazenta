@extends('home')
<head>
    <title>Nueva Solicitud | Trazenta</title>
</head>
@section('content')
    <div class="container-fluid">
        {{-- Encabezado Responsivo de la Vista --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="mb-3 mb-md-0">
                <h2 style="font-weight: 700; color: #334155; margin: 0; font-size: 1.5rem;">Alta de Solicitudes</h2>
                <p class="text-muted small mb-0">Registra y administra los trámites de inversión, seguros y pólizas de los clientes.</p>
            </div>
        </div>

        {{-- Contenedor Principal (Card Premium) --}}
        <div class="card shadow-sm" style="max-width: 100%; margin: auto; border-radius: 15px; border: none; overflow: hidden;">
            <div class="card-body p-4" style="background-color: #ffffff;">
                
                <form class="needs-validation" id="validationSolicitud" novalidate>
                    <div class="container-fluid p-0">
                        
                        {{-- SECCIÓN 1: Datos del Cliente (Versión Proporcionada y Compacta) --}}
                        <h6 class="text-uppercase text-muted small font-weight-bold mb-4" style="letter-spacing: 1px;">
                            <i class="fas fa-user-circle mr-2 text-primary" style="font-size: 1rem;"></i> Datos del Cliente
                        </h6>
                        <div class="row mb-4">
                            {{-- En móvil ocupa todo (col-12), en tablets medianas 9 de 12, y en pantallas grandes se frena en 6 (la mitad) --}}
                            <div class="col-12 col-md-9 col-lg-12">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold small text-muted text-uppercase">Cliente <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                        
                                        {{-- Icono decorativo al inicio para darle más estilo --}}
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0" style="background-color: #f1f5f9; color: #94a3b8; border-radius: 8px 0 0 8px;">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>

                                        <input type="text" id="client_edit" class="form-control font-weight-bold border-left-0" disabled placeholder="Cliente" 
                                            style="background-color: #f1f5f9; color: #334155; height: 42px; border-radius: {{ $perm_btn['modify'] == 1 ? '0' : '0 8px 8px 0' }};" required>
                                        
                                        @if ($perm_btn['modify'] == 1)
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary d-flex align-items-center" onclick="buscarclientes()" 
                                                        style="border-radius: 0 8px 8px 0; font-weight: 600; background: border: none; padding: 0 1.25rem; height: 42px;">
                                                    <i class="fas fa-search mr-2"></i> Seleccionar Cliente
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: Datos de la Solicitud --}}
                        <h6 class="text-uppercase text-muted small font-weight-bold mt-2 mb-4" style="letter-spacing: 1px;">
                            <i class="fas fa-file-alt mr-2 text-primary" style="font-size: 1rem;"></i> Datos de la Solicitud
                        </h6>
                        
                        <div class="row" style="display: none;">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold small">Inversión o seguro <span class="text-danger">*</span></label>
                                    <select name="inversion_seguro" id="inversion_seguro" class="form-select form-control" style="border-radius: 8px;" required>
                                        {{-- <option hidden selected value="">Selecciona una opción</option> --}}
                                        <option selected value="inv">Inversión</option>
                                        {{-- <option selected value="seg">Seguro</option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SUB-SECCIÓN: Bloque Inversión (Oculto por defecto) --}}
                        <div id="bloque_inversion">
                            <div class="row">
                                <div class="col-12 col-md-9 col-lg-6">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold small text-muted text-uppercase">Contrato/NUC Asociado</label>
                                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                            
                                            {{-- Icono decorativo de documento/contrato al inicio --}}
                                            <div class="input-group-prepend">
                                                <span class="input-group-text border-right-0" style="background-color: #f1f5f9; color: #94a3b8; border-radius: 8px 0 0 8px;">
                                                    <i class="fas fa-file"></i>
                                                </span>
                                            </div>

                                            <input type="text" id="policy_edit" class="form-control font-weight-bold border-left-0" disabled placeholder="Sin contrato/NUC seleccionado" 
                                                style="background-color: #f1f5f9; color: #334155; height: 42px; border-radius: {{ $perm_btn['modify'] == 1 ? '0' : '0 8px 8px 0' }};">
                                            
                                            @if ($perm_btn['modify'] == 1)
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary d-flex align-items-center" onclick="buscarpolizas('policy_edit')" id="btn_buscar_poliza" name="btn_buscar_poliza"
                                                            style="border-radius: 0 8px 8px 0; font-weight: 600; border: none; padding: 0 1.25rem; height: 42px;" disabled>
                                                        <i class="fas fa-search mr-2"></i> Seleccionar contrato/NUC
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Servicio <span class="text-danger">*</span></label>
                                        <select name="inversiones" id="inversiones" class="form-select form-control" style="border-radius: 8px;" required>
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
                        </div>

                        {{-- SUB-SECCIÓN: Bloque Seguro (Oculto por defecto) --}}
                        <div id="bloque_seguro" style="display: none;">
                            <div class="row mb-4" id="col_polizas">
                                {{-- Mantenemos la proporción perfecta: col-12 en móvil, col-md-9 en tablet y col-lg-6 en escritorio --}}
                                <div class="col-12 col-md-9 col-lg-12">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold small text-muted text-uppercase">Contrato/NUC Asociada</label>
                                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                            
                                            {{-- Icono decorativo de documento/Contrato/NUC al inicio --}}
                                            <div class="input-group-prepend">
                                                <span class="input-group-text border-right-0" style="background-color: #f1f5f9; color: #94a3b8; border-radius: 8px 0 0 8px;">
                                                    <i class="fas fa-file"></i>
                                                </span>
                                            </div>

                                            <input type="text" id="policy_edit" class="form-control font-weight-bold border-left-0" disabled placeholder="Sin Contrato/NUC seleccionado" 
                                                style="background-color: #f1f5f9; color: #334155; height: 42px; border-radius: {{ $perm_btn['modify'] == 1 ? '0' : '0 8px 8px 0' }};">
                                            
                                            @if ($perm_btn['modify'] == 1)
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary d-flex align-items-center" onclick="buscarpolizas('policy_edit')" id="btn_buscar_poliza" name="btn_buscar_poliza"
                                                            style="border-radius: 0 8px 8px 0; font-weight: 600; border: none; padding: 0 1.25rem; height: 42px;" disabled>
                                                        <i class="fas fa-search mr-2"></i> Seleccionar Contrato/NUC
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Compañía <span class="text-danger">*</span></label>
                                        <select name="compania" id="compania" class="form-select form-control" onchange="llenarRamos()" style="border-radius: 8px;">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            @if(isset($insurances))
                                                @foreach($insurances as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold small">Ramo <span class="text-danger">*</span></label>
                                    <select name="ramo" id="ramo" class="form-select form-control" style="border-radius: 8px;">
                                        <option selected value="">Selecciona Ramo</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Clave de Agente <span class="text-danger">*</span></label>
                                    <select name="fk_agent_key" id="fk_agent_key" class="form-select form-control" style="border-radius: 8px;" disabled>
                                        <option hidden selected value="">Selecciona una compañía</option>
                                        <option value="0">Clave de ELAN</option>
                                        @if(isset($keys))
                                            @foreach($keys as $key)
                                                <option value="{{ $key->id }}" data-insurance-id="{{ $key->insurance_id }}">{{ $key->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Tipo de servicio <span class="text-danger">*</span></label>
                                        <select name="tipo_servicio" id="tipo_servicio" class="form-select form-control" style="border-radius: 8px;">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            <option value="1">AGENTES (COMISIONES, ACT. CÉDULA, PÓLIZA RC, ETC.)</option>
                                            <option value="2">SERVICIOS</option>
                                            <option value="3">COTIZACIONES</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Campos dependientes del tipo de servicio --}}
                            <div class="row">
                                <div class="col-md-12" id="col_servicios_agentes" style="display: none;">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Servicios de agentes <span class="text-danger">*</span></label>
                                        <select name="servicios_agentes" id="servicios_agentes" class="form-select form-control" style="border-radius: 8px;">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            <option value="1">ACLARACIÓN COMISIONES</option>
                                            <option value="2">ACLARACIÓN DE BONOS</option>
                                            <option value="3">ACTUALIZACIÓN CEDULA</option>
                                            <option value="4">ACTUALIZACIÓN RC</option>
                                            <option value="5">ALTA AGENTE</option>
                                            <option value="6">CAMBIO CUENTA BANCARIA</option>
                                            <option value="7">PAGO DE COMISIONES</option>
                                            <option value="8">VARIOS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12" id="col_servicios" style="display: none;">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small">Servicios <span class="text-danger">*</span></label>
                                        <select name="servicios" id="servicios" class="form-select form-control" style="border-radius: 8px;">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            <option value="1">ACLARACION DE PAGOS</option>
                                            <option value="2">ACLARACION DE RESCATE</option>
                                            <option value="3">ACTUALIZACION DE DATOS</option>
                                            <option value="4">ALTA /BAJA FIDEICOMISO</option>
                                            <option value="5">ALTA CUENTAS</option>
                                            <option value="6">ALTA/BAJA BIEN ASEGURADO</option>
                                            <option value="7">ALTA/BAJA DE ASEGURADO</option>
                                            <option value="8">APLICACIÓN PAGO</option>
                                            <option value="9">AUMENTO /DISMINUSIÓN DE COBERTURAS</option>
                                            <option value="10">AUMENTO /DISMINUSIÓN DE SUMA ASEGURADA</option>
                                            <option value="11">CAMBIO AGENTE</option>
                                            <option value="12">CAMBIO BENEFICIARIO</option>
                                            <option value="13">CAMBIO DE CONDUCTO DE COBRO</option>
                                            <option value="14">CAMBIO DE CONTRATANTE</option>
                                            <option value="15">CAMBIO DE DEDUCIBLE /COASEGURO</option>
                                            <option value="16">CAMBIO FORMA DE PAGO</option>
                                            <option value="17">CAMBIO PLAN</option>
                                            <option value="18">CANCELACION</option>
                                            <option value="19">CONVERSIÓN POLIZA</option>
                                            <option value="20">COTIZACIÓN</option>
                                            <option value="21">DESCUENTO</option>
                                            <option value="22">FACTURACION</option>
                                            <option value="23">FALLECIMIENTO</option>
                                            <option value="24">LIGAS DE PAGO</option>
                                            <option value="25">MODIFICACIONES GENERALES</option>
                                            <option value="26">PROGRAMACIÓN DE CIRUGIA</option>
                                            <option value="27">RECONOCIMIENTO DE ANTIGÜEDAD</option>
                                            <option value="28">RECONSIDERACION DE RIESGO</option>
                                            <option value="29">RECONSIDERACION DE SINIESTRO</option>
                                            <option value="30">REEMBOLSO</option>
                                            <option value="31">REHABILITACIÓN</option>
                                            <option value="32">REIMPRESIÓN PÓLIZA</option>
                                            <option value="33">RENOVACIÓN</option>
                                            <option value="34">RESCATE PARCIAL</option>
                                            <option value="35">VARIOS</option>
                                            <option value="36">VENCIMIENTO /RESCATE TOTAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: Documentación y Detalles (Soportando múltiples archivos) --}}
                        <h6 class="text-uppercase text-muted small font-weight-bold mt-2 mb-4" style="letter-spacing: 1px;">
                            <i class="fas fa-paperclip mr-2 text-primary" style="font-size: 1rem;"></i> Documentación y Detalles
                        </h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4 p-3" style="background-color: #f8f9fa; border-radius: 12px; border: 1px solid #eee;">
                                    <label for="subir_archivo" class="font-weight-bold small text-muted text-uppercase">Subir archivos masivos</label>
                                    
                                    {{-- Cambios aquí: name="subir_archivo[]", se añade 'multiple' y el evento onchange --}}
                                    <input type="file" id="subir_archivo" name="subir_archivo[]" class="form-control-file d-block mb-1" accept="image/*,.pdf,.xls,.xlsx,.csv,.zip,.rar,.xml" multiple onchange="manejarMultiplesArchivos(this)">
                                    <small class="text-muted">Puedes seleccionar varios documentos a la vez (PDF o imágenes).</small>

                                    {{-- Contenedor dinámico premium para listar los archivos elegidos --}}
                                    <div id="lista_archivos_container" class="mt-3 d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN: Detalles del Servicio --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    {{-- Etiqueta con asterisco rojo de obligatorio --}}
                                    <label for="comentarios" class="font-weight-bold small text-muted text-uppercase">
                                        Detalles del Servicio / Comentarios <span class="text-danger">*</span>
                                    </label>
                                    
                                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                        {{-- Icono decorativo alineado a la parte superior del cuadro de texto --}}
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0" style="background-color: #f1f5f9; color: #94a3b8; border-radius: 8px 0 0 8px; align-items: flex-start; padding-top: 0.75rem;">
                                                <i class="fas fa-comment-alt mt-1"></i>
                                            </span>
                                        </div>

                                        {{-- Textarea con atributo required y desactivado el redimensionamiento manual (resize:none) --}}
                                        <textarea name="comentarios" id="comentarios" class="form-control border-left-0" rows="4" required
                                                placeholder="Describe detalladamente las especificaciones, observaciones o notas importantes sobre el servicio solicitado..."
                                                style="background-color: #ffffff; color: #334155; border-radius: 0 8px 8px 0; resize: none; padding: 0.75rem; font-size: 0.9rem; line-height: 1.5;"></textarea>
                                    </div>
                                    
                                    {{-- Mensaje de error nativo si intentan enviar el formulario vacío --}}
                                    <div class="invalid-feedback mt-2 font-weight-bold small">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Es obligatorio detallar el motivo o descripción de este servicio.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr style="border-top: 1px solid #f1f5f9; margin: 2rem 0 1.5rem 0;">

                        {{-- Botonera del Formulario --}}
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end align-items-center">
                                <a href="{{ url()->previous() }}" class="btn btn-link text-muted mr-3" style="text-decoration: none; font-weight: 600;">
                                    Cancelar
                                </a>
                                <button type="button" id="btnGuardarSolicitud" onclick="guardarSolicitud()" class="btn btn-primary shadow" style="border-radius: 8px; padding: 0.6rem 2.5rem; font-weight: 600; border: none;">
                                    <i class="fas fa-save mr-2"></i> Enviar Solicitud
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                {{-- PANTALLA DE AGRADECIMIENTO (Oculta por defecto) --}}
                <div id="pantalla_agradecimiento" style="display: none;" class="text-center py-5">
                    <div class="mb-4 animate__animated animate__zoomIn">
                        {{-- Icono Check Premium con sombra suave --}}
                        <i class="fas fa-check-circle text-success" style="font-size: 4.5rem; filter: drop-shadow(0 4px 12px rgba(40, 167, 69, 0.25));"></i>
                    </div>
                    
                    <h3 style="font-weight: 700; color: #1e293b;" class="mb-2">¡Solicitud Registrada con Éxito!</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 520px; font-size: 0.95rem; line-height: 1.6;">
                        Tu ticket ha sido generado correctamente en el sistema. Nuestro equipo operativo revisará los detalles y nos pondremos en contacto contigo a la brevedad.
                    </p>
                    
                    {{-- Botonera de Control de Flujo --}}
                    <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 1rem;">
                        {{-- Botón 1: Ir al historial --}}
                        <a href="{{ url('tickets/view') }}" class="btn btn-light border font-weight-bold px-4 py-2" 
                           style="border-radius: 8px; color: #475569; background-color: #f8fafc; transition: all 0.2s;">
                            <i class="fas fa-list-alt mr-2" style="color: #64748b;"></i> Ver los Tickets
                        </a>
                        
                        {{-- Botón 2: Crear uno nuevo --}}
                        <button type="button" onclick="reiniciarFormulario()" class="btn btn-primary font-weight-bold px-4 py-2 shadow-sm" 
                                style="border-radius: 8px; border: none; transition: all 0.2s;">
                            <i class="fas fa-plus-circle mr-2"></i> Crear una Nueva Solicitud
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modales (Mantenido intacto) --}}
    <div id="modalSrcClient" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">

                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                    <h4 class="modal-title" id="gridModalLabek" style="font-weight: 700; color: #334155;">Buscar Clientes</h4>
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

                <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-warning" id="btnClienteNoRegistrado" onclick="toggleModoCliente('no_registrado')">
                        <i class="fas fa-user-plus mr-1"></i> Cliente no Registrado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalSrcPolicy" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">

                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                    <h4 class="modal-title" id="gridModalLabek" style="font-weight: 700; color: #334155;">Seleccionar contrato/NUC</h4>
                    <button type="button" class="close" onclick="ocultar('#modalSrcPolicy')" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4" style="background-color: #fff;"> <div class="container-fluid">
                        <div class="table-responsive">
                            <table class="table table-hover text-center" style="width:100%; border-collapse: collapse;" id="srcPolicy">
                                <thead style="background-color: #f8fafc; border-bottom: 2px solid #edf2f7;">
                                    <tr>
                                        <th class="text-muted small text-uppercase font-weight-bold" style="padding: 12px 20px;">Contrato/NUC</th>
                                        <th class="text-muted small text-uppercase font-weight-bold" style="padding: 12px 20px;">Fondo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold" style="padding: 12px 20px;">Tipo</th>
                                        <th class="text-muted small text-uppercase font-weight-bold text-center" style="padding: 12px 20px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #eee; border-radius: 0 0 15px 15px; padding: 1.25rem;">
                    <button type="button" onclick="ocultar('#modalSrcPolicy')" class="btn btn-link text-muted" style="text-decoration: none;">Cerrar</button>
                    <button type="button" class="btn btn-warning" id="btnPólizaNoRegistrada" onclick="toggleModoPoliza()">
                        <i class="fas fa-file-alt mr-1"></i> Contrato/NUC no Registrada
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <style>
        /* Estilos complementarios para inputs */
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        
        .gap-3 {
            gap: 1rem !important;
        }

        textarea.form-control {
            resize: none;
        }
    </style>
    <script src="{{URL::asset('js/tickets/createticket.js')}}"></script>
@endpush