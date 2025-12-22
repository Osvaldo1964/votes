<?php
class Candidatos extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(4);
    }

    public function Candidatos()
    {
        $data['page_id'] = 4;
        $data['page_tag'] = "Candidatos";
        $data['page_title'] = "Candidatos - Sistema de votos";
        $data['page_name'] = "candidatos";
        $data['page_functions_js'] = "functions_candidatos.js";
        $this->views->getView($this, "candidatos", $data);
    }
}
