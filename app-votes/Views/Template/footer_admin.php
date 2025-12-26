<script>
  const base_url = "<?= base_url(); ?>";
  const base_url_api = "<?= BASE_URL_API; ?>";
  // Alias for legacy support or stylistic preference in JS files
  const BASE_URL = base_url;
  const BASE_URL_API = base_url_api;
</script>
<!-- Essential javascripts for application to work-->
<script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
<script src="<?= media(); ?>/js/popper.min.js"></script>
<script src="<?= media(); ?>/js/bootstrap.min.js"></script>
<script src="<?= media(); ?>/js/main.js"></script>
<script src="<?= media(); ?>/js/fontawesome.js"></script>
<script src="<?= media(); ?>/js/functions_admin.js"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="<?= media(); ?>/js/plugins/pace.min.js"></script>
<!-- Page specific javascripts-->
<script type="text/javascript" src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>

<!-- Data table plugin-->
<script type="text/javascript" src="<?= media(); ?>/js/plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/bootstrap-select.min.js"></script>

<script src="<?= media(); ?>/js/functions_admin.js"></script>
<?php if ($data['page_name'] == "agenda") { ?>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<?php } ?>
<script src="<?= media(); ?>/js/<?= $data['page_functions_js'] ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    verificarExpiracionToken();
  });
</script>

</body>

</html>