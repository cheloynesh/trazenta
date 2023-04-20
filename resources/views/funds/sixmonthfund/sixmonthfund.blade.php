@extends('home')
<head>
    <title>Fondo LP | Trazenta</title>
</head>
{{-- @section('title','Perfiles') --}}
@section('content')
    <div class="text-center"><h1>Fondo Largo Plazo</h1></div>
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
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                            <table class="table table-striped table-hover text-center" id="tbProf1">
                                                <thead>
                                                    <th class="text-center">Numero de pago</th>
                                                    <th class="text-center">Fecha de pago</th>
                                                    <th class="text-center">Monto</th>
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
                        {{-- <button type="button" onclick="excel_nuc()" class="btn btn-primary">Exportar Excel</button> --}}
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <img src="{{ URL::asset('img/SpinnerLittle.gif') }}">
                                            <label> Se están procesando los datos, por favor espere.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
    @include('funds.status.status')
        {{-- Inicia pantalla de inicio --}}
        <br>
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
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <br>
        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProf">
                <thead>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">NUC</th>
                    <th class="text-center">Monto</th>
                    <th class="text-center">Moneda</th>
                    <th class="text-center">Fecha de Inicio</th>
                    <th class="text-center">Fecha de Fin</th>
                    {{-- <th class="text-center">Estatus</th> --}}
                    <th class="text-center">Opciones</th>
                </thead>

                <tbody>
                    @foreach ($nucs as $nuc)
                        <tr id="{{$nuc->id}}">
                            <td>{{$nuc->name}}</td>
                            <td>{{$nuc->nuc}}</td>
                            <td>{{$nuc->amount}}</td>
                            <td>{{$nuc->currency}}</td>
                            <td>{{$nuc->initial_date}}</td>
                            <td>{{$nuc->end_date}}</td>
                            {{-- <td>
                                <button class="btn btn-info" style="background-color: #{{$nuc->color}}; border-color: #{{$nuc->color}}" onclick="opcionesEstatus({{$nuc->id}},{{$nuc->statId}})">{{$nuc->estatus}}</button>
                            </td> --}}
                            <td>
                                <a href="#|" class="btn btn-primary" onclick="nuevoMovimiento({{$nuc->id}})" >Cuponera</a>
                                @if ($perm_btn['modify']==1)
                                    <button href="#|" class="btn btn-danger" onclick="eliminarNuc({{$nuc->id}})" ><i class="fa-solid fa-trash"></i></button>
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
    <script src="{{URL::asset('js/funds/sixmonthfund.js')}}"></script>
@endpush
