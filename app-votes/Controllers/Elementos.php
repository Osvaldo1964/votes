<?php
class Elementos extends Controllers
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

        // Poniendo 14 por defecto (Control Administrativo), ajustalo si creaste un modulo especifico para Elementos/Inventario
        getPermisos(9);
    }

    public function elementos()
    {
        $data['page_tag'] = "Elementos de Campaña";
        $data['page_title'] = "GESTIÓN DE ELEMENTOS (INVENTARIO) <small>Sistema Electoral</small>";
        $data['page_name'] = "elementos";
        $data['page_functions_js'] = "functions_elementos.js";
        $this->views->getView($this, "elementos", $data);
    }
}
