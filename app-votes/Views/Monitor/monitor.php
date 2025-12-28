<?php
headerAdmin($data);
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-tachometer"></i> <?= $data['page_title'] ?></h1>
            <p>Monitoreo de Participación Día D</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/monitor"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <style>
        /* Estilos Forzados para Grid Monitor */
        #divMesasContainer {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: 20px !important;
            width: 100%;
            box-sizing: border-box;
        }

        /* Responsividad */
        @media(max-width: 992px) {
            #divMesasContainer {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        @media(max-width: 576px) {
            #divMesasContainer {
                grid-template-columns: 1fr !important;
            }
        }

        /* Loading state override */
        #divMesasContainer.loading {
            display: block !important;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <!-- Formulario de Filtros -->
                    <form id="formMonitor" name="formMonitor" class="form-horizontal">
                        <div class="form-row align-items-end">
                            <!-- Departamento (2) -->
                            <div class="form-group col-md-2">
                                <label for="listDpto">Departamento</label>
                                <select class="form-control selectpicker" id="listDpto" name="listDpto" data-live-search="true" required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <!-- Municipio (2) -->
                            <div class="form-group col-md-2">
                                <label for="listMuni">Municipio</label>
                                <select class="form-control selectpicker" id="listMuni" name="listMuni" data-live-search="true" disabled required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <!-- Zona (2) -->
                            <div class="form-group col-md-2">
                                <label for="listZona">Zona</label>
                                <select class="form-control selectpicker" id="listZona" name="listZona" data-live-search="true" disabled required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <!-- Puesto (3) -->
                            <div class="form-group col-md-3">
                                <label for="listPuesto">Puesto de Votación</label>
                                <select class="form-control selectpicker" id="listPuesto" name="listPuesto" data-live-search="true" disabled required>
                                    <option value="" selected>Seleccione...</option>
                                </select>
                            </div>
                            <!-- Auto-Refresh (2) -->
                            <div class="form-group col-md-2">
                                <label for="listRefresh"> <i class="fa fa-clock-o"></i> Refresco</label>
                                <select class="form-control" id="listRefresh" onchange="fntSetRefresh()">
                                    <option value="0">Manual</option>
                                    <option value="120000">2 Min</option>
                                    <option value="240000">4 Min</option>
                                    <option value="360000">6 Min</option>
                                </select>
                            </div>
                            <!-- Botón Mostrar (1) -->
                            <div class="form-group col-md-1">
                                <button type="button" class="btn btn-primary btn-block" onclick="fntMonitorShow();" title="Actualizar"><i class="fa fa-eye"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor General de Mesas -->
    <div id="divMesasContainer" style="display:none;">
        <!-- Aquí se inyectarán las tarjetas JS -->
    </div>

</main>
<?php footerAdmin($data); ?>