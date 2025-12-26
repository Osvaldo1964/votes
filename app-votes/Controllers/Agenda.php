<?php
class Agenda extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        sessionUser(); // Con esta sola línea ya proteges todo el controlador
        getPermisos(14);
    }

    public function agenda()
    {
        $data['page_id'] = 4;
        $data['page_tag'] = "Agenda";
        $data['page_title'] = "Agenda - Sistema de votos";
        $data['page_name'] = "agenda";
        $data['page_functions_js'] = "functions_agenda.js";
        $this->views->getView($this, "agenda", $data);
    }
}
