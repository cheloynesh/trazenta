<div id="modalSrcNuc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="gridModalLabek">Buscar Clientes</h4>
                <button type="button" class="close" onclick="cerrarModal('#modalSrcNuc')"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid bd-example-row">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center" id="srcNuc">
                            <thead>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Contrato</th>
                                <th class="text-center">Accion</th>

                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secundary" onclick="cerrarModal('#modalSrcNuc')">Cancelar</button>
            </div>
        </div>
    </div>
</div>
