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
                $arrResponse = array('status' => false, 'msg' => 'Datos incorrectos.');
            } else {
                $idEvento = intval($_POST['id']);
                $strTitulo = strClean($_POST['title']);
                $strDescripcion = strClean($_POST['description']);
                $strColor = strClean($_POST['color']);
                $strFechaInicio = strClean($_POST['start']);
                $strFechaFin = !empty($_POST['end']) ? strClean($_POST['end']) : $strFechaInicio;

                if ($idEvento == 0) {
                    $option = 1;
                    $request_agenda = $this->model->insertEvento($strTitulo, $strFechaInicio, $strFechaFin, $strDescripcion, $strColor);
                } else {
                    $option = 2;
                    $request_agenda = $this->model->updateEvento($idEvento, $strTitulo, $strFechaInicio, $strFechaFin, $strDescripcion, $strColor);
                }

                if ($request_agenda > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Evento guardado correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Evento actualizado correctamente.');
                    }
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getAgendaReport()
    {
        if ($_POST) {
            if (empty($_POST['fechaInicio']) || empty($_POST['fechaFin'])) {
                $arrResponse = array('status' => false, 'msg' => 'Fechas incorrectas.');
            } else {
                $strFechaInicio = strClean($_POST['fechaInicio']);
                $strFechaFin = strClean($_POST['fechaFin']); // . ' 23:59:59' si quieres incluir todo el día

                // Si viene solo fecha YYYY-MM-DD, para comparar correctamente,
                // aseguramos que Fin cubra el final del día si es necesario, 
                // pero como es string comparison YYYY-MM-DDTHH:mm, depende del input.
                // Asumiremos que el frontend manda YYYY-MM-DD o timestamp compatible.

                $arrData = $this->model->selectAgendaReport($strFechaInicio, $strFechaFin);
                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'No hay eventos en este rango.');
                } else {
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
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
