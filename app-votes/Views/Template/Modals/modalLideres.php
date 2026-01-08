<!-- Modal -->
<div class="modal fade" id="modalFormLider" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Lider</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formLider" name="formLider" class="form-horizontal">
                    <input type="hidden" id="id_lider" name="id_lider" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>

                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="ident_lider">Cédula</label>
                            <input type="text" class="form-control valid validNumber" id="ident_lider"
                                name="ident_lider" required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ape1_lider">Primer apellido</label>
                            <input type="text" class="form-control valid validText" id="ape1_lider" name="ape1_lider"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ape2_lider">Segundo apellido</label>
                            <input type="text" class="form-control valid validText" id="ape2_lider" name="ape2_lider"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="nom1_lider">Primer nombre</label>
                            <input type="text" class="form-control valid validText" id="nom1_lider" name="nom1_lider"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="nom2_lider">Segundo nombre</label>
                            <input type="text" class="form-control valid validText" id="nom2_lider" name="nom2_lider"
                                required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="telefono_lider">Teléfono</label>
                            <input type="text" class="form-control valid validNumber" id="telefono_lider"
                                name="telefono_lider" required="" onkeypress="return controlTag(event)">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="email_lider">Email</label>
                            <input type="email" class="form-control valid validEmail" id="email_lider"
                                name="email_lider" required="">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="direccion_lider">Dirección</label>
                            <input type="text" class="form-control valid validAddress" id="direccion_lider"
                                name="direccion_lider" required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="dpto_lider">Departamento</label>
                            <select class="form-control selectpicker" id="dpto_lider" name="dpto_lider"
                                data-live-search="true" data-size="5" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="muni_lider">Municipio</label>
                            <select class="form-control selectpicker" id="muni_lider" name="muni_lider"
                                data-live-search="true" data-size="5" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="estado_lider">Estado</label>
                            <select class="form-control selectpicker" id="estado_lider" name="estado_lider"
                                data-size="5" required>
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
<div class="modal fade" id="modalViewLider" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal">Datos del usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
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
                            <td>Departamento:</td>
                            <td id="celDpto">Larry</td>
                        </tr>
                        <tr>
                            <td>Municipio:</td>
                            <td id="celMuni">Larry</td>
                        </tr>
                        <tr>
                            <td>Dirección:</td>
                            <td id="celDireccion">Larry</td>
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