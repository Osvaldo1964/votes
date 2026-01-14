<!-- Modal -->
<div class="modal fade" id="modalFormModulo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Módulo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formModulo" name="formModulo">
                            <input type="hidden" id="idModulo" name="idModulo" value="">
                            <div class="form-group">
                                <label class="control-label">Título</label>
                                <input class="form-control" id="txtTitulo" name="txtTitulo" type="text"
                                    placeholder="Título del módulo" required="">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Descripción</label>
                                <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="2"
                                    placeholder="Descripción del módulo" required=""></textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleSelect1">Estado</label>
                                <select class="form-control" id="listStatus" name="listStatus" required="">
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                            <div class="tile-footer">
                                <button id="btnActionForm" class="btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span>
                                </button>&nbsp;&nbsp;&nbsp;<button class="btn btn-secondary" type="button"
                                    data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>