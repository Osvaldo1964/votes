<?php

class Terceros extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // Validar sesión si es necesario, aunque en API suele ser Token
        // session_start();
        // if(empty($_SESSION['login'])) { ... }
    }

    public function getTerceros()
    {
        $arrData = $this->model->selectTerceros();

        // Formatear para DataTables y Botones de Acción
        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '';
            $btnDelete = '';

            // Estado (Visual)
            if ($arrData[$i]['estado_tercero'] == 1) {
                $arrData[$i]['estado_tercero'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['estado_tercero'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            // Botones
            $btnEdit = '<button class="btn btn-primary btn-sm btnEditTercero" onClick="fntEditTercero(' . $arrData[$i]['id_tercero'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelTercero" onClick="fntDelTercero(' . $arrData[$i]['id_tercero'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

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

    public function getTercero($idtercero)
    {
        $intIdTercero = intval(strClean($idtercero));
        if ($intIdTercero > 0) {
            $arrData = $this->model->selectTercero($intIdTercero);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setTercero()
    {
        if ($_POST) {
            if (empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idTercero = intval($_POST['idTercero']);
                $strIdentificacion = strClean($_POST['txtIdentificacion']);
                $strNombre = strtoupper(strClean($_POST['txtNombre'])); // Mayúsculas
                $strTelefono = strClean($_POST['txtTelefono']);
                $strEmail = strClean($_POST['txtEmail']);
                $strDireccion = strClean($_POST['txtDireccion']);

                if ($idTercero == 0) {
                    // Crear
                    $request_user = $this->model->insertTercero($strIdentificacion, $strNombre, $strTelefono, $strEmail, $strDireccion);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_user = $this->model->updateTercero($idTercero, $strIdentificacion, $strNombre, $strTelefono, $strEmail, $strDireccion);
                    $option = 2;
                }

                if ($request_user > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                    }
                } else if ($request_user == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! La Identificación ya existe.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delTercero()
    {
        if ($_POST) {
            $intIdTercero = intval($_POST['idTercero']);
            $requestDelete = $this->model->deleteTercero($intIdTercero);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el tercero');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el tercero.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
