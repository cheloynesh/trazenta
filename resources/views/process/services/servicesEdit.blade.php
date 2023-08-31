<div id="myModaledit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="gridModalLabek">Editar Servicio</h4>
                <button type="button" class="close" onclick="cerrarModal('#myModaledit')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid bd-example-row">

                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fondo:</label>
                                <select name="selectFund1" id="selectFund1" class="form-select">
                                    <option hidden selected value=0>Selecciona una opción</option>
                                    <option value="CP">CP</option>
                                    <option value="LP">LP</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Contrato</label>
                                <input type="text" id="nuc_edit1" class="form-control" disabled placeholder="Contrato">
                            </div>
                        </div>
                        <div class="col-md-3">
                            @if ($perm_btn['modify']==1)
                                <label for="">Cambiar</label>
                                <button type="button" id="btnSrcNuc1" class="btn btn-primary" onclick="buscarnuc(1)">Buscar</button>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Servicio</label>
                                <select name="selectService1" id="selectService1" class="form-select">
                                    <option hidden selected value="">Selecciona una opción</option>
                                    @foreach ($services_types as $id => $type)
                                        <option value='{{ $id }}'>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Tipo de Servicio</label>
                                <select name="selectType1" id="selectType1" class="form-select">
                                    <option hidden selected value="">Selecciona una opción</option>
                                    <option value=0>DIGITAL</option>
                                    <option value=1>ENTREGA ORIGINAL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Entregado</label>
                                <select name="selectDelivered1" id="selectDelivered1" class="form-select">
                                    <option hidden selected value="">Selecciona una opción</option>
                                    <option value=0>FALTANTE</option>
                                    <option value=1>ENTREGADO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secundary" onclick="cerrarModal('#myModaledit')">Cancelar</button>
                @if ($perm_btn['modify']==1)
                    <button type="button" onclick="actualizarServicio()" class="btn btn-primary">Guardar</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="myModalStatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="gridModalLabek">Estatus:</h4>
                <button type="button" onclick="cerrarModal('#myModalStatus')" class="close" aria-label="Close">&times;</button>
            </div>

            <div class="modal-body">
                <div class="container-fluid bd-example-row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Estatus:</label>
                                    <select name="selectStatus" id="selectStatus" class="form-select" onchange="statusChange()">
                                        <option hidden selected value="">Selecciona una opción</option>
                                        @foreach ($cmbStatus as $id => $status)
                                            <option value='{{ $id }}'>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="divDates">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Temporalidad de Pago:</label>
                                        <select name="selectTime" id="selectTime" class="form-select" onchange="dateChange()">
                                            <option selected value=5>5</option>
                                            <option value=10>10</option>
                                            <option value=15>15</option>
                                            <option value=21>21</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha de Atendido:</label>
                                        <input type="date" id="attendDate" name="attendDate" class="form-control" onchange="dateChange()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Fecha de Pago:</label>
                                        <input type="date" id="payDate" name="payDate" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal('#myModalStatus')" class="btn btn-secundary">Cancelar</button>
                @if ($perm_btn['modify']==1)
                    <button type="button" onclick="actualizarEstatus()" class="btn btn-primary">Guardar</button>
                @endif
            </div>
        </div>
    </div>
</div>
