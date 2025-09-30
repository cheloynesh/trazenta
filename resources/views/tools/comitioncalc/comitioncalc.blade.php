@extends('home')
<head>
    <title>
        Cálculo de comisiones | Trazenta
    </title>
</head>
@section('content')
    <div class="text-center"><h1>Cálculo de comisiones</h1></div>
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
                            <form class="needs-validation" id="validationCP" onsubmit="return false;" novalidate>
                                <div class="row justify-content-center">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Compañía:</label>
                                            <select name="selectInsuranceCP" id="selectInsuranceCP" class="form-select" onchange="fundchange('CP')" required>
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($insurancesCP as $insurance)
                                                    <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrCP" id="selectCurrCP" class="form-select" disabled>
                                                <option selected hidden value=""></option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
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
                                </div>
                            </form>

                            <div class="row justify-content-center" style="border-top: 1px solid #ccc; padding-top: 30px;">
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <label for="">Obtendras un adelanto de los primeros 6 meses que equivale a:</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$</div>
                                            </div>
                                            <input type="text" name="fstpayCP" id="fstpayCP" value="" data-type="currency" class="form-control" placeholder="Adelanto 6 meses" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <label for="">Pasados los 6 meses tendrás una comision de arrastre equivalente a:</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$</div>
                                            </div>
                                            <input type="text" name="recCP" id="recCP" value="" data-type="currency" class="form-control" placeholder="Pago recurrente" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <div class="col-9">
                                <p style="color:red">El cálculo es un aproximado, puede variar dependiendo del tipo de cambio del mes en el que se paga y de cambios en los porcentajes de comisiones.</p>
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
                            <form class="needs-validation" id="validationLP" onsubmit="return false;" novalidate>
                                <div class="row justify-content-center">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Compañía:</label>
                                            <select name="selectInsuranceLP" id="selectInsuranceLP" class="form-select" onchange="fundchange('LP')" required>
                                                <option hidden selected value="">Selecciona una opción</option>
                                                @foreach ($insurancesLP as $insurance)
                                                    <option value='{{ $insurance->id }}'>{{ $insurance->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">Moneda:</label>
                                            <select name="selectCurrLP" id="selectCurrLP" class="form-select" disabled>
                                                <option selected hidden value=""></option>
                                                <option value="MXN">MXN</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
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
                                </div>
                            </form>
                            <div class="row justify-content-center" style="border-top: 1px solid #ccc; padding-top: 30px;">
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <label for="">Obtendras un pago único que equivale a:</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$</div>
                                            </div>
                                            <input type="text" name="fstpayLP" id="fstpayLP" value="" data-type="currency" class="form-control" placeholder="Comisión largo plazo" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <div class="col-9">
                                <p style="color:red">El cálculo es un aproximado, puede variar dependiendo del tipo de cambio del mes en el que se paga y de cambios en los porcentajes de comisiones.</p>
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
    <script src="{{URL::asset('js/tools/comitioncalc.js')}}"></script>
@endpush
