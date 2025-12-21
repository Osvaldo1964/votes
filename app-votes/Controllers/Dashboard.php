<?php
class Dashboard extends Controllers
{
    public function __construct()
    {
        // 1. Siempre iniciar la sesión primero para poder leer $_SESSION
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Ejecutar el constructor del padre
        parent::__construct();

        // 3. Validar si existe la sesión de login
        if (empty($_SESSION['login'])) {
            // Si no hay sesión, redirigimos al login
            header('Location: ' . base_url() . '/login');
            die();
        }

        // Opcional: Si quieres ver qué hay en la sesión mientras pruebas, 
        // pon el dep DESPUÉS de session_start.
        // dep($_SESSION); 
    }

    public function dashboard($params)
    {
        $data['page_id'] = 1;
        $data['page_tag'] = "Dashboard";
        $data['page_name'] = "dashboard";
        $data['page_title'] = "Página principal - Sistema de votos";

        // Ahora puedes usar los datos que guardamos en crearSesion
        $data['usuario'] = $_SESSION['userData'];

        $this->views->getView($this, "dashboard", $data);
    }
}