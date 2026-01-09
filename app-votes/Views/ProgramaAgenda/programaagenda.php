<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-calendar-check-o"></i>
                <?= $data['page_title'] ?>
            </h1>
            <p>Generación de reportes de agenda por rango de fechas</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/programaagenda">
                    <?= $data['page_title'] ?>
                </a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formReporteAgenda" name="formReporteAgenda" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="txtFechaInicio">Fecha Inicio</label>
                                    <input class="form-control" type="date" id="txtFechaInicio" name="txtFechaInicio"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="txtFechaFin">Fecha Fin</label>
                                    <input class="form-control" type="date" id="txtFechaFin" name="txtFechaFin"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary btn-block"><i
                                            class="fa fa-file-text-o"></i> Generar Reporte</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="divResultados" style="display: none;">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Resultados</h3>
                    <p><button class="btn btn-secondary" type="button" onclick="fntImprimirReporte()"><i
                                class="fa fa-print"></i> Imprimir</button></p>
                </div>
                <div class="tile-body">
                    <!-- Área de Impresión -->
                    <div id="printableArea">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tableAgendaReporte">
                                <thead>
                                    <tr>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Título</th>
                                        <th>Descripción</th>
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
    </div>
</main>
<?php footerAdmin($data); ?>