<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-chart-pie"></i> <?= $data['page_title'] ?></h1>
            <p>Resultados de los votos E-14</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/resultados"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formReporte" name="formReporte" class="form-horizontal">
                        <div class="row">
                            <!-- Filtros en Cascada -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listDpto">Departamento</label>
                                    <select class="form-control" id="listDpto" name="listDpto" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listMuni">Municipio</label>
                                    <select class="form-control" id="listMuni" name="listMuni" required disabled>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listZona">Zona</label>
                                    <select class="form-control" id="listZona" name="listZona" disabled>
                                        <option value="">Todas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listPuesto">Puesto</label>
                                    <select class="form-control" id="listPuesto" name="listPuesto" disabled>
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listMesa">Mesa</label>
                                    <select class="form-control" id="listMesa" name="listMesa" disabled>
                                        <option value="">Todas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="button" id="btnCargarResultados" class="btn btn-primary btn-block"><i class="fa fa-file-text"></i> Cargar Resultados</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="row" id="divResultados" style="display: none;">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Resultados del Informe</h3>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                            <div class="info">
                                <h4>Potencial Total</h4>
                                <p><b id="lblTotalPotencial">0</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget-small warning coloured-icon"><i class="icon fa fa-check-square-o fa-3x"></i>
                            <div class="info">
                                <h4>Mis Votos</h4>
                                <p><b id="lblMisVotos">0</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget-small info coloured-icon"><i class="icon fa fa-pie-chart fa-3x"></i>
                            <div class="info">
                                <h4>% Participación</h4>
                                <p><b id="lblPorcentaje">0%</b></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableReporte">
                            <thead>
                                <tr>
                                    <th>Zona</th>
                                    <th>Puesto</th>
                                    <th>Mesa</th>
                                    <th>Potencial</th>
                                    <th>Mis Votos</th>
                                    <th>% Cobertura</th>
                                    <th>Barra</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<?php footerAdmin($data); ?>