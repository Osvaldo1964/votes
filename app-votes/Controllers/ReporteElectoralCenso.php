<?php
class ReporteElectoralCenso extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // sessionStart();
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Verificar sesión y permisos
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        // getPermisos(CLIENT_JWT); // Comentado temporalmente hasta registrar módulo en BD
    }

    public function ReporteElectoralCenso()
    {
        // if (empty($_SESSION['permisosMod']['r_permiso'])) {
        //     header("Location:" . base_url() . '/dashboard');
        // }
        // Se puede descomentar si ya tienes el módulo creado en BD con ID y permisos.

        $data['page_tag'] = "Reporte Electoral (Censo)";
        $data['page_title'] = "Reporte Electoral - Potencial vs Realidad";
        $data['page_name'] = "reporte_electoral_censo";
        $data['page_functions_js'] = "functions_reporteelectoralcenso.js";
        $this->views->getView($this, "reporteelectoralcenso", $data);
    }
}
