<?php
class Entradas extends Controllers
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

        // ID Modulo: Control Administrativo (o especifico Entradas)
        getPermisos(14);
    }

    public function entradas()
    {
        $data['page_tag'] = "Entradas de Almacén";
        $data['page_title'] = "COMPRAS DE INVENTARIO <small>Sistema Electoral</small>";
        $data['page_name'] = "entradas";
        $data['page_functions_js'] = "functions_entradas.js";
        $this->views->getView($this, "entradas", $data);
    }
}
