<div class="modal fade modalPermisos" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title h4">Permisos Roles de Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="tile">
            <form action="" id="formPermisos" name="formPermisos" onsubmit="event.preventDefault(); fntSavePermisos();">
              <input type="hidden" id="idrol" name="idrol" value="" required="">

              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Módulo</th>
                      <th>Ver</th>
                      <th>Crear</th>
                      <th>Actualizar</th>
                      <th>Eliminar</th>
                    </tr>
                  </thead>
                  <tbody id="contentAjax">
                  </tbody>
                </table>
              </div>
              <div class="text-center">
                <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"
                    aria-hidden="true"></i> Guardar</button>
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i
                    class="app-menu__icon fas fa-sign-out-alt" aria-hidden="true"></i> Salir</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>