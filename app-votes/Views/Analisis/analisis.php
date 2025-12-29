<?php
headerAdmin($data);
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-balance-scale"></i> <?= $data['page_title'] ?></h1>
            <p>Comparativo: Puesto de Votación vs Escrutinio E-14</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/analisis"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formAnalisis" name="formAnalisis" class="form-horizontal">
                        <div class="form-row">
                            <!-- Filtros Ubicación -->
                            <div class="form-group col-md-2">
                                <label for="listDpto">Departamento</label>
                                <select class="form-control selectpicker" id="listDpto" name="listDpto"
                                    data-live-search="true" required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="listMuni">Municipio</label>
                                <select class="form-control selectpicker" id="listMuni" name="listMuni"
                                    data-live-search="true" disabled required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="listZona">Zona</label>
                                <select class="form-control selectpicker" id="listZona" name="listZona"
                                    data-live-search="true" disabled required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="listPuesto">Puesto</label>
                                <select class="form-control selectpicker" id="listPuesto" name="listPuesto"
                                    data-live-search="true" disabled required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>

                            <!-- Filtro Candidato (Vital) -->
                            <div class="form-group col-md-3">
                                <label for="listCandidato" class="text-danger">Candidato a Auditar</label>
                                <select class="form-control selectpicker" id="listCandidato" name="listCandidato"
                                    data-live-search="true" required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row text-center">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" onclick="fntViewReporte()"><i
                                        class="fa fa-search"></i> Generar Análisis</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body" id="divReporte">
                    <div class="text-center text-muted p-5">
                        <i class="fa fa-bar-chart fa-3x"></i><br>
                        Seleccione los filtros para ver la auditoría.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>