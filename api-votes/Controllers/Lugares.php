<?php

class Lugares extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDepartamentos()
    {
        $arrData = $this->model->getDepartamentos();
        if (empty($arrData) || is_string($arrData)) {
            $arrResponse = array('status' => false, 'msg' => is_string($arrData) ? $arrData : 'Datos no encontrados');
        } else {
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getMunicipios($idDpto)
    {
        if (empty($idDpto)) {
            $arrResponse = array("status" => false, "msg" => "Datos incorrectos");
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        $arrData = $this->model->getMunicipios($idDpto);
        if (empty($arrData) || is_string($arrData)) {
            $arrResponse = array('status' => false, 'msg' => is_string($arrData) ? $arrData : 'Datos no encontrados');
        } else {
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getZonas($idMuni)
    {
        if (empty($idMuni)) {
            $arrResponse = array("status" => false, "msg" => "Datos incorrectos");
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        $arrData = $this->model->getZonas($idMuni);
        if (empty($arrData) || is_string($arrData)) {
            $arrResponse = array('status' => false, 'msg' => is_string($arrData) ? $arrData : 'Datos no encontrados');
        } else {
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getPuestos($idZona)
    {
        if (empty($idZona)) {
            $arrResponse = array("status" => false, "msg" => "Datos incorrectos");
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        $arrData = $this->model->getPuestos($idZona);
        if (empty($arrData) || is_string($arrData)) {
            $arrResponse = array('status' => false, 'msg' => is_string($arrData) ? $arrData : 'Datos no encontrados');
        } else {
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getMesas()
    {
        // Recibe por POST o GET params compuestos
        // idZona y nombrePuesto
        // Como es GET usaremos parametro url o query string. 
        // Mejor usar POST para strings complejos o GET con params.
        // Aquí simplificamos recibiendo JSON o variables

        // Vamos a esperar POST
        if ($_POST) {
            $idZona = intval($_POST['idZona']);
            $idPuesto = isset($_POST['idPuesto']) ? intval($_POST['idPuesto']) : 0;

            if (empty($idPuesto)) {
                $arrResponse = array("status" => false, "msg" => "Id Puesto requerido");
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            // idZona is optional now since we query by idPuesto
            $arrData = $this->model->getMesas($idZona, $idPuesto);
            if (empty($arrData) || is_string($arrData)) {
                $arrResponse = array('status' => false, 'msg' => is_string($arrData) ? $arrData : 'Datos no encontrados');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        die();
    }

    public function getPotencialMesa()
    {
        if ($_POST) {
            $idZona = intval($_POST['idZona']);
            $nombrePuesto = $_POST['nombrePuesto'];
            $nombreMesa = $_POST['nombreMesa'];

            if (empty($idZona) || empty($nombrePuesto) || empty($nombreMesa)) {
                $arrResponse = array("status" => false, "msg" => "Datos incorrectos");
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            $arrData = $this->model->getPotencialMesa($idZona, $nombrePuesto, $nombreMesa);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        die();
    }
    public function getMisVotos($idMesa)
    {
        $idMesa = intval($idMesa);
        if ($idMesa > 0) {
            $arrData = $this->model->getMisVotos($idMesa);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'data' => ['total' => 0]);
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
