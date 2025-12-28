<?php headerAdmin($data); ?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-dashboard"></i> <?= $data['page_title'] ?></h1>
      <p>Resumen General de Campaña</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard">Dashboard</a></li>
    </ul>
  </div>

  <!-- Widgets Superiores -->
  <div class="row">
    <!-- Electores -->
    <div class="col-md-6 col-lg-3">
      <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
        <div class="info">
          <h4>Electores</h4>
          <p><b id="lblTotalElectores">0</b></p>
        </div>
      </div>
    </div>
    <!-- Meta -->
    <div class="col-md-6 col-lg-3">
      <div class="widget-small info coloured-icon"><i class="icon fa fa-flag fa-3x"></i>
        <div class="info">
          <h4>Meta (<span id="lblPorcentajeMeta">0%</span>)</h4>
          <p><b id="lblMetaGlobal">0</b></p>
        </div>
      </div>
    </div>
    <!-- Líderes -->
    <div class="col-md-6 col-lg-3">
      <div class="widget-small warning coloured-icon"><i class="icon fa fa-briefcase fa-3x"></i>
        <div class="info">
          <h4>Líderes</h4>
          <p><b id="lblTotalLideres">0</b></p>
        </div>
      </div>
    </div>
    <!-- Votos (Dia D) -->
    <div class="col-md-6 col-lg-3">
      <div class="widget-small danger coloured-icon"><i class="icon fa fa-check-square-o fa-3x"></i>
        <div class="info">
          <h4>Votos Monitor</h4>
          <p><b id="lblTotalVotos">0</b></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos -->
  <div class="row">
    <div class="col-md-6">
      <div class="tile">
        <h3 class="tile-title">Top Líderes (Rendimiento)</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <canvas class="embed-responsive-item" id="chartLideres"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="tile">
        <h3 class="tile-title">Distribución por Municipio</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <canvas class="embed-responsive-item" id="chartMunicipios"></canvas>
        </div>
      </div>
    </div>
  </div>
</main>
<!-- Plugin Chart.js -->
<script type="text/javascript" src="<?= media(); ?>/js/plugins/chart.js"></script>
<?php footerAdmin($data); ?>