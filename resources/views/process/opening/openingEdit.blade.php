    {{-- modal| --}}
    <div id="myModaledit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="gridModalLabek">Registro de Apertura</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid bd-example-row">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Agente:</label>
                                    <select name="selectAgent1" id="selectAgent1" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($agents as $id => $agent)
                                            <option value='{{ $id }}'>{{ $agent }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class = "row" id = "fisicaInitial">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Nombre</label>
                                    <input type="text" id="name1" name="name1" class="form-control" placeholder="Nombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Apellido paterno</label>
                                    <input type="text" id="firstname1" name="firstname1" class="form-control" placeholder="Apellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Apellido materno</label>
                                    <input type="text" id="lastname1" name="lastname1" class="form-control" placeholder="Apellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">RFC</label>
                                    <input type="text" id="rfc1" name="rfc1" class="form-control" placeholder="RFC">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Compañía:</label>
                                    <select name="selectInsurance1" id="selectInsurance1" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($insurances as $id => $insurance)
                                            <option value='{{ $id }}'>{{ $insurance }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Monto:</label>
                                    <input type="text" id="amount1" name="amount1" class="form-control" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Moneda</label>
                                    <select name="selectCurrency1" id="selectCurrency1" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($currencies as $id => $currency)
                                            <option value='{{ $id }}'>{{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Tipo Solicitud:</label>
                                    <select name="selectAppli1" id="selectAppli1" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($applications as $id => $appli)
                                            <option value='{{ $id }}'>{{ $appli }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Forma de Pago:</label>
                                    <select name="selectPaymentform1" id="selectPaymentform1" class="form-select">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($paymentForms as $id => $payment_form)
                                            <option value='{{ $id }}'>{{ $payment_form }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Domicilio Fiscal</label>
                                    <input type="text" id="address1" name="address1" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">NUC/Contrato</label>
                                    <input type="text" id="nuc1" name="nuc1"  class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secundary" onclick="cancelEdit()">Cancelar</button>
                    @if ($perm_btn['modify']==1)
                        <button type="button" onclick="actualizarApertura()" class="btn btn-primary">Guardar</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- fin modal| --}}
