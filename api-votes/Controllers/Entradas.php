<?php

class Entradas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getEntradas()
    {
        $arrData = $this->model->selectEntradas();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '';
            $btnDelete = '';

            // Formato Moneda
            $arrData[$i]['total_entrada'] = '$ ' . number_format($arrData[$i]['total_entrada'], 0);
            $arrData[$i]['unitario_entrada'] = '$ ' . number_format($arrData[$i]['unitario_entrada'], 0);

            // Estado
            if ($arrData[$i]['estado_entrada'] == 1) {
                $arrData[$i]['estado_entrada'] = '<span class="badge badge-success">Registrada</span>';
            } else {
                $arrData[$i]['estado_entrada'] = '<span class="badge badge-danger">Anulada</span>';
            }

            // Botones
            $btnEdit = '<button class="btn btn-primary btn-sm btnEditEntrada" onClick="fntEditEntrada(' . $arrData[$i]['id_entrada'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelEntrada" onClick="fntDelEntrada(' . $arrData[$i]['id_entrada'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
        }

        if (empty($arrData)) {
            $arrResponse = array('status' => false, 'msg' => 'No hay datos para mostrar.');
        } else {
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getEntrada($identrada)
    {
        $intIdEntrada = intval(strClean($identrada));
        if ($intIdEntrada > 0) {
            $arrData = $this->model->selectEntrada($intIdEntrada);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setEntrada()
    {
        if ($_POST) {
            if (empty($_POST['listTercero']) || empty($_POST['listElemento']) || empty($_POST['txtCantidad']) || empty($_POST['txtTotal'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
            } else {
                $idEntrada = intval($_POST['idEntrada']);
                $strFecha = strClean($_POST['txtFecha']);
                $intTercero = intval($_POST['listTercero']);
                $strFactura = strClean($_POST['txtFactura']);
                $intElemento = intval($_POST['listElemento']);
                $decCantidad = floatval($_POST['txtCantidad']);
                $decUnitario = floatval($_POST['txtUnitario']);
                $decTotal = floatval($_POST['txtTotal']);

                if ($idEntrada == 0) {
                    // Crear
                    $request_user = $this->model->insertEntrada($strFecha, $intTercero, $strFactura, $intElemento, $decCantidad, $decUnitario, $decTotal);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_user = $this->model->updateEntrada($idEntrada, $strFecha, $intTercero, $strFactura, $intElemento, $decCantidad, $decUnitario, $decTotal);
                    $option = 2;
                }

                if ($request_user > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Entrada registrada correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Entrada actualizada correctamente.');
                    }
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delEntrada()
    {
        if ($_POST) {
            $intIdEntrada = intval($_POST['idEntrada']);
            $requestDelete = $this->model->deleteEntrada($intIdEntrada);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado (anulado) la entrada.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar la entrada.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Endpoints para llenar combos en el modal
    public function getSelectTerceros()
    {
        $arrData = $this->model->selectTerceros();
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
