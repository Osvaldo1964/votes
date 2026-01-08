<?php
class ReporteTestigos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        // Using same permission module as Testigos (18) or Reports (17/New)?
        // nav_admin shows Reports under 'Reportes Electorales' (no specific ID yet, but creating one or reusing).
        // For now, I'll leverage logic similar to Testigos (18) or a generalized Report permission.
        // Assuming module 18 (Testigos) access implies ability to see this report, OR module 17.
        // Let's use 18 (Testigos) for consistency with the domain.
        getPermisos(18);
    }

    public function ReporteTestigos()
    {
        if (empty($_SESSION['permisos'][18]['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Reporte de Testigos";
        $data['page_title'] = "Reporte de Testigos Electorales";
        $data['page_name'] = "reporte_testigos";
        $data['page_functions_js'] = "functions_reporte_testigos.js";
        $this->views->getView($this, "reporte_testigos", $data);
    }
}
