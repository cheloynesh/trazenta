@extends('home')
<head>
    <title>
        Flujo de Dinero | Trazenta
    </title>
</head>
@section('content')
    <div class="text-center"><h1>Reporte de Flujo de Dinero</h1></div>
    <br><br>
    {{-- modal columnas --}}
    <div id="myModalColumn" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Mostrar/Ocultar Columnas</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelar('#myModalColumn')"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Dinero Nuevo</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Dinero Nuevo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="cancelar('#myModalColumn')" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal columnas --}}

    <div style="max-width: auto; margin: auto;">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Trimestre</label>
                    <select class="form-select" id="selectQuarter" aria-label="Default select example" onchange="QuarterChange()">
                        <option selected hidden value="%">Selecciona una opción</option>
                        <option value="1">Primer Trimestre</option>
                        <option value="2">Segundo Trimestre</option>
                        <option value="3">Tercer Trimestre</option>
                        <option value="4">Cuarto Trimestre</option>
                        <option value="%">Todos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Mes Comercial</label>
                    <select class="form-select" id="month" aria-label="Default select example" onchange="MonthChange()">
                        <option selected hidden value="%">Selecciona una opción</option>
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                        <option value="%">Todos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Año</label>
                    <select class="form-select" id="selectYear" aria-label="Default select example" onchange="GetFilters()">
                    </select>
                    {{-- <input type="number" class="form-control" min="1900" max="2099" step="1" value="2016" /> --}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Fondo</label>
                    <select class="form-select" id="selectFund" aria-label="Default select example" onchange="GetFilters()">
                        <option selected hidden value="%">Selecciona una opción</option>
                        @foreach ($insurances as $id => $insurance)
                            <option value='{{ $id }}'>{{ $insurance }}</option>
                        @endforeach
                        <option value="%">Todos</option>
                    </select>
                </div>
            </div>
        </div>
        <br>
        {{-- <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">S-Ingresadas</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">P-Emitidas</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">P-Pagadas</label>
                </div>
            </div>
        </div>
        <div class="row" style="text-align: right">
            <div class="col-md-3">
                <div class="form-group">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label id="conting" style="font-weight: bold; font-size : 20px"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label id="contenit" style="font-weight: bold; font-size : 20px"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label id="contpay" style="font-weight: bold; font-size : 20px"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Prima 1er Recibo por Emitir</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Prima 1er Recibo por Pagar</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Prima 1er Recibo Pagado</label>
                </div>
            </div>
        </div>
        <div class="row" style="text-align: right">
            <div class="col-md-3">
                <div class="form-group">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label id="suming" style="font-weight: bold; font-size : 20px"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label id="sumemit" style="font-weight: bold; font-size : 20px"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label id="sumpay" style="font-weight: bold; font-size : 20px"></label>
                </div>
            </div>
        </div> --}}
        {{-- Inicia pantalla de inicio --}}
          <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
            <table class="table table-striped table-hover text-center" style="width:100%" id="tbProf">
                <thead>
                    <th class="text-center">Agente</th>
                    <th class="text-center">Nuevo CP</th>
                    <th class="text-center">Nuevo LP</th>
                    <th class="text-center">Suma Nuevo</th>
                    <th class="text-center">Salida CP</th>
                    <th class="text-center">Salida LP</th>
                    <th class="text-center">Suma Salida</th>
                    <th class="text-center">Reinv. CP</th>
                    <th class="text-center">Reinv. LP</th>
                    <th class="text-center">Suma Reinv.</th>
                </thead>

                <tbody>
                    @foreach ($moneyflow as $flow)
                        <tr id="{{$flow->AgentId}}">
                            <td>{{$flow->AgentName}}</td>
                            <td>{{$flow->NCP}}</td>
                            <td>{{$flow->NLP}}</td>
                            <td>{{$flow->ToN}}</td>
                            <td>{{$flow->OCP}}</td>
                            <td>{{$flow->OLP}}</td>
                            <td>{{$flow->ToO}}</td>
                            <td>{{$flow->RCP}}</td>
                            <td>{{$flow->RLP}}</td>
                            <td>{{$flow->ToR}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <canvas id="insuranceChart" width="400" height="400"></canvas>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <canvas id="branchesChart" width="400" height="400"></canvas>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <canvas id="statusChart" width="400" height="400"></canvas>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <canvas id="payChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('head')
    <script src="{{URL::asset('js/reports/moneyflow.js')}}"></script>
@endpush
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script> --}}
{{-- <script>
    var ctx = document.getElementById('myChart'); // node
    // var ctx = document.getElementById('myChart').getContext('2d'); // 2d context
    // var ctx = $('#myChart'); // jQuery instance
    // var ctx = 'myChart'; // element id

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
          label: '# of Votes',
          data: [12, 19, 3, 5, 2, 3],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
</script> --}}
