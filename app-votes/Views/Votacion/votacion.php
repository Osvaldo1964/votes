<?php headerAdmin($data); ?>

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-pencil-square-o"></i> <?= $data['page_title'] ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="row text-center align-items-center justify-content-center">
                        <div class="col-md-6">
                            <h3>Registrar Voto de Elector</h3>
                            <p class="text-muted">Ingrese la cédula del elector para registrar su participación.</p>

                            <form id="formVoto" name="formVoto" class="mt-4">
                                <div class="form-group">
                                    <label class="control-label" for="txtIdentificacion">Número de Identificación</label>
                                    <input class="form-control form-control-lg text-center" id="txtIdentificacion" name="identificacion" type="text" placeholder="Ej: 12345678" required autocomplete="off" autofocus>
                                </div>
                                <div class="form-group mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">
                                        <i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO
                                    </button>
                                </div>
                            </form>

                            <div id="alertMsg" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php footerAdmin($data); ?>