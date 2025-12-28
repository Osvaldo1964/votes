<?php
class Conceptos extends Controllers
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

        // CAMBIAR POR EL ID REAL DEL MODULO CONCEPTOS
        getPermisos(8);
    }

    public function conceptos()
    {
        $data['page_tag'] = "Conceptos Financieros";
        $data['page_title'] = "GESTIÓN DE CONCEPTOS <small>Sistema Electoral</small>";
        $data['page_name'] = "conceptos";
        $data['page_functions_js'] = "functions_conceptos.js";
        $this->views->getView($this, "conceptos", $data);
    }
}
