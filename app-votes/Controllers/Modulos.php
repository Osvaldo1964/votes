<?php
class Modulos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        sessionUser();
        getPermisos(19);
    }

    public function Modulos()
    {
        $data['page_tag'] = "Módulos del Sistema";
        $data['page_title'] = "Módulos"; // Title for header
        $data['page_name'] = "modulos";
        $data['page_functions_js'] = "functions_modulos.js";
        $this->views->getView($this, "modulos", $data);
    }
}
