<!-- Vertically centered scrollable modal -->
<div class="modal fade col-md-8" id="modalFormRol" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Rol</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!--begin::Horizontal Form-->
        <div class="card card-warning card-outline mb-4">
          <!--begin::Header-->
          <!--end::Header-->
          <!--begin::Form-->
          <form id="formRol" name="formRol">
            <!--begin::Body-->
            <div class="card-body">
              <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">Nombre</span>
                <input type="text" class="form-control" id="txtNombre" name="txtNombre" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
              </div>
              <div class="input-group mb-3">
                <span class="input-group-text">Descripción</span>
                <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" aria-label="With textarea"></textarea>
              </div>

              <!--begin::Col-->
              <div class="input-group mb-3">
                <label class="input-group-text" for="inputGroupSelect01">Options</label>
                <select class="form-select" id="listEstado" name="listEstado">
                  <option selected>Seleccione...</option>
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>
              <!--end::Col-->
            </div>
            <!--end::Body-->
            <!--begin::Footer-->
            <div class="card-footer">
              <button type="submit" class="btn btn-success">Guardar</button>
              <button type="submit" class="btn btn-secondary float-end" data-dismiss="modal">Cancelar</button>
            </div>
            <!--end::Footer-->
          </form>
          <!--end::Form-->
        </div>
        <!--end::Horizontal Form-->
      </div>
    </div>
  </div>
</div>