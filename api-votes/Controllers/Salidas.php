<?php

class Salidas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSalidas()
    {
        $arrData = $this->model->selectSalidas();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '';
            $btnDelete = '';

            // Estado
            if ($arrData[$i]['estado_salida'] == 1) {
                $arrData[$i]['estado_salida'] = '<span class="badge badge-success">Entregado</span>';
            } else {
                $arrData[$i]['estado_salida'] = '<span class="badge badge-danger">Anulado</span>';
            }

            // Botones
            $btnEdit = '<button class="btn btn-primary btn-sm btnEditSalida" onClick="fntEditSalida(' . $arrData[$i]['id_salida'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelSalida" onClick="fntDelSalida(' . $arrData[$i]['id_salida'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getSalida($idsalida)
    {
        $intIdSalida = intval(strClean($idsalida));
        if ($intIdSalida > 0) {
            $arrData = $this->model->selectSalida($intIdSalida);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setSalida()
    {
        if ($_POST) {
            if (empty($_POST['listLider']) || empty($_POST['listElemento']) || empty($_POST['txtCantidad'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
            } else {
                $idSalida = intval($_POST['idSalida']);
                $strFecha = strClean($_POST['txtFecha']);
                $intLider = intval($_POST['listLider']);
                $intElemento = intval($_POST['listElemento']);
                $decCantidad = floatval($_POST['txtCantidad']);

                if ($idSalida == 0) {
                    // Crear
                    $request_user = $this->model->insertSalida($strFecha, $intLider, $intElemento, $decCantidad);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_user = $this->model->updateSalida($idSalida, $strFecha, $intLider, $intElemento, $decCantidad);
                    $option = 2;
                }

                if ($request_user > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Entrega registrada correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Entrega actualizada correctamente.');
                    }
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delSalida()
    {
        if ($_POST) {
            $intIdSalida = intval($_POST['idSalida']);
            $requestDelete = $this->model->deleteSalida($intIdSalida);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado (anulado) la entrega.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar la entrega.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Endpoints selects - getSelectLideres ahora trae metricas
    public function getSelectLideres()
    {
        $arrData = $this->model->selectLideresMetrics();
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getSelectElementos()
    {
        $arrData = $this->model->selectElementos();
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }
}
