@extends('home')
<head>
    <title>Clientes | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Catálogo de Clientes</h1></div>
    <div style="max-width: 100%; margin: auto;">
        @include('admin.client.clientnew')
        @include('admin.client.clientedit')
        {{-- modal| --}}
        <div id="nucModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Nuevo nuc</h4>
                        <button type="button" class="close" onclick="cerrarNuc()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Nuc</label>
                                            <input type="text" id="nuc" name="nuc" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrency" id="selectCurrency" class="form-select">
                                                <option hidden selected>Selecciona una opción</option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Compañía:</label>
                                            <select name="selectInsurance" id="selectInsurance" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($insurances as $insurance)
                                                    @if($insurance->fund_type == "CP")
                                                        <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
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
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
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
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Conducto de Apertura:</label>
                                            <select name="selectCharge" id="selectCharge" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($charges as $id => $charge)
                                                    <option value='{{ $id }}'>{{ $charge }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cerrarNuc()">Cancelar</button>
                        <button type="button" onclick="guardarNuc()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- modal| --}}
        <div id="sixMonthNucModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Nueva Obligación fondo largo plazo</h4>
                        <button type="button" class="close" onclick="cerrarNucSixMonth()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Obligación</label>
                                            <input type="text" id="nucSixMonth" name="nucSixMonth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Monto</label>
                                            <input type="text" id="amountSixMonth" name="amountSixMonth" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrencySixMonth" id="selectCurrencySixMonth" class="form-select">
                                                <option hidden selected>Selecciona una opción</option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Agente:</label>
                                            <select name="selectAgentSixMonth" id="selectAgentSixMonth" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($agents as $id => $agent)
                                                    <option value='{{ $id }}'>{{ $agent }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Compañía:</label>
                                            <select name="selectInsuranceSixMonth" id="selectInsuranceSixMonth" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($insurances as $insurance)
                                                    @if($insurance->fund_type == "LP")
                                                        <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha de aplicación</label>
                                            <input type="date" id="initial_date" name="initial_date" class="form-control" placeholder="Fecha de aplicación">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Tipo Solicitud:</label>
                                            <select name="selectAppliSixMonth" id="selectAppliSixMonth" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($applications as $id => $appli)
                                                    <option value='{{ $id }}'>{{ $appli }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Forma de Pago del Premio:</label>
                                            <select name="selectPaymentformSixMonth" id="selectPaymentformSixMonth" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($paymentForms as $id => $payment_form)
                                                    <option value='{{ $id }}'>{{ $payment_form }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="d-grid gap-2 col-12 mx-auto">
                                                <button type="button" class="btn btn-primary" onclick="OpenCharge()">Abrir conductos de cobro</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cerrarNucSixMonth()">Cancelar</button>
                        <button type="button" onclick="guardarNucSixMonth()" class="btn btn-primary">Guardar</button>
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
                                            <select name="selectChargeS" id="selectChargeS" class="form-select">
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
                                <br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                            <table class="table table-striped table-hover text-center" id="tbProf1">
                                                <thead>
                                                    <th class="text-center">Monto</th>
                                                    <th class="text-center">Conducto</th>
                                                    <th class="text-center">Fecha</th>
                                                    <th class="text-center">Opciones</th>
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
        {{-- Inicia pantalla de inicio --}}
        <div class="bd-example bd-example-padded-bottom">
            @if ($perm_btn['addition']==1)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNewClient"><i class="fas fa-plus"></i></button>
            @endif
        </div>
        <br><br>
        <div class="tab-content" id="mytabcontent">
            <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                <table class="table table-striped table-hover text-center" id="tbClient">
                    <thead>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Apellidos</th>
                        <th class="text-center">RFC</th>
                        @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                            <th class="text-center">Opciones</th>
                        @endif
                    </thead>

                    <tbody>
                        @foreach ($clients as $client)
                            <tr id="{{$client->id}}">
                                <td>{{$client->name}}</td>
                                <td>{{$client->firstname}} {{$client->lastname}}</td>
                                <td>{{$client->rfc}}</td>
                                @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                    <td>
                                        @if ($perm_btn['modify']==1)
                                            <a href="#|" class="btn btn-primary" onclick="nuevoNucSixMonth({{$client->id}})" ><i class="fas fa-plus"></i> LP</a>
                                            <a href="#|" class="btn btn-primary" onclick="nuevoNuc({{$client->id}})" ><i class="fas fa-plus"></i> CP</a>
                                            <button href="#|" class="btn btn-warning" onclick="editarCliente({{$client->id}})" ><i class="fas fa-edit"></i></button>
                                        @endif
                                        @if ($perm_btn['erase']==1)
                                            <button href="#|" class="btn btn-danger" onclick="eliminarCliente({{$client->id}})"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script src="{{URL::asset('js/currencyformat.js')}}" ></script>
@endsection
@push('head')
    <script src="{{URL::asset('js/admin/client.js')}}"></script>
@endpush
