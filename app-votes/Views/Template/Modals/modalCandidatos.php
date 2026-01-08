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
                    <input type="hidden" id="id_candidato" name="id_candidato" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>

                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="ident_candidato">Cédula</label>
                            <input type="text" class="form-control valid validNumber" id="ident_candidato"
                                name="ident_candidato" required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ape1_candidato">Primer Apellido</label>
                            <input type="text" class="form-control valid validText" id="ape1_candidato"
                                name="ape1_candidato" required="">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ape2_candidato">Segundo Apellido</label>
                            <input type="text" class="form-control valid validText" id="ape2_candidato"
                                name="ape2_candidato" required="">
                        </div>
                        <div class="form-group col-md-">
                            <label for="nom1_candidato">Primer Nombre</label>
                            <input type="text" class="form-control valid validText" id="nom1_candidato"
                                name="nom1_candidato" required="">
                        </div>
                        <div class="form-group col-md-">
                            <label for="nom2_candidato">Segundo Nombre</label>
                            <input type="text" class="form-control valid validText" id="nom2_candidato"
                                name="nom2_candidato" required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="telefono_candidato">Teléfono</label>
                            <input type="text" class="form-control valid validNumber" id="telefono_candidato"
                                name="telefono_candidato" required="" onkeypress="return controlTag(event)">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="email_candidato">Email</label>
                            <input type="email" class="form-control valid validEmail" id="email_candidato"
                                name="email_candidato" required="">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="direccion_candidato">Dirección</label>
                            <input type="text" class="form-control valid validAddress" id="direccion_candidato"
                                name="direccion_candidato" required="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="dpto_candidato">Departamento</label>
                            <select class="form-control selectpicker" id="dpto_candidato" name="dpto_candidato"
                                data-live-search="true" data-size="5" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="muni_candidato">Municipio</label>
                            <select class="form-control selectpicker" id="muni_candidato" name="muni_candidato"
                                data-live-search="true" data-size="5" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="curul_candidato">Curul</label>
                            <select class="form-control selectpicker" id="curul_candidato" name="curul_candidato"
                                data-live-search="true" data-size="5" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="partido_candidato">Partido Político</label>
                            <select class="form-control selectpicker" id="partido_candidato" name="partido_candidato"
                                data-live-search="true" data-size="5" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="estado_candidato">Estado</label>
                            <select class="form-control selectpicker" id="estado_candidato" name="estado_candidato"
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