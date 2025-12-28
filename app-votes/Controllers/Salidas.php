<?php
class Salidas extends Controllers
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

        // ID Modulo: Salidas (Entregas)
        getPermisos(12);
    }

    public function salidas()
    {
        $data['page_tag'] = "Salidas de Almacén";
        $data['page_title'] = "ENTREGAS DE INVENTARIO <small>Sistema Electoral</small>";
        $data['page_name'] = "salidas";
        $data['page_functions_js'] = "functions_salidas.js";
        $this->views->getView($this, "salidas", $data);
    }
}
