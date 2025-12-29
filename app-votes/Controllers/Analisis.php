<?php

class Analisis extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(17); // Using 17 as per nav_admin logic or similar reporting permission
    }

    public function Analisis()
    {
        if (empty($_SESSION['permisos'][17]['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Análisis E-14";
        $data['page_title'] = "Auditoría de Resultados E-14";
        $data['page_name'] = "analisis_e14";
        $data['page_functions_js'] = "functions_analisis.js";
        $this->views->getView($this, "analisis", $data);
    }
}
