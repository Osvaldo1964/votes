<?php headerAdmin($data); ?>
<main class="app-content">


    <?php if (empty($_SESSION['permisosMod']['r_permiso'])) { ?>
        <p>No tienes permisos para ver esta página</p>
    <?php } else { ?>
        <div class="app-title">
            <div>
                <h1><i class="fas fa-money-check-alt"></i> <?= $data['page_title'] ?>
                    <?php if (!empty($_SESSION['permisosMod']['w_permiso'])) { ?>
                        <button class="btn btn-primary" type="button" onclick="openModal();"><i class="fas fa-plus-circle"></i> Nuevo</button>
                    <?php } ?>
                </h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a href="<?= base_url(); ?>/movimientos"><?= $data['page_title'] ?></a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tableMovimientos">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Tercero</th>
                                        <th>Concepto</th>
                                        <th>Tipo (Op)</th>
                                        <th>Valor</th>
                                        <th>Acciones</th>
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
    <?php } ?>
</main>

<!-- Modal -->
<div class="modal fade" id="modalFormMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Movimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formMovimiento" name="formMovimiento" class="form-horizontal">
                    <input type="hidden" id="id_movimiento" name="id_movimiento" value="">
                    <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fecha_movimiento">Fecha <span class="required">*</span></label>
                            <input class="form-control" id="fecha_movimiento" name="fecha_movimiento" type="date" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tercero_movimiento">Tercero <span class="required">*</span></label>
                            <select class="form-control" data-live-search="true" id="tercero_movimiento" name="tercero_movimiento" required="">
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="concepto_movimiento">Concepto <span class="required">*</span></label>
                            <select class="form-control" data-live-search="true" id="concepto_movimiento" name="concepto_movimiento" required="">
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tipo_movimiento">Norma Contable <span class="required">*</span></label>
                            <select class="form-control" id="tipo_movimiento" name="tipo_movimiento" required="">
                                <option value="1">Norma contable campaña</option>
                                <option value="2">Otra norma contable</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="obs_movimiento">Observaciones</label>
                        <textarea class="form-control" id="obs_movimiento" name="obs_movimiento" rows="2"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="valor_movimiento">Valor <span class="required">*</span></label>
                            <input class="form-control" id="valor_movimiento" name="valor_movimiento" type="number" step="0.01" placeholder="Monto del movimiento" required="">
                        </div>
                    </div>

                    <div class="tile-footer">
                        <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php footerAdmin($data); ?>