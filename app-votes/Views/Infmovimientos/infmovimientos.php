<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text-o"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/infmovimientos"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <!-- Filtros -->
                    <form id="formReporte" class="row align-items-end mb-4">
                        <div class="form-group col-md-3">
                            <label for="txtFechaInicio">Fecha Inicio</label>
                            <input class="form-control" type="date" id="txtFechaInicio" name="fechaInicio" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="txtFechaFin">Fecha Fin</label>
                            <input class="form-control" type="date" id="txtFechaFin" name="fechaFin" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="listConcepto">Concepto (Opcional)</label>
                            <select class="form-control selectpicker" id="listConcepto" name="concepto" data-live-search="true">
                                <option value="0">TODOS LOS CONCEPTOS</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 d-print-none">
                            <button type="button" class="btn btn-primary btn-block" onclick="fntViewReporte()">
                                <i class="fa fa-search"></i> Generar
                            </button>
                            <button type="button" class="btn btn-secondary btn-block mt-2" onclick="window.print()">
                                <i class="fa fa-print"></i> Imprimir
                            </button>
                        </div>
                    </form>

                    <hr>

                    <style media="print">
                        .widget-small {
                            border: 1px solid #ccc;
                            page-break-inside: avoid;
                        }

                        .col-md-4 {
                            -ms-flex: 0 0 33.333333%;
                            flex: 0 0 33.333333%;
                            max-width: 33.333333%;
                        }
                    </style>

                    <!-- Resumen -->
                    <div id="divResumen" class="row text-center mb-4" style="display:none;">
                        <div class="col-md-4">
                            <div class="widget-small primary coloured-icon"><i class="icon fa fa-arrow-up fa-3x"></i>
                                <div class="info">
                                    <h4>Total Ingresos</h4>
                                    <p><b id="lblIngresos">$0</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="widget-small danger coloured-icon"><i class="icon fa fa-arrow-down fa-3x"></i>
                                <div class="info">
                                    <h4>Total Gastos</h4>
                                    <p><b id="lblGastos">$0</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="widget-small info coloured-icon"><i class="icon fa fa-balance-scale fa-3x"></i>
                                <div class="info">
                                    <h4>Balance</h4>
                                    <p><b id="lblBalance">$0</b></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped text-center" id="tableMovimientos">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tercero</th>
                                    <th>Concepto</th>
                                    <th>Tipo</th>
                                    <th>Observación</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody id="tblReporte">
                                <tr>
                                    <td colspan="6">Seleccione un rango de fechas y genere el reporte.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>