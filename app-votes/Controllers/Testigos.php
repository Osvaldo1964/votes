<?php
class Testigos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(18); // ID Modulo Testigos segun nav_admin
    }

    public function Testigos()
    {
        if (empty($_SESSION['permisos'][18]['r_permiso'])) {
            header("Location: " . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Testigos Electorales";
        $data['page_title'] = "Testigos Electorales";
        $data['page_name'] = "testigos";
        $data['page_functions_js'] = "functions_testigos.js";
        $this->views->getView($this, "testigos", $data);
    }
}
