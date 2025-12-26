<?php
class Votacion extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(15);
    }

    public function votacion()
    {
        $data['page_id'] = 6;
        $data['page_tag'] = "Votacion";
        $data['page_title'] = "Votacion - Sistema de votos";
        $data['page_name'] = "votacion";
        $data['page_functions_js'] = "functions_votacion.js";
        $this->views->getView($this, "votacion", $data);
    }
}
