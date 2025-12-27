<?php
class Resultados extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(16);
    }

    public function Resultados()
    {
        $data['page_id'] = 16;
        $data['page_tag'] = "Resultados";
        $data['page_title'] = "Resultados - Sistema de votos";
        $data['page_name'] = "resultados";
        $data['page_functions_js'] = "functions_resultados.js";
        $this->views->getView($this, "resultados", $data);
    }
}
