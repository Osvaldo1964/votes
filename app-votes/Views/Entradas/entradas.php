<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-dolly-flatbed"></i> <?= $data['page_title'] ?>
                <button class="btn btn-primary" type="button" onclick="openModal();"><i class="fas fa-plus-circle"></i> Nueva Compra</button>
            </h1>
            <p>Registro de Entrada de Material Publicitario</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/entradas"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableEntradas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Elemento</th>
                                    <th>Cant.</th>
                                    <th>Unitario</th>
                                    <th>Total</th>
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
<div class="modal fade" id="modalFormEntrada" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nueva Entrada</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEntrada" name="formEntrada" class="form-horizontal">
                    <input type="hidden" id="idEntrada" name="idEntrada" value="">
                    <p class="text-primary">Todos los campos son obligatorios.</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="txtFecha">Fecha de Compra</label>
                            <input type="date" class="form-control" id="txtFecha" name="txtFecha" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="txtFactura">No. Factura / Soporte</label>
                            <input type="text" class="form-control" id="txtFactura" name="txtFactura" placeholder="FAC-001" required="">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="listTercero">Proveedor (Tercero)</label>
                            <select class="form-control" data-live-search="true" id="listTercero" name="listTercero" required="">
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listElemento">Elemento (Producto)</label>
                            <select class="form-control" data-live-search="true" id="listElemento" name="listElemento" required="">
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="txtCantidad">Cantidad</label>
                            <input type="number" step="0.01" class="form-control" id="txtCantidad" name="txtCantidad" required="" onchange="fntCalcularTotal();" onkeyup="fntCalcularTotal();">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="txtUnitario">Precio Unitario</label>
                            <input type="number" step="0.01" class="form-control" id="txtUnitario" name="txtUnitario" required="" onchange="fntCalcularTotal();" onkeyup="fntCalcularTotal();">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="txtTotal">Total Compra</label>
                            <input type="number" step="0.01" class="form-control" id="txtTotal" name="txtTotal" required="" readonly>
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