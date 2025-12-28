<?php

class Monitor extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(17);
    }

    public function Monitor()
    {
        $data['page_tag'] = "Monitor Día D";
        $data['page_title'] = "Monitor de Participación";
        $data['page_name'] = "monitor";
        $data['page_functions_js'] = "functions_monitor.js";
        $this->views->getView($this, "monitor", $data);
    }
}
