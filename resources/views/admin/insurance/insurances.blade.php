@extends('home')
<head>
    <title>Fondos | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Catálogo de Fondos</h1></div>
    <div style="max-width: 1200px; margin: auto;">
        {{-- modal nueva aseguradora --}}
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Registro de Fondos</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Nombre</label>
                                            <input type="text" id="name" name="name" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Tipo de fondo:</label>
                                            <select name="selectType" id="selectType" class="form-select">
                                                <option selected hidden value="">Selecciona una opción</option>
                                                <option value="LP">Largo plazo</option>
                                                <option value="CP">Corto plazo</option>
                                                <option value="MP">Mediano plazo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurr" id="selectCurr" class="form-select">
                                                <option selected hidden value="">Selecciona una opción</option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Tasa bruta:</label>
                                            <div class="input-group mb-3">
                                                <input type="text" id="yield" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Tasa neta:</label>
                                            <div class="input-group mb-3">
                                                <input type="text" id="yield_net" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Activo:</label>
                                            <select name="selectActive" id="selectActive" class="form-select">
                                                <option selected hidden value="">Selecciona una opción</option>
                                                <option value=1>Si</option>
                                                <option value=0>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Razón social:</label>
                                            <select name="selectType" id="selectType" class="form-select">
                                                <option hidden selected value="">Selecciona una opción</option>
                                                <option value=0>APORTACIONES</option>
                                                <option value=1>CAPORTA</option>
                                                <option value=2>OBCREDA</option>
                                                <option value=3>OBCREDA DE OCCIDENTE</option>
                                                <option value=4>OBDENA</option>
                                            </select>
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
        @include('admin.insurance.insuranceedit')
        {{-- Inicia pantalla de inicio --}}
        <div class="bd-example bd-example-padded-bottom">
            @if ($perm_btn['addition']==1)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fas fa-plus"></i></button>
            @endif
        </div>
        <br><br>
          <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" id="tbProfInsurance">
                <thead>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Tipo de fondo</th>
                    <th class="text-center">Moneda</th>
                    <th class="text-center">Tasa bruta</th>
                    <th class="text-center">Tasa neta</th>
                    <th class="text-center">Razón social</th>
                    <th class="text-center">Activo</th>
                    @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                        <th class="text-center">Opciones</th>
                    @endif
                </thead>

                <tbody>
                    @foreach ($insurances as $insurance)
                        <tr id="{{$insurance->id}}">
                            <td>{{$insurance->name}}</td>
                            <td>{{$insurance->fund_type}}</td>
                            <td>{{$insurance->fund_curr}}</td>
                            <td>{{$insurance->yield}}</td>
                            <td>{{$insurance->yield_net}}</td>
                            <td>{{$type[$insurance->type]}}</td>
                            <td>
                                @if ($insurance->active_fund==1) Si @else No @endif
                            </td>
                            @if ($perm_btn['modify']==1 || $perm_btn['erase']==1)
                                <td>
                                    @if ($perm_btn['modify']==1)
                                        <button href="#|" class="btn btn-warning" onclick="editarAseguradora({{$insurance->id}})" ><i class="fas fa-edit"></i></button>
                                    @endif
                                    @if ($perm_btn['erase']==1)
                                        <button href="#|" class="btn btn-danger" onclick="eliminarAseguradora({{$insurance->id}})"><i class="fas fa-trash"></i></button>
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
