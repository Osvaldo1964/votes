<script>
  const base_url = "<?= base_url(); ?>";
  const base_url_api = "<?= BASE_URL_API; ?>";
  // Alias for legacy support or stylistic preference in JS files
  const BASE_URL = base_url;
  const BASE_URL_API = base_url_api;
  const media = "<?= media(); ?>";
</script>
<!-- Essential javascripts for application to work-->
<script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
<script src="<?= media(); ?>/js/popper.min.js"></script>
<script src="<?= media(); ?>/js/bootstrap.min.js"></script>
<script src="<?= media(); ?>/js/main.js?v=2"></script>
<script src="<?= media(); ?>/js/fontawesome.js"></script>
<script src="<?= media(); ?>/js/functions_admin.js?v=2"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="<?= media(); ?>/js/plugins/pace.min.js"></script>
<!-- Page specific javascripts-->
<script type="text/javascript" src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>

<!-- Data table plugin-->
<script type="text/javascript" src="<?= media(); ?>/js/plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/bootstrap-select.min.js"></script>

<script type="text/javascript" language="javascript"
  src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript"
  src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" language="javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript"
  src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript"
  src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

<?php if ($data['page_name'] == "agenda" || $data['page_name'] == "programa_agenda") { ?>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<?php } ?>
<?php if ($data['page_name'] == "agenda") { ?>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<?php } ?>
<script src="<?= media(); ?>/js/<?= $data['page_functions_js'] ?>?v=<?= time(); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    verificarExpiracionToken();
  });
</script>

</body>

</html>