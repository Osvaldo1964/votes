<?php

class Conceptos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getConceptos()
    {
        $arrData = $this->model->selectConceptos();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '';
            $btnDelete = '';

            // Tipo (Visual)
            if ($arrData[$i]['tipo_concepto'] == 1) {
                // Ingreso
                $arrData[$i]['tipo_concepto'] = '<span class="badge badge-success"><i class="fas fa-plus"></i> Ingreso</span>';
            } else {
                // Gasto
                $arrData[$i]['tipo_concepto'] = '<span class="badge badge-danger"><i class="fas fa-minus"></i> Gasto</span>';
            }

            // Estado
            if ($arrData[$i]['estado_concepto'] == 1) {
                $arrData[$i]['estado_concepto'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['estado_concepto'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            // Botones
            $btnEdit = '<button class="btn btn-primary btn-sm btnEditConcepto" onClick="fntEditConcepto(' . $arrData[$i]['id_concepto'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm btnDelConcepto" onClick="fntDelConcepto(' . $arrData[$i]['id_concepto'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getConcepto($idconcepto)
    {
        $intIdConcepto = intval(strClean($idconcepto));
        if ($intIdConcepto > 0) {
            $arrData = $this->model->selectConcepto($intIdConcepto);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setConcepto()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre']) || empty($_POST['listTipo'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idConcepto = intval($_POST['idConcepto']);
                $strNombre = strtoupper(strClean($_POST['txtNombre']));
                $intTipo = intval(strClean($_POST['listTipo']));

                if ($idConcepto == 0) {
                    // Crear
                    $request_user = $this->model->insertConcepto($strNombre, $intTipo);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_user = $this->model->updateConcepto($idConcepto, $strNombre, $intTipo);
                    $option = 2;
                }

                if ($request_user > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                    }
                } else if ($request_user == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! El nombre del concepto ya existe.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delConcepto()
    {
        if ($_POST) {
            $intIdConcepto = intval($_POST['idConcepto']);
            $requestDelete = $this->model->deleteConcepto($intIdConcepto);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el concepto');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el concepto.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
