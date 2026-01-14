<?php

class Modulos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);
        } catch (\Throwable $e) {
            $arrResponse = array('status' => false, 'msg' => 'Token inválido o expirado');
            jsonResponse($arrResponse, 401);
            die();
        }
    }

    public function getModulos()
    {
        try {
            $arrData = $this->model->selectModulos();
            if (empty($arrData)) {
                $arrData = [];
            }

            // Add action buttons
            for ($i = 0; $i < count($arrData); $i++) {
                $btnEdit = '';
                $btnDel = '';

                if ($arrData[$i]['estado_modulo'] == 1) {
                    $arrData[$i]['estado_modulo'] = '<span class="badge badge-success">Activo</span>';
                } else {
                    $arrData[$i]['estado_modulo'] = '<span class="badge badge-danger">Inactivo</span>';
                }

                $btnEdit = '<button class="btn btn-primary btn-sm btnEditModulo" onClick="fntEditModulo(' . $arrData[$i]['id_modulo'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                $btnDel = '<button class="btn btn-danger btn-sm btnDelModulo" onClick="fntDelModulo(' . $arrData[$i]['id_modulo'] . ')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';

                $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDel . '</div>';
            }

            jsonResponse(['status' => true, 'data' => $arrData], 200);

        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function getModulo($idmodulo)
    {
        try {
            $idmodulo = intval($idmodulo);
            if ($idmodulo > 0) {
                $arrData = $this->model->selectModulo($idmodulo);
                if (empty($arrData)) {
                    jsonResponse(['status' => false, 'msg' => 'Datos no encontrados'], 200);
                } else {
                    jsonResponse(['status' => true, 'data' => $arrData], 200);
                }
            } else {
                jsonResponse(['status' => false, 'msg' => 'Parámetros incorrectos'], 200);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function setModulo()
    {
        try {
            if ($_POST) {
                if (empty($_POST['txtTitulo']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus'])) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                }

                $intIdModulo = intval($_POST['idModulo']);
                $strTitulo = strClean($_POST['txtTitulo']);
                $strDescripcion = strClean($_POST['txtDescripcion']);
                $intStatus = intval($_POST['listStatus']);

                if ($intIdModulo == 0) {
                    // Create
                    $request_modulo = $this->model->insertModulo($strTitulo, $strDescripcion, $intStatus);
                    $option = 1;
                } else {
                    // Update
                    $request_modulo = $this->model->updateModulo($intIdModulo, $strTitulo, $strDescripcion, $intStatus);
                    $option = 2;
                }

                if ($request_modulo > 0) {
                    if ($option == 1) {
                        jsonResponse(['status' => true, 'msg' => 'Datos guardados correctamente'], 200);
                    } else {
                        jsonResponse(['status' => true, 'msg' => 'Datos actualizados correctamente'], 200);
                    }
                } else if ($request_modulo == 'exist') {
                    jsonResponse(['status' => false, 'msg' => '¡Atención! El módulo ya existe.'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible almacenar los datos.'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delModulo()
    {
        try {
            if ($_POST) {
                $intIdModulo = intval($_POST['idModulo']);
                $requestDelete = $this->model->deleteModulo($intIdModulo);
                if ($requestDelete) {
                    jsonResponse(['status' => true, 'msg' => 'Se ha eliminado el módulo'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'Error al eliminar el módulo'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }
}
