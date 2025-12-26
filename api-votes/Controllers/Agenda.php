<?php

class Agenda extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // CORS Handling
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);
        } catch (\Throwable $e) {
            jsonResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            die();
        }
    }

    public function getAgenda()
    {
        $arrData = $this->model->selectEventos();
        // FullCalendar espera un array limpio de objetos
        if (empty($arrData)) {
            $arrData = array();
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getEvento($id)
    {
        $intId = intval($id);
        if ($intId > 0) {
            $arrData = $this->model->selectEvento($intId);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            jsonResponse($arrResponse, 200);
        }
        die();
    }

    public function setEvento()
    {
        if ($_POST) {
            if (empty($_POST['title']) || empty($_POST['start'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idEvento = intval($_POST['id']);
                $strTitulo = strClean($_POST['title']);
                $strDescripcion = strClean($_POST['description']);
                $strColor = strClean($_POST['color']);
                $strInicio = $_POST['start']; // FullCalendar envía string ISO
                $strFin = !empty($_POST['end']) ? $_POST['end'] : $strInicio;

                if ($idEvento == 0) {
                    $request_evento = $this->model->insertEvento($strTitulo, $strInicio, $strFin, $strDescripcion, $strColor);
                    $option = 1;
                } else {
                    $request_evento = $this->model->updateEvento($idEvento, $strTitulo, $strInicio, $strFin, $strDescripcion, $strColor);
                    $option = 2;
                }

                if ($request_evento > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Evento guardado correctamente.', 'id' => $request_evento);
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Evento actualizado correctamente.');
                    }
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'No es posible almacenar los datos.');
                }
            }
            jsonResponse($arrResponse, 200);
        }
        die();
    }

    public function delEvento()
    {
        if ($_POST) {
            $intId = intval($_POST['id']);
            $requestDelete = $this->model->deleteEvento($intId);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el evento');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el evento.');
            }
            jsonResponse($arrResponse, 200);
        }
        die();
    }
}
