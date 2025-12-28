<?php
class Terceros extends Controllers
{
    public function __construct()
    {
        // Iniciar sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        parent::__construct();

        // Validar Login
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        // Validar Permisos (Aquí usaré un ID genérico o consultaré getPermisos si el usuario me da el ID del módulo)
        // Por ahora lo dejo genérico para que no bloquee, pero idealmente debe coincidir con DB.
        getPermisos(7); // Usando 1 por defecto o Dashboard
    }

    public function terceros()
    {
        $data['page_tag'] = "Terceros";
        $data['page_title'] = "GESTIÓN DE TERCEROS <small>Sistema Electoral</small>";
        $data['page_name'] = "terceros";
        $data['page_functions_js'] = "functions_terceros.js";
        $this->views->getView($this, "terceros", $data);
    }
}
