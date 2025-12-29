<?php
class Infelectores extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getLideres()
    {
        $arrData = $this->model->selectLideres();
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getReporte()
    {
        if ($_POST) {
            $lider = $_POST['lider'];
            $arrData = $this->model->selectElectoresReport($lider);
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
