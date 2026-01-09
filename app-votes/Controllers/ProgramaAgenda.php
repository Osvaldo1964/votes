<?php
class ProgramaAgenda extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
    }

    public function ProgramaAgenda()
    {
        $data['page_tag'] = "Programa Agenda";
        $data['page_title'] = "Programa Agenda";
        $data['page_name'] = "programa_agenda";
        $data['page_functions_js'] = "functions_programaagenda.js";
        $this->views->getView($this, "programaagenda", $data);
    }
}
