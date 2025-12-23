<!-- Modal -->
<div class="modal fade" id="modalFormCandidato" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Candidato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCandidato" name="formCandidato" class="form-horizontal">
                    <input type="hidden" id="idCandidato" name="idCandidato" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>

                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="txtCedula">Cédula</label>
                            <input type="text" class="form-control valid validNumber" id="txtCedula" name="txtCedula"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="txtApe1">Primer Apellido</label>
                            <input type="text" class="form-control valid validText" id="txtApe1" name="txtApe1"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="txtApe2">Segundo Apellido</label>
                            <input type="text" class="form-control valid validText" id="txtApe2" name="txtApe2"
                                required="">
                        </div>
                        <div class="form-group col-md-">
                            <label for="txtNom1">Primer Nombre</label>
                            <input type="text" class="form-control valid validText" id="txtNom1" name="txtNom1"
                                required="">
                        </div>
                        <div class="form-group col-md-">
                            <label for="txtNom2">Segundo Nombre</label>
                            <input type="text" class="form-control valid validText" id="txtNom2" name="txtNom2"
                                required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="txtTelefono">Teléfono</label>
                            <input type="text" class="form-control valid validNumber" id="txtTelefono"
                                name="txtTelefono" required="" onkeypress="return controlTag(event)">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="txtEmail">Email</label>
                            <input type="email" class="form-control valid validEmail" id="txtEmail" name="txtEmail"
                                required="">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="txtDireccion">Dirección</label>
                            <input type="text" class="form-control valid validAddress" id="txtDireccion"
                                name="txtDireccion" required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="listCurul">Curul</label>
                            <select class="form-control selectpicker" id="listCurul" name="listCurul" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="listPartido">Partido Político</label>
                            <select class="form-control selectpicker" id="listPartido" name="listPartido" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="listEstado">Estado</label>
                            <select class="form-control selectpicker" id="listEstado" name="listEstado" required>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="tile-footer">
                        <button id="btnActionForm" class="btn btn-primary" type="submit"><i
                                class="fa fa-fw fa-lg fa-check-circle"></i><span
                                id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-danger" type="button" data-dismiss="modal"><i
                                class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalViewCandidato" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal">Datos del Candidato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Cédula:</td>
                            <td id="celIdent">Jacob</td>
                        </tr>
                        <tr>
                            <td>Nombres:</td>
                            <td id="celNombre">Jacob</td>
                        </tr>
                        <tr>
                            <td>Apellidos:</td>
                            <td id="celApellido">Jacob</td>
                        </tr>
                        <tr>
                            <td>Teléfono:</td>
                            <td id="celTelefono">Larry</td>
                        </tr>
                        <tr>
                            <td>Email (Usuario):</td>
                            <td id="celEmail">Larry</td>
                        </tr>
                        <tr>
                            <td>Dirección:</td>
                            <td id="celDireccion">Larry</td>
                        </tr>
                        <tr>
                            <td>Curul:</td>
                            <td id="celCurul">Larry</td>
                        </tr>
                        <tr>
                            <td>Partido:</td>
                            <td id="celPartido">Larry</td>
                        </tr>
                        <tr>
                            <td>Estado:</td>
                            <td id="celEstado">Larry</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>