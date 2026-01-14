<?php
class Home extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function home()
    {
        $data['page_tag'] = "Inicio - Campaña Chadan Rosado";
        $data['page_title'] = "Inicio";
        $data['page_name'] = "home";
        $this->views->getView($this, "home", $data);
    }
}
