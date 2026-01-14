<?php
class Parametros extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        sessionUser();
        // Restricción estricta: Solo usuario ID 1
        if ($_SESSION['userData']['id_usuario'] != 1) {
            header("Location: " . base_url() . "/dashboard");
            die();
        }
    }

    public function Parametros()
    {
        $data['page_id'] = 20; // Generic ID, adjust if needed
        $data['page_tag'] = "Configuración del Sistema";
        $data['page_title'] = "Parámetros - Sistema Electoral";
        $data['page_name'] = "parametros";
        $data['page_functions_js'] = "functions_parametros.js";
        $this->views->getView($this, "parametros", $data);
    }
}
