<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-exclamation-triangle"></i>
                <?= $data['page_title'] ?>
            </h1>
            <p>Identificación de Mesas con Votación Inferior a la Esperada</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/ReporteImpugnaciones">
                    <?= $data['page_title'] ?>
                </a></li>
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
                                    <select class="form-control selectpicker" id="listDpto" name="listDpto"
                                        data-live-search="true" data-size="5" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listMuni">Municipio</label>
                                    <select class="form-control selectpicker" id="listMuni" name="listMuni"
                                        data-live-search="true" data-size="5" required disabled>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listZona">Zona</label>
                                    <select class="form-control selectpicker" id="listZona" name="listZona"
                                        data-live-search="true" data-size="5" disabled>
                                        <option value="">Todas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listPuesto">Puesto</label>
                                    <select class="form-control selectpicker" id="listPuesto" name="listPuesto"
                                        data-live-search="true" data-size="5" disabled>
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="listCandidato">Candidato a Analizar (vs E-14)</label>
                                    <select class="form-control selectpicker" id="listCandidato" name="listCandidato"
                                        data-live-search="true" data-size="5" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtPorcentaje">Porcentaje Esperado (%)</label>
                                    <div class="input-group">
                                        <input class="form-control" id="txtPorcentaje" name="txtPorcentaje"
                                            type="number" min="1" max="100" placeholder="Ej: 50" required>
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <small class="form-text text-muted">Mostrar mesas con votación <span
                                            id="lblPorcentajeInfo">menor al % indicado</span> del potencial.</small>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" id="btnConsultar" class="btn btn-primary btn-block"><i
                                            class="fa fa-search"></i> Consultar</button>
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
                <div class="tile-title-w-btn">
                    <h3 class="title">Mesas para Impugnación</h3>
                    <p><button class="btn btn-secondary icon-btn" onclick="fntImprimir();"><i
                                class="fa fa-print"></i>Imprimir</button></p>
                </div>
                <div class="tile-body">
                    <div class="table-responsive">
                        <div id="divTableReporte">
                            <!-- Tabla inyectada por JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<?php footerAdmin($data); ?>