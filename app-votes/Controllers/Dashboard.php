<?php
class Dashboard extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard($params)
    {
        $data['page_id'] = 1;
        $data['page_tag'] = "Dashboard";
        $data['page_name'] = "dashboard";
        $data['page_title'] = "Página principal - Sistema de votos";
        $this->views->getView($this, "dashboard", $data);
    }
}
