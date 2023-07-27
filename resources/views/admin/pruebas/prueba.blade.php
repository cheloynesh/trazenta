@extends('home')
@section('content')
@include('admin.profile.profileedit')

    <div class="text-center"><h1>Pruebas</h1></div>
    {{-- <div style="max-width: 1200px; margin: auto;"> --}}
        {{-- modal| --}}
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Estatus</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Recogido:</label>
                                            <select name="selectStatus" id="selectStatus" class="form-select" onchange="showDate()">
                                                <option hidden selected value="0">Selecciona una opción</option>
                                                <option value="1">SI</option>
                                                <option value="2">PENDIENTE</option>
                                                <option value="3">FECHA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divDate" style="display: none">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Entregado a Agente:</label>
                                            <select name="selectStatus2" id="selectStatus2" class="form-select" onchange="showDate()">
                                                <option hidden selected value="0">Selecciona una opción</option>
                                                <option value="1">SI</option>
                                                <option value="2">PENDIENTE</option>
                                                <option value="3">FECHA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divDate">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Oficina:</label>
                                            <select name="selectStatus3" id="selectStatus3" class="form-select" onchange="showDate()">
                                                <option hidden selected value="0">Selecciona una opción</option>
                                                <option value="1">SI</option>
                                                <option value="2">PENDIENTE</option>
                                                <option value="3">FECHA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divDate">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Entregado a Finestra:</label>
                                            <select name="selectStatus4" id="selectStatus4" class="form-select" onchange="showDate()">
                                                <option hidden selected value="0">Selecciona una opción</option>
                                                <option value="1">SI</option>
                                                <option value="2">PENDIENTE</option>
                                                <option value="3">FECHA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divDate">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                        <button type="button" onclick="save()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- modal| --}}
        <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Estatus</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row" id="divDate">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Pendiente de entrega</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divDate">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Entregado</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divDate">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Recibido</label>
                                            <input type="date" id="auth" name="auth" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                        <button type="button" onclick="save()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- Inicia pantalla de inicio --}}

        <br><br>

        <div>
            Columnas: <a class="toggle-vis" data-column="4">Recogido</a> - <a class="toggle-vis" data-column="5">Entregado a agente</a> - <a class="toggle-vis" data-column="6">Oficina</a> - <a class="toggle-vis" data-column="7">Entregado a finestra</a>
            <input class="toggle-vis" data-column="4" id = "onoff" type="checkbox" checked data-toggle="toggle" data-on = "Si" data-off="No" data-width="120" data-offstyle="secondary">
        </div>

        <br><br>

        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" style="width:100%" id="example">
                <thead>
                    <tr>
                        <th>Agente</th>
                        <th>Cliente</th>
                        <th>Fondo</th>
                        <th>Estatus</th>
                        <th>Interno</th>
                        <th>Entregado a agente</th>
                        <th>Oficina</th>
                        <th>Entregado a finestra</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Jorge Enrique Quintanar Navarro</td>
                        <td>Aida Elena Guerrero Tapia</td>
                        <td>Corto Plazo</td>
                        <td>
                            <button id="1es" class="btn btn-info" style="background-color: #129c00; border-color: #129c00" onclick="changeRe2('1re')">ENTREGADO</button>
                        </td>
                        <td>
                            <button id="1re" class="btn btn-info" style="background-color: #c9bd0e; border-color: #c9bd0e" onclick="changeRe('1re')">SI</button>
                        </td>
                        <td>
                            <button id="1ea" class="btn btn-info" style="background-color: #129c00; border-color: #129c00" onclick="">2023-02-15</button>
                        </td>
                        <td>
                            <button id="1of" class="btn btn-info" style="background-color: #fc1303; border-color: #fc1303" onclick="">PENDIENTE</button>
                        </td>
                        <td>
                            <button id="1ef" class="btn btn-info" style="background-color: #129c00; border-color: #129c00" onclick="">2023-02-16</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    {{-- </div> --}}
@endsection
@push('head')
    <script src="{{URL::asset('js/admin/prueba.js')}}"></script>
@endpush






{{-- tabla --}}

{{-- <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
    <table class="table table-striped table-hover text-center" id="tbProf">
        <thead>
            <th class="text-center">Nombre</th>
            <th class="text-center">Opciones</th>
        </thead>

        <tbody>
            @foreach ($profiles as $profile)
                <tr id="{{$profile->id}}">
                    <td>{{$profile->name}}</td>
                    <td>
                        <button href="#|" class="btn btn-warning" onclick="editarperfil({{$profile->id}})" ><i class="fas fa-edit"></i></button>
                        <button href="#|" class="btn btn-danger" onclick="eliminarperfil({{$profile->id}})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> --}}

{{-- <div class="bs-example"> --}}
    {{-- <ul class="nav">
       <li class="active"><a data-toggle="tab" href="#sectionA" aria-selected="true"><h2>Buildings</h2></a></li>
       <li><a data-toggle="tab" href="#sectionB" aria-selected="false"><h2>Products/Services</h2></a></li>
    </ul> --}}

    {{-- <div class="tab-content"> --}}
        {{-- seccion a  --}}
       {{-- <div id="sectionA" class="tab-pane fade in active"> --}}


       {{-- </div> --}}
       <!--section b-->
       {{-- <div id="sectionB" class="tab-pane fade"> --}}

       {{-- </div> --}}
    {{-- </div> --}}
 {{-- </div> --}}
