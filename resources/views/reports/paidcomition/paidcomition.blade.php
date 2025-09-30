@extends('home')
{{-- @section('title','Servicios') --}}
<head>
    <title>Ver Comisiones | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Ver Comisiones</h1></div>
    <div style="max-width: 1200px; margin: auto;">
        {{-- Inicia pantalla de inicio --}}
        <div class="bd-example bd-example-padded-bottom">
            @if ($perm_btn['addition']==1)
            <div class="col-md-12">
                <form class="needs-validation" id="validationSelect" novalidate>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha de inicio*</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Fecha de nacimiento" onchange="selectEndDate('start_date')" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha de fin*</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" placeholder="Fecha de nacimiento" onchange="selectEndDate('end_date')" required>
                            </div>
                        </div>
                        <div class="col-md-3" style="align-content: end">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" onclick="getComitions()">Buscar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
        <br><br>
        <div class="card">
            <div class="card-header" style="color: black">
                Totales
            </div>
            <div class="card-body">
                <div class="col-lg-12" id = "fisica">
                    <form class="needs-validation" id="validationClient" novalidate>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Total recurrente</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input type="text" name="totalrec" id="totalrec" value="0" data-type="currency" class="form-control" placeholder="Total recurrente" disabled
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Total primer pago</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input type="text" name="totalfst" id="totalfst" value="0" data-type="currency" class="form-control" placeholder="Total primer pago" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Total abonos</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input type="text" name="totalabon" id="totalabon" value="0" data-type="currency" class="form-control" placeholder="Descuento" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Total largo plazo</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input type="text" name="totallp" id="totallp" value="0" data-type="currency" class="form-control" placeholder="Descuento" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" style="color: black">
                Agentes
            </div>
            <div class="card-body">
                <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                    <table class="table table-striped table-hover text-center" style="width:100%" id="tbProf">
                        <thead>
                            <th class="text-center">Agente</th>
                            <th class="text-center">Recurrente</th>
                            <th class="text-center">Primer pago</th>
                            <th class="text-center">Abonos</th>
                            <th class="text-center">Largo plazo</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{URL::asset('js/currencyformat.js')}}" ></script>
@endsection
@push('head')
    <script src="{{URL::asset('js/reports/paidcomition.js')}}"></script>
@endpush
