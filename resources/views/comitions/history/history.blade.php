@extends('home')
<head>
    <title>Historial de Comisiones | Trazenta</title>
</head>
{{-- @section('title','Perfiles') --}}
@section('content')
    <div class="text-center"><h1>Historial de Comisiones</h1></div>
    <div style="max-width: 100%; margin: auto;">
        {{-- inicia modal --}}
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Comisiones</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelar('#myModal')"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                {{-- comision recurrente --}}
                                <div class="card" id="cardRec">
                                    <div class="card-header">
                                        <h4>Recurrente</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="container-fluid bd-example-row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                                            <table class="table table-striped table-hover text-center" id="tbRec">
                                                                <thead>
                                                                    <th class="text-center">Agente</th>
                                                                    <th class="text-center">Enviado</th>
                                                                    <th class="text-center">Pago</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                {{-- comisiones nuevas --}}
                                <div class="card" id="cardNC">
                                    <div class="card-header">
                                        <h4>Nuevos Contratos</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="container-fluid bd-example-row">
                                            <div class="col-lg-12">
                                                {{-- <div class="row align-items-end">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Tipo de Cambio</label>
                                                            <input type="text" id="change" name="change" placeholder="Ingresa el tipo de cambio" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Mes</label>
                                                            <input type="month" id="month" name="month" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Regimen</label> <br>
                                                            <select name="selectRegime" id="selectRegime" class="form-select" onchange=updateRegime()>
                                                                <option hidden selected>Selecciona una opción</option>
                                                                @foreach ($regimes as $id => $regime)
                                                                    <option value='{{ $id }}'>{{ $regime }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br> --}}
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                                            <table class="table table-striped table-hover text-center" id="tbNC">
                                                                <thead>
                                                                    <th class="text-center">Nuc</th>
                                                                    <th class="text-center">Cliente</th>
                                                                    <th class="text-center">Apertura</th>
                                                                    <th class="text-center">Enviado</th>
                                                                    <th class="text-center">Pago</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                {{-- comision de adelantos --}}
                                <div class="card" id="cardAd">
                                    <div class="card-header">
                                        <h4>Incrementos (Abonos)</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="container-fluid bd-example-row">
                                            <div class="col-lg-12">
                                                {{-- <div class="row align-items-end">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Tipo de Cambio</label>
                                                            <input type="text" id="change" name="change" placeholder="Ingresa el tipo de cambio" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Mes</label>
                                                            <input type="month" id="month" name="month" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Regimen</label> <br>
                                                            <select name="selectRegime" id="selectRegime" class="form-select" onchange=updateRegime()>
                                                                <option hidden selected>Selecciona una opción</option>
                                                                @foreach ($regimes as $id => $regime)
                                                                    <option value='{{ $id }}'>{{ $regime }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br> --}}
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                                            <table class="table table-striped table-hover text-center" id="tbAd">
                                                                <thead>
                                                                    <th class="text-center">NUC</th>
                                                                    <th class="text-center">Cliente</th>
                                                                    <th class="text-center">Fecha</th>
                                                                    <th class="text-center">Saldo anterior</th>
                                                                    <th class="text-center">Saldo actual</th>
                                                                    <th class="text-center">Moneda</th>
                                                                    <th class="text-center">Monto</th>
                                                                    <th class="text-center">Enviado</th>
                                                                    <th class="text-center">Pago</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                {{-- comision de largo plazo --}}
                                <div class="card" id="cardLP">
                                    <div class="card-header">
                                        <h4>Largo Plazo</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="container-fluid bd-example-row">
                                            <div class="col-lg-12">
                                                {{-- <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Comisión</label>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">$</div>
                                                                </div>
                                                                <input type="text" id="comitionAll" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" name="comitionAll" placeholder="Ingresa la comsion" value="1,300.00" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Mes</label>
                                                            <input type="month" id="monthAll" name="monthAll" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Regimen</label> <br>
                                                            <select name="selectRegimeAll" id="selectRegimeAll" class="form-select" onchange="updateRegime('#selectRegimeAll')">
                                                                <option hidden selected>Selecciona una opción</option>
                                                                @foreach ($regimes as $id => $regime)
                                                                    <option value='{{ $id }}'>{{ $regime }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br> --}}
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                                                            <table class="table table-striped table-hover text-center" id="tbLP">
                                                                <thead>
                                                                    <th class="text-center">Obligación</th>
                                                                    <th class="text-center">Cliente</th>
                                                                    <th class="text-center">Fecha</th>
                                                                    <th class="text-center">Enviado</th>
                                                                    <th class="text-center">Pago</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelar('#myModal')">Cancelar</button>
                        {{-- <button type="button" onclick="calcular()" class="btn btn-primary">Descargar todo PDF</button> --}}
                        {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal --}}
        {{-- modal auth --}}
        <div id="viewPdfModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Ver fecha</h4>
                        <button type="button" class="close" onclick="cancelar('#viewPdfModal')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <a href="" id="viewPDF" target="_blank" class="btn btn-primary" style="width: 100%">Ver Recibo</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <button type="button" onclick="setNullDate()" class="btn btn-primary" style="width: 100%">Remover Fecha</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelar('#viewPdfModal')">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
        {{-- Inicia pantalla de inicio --}}
        <br><br>
        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" style="width: 100%;" id="tbProf">
                <thead>
                    <th class="text-center">Agente</th>
                    <th class="text-center">Opciones</th>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr id="{{$user->uid}}">
                            <td>{{$user->aname}}</td>
                            <td><a href="#|" class="btn btn-primary" onclick="abrirComision({{$user->uid}},{{$user->rec_delay}},{{$user->contpp}},{{$user->contpa}},{{$user->lpnopay}})" >Ver</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
<script src="{{URL::asset('js/currencyformat.js')}}" ></script>
@endsection
@push('head')
    <script src="{{URL::asset('js/comitions/history.js')}}"></script>
@endpush
