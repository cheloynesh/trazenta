@extends('home')
<head>
    <title>Apertura | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Aperturas
        </h1></div>
        {{-- modal| --}}
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Registro de Apertura</h4>
                    <button type="button" class="close" onclick="cerrarApertura()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Agente:</label>
                                    <select name="selectAgent" id="selectAgent" class="form-select">
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
                                @if ($perm_btn['modify']==1)
                                    <label for="">Cambiar Cliente</label>
                                    <button type="button" class="btn btn-primary" onclick="buscarclientes(0)">Buscar</button>
                                @endif
                            </div>
                        </div>

                        <div id = "fisica" style = "display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Nombre">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Apellido paterno</label>
                                        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Apellido">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Apellido materno</label>
                                        <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Apellido">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Fecha de nacimiento</label>
                                        <input type="date" id="birth_date" name="birth_date" class="form-control" placeholder="Fecha de nacimiento">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">RFC</label>
                                        <input type="text" id="rfc" name="rfc" class="form-control" placeholder="RFC">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">CURP</label>
                                        <input type="text" id="curp" name="curp" class="form-control" placeholder="CURP">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Celular</label>
                                        <input type="text" id="cellphone" name="cellphone" class="form-control" placeholder="Celular">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Correo</label>
                                        <input type="text" id="email" name="email" class="form-control" placeholder="Correo">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Domicilio Fiscal</label>
                                        <input type="text" id="address" name="address" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Tipo Solicitud:</label>
                                    <select name="selectAppli" id="selectAppli" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($applications as $id => $appli)
                                            <option value='{{ $id }}'>{{ $appli }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Forma de Pago del Premio:</label>
                                    <select name="selectPaymentform" id="selectPaymentform" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($paymentForms as $id => $payment_form)
                                            <option value='{{ $id }}'>{{ $payment_form }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Moneda</label>
                                    <select name="selectCurrency" id="selectCurrency" class="form-select">
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
                                    <label for="">Compañía:</label>
                                    <select name="selectInsurance" id="selectInsurance" class="form-select" onchange="fundchange(0)">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($insurances as $insurance)
                                            <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">NUC/Obligacion</label>
                                    <input type="text" id="nuc" name="nuc" value="PENDIENTE" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-end" id="divLP" style = "display: none;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Monto:</label>
                                    <input type="text" id="amount" name="amount" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Fecha de aplicación</label>
                                    <input type="date" id="initial_date" name="initial_date" class="form-control" placeholder="Fecha de aplicación">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-grid gap-2 col-12 mx-auto">
                                        <button type="button" class="btn btn-primary" onclick="OpenCharge()">Abrir conductos de cobro</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="divCP" style = "display: none;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Estatus:</label>
                                    &nbsp;&nbsp;
                                    <input id = "onoff" type="checkbox" checked data-toggle="toggle" data-on = "Reinversión" data-off="Depósito" data-width="120" data-offstyle="secondary">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cerrarApertura()">Cancelar</button>
                    <button type="button" onclick="guardarApertura()" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
    @include('process.opening.openingEdit')
    {{-- modal| --}}
    <div id="myModalStatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Estatus</h4>
                    <button type="button" class="close" onclick="closeStatusInt()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" style="font-weight: bold;">Estatus:</label>
                                        <select name="selectStatus" id="selectStatus" class="form-select">
                                            <option hidden selected value="">Selecciona una opción</option>
                                            @foreach ($cmbStatus as $id => $status)
                                                <option value='{{ $id }}'>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" style="font-weight: bold;">Recogido:</label>
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
                                        <label for="" style="font-weight: bold;">Entregado a Agente:</label>
                                        <select name="selectStatusAgent" id="selectStatusAgent" class="form-select" onchange="showDate('Agent')">
                                            <option hidden selected value="0">Selecciona una opción</option>
                                            <option value="1">PENDIENTE</option>
                                            <option value="2">FECHA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="divDateAgent">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha</label>
                                        <input type="date" id="authAgent" name="authAgent" class="form-control">
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
                    <button type="button" class="btn btn-secundary" onclick="closeStatusInt()">Cancelar</button>
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
                    <button type="button" class="close" onclick="closeStatusAg()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                            @foreach ($cmbStatus as $id => $status)
                                                <option value='{{ $id }}'>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Pendiente de entrega</label>
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
                                        <label for="">Entregado</label>
                                        <input type="text" disabled id="authAgent1" name="authAgent1" class="form-control">
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
                    <button type="button" class="btn btn-secundary" onclick="closeStatusAg()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
    {{-- modal conducto de cobro --}}
    <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Conductos de Cobro</h4>
                    <button type="button" class="close" aria-label="Close" onclick="cancelar('#myModal2')"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-lg-12">
                            {{-- <div class="row align-items-center"> --}}
                            @if ($profile != 12)
                                <div class="row align-items-end">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Monto</label>
                                            <input type="text" id="camount" name="camount" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Conducto de Apertura:</label>
                                            <select name="selectCharge" id="selectCharge" class="form-select">
                                                <option hidden selected value=0>Selecciona una opción</option>
                                                @foreach ($charges as $id => $charge)
                                                    <option value='{{ $id }}'>{{ $charge }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Fecha de aplicación</label>
                                            <input type="date" id="apply_date" name="apply_date" class="form-control" placeholder="Fecha de aplicación">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <div class="d-grid gap-2 col-12 mx-auto">
                                                <button type="button" class="btn btn-primary" onclick="guardarConducto()">Agregar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <br>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                        <table class="table table-striped table-hover text-center" id="tbProf1">
                                            <thead>
                                                <th class="text-center">Monto</th>
                                                <th class="text-center">Conducto</th>
                                                <th class="text-center">Fecha</th>
                                                @if ($profile != 12)
                                                    <th class="text-center">Opciones</th>
                                                @endif
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cancelar('#myModal2')">Cerrar</button>
                    {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal --}}
    {{-- modal editar conducto de cobro --}}
    <div id="myModalEditCharge" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Editar Conducto</h4>
                    <button type="button" class="close" aria-label="Close" onclick="cancelar('#myModalEditCharge')"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-lg-12">
                            {{-- <div class="row align-items-center"> --}}
                            <div class="row align-items-end">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Monto</label>
                                        <input type="text" id="camount1" name="camount1" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Conducto de Apertura:</label>
                                        <select name="selectCharge1" id="selectCharge1" class="form-select">
                                            <option hidden selected value=0>Selecciona una opción</option>
                                            @foreach ($charges as $id => $charge)
                                                <option value='{{ $id }}'>{{ $charge }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Fecha de aplicación</label>
                                        <input type="date" id="apply_date1" name="apply_date1" class="form-control" placeholder="Fecha de aplicación">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="d-grid gap-2 col-12 mx-auto">
                                            <button type="button" class="btn btn-primary" onclick="actualizarConducto()">Actualizar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cancelar('#myModalEditCharge')">Cerrar</button>
                    {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal --}}
    @include('searchclient')
    {{-- Inicia pantalla de inicio --}}
    <div class="col-lg-12">
        <div class="row">
            @if ($perm_btn['modify']==1)
                <div class="col-md-12">
                    <div class="form-group">
                        @if ($perm_btn['addition']==1)
                            <button type="button" class="btn btn-primary" onclick="nuevaApertura()" title="Nuevo Servicio"><i class="fas fa-plus"></i></button>
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
                    <th class="text-center">Cliente</th>
                    <th class="text-center">Fondo</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Contrato</th>
                    <th class="text-center">Fecha Límite</th>
                    <th class="text-center">Estatus</th>
                    @if ($profile != 12)
                        <th class="text-center">Interno</th>
                    @endif
                    <th class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($openings as $opening)
                    <tr id="{{$opening->oid}}">
                        {{-- <td>{{$initial->date}}</td> --}}
                        <td>{{$opening->agent}}</td>
                        <td>{{$opening->cname}}
                        </td>
                        <td>{{$opening->insurance}}</td>
                        <td>{{$opening->fund_type}}</td>
                        <td>{{$opening->nuc}}</td>
                        <td>{{$opening->limit_status}}</td>
                        <td>
                            <button class="btn btn-info" style="color: #{{$opening->font_color}}; background-color: #{{$opening->color}}; border-color: #{{$opening->border_color}}" onclick="opcionesEstatusAgente({{$opening->oid}})">{{$opening->name}}</button>
                        </td>
                        @if ($profile != 12)
                            <td>
                                @if($opening->statid == 6 && $opening->finestra_status == null)
                                    <button class="btn btn-info" style="color: #ffffff; background-color: #e98c46; border-color: #e98c46" onclick="opcionesEstatus({{$opening->oid}})">Pend. Finestra</button>
                                @else
                                    <button class="btn btn-info" style="color: #{{$opening->font_color}}; background-color: #{{$opening->color}}; border-color: #{{$opening->border_color}}" onclick="opcionesEstatus({{$opening->oid}})">{{$opening->name}}</button>
                                @endif
                            </td>
                        @endif
                        <td>
                            <button href="#|" class="btn btn-warning" onclick="editarApertura({{$opening->oid}})" ><i class="fas fa-edit"></i></button>
                            @if ($perm_btn['erase']==1)
                                <button href="#|" class="btn btn-danger" onclick="eliminarApertura({{$opening->oid}})"><i class="fa fa-trash"></i></button>
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
<script src="{{URL::asset('js/process/opening.js')}}" ></script>
@endpush
