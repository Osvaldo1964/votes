<?php
class Dashboard extends Controllers
{
    public function __construct()
    {
        // 1. Siempre iniciar la sesión primero para poder leer $_SESSION
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();

        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }

        getPermisos(1);
    }

    public function dashboard($params)
    {
        $data['page_id'] = 1;
        $data['page_tag'] = "Dashboard";
        $data['page_name'] = "dashboard";
        $data['page_title'] = "Página principal - Sistema de votos";
        $data['usuario'] = $_SESSION['userData'];
        $data['page_functions_js'] = "functions_dashboard.js";
        $this->views->getView($this, "dashboard", $data);
    }
}
