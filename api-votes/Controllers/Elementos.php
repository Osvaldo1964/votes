<?php

class Elementos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getElementos()
    {
        $arrData = $this->model->selectElementos();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '';
            $btnDelete = '';

            // Estado
            if ($arrData[$i]['estado_elemento'] == 1) {
                $arrData[$i]['estado_elemento'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['estado_elemento'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            // Botones
            $btnEdit = '<button class="btn btn-primary btn-sm btnEditElemento" onClick="fntEditElemento(' . $arrData[$i]['id_elemento'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelElemento" onClick="fntDelElemento(' . $arrData[$i]['id_elemento'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getElemento($idelemento)
    {
        $intIdElemento = intval(strClean($idelemento));
        if ($intIdElemento > 0) {
            $arrData = $this->model->selectElemento($intIdElemento);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setElemento()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idElemento = intval($_POST['idElemento']);
                $strNombre = strtoupper(strClean($_POST['txtNombre']));

                if ($idElemento == 0) {
                    // Crear
                    $request_user = $this->model->insertElemento($strNombre);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_user = $this->model->updateElemento($idElemento, $strNombre);
                    $option = 2;
                }

                if ($request_user > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                    }
                } else if ($request_user == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! El elemento ya existe.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delElemento()
    {
        if ($_POST) {
            $intIdElemento = intval($_POST['idElemento']);
            $requestDelete = $this->model->deleteElemento($intIdElemento);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el elemento');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el elemento.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
