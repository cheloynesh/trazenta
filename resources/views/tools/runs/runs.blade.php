@extends('home')
<head>
    <title>
        Corridas | Trazenta
    </title>
</head>
@section('content')
    <div class="text-center"><h1>Corridas</h1></div>
    <br><br>

    <div style="max-width: auto; margin: auto;">
        <br>
        <div id="accordion">
            <div class="card" id="cardCP">
                <div class="card-header" style="color: black">
                    <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseCP">Corto plazo y mediano plazo</a>
                    </h4>
                </div>
                <div id="collapseCP" class="collapse">
                    <div class="card-body">
                        <div class="col-lg-12" id = "fisica">
                            <form class="needs-validation" id="validationCP" novalidate>
                                <div class="row justify-content-center">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Tipo:</label>
                                            <select name="selectInsuranceCP" id="selectInsuranceCP" class="form-select" onchange="fundchange('CP')" required>
                                                <option hidden selected value="">Selecciona una opción</option>
                                                <option value="CP">Corto plazo</option>
                                                <option value="MP">Mediano plazo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrCP" id="selectCurrCP" class="form-select" onchange="fundchange('CP')" required>
                                                <option selected hidden value="">Selecciona una opción</option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Tasa:</label>
                                            <div class="input-group mb-3">
                                                <input type="text" id="yieldCP" style="text-align:right;" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa" disabled>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center" style="padding-bottom: 20px;">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Monto apertura:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="openingCP" id="openingCP" value="" data-type="currency" class="form-control" placeholder="Monto apertura" onchange="calculoCP()" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Incremento mensual:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="incrementCP" id="incrementCP" value="" data-type="currency" class="form-control" placeholder="Incremento mensual" onchange="calculoCP()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group">
                                            <label for="">Reinversión:</label>
                                            <select name="selectReinvCP" id="selectReinvCP" class="form-select" onchange="calculoCP()">
                                                <option selected value="1">Si</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="">Años:</label>
                                            <select name="selectyearsCP" id="selectyearsCP" class="form-select" onchange="calculoCP()">
                                                <option selected value="1">1</option>
                                                @for ($var = 2; $var <= 100; $var++)
                                                    <option value={{$var}}>{{$var}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row" style="border-top: 1px solid #ccc; padding-top: 30px;">
                                <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                                    <table class="table table-striped table-hover text-center" style="width:100%" id="tbProfCP">
                                        <thead>
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">Saldo inicial</th>
                                            <th class="text-center">Rendimientos</th>
                                            <th class="text-center">Incrementos</th>
                                            <th class="text-center">Saldo final</th>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class = "col-lg-12 text-right">
                                        <button type="button" onclick="pdfCP()" class="btn btn-primary">Descargar PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="cardLP">
                <div class="card-header" style="color: black">
                    <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseLP">Largo plazo</a>
                    </h4>
                </div>
                <div id="collapseLP" class="collapse">
                    <div class="card-body">
                        <div class="col-lg-12" id = "fisica">
                            <form class="needs-validation" id="validationLP" novalidate>
                                <div class="row justify-content-center">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Tipo:</label>
                                            <select name="selectInsuranceLP" id="selectInsuranceLP" class="form-select" disabled>
                                                <option hidden selected value="LP">Largo plazo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrLP" id="selectCurrLP" class="form-select" onchange="fundchange('LP')" required>
                                                <option selected hidden value="">Selecciona una opción</option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Tasa:</label>
                                            <div class="input-group mb-3">
                                                <input type="text" id="yieldLP" style="text-align:right;" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa" disabled>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center" style="padding-bottom: 20px;">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Monto apertura:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="openingLP" id="openingLP" value="" data-type="currency" class="form-control" placeholder="Monto apertura" onchange="calculoLP()" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Obligaciones:</label>
                                            <input type="text" name="obligationsLP" id="obligationsLP" class="form-control" placeholder="Obligaciones" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Años:</label>
                                            <select name="selectyearsLP" id="selectyearsLP" class="form-select" onchange="calculoLP()">
                                                <option selected value="2">2</option>
                                                @for ($var = 4; $var <= 100; $var = $var + 2)
                                                    <option value={{$var}}>{{$var}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row justify-content-center" style="border-top: 1px solid #ccc; padding-top: 30px;">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Saldo primer año:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="firstLP" id="firstLP" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Utilidad primer año:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="firstutilityLP" id="firstutilityLP" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Saldo segundo año:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="secondLP" id="secondLP" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Utilidad segundo año:</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>
                                                <input type="text" name="secondutilityLP" id="secondutilityLP" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                                    <table class="table table-striped table-hover text-center" style="width:100%" id="tbProfLP">
                                        <thead>
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">Saldo inicial</th>
                                            <th class="text-center">Rendimientos</th>
                                            <th class="text-center">Saldo final</th>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class = "col-lg-12 text-right">
                                        <button type="button" onclick="pdfLP()" class="btn btn-primary">Descargar PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="cardBoth">
                <div class="card-header" style="color: black">
                    <h4 class="card-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseB">Combinación de fondos</a>
                    </h4>
                </div>
                <div id="collapseB" class="collapse">
                    <div class="card-body">
                        <div class="col-lg-12" id = "fisica">
                            <form class="needs-validation" id="validationB" novalidate>
                                <div class="row" style="padding-bottom: 20px;">
                                    <div class="col-lg-6">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Tipo:</label>
                                                    <select name="selectInsuranceCPB" id="selectInsuranceCPB" class="form-select" onchange="fundchange('CPB')" required>
                                                        <option hidden selected value="">Selecciona una opción</option>
                                                        <option value="CP">Corto plazo</option>
                                                        <option value="MP">Mediano plazo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Moneda:</label>
                                                    <select name="selectCurrCPB" id="selectCurrCPB" class="form-select" onchange="fundchange('CPB')" required>
                                                        <option selected hidden value="">Selecciona una opción</option>
                                                        <option value="MXN">MXN</option>
                                                        <option value="USD">USD</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Tasa:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="yieldCPB" style="text-align:right;" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa" disabled>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Monto apertura:</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">$</div>
                                                        </div>
                                                        <input type="text" name="openingCPB" id="openingCPB" value="" data-type="currency" class="form-control" placeholder="Monto apertura" onchange="calculoB()" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Incremento mensual:</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">$</div>
                                                        </div>
                                                        <input type="text" name="incrementCPB" id="incrementCPB" value="" data-type="currency" class="form-control" placeholder="Incremento mensual" onchange="calculoB()">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Reinversión:</label>
                                                    <select name="selectReinvCPB" id="selectReinvCPB" class="form-select" onchange="calculoB()">
                                                        <option selected value="1">Si</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Tipo:</label>
                                                    <select name="selectInsuranceLPB" id="selectInsuranceLPB" class="form-select" disabled>
                                                        <option hidden selected value="LP">Largo plazo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="">Moneda:</label>
                                                    <select name="selectCurrLPB" id="selectCurrLPB" class="form-select" onchange="fundchange('LPB')" required>
                                                        <option selected hidden value=""></option>
                                                        <option value="MXN">MXN</option>
                                                        <option value="USD">USD</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="">Tasa:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="yieldLPB" style="text-align:right;" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa" disabled>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="">Años:</label>
                                                    <select name="selectyearsB" id="selectyearsB" class="form-select" onchange="calculoB()">
                                                        <option selected value="2">2</option>
                                                        @for ($var = 4; $var <= 100; $var = $var + 2)
                                                            <option value={{$var}}>{{$var}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Monto apertura:</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">$</div>
                                                        </div>
                                                        <input type="text" name="openingLPB" id="openingLPB" value="" data-type="currency" class="form-control" placeholder="Monto apertura" onchange="calculoB()" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Obligaciones:</label>
                                                    <input type="text" name="obligationsLPB" id="obligationsLPB" class="form-control" placeholder="Obligaciones" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="">Reinversión en CT:</label>
                                                    <select name="selectReinvToCPB" id="selectReinvToCPB" class="form-select" onchange="calculoB()">
                                                        <option selected value="1">Si</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row justify-content-center" style="border-top: 1px solid #ccc; padding-top: 30px;">
                                <div class="col-lg-6">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="">Saldo primer año:</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input type="text" name="firstLPB" id="firstLPB" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="">Utilidad primer año:</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input type="text" name="firstutilityLPB" id="firstutilityLPB" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="">Saldo segundo año:</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input type="text" name="secondLPB" id="secondLPB" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="">Utilidad segundo año:</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input type="text" name="secondutilityLPB" id="secondutilityLPB" value="" data-type="currency" class="form-control" placeholder="Monto apertura" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 10px;">
                                <div class="table-responsive" style="margin-bottom: 10px; max-width: 100%; margin: auto;">
                                    <table class="table table-striped table-hover text-center" style="width:100%" id="tbProfB">
                                        <thead>
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">Saldo inicial</th>
                                            <th class="text-center">Rendimientos</th>
                                            <th class="text-center">Incrementos</th>
                                            <th class="text-center">Saldo final</th>
                                            <th class="text-center">Saldo inicial</th>
                                            <th class="text-center">Rendimientos</th>
                                            <th class="text-center">Saldo final</th>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="container-fluid bd-example-row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class = "col-lg-12 text-right">
                                        <button type="button" onclick="pdfB()" class="btn btn-primary">Descargar PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="{{URL::asset('js/currencyformat.js')}}" ></script>
@endsection
@push('head')
    <script src="{{URL::asset('js/tools/runs.js')}}"></script>
@endpush
