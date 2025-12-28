<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-area-chart"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/infsaldos"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formReporte" name="formReporte" class="form-horizontal">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-6">
                                <label for="listElementos">Seleccionar Elemento</label>
                                <select class="form-control" id="listElementos" name="listElementos" required="">
                                    <option value="0">Todos los Elementos (Resumen General)</option>
                                    <!-- Se llena con JS -->
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <button type="button" onclick="fntGenerarReporte()" class="btn btn-primary"><i class="fa fa-search"></i> Generar Reporte</button>
                                <button type="button" onclick="fntImprimir()" class="btn btn-secondary"><i class="fa fa-print"></i> Imprimir</button>
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
                <div class="tile-body">
                    <div id="divResultados">
                        <!-- Aquí se renderiza la tabla dinámicamente -->
                        <div class="text-center text-muted">Seleccione un filtro y haga clic en Generar para ver resultados.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>