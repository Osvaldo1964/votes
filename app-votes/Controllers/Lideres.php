<?php
class Lideres extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(2);
    }

    public function Lideres()
    {
        $data['page_id'] = 2;
        $data['page_tag'] = "Lideres";
        $data['page_title'] = "Lideres - Sistema de votos";
        $data['page_name'] = "lideres";
        $data['page_functions_js'] = "functions_lideres.js";
        $this->views->getView($this, "lideres", $data);
    }
}
