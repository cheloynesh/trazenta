@extends('home')
{{-- @section('title','Perfiles') --}}
@section('content')
    <div class="text-center"><h1>Fondo Largo Plazo</h1></div>
    <div style="max-width: 1200px; margin: auto;">
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
        {{-- modal autorizar --}}
    @include('funds.status.status')
        {{-- Inicia pantalla de inicio --}}
        <br>
        <br>
        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProf">
                <thead>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">NUC</th>
                    <th class="text-center">Fecha de Inicio</th>
                    <th class="text-center">Fecha de Fin</th>
                    {{-- <th class="text-center">Estatus</th> --}}
                    @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                        <th class="text-center">Opciones</th>
                    @endif
                </thead>

                <tbody>
                    @foreach ($nucs as $nuc)
                        <tr id="{{$nuc->id}}">
                            <td>{{$nuc->name}}</td>
                            <td>{{$nuc->nuc}}</td>
                            <td>{{$nuc->initial_date}}</td>
                            <td>{{$nuc->end_date}}</td>
                            {{-- <td>
                                <button class="btn btn-info" style="background-color: #{{$nuc->color}}; border-color: #{{$nuc->color}}" onclick="opcionesEstatus({{$nuc->id}},{{$nuc->statId}})">{{$nuc->estatus}}</button>
                            </td> --}}
                            @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                <td>
                                    @if ($perm_btn['modify']==1)
                                        <a href="#|" class="btn btn-primary" onclick="nuevoMovimiento({{$nuc->id}})" >Movimientos</a>
                                        {{-- <a href="#|" class="btn btn-warning" onclick="editarNuc({{$nuc->id}})" >Editar</a> --}}
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
    <script src="{{URL::asset('js/funds/sixmonthfund.js')}}"></script>
@endpush
