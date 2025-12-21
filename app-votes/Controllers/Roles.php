<?php
class Roles extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(3);
    }

    public function Roles($params)
    {
        $data['page_id'] = 3;
        $data['page_tag'] = "Roles de Usuario";
        $data['page_title'] = "Roles de Usuario - Sistema de votos";
        $data['page_name'] = "rol_usuario";
        $data['page_functions_js'] = "functions_roles.js";
        $this->views->getView($this, "roles", $data);
    }
}
