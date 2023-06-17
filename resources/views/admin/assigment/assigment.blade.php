@extends('home')
<head>
    <title>Asignaciones | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Asignaciones</h1></div>
    <br><br>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <button type="button" onclick="nonAssigned()" class="btn btn-primary">Asignar Contrato</button>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <div style="max-width: auto; margin: auto;">
        {{-- modal contratos por agente| --}}
        <div id="assigmentModal" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Asignaciones</h4>
                        <button type="button" class="close" onclick="cerrarmodal('#assigmentModal')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                        <table class="table table-striped table-hover text-center" id="tableClients">
                                            <thead>
                                                <th class="text-center">NUC/Obligación</th>
                                                <th class="text-center">Fondo</th>
                                                <th class="text-center">Cliente</th>
                                                <th class="text-center">Opciones</th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cerrarmodal('#assigmentModal')">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- modal contratos sin asignación| --}}
        <div id="agentsModal" class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Asignaciones</h4>
                        <button type="button" class="close" onclick="cerrarmodal('#agentsModal')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Agentes</label>
                                            <select name="selectAgentmodal" id="selectAgentmodal" class="form-select" onchange="changeagent()">
                                                <option value=0 hidden selected>Selecciona una opción</option>
                                                @foreach ($agents as $id => $agent)
                                                    <option value='{{ $id }}'>{{ $agent }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cerrarmodal('#agentsModal')">Cancelar</button>
                        <button type="button" name="btnAssign" id="btnAssign" onclick="assign()" class="btn btn-primary" disabled>Asignar</button>
                    </div>

                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- Inicia pantalla de inicio --}}
        <div class="tab-content" id="mytabcontent">
            <div class="table-responsive" style="margin-bottom: 10px; max-width: auto; margin: auto;">
                <table class="table table-striped table-hover text-center" id="tbClient">
                    <thead>
                        <th class="text-center">Agente</th>
                        <th class="text-center">Correo</th>
                        @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                            <th class="text-center">Opciones</th>
                        @endif
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr id="{{$user->id}}">
                                <td>{{$user->name}} {{$user->firstname}} {{$user->lastname}}</td>
                                <td>{{$user->email}}</td>
                                @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                    <td>
                                        @if ($perm_btn['modify']==1)
                                            <a href="#|" class="btn btn-primary" onclick="assignment({{$user->id}})" >Ver Contratos</a>
                                            {{-- <button href="#|" class="btn btn-warning" onclick="editarCliente({{$client->id}})" ><i class="fas fa-edit"></i></button> --}}
                                        @endif
                                        @if ($perm_btn['erase']==1)
                                            {{-- <button href="#|" class="btn btn-danger" onclick="eliminarCliente({{$client->id}})"><i class="fas fa-trash"></i></button> --}}
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
    <script src="{{URL::asset('js/admin/assigment.js')}}"></script>
@endpush
