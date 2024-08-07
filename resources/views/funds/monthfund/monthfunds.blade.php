@extends('home')
<head>
    <title>Fondo CP | Trazenta</title>
</head>
{{-- @section('title','Perfiles') --}}
@section('content')
    <div class="text-center"><h1>Fondo Mensual</h1></div>
    <div style="max-width: 100%; margin: auto;">
        {{-- inicia modal --}}
        <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Movimientos</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarMovimiento()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                {{-- <div class="row align-items-center"> --}}
                                @if ($perm_btn['modify']==1)
                                    <div class="row align-items-end">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="">Monto</label>
                                                <input type="text" id="amount" name="amount" placeholder="Ingresa el monto" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="">Fecha de aplicación</label>
                                                    <input type="date" id="apply_date" name="apply_date" class="form-control" placeholder="Fecha de aplicación">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="">Movimiento:</label>
                                                <select name="selectType" id="selectType" class="form-select">
                                                    <option hidden selected>Selecciona una opción</option>
                                                    {{-- <option>Apertura</option>
                                                    <option>Reinversion</option>
                                                    <option>Abono</option>
                                                    <option>Retiro parcial</option>
                                                    <option>Retiro total</option>
                                                    <option>Ajuste</option> --}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <div class="d-grid gap-2 col-12 mx-auto">
                                                    <button type="button" class="btn btn-primary" onclick="guardarMovimiento()">Aplicar</button>
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
                                                    <th class="text-center">Fecha</th>
                                                    <th class="text-center">Autorización</th>
                                                    <th class="text-center">Saldo anterior</th>
                                                    <th class="text-center">Saldo actual</th>
                                                    <th class="text-center">Moneda</th>
                                                    <th class="text-center">Monto</th>
                                                    <th class="text-center">Tipo</th>
                                                    @if ($perm_btn['modify']==1)
                                                        <th class="text-center">Opciones</th>
                                                    @endif
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelarMovimiento()">Cancelar</button>
                        <button type="button" onclick="excel_nuc()" class="btn btn-primary">Exportar Excel</button>
                        {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- modal autorizar --}}
        <div id="authModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Autorizar Movimiento</h4>
                        <button type="button" class="close" onclick="cerrarAuth()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha de autorización</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cerrarAuth()">Cancelar</button>
                        <button type="button" onclick="guardarAuth()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- modal editar --}}
        <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Editar NUC</h4>
                        <button type="button" onclick="cerrarNuc()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">NUC</label>
                                            <input type="text" id="nuc" name="nuc" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Cliente:</label>
                                            <select name="selectClient" id="selectClient" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($clients as $id => $client)
                                                    <option value='{{ $id }}'>{{ $client }}</option>
                                                @endforeach
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
                                                    <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
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
                                            <label for="">Forma de Pago:</label>
                                            <select name="selectPaymentform" id="selectPaymentform" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($paymentForms as $id => $payment_form)
                                                    <option value='{{ $id }}'>{{ $payment_form }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Conducto de Pago:</label>
                                            <select name="selectCharge" id="selectCharge" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($charges as $id => $charge)
                                                    <option value='{{ $id }}'>{{ $charge }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Estatus:</label>
                                            <select name="selectActiveStat" id="selectActiveStat" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                <option value=0>INACTIVO</option>
                                                <option value=1>ACTIVO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="cerrarNuc()" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                        @if ($perm_btn['modify']==1)
                            <button type="button" onclick="actualizarNuc()" class="btn btn-primary">Guardar</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
        {{-- modal editar conducto de cobro --}}
    <div id="myModalEditCharge" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
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
                                <div class="col-lg-12">
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cancelar('#myModalEditCharge')">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="actualizarConducto()">Actualizar</button>
                    {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal --}}
        {{-- modal espera --}}
        <div id="waitModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Importando Excel</h4>
                        {{-- <button type="button" onclick="cerrarNuc()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> --}}
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row" id="waitrow">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <img src="{{ URL::asset('img/SpinnerLittle.gif') }}">
                                            <label> Se están procesando los datos, por favor espere.</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="resultrow" style="display: none">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Movimientos Importados</label>
                                                <input disabled type="text" id="importados" name="importados" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Movimientos Repetidos</label>
                                                <input disabled type="text" id="repetidos" name="repetidos" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Nucs no Encontrados</label>
                                                <input disabled type="text" id="notFnd" name="notFnd" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <table class="table table-striped table-hover text-center" id="tbnotFnd">
                                                    <thead>
                                                        <th class="text-center">NUC</th>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="closeBtn" hidden onclick="cerrarWait()" class="btn btn-secundary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
    @include('funds.status.status')
        {{-- Inicia pantalla de inicio --}}
        <br>
        <div class="bd-example bd-example-padded-bottom">
            {{-- <form action="monthfunds/import" method="post" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class = "form-group">
                                <input type="file" name="file" accept=".xlsx, .xls, .csv" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class = "form-group">
                                <button type="submit" class="btn btn-primary">Importar Excel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form> --}}
            @if ($perm_btn['addition']==1)
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class = "form-group">
                                <input type="file" name="excl" id="excl" accept=".xlsx, .xls, .csv" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class = "form-group">
                                <button class="btn btn-primary" onclick="importexc()">Importar Excel</button>
                                <button class="btn btn-primary" onclick="actualizarFondo()">Actualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class = "form-group">
                            <input class="form-check-input" type="checkbox" onclick="chkActive()" id="chkActive"> Mostrar Inactivos
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProf">
                <thead>
                    <th class="text-center">Agente</th>
                    <th class="text-center">NUC</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">Estatus</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Opciones</th>
                </thead>

                <tbody>
                    @foreach ($nucs as $nuc)
                        <tr id="{{$nuc->id}}">
                            <td>{{$nuc->agname}}</td>
                            <td>{{$nuc->nuc}}</td>
                            <td>{{$nuc->name}}</td>
                            @if($nuc->active_stat == 0)
                                <td style="color: red">INACTIVO</td>
                            @else
                                <td style="color: green">ACTIVO</td>
                            @endif
                            <td>
                                <button class="btn btn-info" style="background-color: #{{$nuc->color}}; border-color: #{{$nuc->color}}" onclick="opcionesEstatus({{$nuc->id}},{{$nuc->statId}})">{{$nuc->estatus}}</button>
                            </td>
                            <td>
                                <a href="#|" class="btn btn-primary" onclick="nuevoMovimiento({{$nuc->id}})" >Movimientos</a>
                                <button href="#|" class="btn btn-warning" onclick="editarNuc({{$nuc->id}})" ><i class="fas fa-edit"></i></button>
                                @if ($perm_btn['erase']==1)
                                    <button href="#|" class="btn btn-danger" onclick="deleteFund({{$nuc->id}})" ><i class="fa fa-trash"></i></button>
                                    {{-- <a href="#|" class="btn btn-primary" data-toggle="modal" data-target="#myModal2" >Movimientos</a> --}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('head')
    <script src="{{URL::asset('js/funds/monthfund.js')}}"></script>
@endpush
