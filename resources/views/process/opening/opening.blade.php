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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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

                        <div class = "row" id = "fisicaInitial">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Nombre</label>
                                    <input type="text" id="name" name="nameEdit" class="form-control" placeholder="Nombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Apellido paterno</label>
                                    <input type="text" id="firstname" name="firstnameEdit" class="form-control" placeholder="Apellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Apellido materno</label>
                                    <input type="text" id="lastname" name="lastnameEdit" class="form-control" placeholder="Apellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">RFC</label>
                                    <input type="text" id="rfc" name="rfc" class="form-control" placeholder="RFC">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Compañía:</label>
                                    <select name="selectInsurance" id="selectInsurance" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($insurances as $id => $insurance)
                                            <option value='{{ $id }}'>{{ $insurance }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Tipo de Fondo:</label>
                                    <select name="selectType" id="selectType" class="form-select">
                                        <option selected hidden value="">Selecciona una opción</option>
                                        <option value="LP">Largo plazo</option>
                                        <option value="CP">Corto plazo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Moneda</label>
                                    <select name="selectCurrency" id="selectCurrency" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($currencies as $id => $currency)
                                            <option value='{{ $id }}'>{{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Domicilio Fiscal</label>
                                    <input type="text" id="contract" name="contract" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                    <button type="button" onclick="guardarInicial()" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
    {{-- Inicia pantalla de inicio --}}
    <div class="col-lg-12">
        <div class="row">
            @if ($perm_btn['modify']==1)
                <div class="col-md-12">
                    <div class="form-group">
                        @if ($perm_btn['addition']==1)
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" title="Nuevo Servicio"><i class="fas fa-plus"></i></button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div>
        Columnas: <a class="toggle-vis" data-column="6">Fecha límite</a> - <a class="toggle-vis" data-column="7">Entregado a agente</a> - <a class="toggle-vis" data-column="8">Entregado a finestra</a>
    </div>

    <br><br>

    <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
        <table class="table table-striped table-hover text-center" style="width:100%" id="example">
            <thead>
                <tr>
                    <th>Agente</th>
                    <th>Cliente</th>
                    <th>Fondo</th>
                    <th>Compañía</th>
                    <th>Contrato</th>
                    <th>Estatus</th>
                    <th>Fecha límite</th>
                    <th>Entregado a agente</th>
                    <th>Entregado a finestra</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
@endsection
@push('head')
<script src="{{URL::asset('js/process/opening.js')}}" ></script>
@endpush
