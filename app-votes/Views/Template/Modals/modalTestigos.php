<!-- Modal Testigos -->
<div class="modal fade" id="modalFormTestigo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Testigo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTestigo" name="formTestigo" class="form-horizontal">
                    <input type="hidden" id="idTestigo" name="idTestigo" value="">

                    <p class="text-primary">Seleccione al elector que ejercerá como testigo.</p>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="listElector">Elector <span class="text-danger">*</span></label>
                            <select class="form-control selectpicker" data-live-search="true" data-size="5"
                                id="listElector" name="listElector" required>
                                <!-- Async Loaded -->
                            </select>
                        </div>
                    </div>

                    <hr>
                    <p class="text-primary">Ubicación de Asignación (Donde vigilará)</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="listDpto">Departamento</label> <!-- Optional -->
                            <select class="form-control selectpicker" data-live-search="true" data-size="5"
                                id="listDpto" name="listDpto">
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listMuni">Municipio</label>
                            <select class="form-control selectpicker" data-live-search="true" data-size="5"
                                id="listMuni" name="listMuni" disabled>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="listZona">Zona</label>
                            <select class="form-control selectpicker" data-live-search="true" data-size="5"
                                id="listZona" name="listZona" disabled>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listPuesto">Puesto de Votación</label>
                            <select class="form-control selectpicker" data-live-search="true" data-size="5"
                                id="listPuesto" name="listPuesto" disabled>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <hr>
                            <label for="listMesas">Mesas de Votación (Disponibles + Asignadas)</label>
                            <select class="form-control selectpicker" data-live-search="true" multiple
                                data-selected-text-format="count > 3" data-size="5" id="listMesas" name="listMesas[]"
                                title="Seleccione las mesas..." disabled>
                                <!-- Async Loaded -->
                            </select>
                            <small class="form-text text-muted">Aparecerán las mesas libres y las que ya tiene asignadas
                                este testigo.</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="listEstado">Estado <span class="text-danger">*</span></label>
                            <select class="form-control selectpicker" id="listEstado" name="listEstado" data-size="5"
                                required>
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