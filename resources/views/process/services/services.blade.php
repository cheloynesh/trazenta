@extends('home')
<head>
    <title>Servicios | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Servicios
        </h1></div>
        {{-- modal| --}}
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Registro de Servicio</h4>
                    <button type="button" class="close" onclick="cerrarModal('#myModal')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">

                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Fondo:</label>
                                    <select name="selectFund" id="selectFund" class="form-select" onchange="fundChange()">
                                        <option hidden selected value=0>Selecciona una opción</option>
                                        <option value="CP">CP</option>
                                        <option value="LP">LP</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Contrato</label>
                                    <input type="text" id="nuc_edit" class="form-control" disabled placeholder="Contrato">
                                </div>
                            </div>
                            <div class="col-md-3">
                                @if ($perm_btn['modify']==1)
                                    <label for="">Cambiar</label>
                                    <button type="button" id="btnSrcNuc" class="btn btn-primary" onclick="buscarnuc(0)" disabled>Buscar</button>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Folio</label>
                                    <input type="text" id="folio" class="form-control" placeholder="Folio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Tipo de Servicio</label>
                                    <select name="selectType" id="selectType" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        <option value=0>Digital</option>
                                        <option value=1>Entrega Original</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Servicio</label>
                                    <select name="selectService" id="selectService" class="form-select" onchange="showAmount('#selectService','amountDiv','#amount')">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($services_types as $id => $type)
                                            <option value='{{ $id }}'>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="amountDiv">
                                <div class="form-group">
                                    <label for="">Monto</label>
                                    <input type="text" id="amount" name="amount" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value=0 data-type="currency">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cerrarModal('#myModal')">Cancelar</button>
                    <button type="button" onclick="guardarServicio()" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
    {{-- modal| --}}
    <div id="myModalStatusInt" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Estatus</h4>
                    <button type="button" class="close" onclick="cerrarModal('#myModalStatusInt')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" style="font-weight: bold;">Estatus:</label>
                                        <select name="selectStatusInt" id="selectStatusInt" class="form-select">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            @foreach ($cmbStatusInt as $id => $status)
                                                <option value='{{ $id }}'>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" style="font-weight: bold;">Inicio de trámite:</label>
                                        <select name="selectStatusPick" id="selectStatusPick" class="form-select" onchange="showDate('Pick')">
                                            <option hidden selected value="0">Selecciona una opción</option>
                                            <option value="1">PENDIENTE</option>
                                            <option value="2">FECHA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="divDatePick">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha</label>
                                        <input type="date" id="authPick" name="authPick" class="form-control" onchange="changeLimit()">
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="divDateLimit">
                                <div class="col-md-12" >
                                    <div class="form-group">
                                        <label for="">Fecha Límite</label>
                                        <input type="date" id="authLimit" name="authLimit" class="form-control" style="border: 1px solid red">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" style="font-weight: bold;">Oficina:</label>
                                        <select name="selectStatusOffice" id="selectStatusOffice" class="form-select" onchange="showDate('Office')">
                                            <option hidden selected value="0">Selecciona una opción</option>
                                            <option value="1">PENDIENTE</option>
                                            <option value="2">FECHA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="divDateOffice">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha</label>
                                        <input type="date" id="authOffice" name="authOffice" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" style="font-weight: bold;">Entregado a Finestra:</label>
                                        <select name="selectStatusFinestra" id="selectStatusFinestra" class="form-select" onchange="showDate('Finestra')">
                                            <option hidden selected value="0">Selecciona una opción</option>
                                            <option value="1">PENDIENTE</option>
                                            <option value="2">FECHA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="divDateFinestra">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha</label>
                                        <input type="date" id="authFinestra" name="authFinestra" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cerrarModal('#myModalStatusInt')">Cancelar</button>
                    <button type="button" onclick="save()" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
    {{-- modal| --}}
    <div id="myModalStatusAgent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Estatus</h4>
                    <button type="button" class="close" onclick="cerrarModal('#myModalStatusAgent')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Estatus:</label>
                                        <select name="selectStatus1" id="selectStatus1" class="form-select" disabled>
                                            <option hidden selected value="">Selecciona una opción</option>
                                            @foreach ($cmbStatusInt as $id => $status)
                                                <option value='{{ $id }}'>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Inicio de trámite</label>
                                        <input type="text" disabled id="authPick1" name="authPick1" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="divLimit">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha Límite</label>
                                        <input type="text" disabled id="authLimit1" name="authLimit1" class="form-control" style="border: 1px solid red;">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Recibido</label>
                                        <input type="text" disabled id="authOffice1" name="authOffice1" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cerrarModal('#myModalStatusAgent')">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
    @include('process.services.servicesEdit')
    @include('searchnuc')
    {{-- Inicia pantalla de inicio --}}
    <div class="col-lg-12">
        <div class="row">
            @if ($perm_btn['modify']==1)
                <div class="col-md-12">
                    <div class="form-group">
                        @if ($perm_btn['addition']==1)
                            <button type="button" class="btn btn-primary" onclick="nuevoServicio()" title="Nuevo Servicio"><i class="fas fa-plus"></i></button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- <div>
        Columnas: <a class="toggle-vis" data-column="6">Fecha límite</a> - <a class="toggle-vis" data-column="7">Entregado a agente</a> - <a class="toggle-vis" data-column="8">Entregado a finestra</a>
    </div> --}}

    <br><br>

    <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
        <table class="table table-striped table-hover text-center" style="width:100%" id="example">
            <thead>
                <tr>
                    <th class="text-center">Agente</th>
                    <th class="text-center">Folio</th>
                    <th class="text-center">Contrato</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">Servicio</th>
                    <th class="text-center">Monto</th>
                    <th class="text-center">Estatus</th>
                    <th class="text-center">Documentos</th>
                    @if ($profile != 12)
                        <th class="text-center">Interno</th>
                    @endif
                    <th class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr id="{{$service->sid}}">
                        <td>{{$service->agent}}</td>
                        <td>{{$service->folio}}</td>
                        <td>{{$service->nuc}}</td>
                        <td>{{$service->cname}}</td>
                        <td>{{$service->servname}}</td>
                        <td>{{$service->mnt}}</td>
                        <td>
                            <button class="btn btn-info" style="color: #{{$service->font_color}}; background-color: #{{$service->color}}; border-color: #{{$service->border_color}}" onclick="opcionesEstatus({{$service->sid}})">{{$service->name}}</button>
                        </td>
                        <td>
                            <button class="btn btn-info" style="color: #{{$service->intFont}}; background-color: #{{$service->intColor}}; border-color: #{{$service->intBorder}}" onclick="opcionesEstatusAgente({{$service->sid}})">{{$service->intName}}</button>
                        </td>
                        @if ($profile != 12)
                            <td>
                                @if($service->intId == 6 && $service->finestra_status == null)
                                    <button class="btn btn-info" style="color: #ffffff; background-color: #e98c46; border-color: #e98c46" onclick="opcionesEstatusInt({{$service->sid}})">Pend. Finestra</button>
                                @else
                                    <button class="btn btn-info" style="color: #{{$service->intFont}}; background-color: #{{$service->intColor}}; border-color: #{{$service->intBorder}}" onclick="opcionesEstatusInt({{$service->sid}})">{{$service->intName}}</button>
                                @endif
                            </td>
                        @endif
                        <td>
                            <button href="#|" class="btn btn-warning" onclick="editarApertura({{$service->sid}})" ><i class="fas fa-edit"></i></button>
                            @if ($perm_btn['erase']==1)
                                <button href="#|" class="btn btn-danger" onclick="eliminarApertura({{$service->sid}})"><i class="fa fa-trash"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="{{URL::asset('js/currencyformat.js')}}" ></script>
@endsection
@push('head')
<script src="{{URL::asset('js/process/services.js')}}" ></script>
@endpush
