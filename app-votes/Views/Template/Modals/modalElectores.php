<!-- Modal -->
<div class="modal fade" id="modalFormElector" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Elector</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formElector" name="formElector" class="form-horizontal">
                    <input type="hidden" id="id_elector" name="id_elector" value="">
                    <input type="hidden" id="insc_elector" name="insc_elector" value="1">
                    <p class="text-primary">Todos los campos son obligatorios.</p>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="lider_elector">Seleccion un Lider</label>
                            <select class="form-control selectpicker" id="lider_elector" name="lider_elector" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="ident_elector">Cédula</label>
                            <input type="text" class="form-control valid validNumber" id="ident_elector" name="ident_elector"
                                required="">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="ape1_elector">Primer apellido</label>
                            <input type="text" class="form-control valid validText" id="ape1_elector" name="ape1_elector"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ape2_elector">Segundo apellido</label>
                            <input type="text" class="form-control valid validText" id="ape2_elector" name="ape2_elector"
                                required="">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nom1_elector">Primer nombre</label>
                            <input type="text" class="form-control valid validText" id="nom1_elector" name="nom1_elector"
                                required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="nom2_elector">Segundo nombre</label>
                            <input type="text" class="form-control valid validText" id="nom2_elector" name="nom2_elector"
                                required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="telefono_elector">Teléfono</label>
                            <input type="text" class="form-control valid validNumber" id="telefono_elector"
                                name="telefono_elector" required="" onkeypress="return controlTag(event)">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="email_elector">Email</label>
                            <input type="email" class="form-control valid validEmail" id="email_elector" name="email_elector"
                                required="">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="direccion_elector">Dirección</label>
                            <input type="text" class="form-control valid validAddress" id="direccion_elector"
                                name="direccion_elector" required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="dpto_elector">Departamento</label>
                            <select class="form-control selectpicker" id="dpto_elector" name="dpto_elector" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="muni_elector">Municipio</label>
                            <select class="form-control selectpicker" id="muni_elector" name="muni_elector" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="estado_elector">Estado</label>
                            <select class="form-control selectpicker" id="estado_elector" name="estado_elector" required>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="col-md-12">
                            <p>Lugar de Votación</p>
                        </div>
                        <div class="input-group mb-3 col-md-3">
                            <span class="input-group-text" id="basic-addon1">Zona</span>
                            <input type="text" class="form-control" id="txtZona" aria-label="Zona" aria-describedby="basic-addon1" disabled />
                        </div>
                        <div class="input-group mb-3 col-md-4 ml-2">
                            <span class="input-group-text" id="basic-addon1">Puesto</span>
                            <input type="text" class="form-control" id="txtPuesto" aria-label="Puesto" aria-describedby="basic-addon1" disabled />
                        </div>
                        <div class="input-group mb-3 col-md-4 ml-2">
                            <span class="input-group-text" id="basic-addon1">Mesa</span>
                            <input type="text" class="form-control" id="txtMesa" aria-label="Mesa" aria-describedby="basic-addon1" disabled />
                        </div>

                    </div>
                    <hr>
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
<div class="modal fade" id="modalViewElector" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal">Datos del elector</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Lider:</td>
                            <td id="celLider">Larry</td>
                        </tr>
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
                            <td>Departamento:</td>
                            <td id="celDpto">Larry</td>
                        </tr>
                        <tr>
                            <td>Municipio:</td>
                            <td id="celMuni">Larry</td>
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