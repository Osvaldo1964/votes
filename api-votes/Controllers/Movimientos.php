<?php

class Movimientos extends Controllers
{
    public function __construct()
    {
        parent::__construct();

        // 1. Manejo global de CORS Preflight (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }

        // 2. Validar token
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);
        } catch (\Throwable $e) {
            $arrResponse = array('status' => false, 'msg' => 'Token inválido o expirado');
            jsonResponse($arrResponse, 401);
            die();
        }
    }

    public function getMovimientos()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                // Obtener ID (Rol) del usuario para validar permisos
                $rolUser = isset($_GET['rolUser']) ? intval($_GET['rolUser']) : 0;

                $arrData = $this->model->selectMovimientos();

                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => "");
                } else {
                    $requestPermisos = getPermisos($rolUser);

                    for ($i = 0; $i < count($arrData); $i++) {
                        // Formatear Dinero
                        $arrData[$i]['valor_fmt'] = SMONEY . formatMoney($arrData[$i]['valor_movimiento']);

                        // Formatear Tipo (Visualmente Aporte/Gasto basado en concepto)
                        // tipo_operacion viene del JOIN con conceptos: 1=Ingreso, 2=Gasto
                        $tipoOp = $arrData[$i]['tipo_operacion'];
                        if ($tipoOp == 1) {
                            $arrData[$i]['tipo_badged'] = '<span class="badge badge-success">Ingreso</span>';
                        } else {
                            $arrData[$i]['tipo_badged'] = '<span class="badge badge-danger">Gasto</span>';
                        }

                        // Botones de Acción
                        $btnEdit = '';
                        $btnDel = '';

                        // Permisos ID 10 para Movimientos (según nav_admin.php)
                        if (!empty($requestPermisos[10]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-primary btn-sm btnEditMovimiento" onClick="fntEditMovimiento(' . $arrData[$i]['id_movimiento'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[10]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDelMovimiento" onClick="fntDelMovimiento(' . $arrData[$i]['id_movimiento'] . ')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                        }

                        $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDel . '</div>';
                    }
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrData);
                }
                jsonResponse($response, 200);
            }
        } catch (\Throwable $th) {
            jsonResponse(['status' => false, 'msg' => $th->getMessage()], 500);
        }
        die();
    }

    public function getMovimiento($idmovimiento)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $idmovimiento = intval($idmovimiento);
                if ($idmovimiento > 0) {
                    $arrData = $this->model->selectMovimiento($idmovimiento);
                    if (empty($arrData)) {
                        $response = array('status' => false, 'msg' => 'Datos no encontrados.');
                    } else {
                        $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrData);
                    }
                    jsonResponse($response, 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function setMovimiento()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $_POST = json_decode(file_get_contents('php://input'), true);

                if (
                    empty($_POST['fecha_movimiento']) || empty($_POST['tercero_movimiento']) ||
                    empty($_POST['concepto_movimiento']) || empty($_POST['valor_movimiento'])
                ) {
                    $response = array('status' => false, 'msg' => 'Todos los campos obligatorios deben ser llenados.');
                    jsonResponse($response, 200);
                    die();
                }

                $intIdMovimiento = intval($_POST['id_movimiento']);
                $strFecha = strClean($_POST['fecha_movimiento']);
                $intTercero = intval($_POST['tercero_movimiento']);
                $intConcepto = intval($_POST['concepto_movimiento']);
                // tipo_movimiento ahora es Norma Contable
                $intTipo = isset($_POST['tipo_movimiento']) ? intval($_POST['tipo_movimiento']) : 1;
                $strObservacion = strClean($_POST['obs_movimiento']);
                $decValor = floatval($_POST['valor_movimiento']);

                if ($intIdMovimiento == 0) {
                    // Crear
                    $request_movimiento = $this->model->insertMovimiento($strFecha, $intTercero, $intConcepto, $intTipo, $strObservacion, $decValor);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_movimiento = $this->model->updateMovimiento($intIdMovimiento, $strFecha, $intTercero, $intConcepto, $intTipo, $strObservacion, $decValor);
                    $option = 2;
                }

                if ($request_movimiento > 0) {
                    $msg = ($option == 1) ? 'Movimiento registrado correctamente.' : 'Movimiento actualizado correctamente.';
                    $response = array('status' => true, 'msg' => $msg);
                } else {
                    $response = array('status' => false, 'msg' => 'No es posible almacenar los datos.');
                }
                jsonResponse($response, 200);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delMovimiento()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $_POST = json_decode(file_get_contents('php://input'), true);
                $intIdMovimiento = intval($_POST['id_movimiento']);

                $requestDelete = $this->model->deleteMovimiento($intIdMovimiento);
                if ($requestDelete) {
                    $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el movimiento');
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el movimiento.');
                }
                jsonResponse($arrResponse, 200);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    // Endpoint auxiliar para llenar combos desde un solo llamado si se desea,
    // pero idealmente el frontend llamará a terceros/getSelectTerceros y conceptos/getSelectConceptos por separado o juntos.
    public function getSelects()
    {
        $arrTerceros = $this->model->selectTerceros();
        $arrConceptos = $this->model->selectConceptos();

        $arrResponse = array(
            'terceros' => $arrTerceros,
            'conceptos' => $arrConceptos
        );
        jsonResponse($arrResponse, 200);
        die();
    }
}
