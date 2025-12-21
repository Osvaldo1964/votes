<?php
class Usuarios extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(2);
    }

    public function Usuarios()
    {
        $data['page_id'] = 2;
        $data['page_tag'] = "Usuarios";
        $data['page_title'] = "Usuarios - Sistema de votos";
        $data['page_name'] = "usuarios";
        $data['page_functions_js'] = "functions_usuarios.js";
        $this->views->getView($this, "usuarios", $data);
    }
}
