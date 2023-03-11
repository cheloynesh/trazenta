@extends('home')
<head>
    <title>Compañías | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Catálogo de Aseguradoras</h1></div>
    <div style="max-width: 1200px; margin: auto;">
        {{-- modal nueva aseguradora --}}
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Registro de Aseguradoras</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre</label>
                                            <input type="text" id="name" name="name" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                        <button type="button" onclick="guardarAseguradora()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- modal abrir ramos --}}
        <div id="myModalBranches" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Ramos</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarBranches()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                {{-- <div class="row align-items-center"> --}}
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Ramo</label>
                                            <select name="assignBranch" id="assignBranch" class="form-select"></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 align-self-end">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" onclick="assignBranches()">Asignar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                                            <table class="table table-striped table-hover text-center" id="tbassignBranches">
                                                <thead>
                                                    <th class="text-center">Nombre</th>
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
                        <button type="button" class="btn btn-secundary" onclick="cancelarBranches()">Cancelar</button>
                        <button type="button" id="btnNewItem" onclick="openNewBranch()" class="btn btn-primary">Nuevo Ramo</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal --}}
        {{-- modal nuevo ramo --}}
        <div id="myModalNewBranch" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Registro de Ramos</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarBranches()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre</label>
                                            <input type="text" id="nameBranch" name="nameBranch" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Días de tolerancia</label>
                                            <select id="select_days" name="select_days" class="form-select">
                                                <option value="15">15</option>
                                                <option value="30">30</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelarNewBranches()">Cancelar</button>
                        <button type="button" onclick="guardarRamo()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal --}}
        {{-- modal abrir planes --}}
        <div id="myModalPlans" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Planes</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarPlans()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                {{-- <div class="row align-items-center"> --}}
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Plan/Contrato</label>
                                            <select name="assignPlan" id="assignPlan" class="form-select"></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 align-self-end">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" onclick="assignatePlans()">Asignar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                                            <table class="table table-striped table-hover text-center" id="tbassignPlans">
                                                <thead>
                                                    <th class="text-center">Nombre</th>
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
                        <button type="button" class="btn btn-secundary" onclick="cancelarPlans()">Cancelar</button>
                        <button type="button" id="btnNewItem" onclick="openNewPlan()" class="btn btn-primary">Nuevo Plan</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal --}}
        {{-- modal| --}}
        <div id="myModalNewPlan" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Registro de Planes</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cancelarNewPlan()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre</label>
                                            <input type="text" id="namePlan" name="namePlan" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cancelarNewPlan()">Cancelar</button>
                        <button type="button" onclick="guardarNewPlan()" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        @include('admin.insurance.insuranceedit')
        {{-- Inicia pantalla de inicio --}}
        <div class="bd-example bd-example-padded-bottom">
            @if ($perm_btn['addition']==1)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Nuevo</button>
            @endif
        </div>
        <br><br>
          <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProfInsurance">
                <thead>
                    <th class="text-center">Nombre</th>
                    @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                        <th class="text-center">Opciones</th>
                    @endif
                </thead>

                <tbody>
                    @foreach ($insurances as $insurance)
                        <tr id="{{$insurance->id}}">
                            <td>{{$insurance->name}}</td>
                            @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                <td>
                                    @if ($perm_btn['modify']==1)
                                        <a href="#|" class="btn btn-primary" onclick="abrirBranches({{$insurance->id}})" >Asignar Ramo</a>
                                        <button href="#|" class="btn btn-warning" onclick="editarAseguradora({{$insurance->id}})" ><i class="fa fa-edit"></i></button>
                                    @endif
                                    @if ($perm_btn['erase']==1)
                                        <button href="#|" class="btn btn-danger" onclick="eliminarAseguradora({{$insurance->id}})"><i class="fa fa-trash"></i></button>
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
    <script src="{{URL::asset('js/admin/insurance.js')}}"></script>
@endpush
