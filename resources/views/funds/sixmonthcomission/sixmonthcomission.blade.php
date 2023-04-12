@extends('home')
<head>
    <title>Comisiones LP | Trazenta</title>
</head>
{{-- @section('title','Perfiles') --}}
@section('content')
    <div class="text-center"><h1>Generar Comisiones del Fondo de Largo Plazo</h1></div>
    <div style="max-width: 100%; margin: auto;">
        {{-- modal Cálculo --}}
        <div id="myModalCalc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Comisiones</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarCalc()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                {{-- <div class="row align-items-center"> --}}

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Comisión</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" id="comition" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" name="comition" placeholder="Ingresa la comsion" value="1,300.00" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Mes</label>
                                            <input type="month" id="month" name="month" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Regimen</label> <br>
                                            <input id = "onoffRegime" type="checkbox" data-toggle="toggle" data-on = "Regimen General" data-off="RESICO" data-width="180" onchange=updateRegime()>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Saldo</label>
                                            <input type="text" id="balance" name="balance" placeholder="Saldo al cierre" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Monto bruto</label>
                                            <input type="text" id="b_amount" name="b_amount" placeholder="Monto bruto" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">IVA</label>
                                            <input type="text" id="iva" name="iva" placeholder="IVA" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">RET ISR</label>
                                            <input type="text" id="ret_isr" name="ret_isr" placeholder="RET ISR" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">RET IVA</label>
                                            <input type="text" id="ret_iva" name="ret_iva" placeholder="RET IVA" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Monto Neto</label>
                                            <input type="text" id="n_amount" name="n_amount" placeholder="Monto Neto" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelarCalc()">Cancelar</button>
                        <button type="button" onclick="fillCalc()" class="btn btn-primary" title="Calcular"><i class="fa-solid fa-arrows-rotate"></i></button>
                        <button type="button" hidden onclick="pay(1)" id="paybtn" class="btn btn-primary" title="Marcar como pagado"><i class="fa-solid fa-check"></i> <i class="fa-solid fa-dollar-sign"></i></button>
                        <button type="button" hidden onclick="pay(0)" id="cancelbtn" class="btn btn-danger" title="Cancelar Pago"><i class="fa-solid fa-ban"></i> <i class="fa-solid fa-dollar-sign"></i></button>
                        <button type="button" onclick="calcular()" class="btn btn-primary" title="Descargar recibo PDF"><i class="fa-regular fa-file-pdf"></i></button>
                        {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
        {{-- Inicia pantalla de inicio --}}
        <br><br>
        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProf">
                <thead>
                    <th class="text-center">Usuarios-Agentes</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">Obligación</th>
                    <th class="text-center">Pagado</th>
                    <th class="text-center">Fecha</th>
                    @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                        <th class="text-center">Opciones</th>
                    @endif
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr id="{{$user->nucid}}">
                            <td>{{$user->usname}}</td>
                            <td>{{$user->clname}}</td>
                            <td>{{$user->nuc}}</td>
                            @if ($user->paid == 0)
                                <td style="color: red;">Falta de pago</td>
                            @else
                                <td style="color: green;">Pagado</td>
                            @endif
                            <td>{{$user->pay_date}}</td>
                            @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                <td>
                                    @if ($perm_btn['modify']==1)
                                        <button type="button" class="btn btn-success" onclick="abrirResumen({{$user->nucid}})"><i class="fas fa-calculator"></i></button>
                                        {{-- <a href="#|" class="btn btn-primary" data-toggle="modal" data-target="#myModal2" >Movimientos</a> --}}
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{URL::asset('js/currencyformat.js')}}" ></script>
@endsection
@push('head')
    <script src="{{URL::asset('js/funds/sixmonthcomission.js')}}"></script>
@endpush
