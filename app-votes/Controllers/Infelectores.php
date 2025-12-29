<?php
class Infelectores extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(6);
    }

    public function infelectores()
    {
        if (empty($_SESSION['permisosMod']['r_permiso'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Informe Electores";
        $data['page_title'] = "Informe de Electores";
        $data['page_name'] = "infelectores";
        $data['page_functions_js'] = "functions_infelectores.js";
        $this->views->getView($this, "infelectores", $data);
    }
}
