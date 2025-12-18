<?php
class Usuarios extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function Usuarios()
    {
        $data['page_id'] = 1;
        $data['page_tag'] = "Usuarios";
        $data['page_title'] = "Usuarios - Sistema de votos";
        $data['page_name'] = "usuarios";
        $this->views->getView($this, "usuarios", $data);
    }
}
