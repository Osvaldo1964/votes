<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-truck-loading"></i> <?= $data['page_title'] ?>
                <button class="btn btn-primary" type="button" onclick="openModal();"><i class="fas fa-plus-circle"></i> Nueva Entrega</button>
            </h1>
            <p>Registro de Salida/Entrega de Material a Líderes</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/salidas"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableSalidas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Líder (Receptor)</th>
                                    <th>Elemento</th>
                                    <th>Cant.</th>
                                    <th>Estado</th>
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
</main>

<!-- Modal -->
<div class="modal fade" id="modalFormSalida" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nueva Entrega</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formSalida" name="formSalida" class="form-horizontal">
                    <input type="hidden" id="idSalida" name="idSalida" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>

                    <div class="form-group">
                        <label for="txtFecha">Fecha de Entrega</label>
                        <input type="date" class="form-control" id="txtFecha" name="txtFecha" required="">
                    </div>

                    <div class="form-group">
                        <label for="listLider">Líder (Quien recibe)</label>
                        <select class="form-control" data-live-search="true" id="listLider" name="listLider" required="" onchange="fntInfoLider();">
                        </select>
                        <!-- Alerta informativa de electores -->
                        <div id="divInfoLider" class="alert alert-info mt-2" style="display: none; padding: 5px 10px; font-size: 14px;">
                            <i class="fas fa-users"></i> Este líder tiene <strong id="lblElectores">0</strong> electores registrados.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="listElemento">Elemento (Producto)</label>
                        <select class="form-control" data-live-search="true" id="listElemento" name="listElemento" required="">
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="txtCantidad">Cantidad a Entregar</label>
                        <input type="number" step="0.01" class="form-control" id="txtCantidad" name="txtCantidad" required="">
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