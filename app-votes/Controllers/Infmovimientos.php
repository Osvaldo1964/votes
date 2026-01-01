<?php
class Infmovimientos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(13); // Módulo Informes
    }

    public function Infmovimientos()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Informe de Movimientos";
        $data['page_title'] = "Informe de Ingresos y Gastos";
        $data['page_name'] = "infmovimientos";
        $data['page_functions_js'] = "functions_infmovimientos.js";
        $this->views->getView($this, "infmovimientos", $data);
    }
}
