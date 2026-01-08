<?php
class ReporteImpugnaciones extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
        }
        getPermisos(17); // Usa el mismo permiso de Analisis por ahora
    }

    public function ReporteImpugnaciones()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Reporte de Impugnaciones";
        $data['page_title'] = "Reporte de Impugnaciones";
        $data['page_name'] = "reporte_impugnaciones";
        $data['page_functions_js'] = "functions_reporte_impugnaciones.js";
        $this->views->getView($this, "reporte_impugnaciones", $data);
    }
}
