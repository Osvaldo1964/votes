<?php
headerAdmin($data);
getModal('modalRoles', $data);
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h5><i class="bi bi-speedometer"></i> <?= $data["page_title"] ?>
                <button class="btn btn-primary btn-round btn-sm" onclick="openModal();"><i class="fas fa-plus-circle"></i> Nuevo Rol</button>
            </h5>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard"> <i class="bi bi-house-door fs-6"></i></a></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/roles"><?= $data["page_tag"] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">Create a beautiful dashboard</div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>