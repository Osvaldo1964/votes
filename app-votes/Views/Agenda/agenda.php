<?php headerAdmin($data); ?>

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-calendar"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/agenda"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <!-- Contenedor del Calendario -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal para Agregar/Editar Eventos -->
<div class="modal fade" id="modalAgenda" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAgenda" name="formAgenda">
                    <input type="hidden" id="id" name="id" value="">

                    <div class="form-group">
                        <label for="title">Título</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start">Fecha Inicio</label>
                            <input type="datetime-local" class="form-control" id="start" name="start" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end">Fecha Fin</label>
                            <input type="datetime-local" class="form-control" id="end" name="end">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="#3788d8" title="Elegir color">
                    </div>

                    <div class="tile-footer">
                        <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span></button>&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar</button>
                        <button class="btn btn-warning" type="button" id="btnEliminar" style="display: none;"><i class="fa fa-fw fa-lg fa-trash"></i>Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php footerAdmin($data); ?>