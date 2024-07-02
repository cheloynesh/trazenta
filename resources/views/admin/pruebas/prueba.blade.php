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
        {{-- Inicia pantalla de inicio --}}
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
                                <div class="row align-items-end">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Monto</label>
                                            <input type="text" id="amount" name="amount" placeholder="Ingresa el monto" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Conducto de Apertura:</label>
                                            <select name="selectCharge" id="selectCharge" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($charges as $id => $charge)
                                                    <option value='{{ $id }}'>{{ $charge }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Fecha de aplicación</label>
                                            <input type="date" id="initial_date" name="initial_date" class="form-control" placeholder="Fecha de aplicación">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <div class="d-grid gap-2 col-12 mx-auto">
                                                <button type="button" class="btn btn-primary" onclick="guardarMovimiento()">Agregar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                            <table class="table table-striped table-hover text-center" id="tbProf1">
                                                <thead>
                                                    <th class="text-center">Monto</th>
                                                    <th class="text-center">Forma de pago</th>
                                                    <th class="text-center">Opciones</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>$200,000.00</td>
                                                        <td>TRANSFERENCIA</td>
                                                        <td>
                                                            <button href="#|" class="btn btn-warning" onclick="editarApertura()" ><i class="fas fa-edit"></i></button>
                                                            <button href="#|" class="btn btn-danger" onclick="eliminarApertura()"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
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

        <br><br>

        <div class="col-md-12">
            <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="nuevo()" title="Nuevo Servicio"><i class="fas fa-plus"></i></button>
            </div>
        </div>

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
