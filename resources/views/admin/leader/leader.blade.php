@extends('home')
<head>
    <title>Líderes | Trazenta</title>
</head>
@section('content')
    <div class="text-center"><h1>Líderes</h1></div>
    <br><br>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <button type="button" onclick="noLeader()" class="btn btn-primary">Designar Líder</button>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <div style="max-width: auto; margin: auto;">
        {{-- modal contratos por agente| --}}
        <div id="leaderModal" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Líderes</h4>
                        <button type="button" class="close" onclick="closeModal('#leaderModal')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                        <table class="table table-striped table-hover text-center" id="tableNoLeader">
                                            <thead>
                                                <th class="text-center">Agente</th>
                                                <th class="text-center">Correo</th>
                                                <th class="text-center">Opciones</th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="closeModal('#leaderModal')">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- modal contratos sin asignación| --}}
        <div id="agentsModal" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Asignaciones</h4>
                        <button type="button" class="close" onclick="cerrarmodal('#agentsModal')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                            <input type="month" id="month" name="month" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="table-responsive" style="margin-bottom: 10px; max-width: 1200px; margin: auto;">
                                        <table class="table table-striped table-hover text-center" id="tableAgents">
                                            <thead>
                                                <th class="text-center">Agente</th>
                                                <th class="text-center">Correo</th>
                                                <th class="text-center">Opciones</th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="closeModal('#agentsModal')">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>
        {{-- fin modal| --}}
        {{-- modal Cálculo --}}
        <div id="myModalCalc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="gridModalLabek">Comisiones</h4>
                        <button type="button" class="close" aria-label="Close" onclick="cerrarmodal('#myModalCalc')"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                {{-- <div class="row align-items-center"> --}}
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Saldo Total</label>
                                            <input type="text" id="b_amount" name="b_amount" placeholder="Monto bruto" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Saldo Total Dolares</label>
                                            <input type="text" id="dll_conv" name="dll_conv" placeholder="IVA" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">/5000</label>
                                            <input type="text" id="usd_invest" name="usd_invest" placeholder="RET ISR" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">*Dolares</label>
                                            <input type="text" id="usd_invest1" name="usd_invest1" placeholder="RET IVA" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">70%</label>
                                            <input type="text" id="seventy" name="seventy" placeholder="Monto Neto" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">30%</label>
                                            <input type="text" id="thirty" name="thirty" placeholder="Monto Neto" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secundary" onclick="cerrarmodal('#myModalCalc')">Cancelar</button>
                        {{-- <button type="button" onclick="guardarperfil()" class="btn btn-primary">Exportar PDF</button> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- termina modal --}}
        {{-- Inicia pantalla de inicio --}}
        <div class="tab-content" id="mytabcontent">
            <div class="table-responsive" style="margin-bottom: 10px; max-width: auto; margin: auto;">
                <table class="table table-striped table-hover text-center" id="tableLeaders">
                    <thead>
                        <th class="text-center">Agente</th>
                        <th class="text-center">Correo</th>
                        <th class="text-center">Opciones</th>
                    </thead>

                    <tbody>
                        @foreach ($leaders as $leader)
                            <tr id="{{$leader->id}}">
                                <td>{{$leader->name}}</td>
                                <td>{{$leader->email}}</td>
                                <td>
                                    <a href="#|" class="btn btn-primary" onclick="viewAgents({{$leader->id}})" >Ver Agentes</a>
                                    <a href="#|" class="btn btn-primary" onclick="assignment({{$leader->id}})" >Asignar</a>
                                    <button type="button" class="btn btn-danger" onclick="deleteLeader({{$leader->id}})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('head')
    <script src="{{URL::asset('js/admin/leader.js')}}"></script>
@endpush
