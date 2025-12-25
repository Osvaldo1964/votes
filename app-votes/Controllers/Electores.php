<?php
class Electores extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(6);
    }

    public function Electores()
    {
        $data['page_id'] = 6;
        $data['page_tag'] = "Electores";
        $data['page_title'] = "Electores - Sistema de votos";
        $data['page_name'] = "electores";
        $data['page_functions_js'] = "functions_electores.js";
        $this->views->getView($this, "electores", $data);
    }
}
