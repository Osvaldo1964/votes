<!-- Vertically centered scrollable modal -->
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" id="modalFormRol">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="titleModal">Nuevo Rol</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="formRol" name="formRol">
                <div class="form-group row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" placeholder="col-sm-4">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" placeholder="col-sm-6">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-8">
                        <input type="text" class="form-control" placeholder="col-sm-8">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" placeholder="col-sm-10">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="col">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary" id="btnActionForm" onclick="submitFormRol(event);">
                <span id="btnText">Guardar</span>
            </button>
        </div>
    </div>
</div>