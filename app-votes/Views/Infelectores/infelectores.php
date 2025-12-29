<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text-o"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/infelectores"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="row noprint">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Seleccionar Líder</label>
                                <select class="form-control" id="listLideres" name="listLideres" required="">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button class="btn btn-primary" type="button" onclick="fntViewReporte()"><i
                                    class="fa fa-file-text"></i> Generar</button>
                        </div>
                    </div>
                    <hr class="noprint">
                    <div class="row">
                        <div class="col-md-12" id="divReporte">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>