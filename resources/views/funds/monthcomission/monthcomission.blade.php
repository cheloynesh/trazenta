@extends('home')
<head>
    <title>Comisiones CP | Trazenta</title>
</head>
{{-- @section('title','Perfiles') --}}
@section('content')
    <div class="text-center"><h1>Generar Comisiones del Fondo de Corto Plazo</h1></div>
    <div style="max-width: 1200px; margin: auto;">
        {{-- inicia modal --}}
        <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Comisiones</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarComision()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row align-items-end">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Tipo de Cambio</label>
                                            <input type="text" id="change" name="change" placeholder="Ingresa el tipo de cambio" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Mes</label>
                                            <input type="month" id="month" name="month" value="<?=date('Y-m')?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Dólares por cada 5000</label>
                                            <input type="text" id="dlls_com" name="dlls_com" placeholder="Ingresa la cantidad" class="form-control" onchange=updateDlls()>
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
                                <br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                            <table class="table table-striped table-hover text-center" id="tbProf1">
                                                <thead>
                                                    <th class="text-center">Nuc</th>
                                                    <th class="text-center">Cliente</th>
                                                    <th class="text-center">Opciones</th>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelarComision()">Cancelar</button>
                        <button type="button" onclick="calcular()" class="btn btn-primary">Descargar todo PDF</button>
                        {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                    </div>
                </div>
            </div>
        </div>
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
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Saldo promedio del mes</label>
                                            <input type="text" id="balance" name="balance" placeholder="Saldo al cierre" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Monto bruto</label>
                                            <input type="text" id="b_amount" name="b_amount" placeholder="Monto bruto" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">IVA</label>
                                            <input type="text" id="iva" name="iva" placeholder="IVA" class="form-control" disabled>
                                        </div>
                                    </div>
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
                                </div>
                                <div class="row">
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
                        <button type="button" onclick="calcular()" class="btn btn-primary">Descargar Desglose PDF</button>
                        {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
        {{-- modal auth --}}
        <div id="authModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Aplicar fecha</h4>
                        <button type="button" class="close" onclick="cerrarAuth()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="doc_row" style="display: none">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Adjuntar comprobante</label>
                                            <input type="file" name="pay_doc" id="pay_doc" accept="application/pdf" class="form-control">
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
        {{-- termina modal --}}
        {{-- Inicia pantalla de inicio --}}
        <br><br>
        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProf">
                <thead>
                    <th class="text-center">Usuarios-Agentes</th>
                    <th class="text-center">Enviado</th>
                    <th class="text-center">Pagado</th>
                    @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                        <th class="text-center">Opciones</th>
                    @endif
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr id="{{$user->uid}}">
                            <td>{{$user->uname}}</td>
                            <td>
                                @if ($user->invoice_flag == null)
                                    <button href="#|" class="btn btn-danger" onclick="setStatDate({{$user->uid}},1)" >Pendiente</button>
                                @else
                                    <button href="#|" class="btn btn-success" onclick="setNullDate({{$user->uid}},1)">{{$user->invoice_flag}}</button>
                                @endif
                            </td>
                            <td>
                                @if ($user->pay_flag == null)
                                    <button href="#|" class="btn btn-danger" onclick="setStatDate({{$user->uid}},2)">Sin Pago</button>
                                @else
                                    <button href="#|" class="btn btn-success btn-sm" onclick="setNullDate({{$user->uid}},2)">{{$user->pay_flag}}</button>
                                @endif
                            </td>
                            @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                <td>
                                    @if ($perm_btn['modify']==1)
                                        <a href="#|" class="btn btn-primary" onclick="abrirComision({{$user->uid}})" >Cálculo de Comision</a>
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
@endsection
@push('head')
    <script src="{{URL::asset('js/funds/monthcomission.js')}}"></script>
@endpush
