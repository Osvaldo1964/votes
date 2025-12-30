<?php
class Movimientos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        sessionUser();
        // Permiso ID 10 para Movimientos
        getPermisos(10);
    }

    public function movimientos()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location: " . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Movimientos Financieros";
        $data['page_title'] = "MOVIMIENTOS FINANCIEROS <small>Ingresos y Gastos</small>";
        $data['page_name'] = "movimientos";
        $data['page_functions_js'] = "functions_movimientos.js";
        $this->views->getView($this, "movimientos", $data);
    }
}
