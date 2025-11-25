<div id="myModaledit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="gridModalLabek">Actualizar Fondo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelarEditar()"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid bd-example-row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nombre</label>
                                    <input type="text" id="name1" name="name1" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Tipo de fondo:</label>
                                    <select name="selectType" id="selectType1" class="form-select">
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
                                    <select name="selectCurr1" id="selectCurr1" class="form-select">
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
                                    <label for="">Tasa:</label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="yield1" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa">
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
                                        <input type="text" id="yield_net1" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" value="" data-type="currency" class="form-control" placeholder="Tasa">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Activo:</label>
                                    <select name="selectActive1" id="selectActive1" class="form-select">
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
                                    <select name="selectFType1" id="selectFType1" class="form-select">
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
                <button type="button" onclick="cancelarEditar()" class="btn btn-secundary" data-dismiss="modal">Cancelar</button>
                <button type="button" onclick="actualizarAseguradora()" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
