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
                                        <label for="">Moneda:</label>
                                        <select name="selectCurrency" id="selectCurrency" class="form-select">
                                            <option hidden selected>Selecciona una opción</option>
                                            <option>MXN</option>
                                            <option>USD</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
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
                        <h4 class="modal-title" id="gridModalLabek">Nuevo nuc fondo largo plazo</h4>
                        <button type="button" class="close" onclick="cerrarNucSixMonth()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Nuc</label>
                                            <input type="text" id="nucSixMonth" name="nucSixMonth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Monto</label>
                                            <input type="text" id="amountSixMonth" name="amountSixMonth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrencySixMonth" id="selectCurrencySixMonth" class="form-select">
                                                <option hidden selected>Selecciona una opción</option>
                                                <option>MXN</option>
                                                <option>USD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha del primer pago</label>
                                            <input type="date" id="initial_date" name="initial_date" class="form-control" placeholder="Fecha de aplicación">
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
                                            <button href="#|" class="btn btn-warning" onclick="editarCliente({{$client->id}})" ><i class="fa-solid fa-pen-to-square"></i></button>
                                        @endif
                                        @if ($perm_btn['erase']==1)
                                            <button href="#|" class="btn btn-danger" onclick="eliminarCliente({{$client->id}})"><i class="fa-solid fa-trash"></i></button>
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
@endsection
@push('head')
    <script src="{{URL::asset('js/admin/client.js')}}"></script>
@endpush
