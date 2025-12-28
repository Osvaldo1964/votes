<?php

class Infsaldos extends Controllers
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        parent::__construct();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(13); // Asumiendo ID 13 para Informes según nav_admin
    }

    public function infsaldos()
    {
        if (empty($_SESSION['permisos'][13]['r_permiso'])) {
            header("Location: " . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Informe de Saldos";
        $data['page_title'] = "INFORME DE SALDOS - <small>Chadan Rosado Taylor</small>";
        $data['page_name'] = "infsaldos";
        $data['page_functions_js'] = "functions_infsaldos.js";
        $this->views->getView($this, "infsaldos", $data);
    }
}
