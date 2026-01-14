<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-cogs"></i>
                <?= $data['page_title'] ?>
            </h1>
            <p>Configuración General del Candidato</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/parametros">
                    <?= $data['page_title'] ?>
                </a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Datos del Candidato</h3>
                <div class="tile-body">
                    <form id="formParametros" name="formParametros" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="listCandidato">Candidato Oficial (Sistema)</label>
                                    <select class="form-control selectpicker" id="listCandidato" name="listCandidato"
                                        data-live-search="true">
                                        <option value="" selected>Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Identificación (*)</label>
                                    <input class="form-control" id="txtIdentificacion" name="txtIdentificacion"
                                        type="text" placeholder="Identificación" readonly>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Teléfono (*)</label>
                                    <input class="form-control" id="txtTelefono" name="txtTelefono" type="text"
                                        placeholder="Teléfono" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input class="form-control" id="txtEmail" name="txtEmail" type="email"
                                        placeholder="Email" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Dirección</label>
                                    <input class="form-control" id="txtDireccion" name="txtDireccion" type="text"
                                        placeholder="Dirección de sede o contacto" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Partido Político</label>
                                    <select class="form-control selectpicker" id="listPartido" name="listPartido"
                                        data-live-search="true" disabled>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Curul</label>
                                    <select class="form-control selectpicker" id="listCurul" name="listCurul"
                                        data-live-search="true" disabled>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Número en Tarjeta/Lista</label>
                                    <input class="form-control" id="txtNumLista" name="txtNumLista" type="number"
                                        placeholder="Ej: 101">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Eslogan de Campaña</label>
                                    <input class="form-control" id="txtEslogan" name="txtEslogan" type="text"
                                        placeholder="Frase de campaña">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="photo">
                                    <label for="foto">Foto Actual</label>
                                    <div class="prevPhoto">
                                        <span class="delPhoto notBlock">X</span>
                                        <label for="foto"></label>
                                        <div style="width: 200px; height: 200px; overflow: hidden;">
                                            <img id="img" src="<?= media(); ?>/images/uploads/portada_categoria.png"
                                                style="width: 100%; height: 100%; object-fit: contain;">
                                        </div>
                                    </div>
                                    <div class="upimg">
                                        <input type="file" name="foto" id="foto">
                                    </div>
                                    <div id="form_alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="tile-footer">
                            <button class="btn btn-primary" type="submit"><i
                                    class="fa fa-fw fa-lg fa-check-circle"></i>Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>