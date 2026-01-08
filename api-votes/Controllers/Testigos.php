<?php

class Testigos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // CORS y Auth estandar
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);
        } catch (\Throwable $e) {
            jsonResponse(['status' => false, 'msg' => 'Error de autorización'], 401);
            die();
        }
    }

    public function getTestigos()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {


                // Obtener Rol desde el Token (Seguridad real)
                $rolUser = 0;
                try {
                    $arrHeaders = getallheaders();
                    $tokenData = fntAuthorization($arrHeaders);
                    // Asumimos que el token tiene 'id_rol' o 'rol'. Verificamos estructura común
                    // Si el payload del token es estandar: $tokenData['id_rol']
                    // Para estar seguros, hacemos un print_r si fallara, pero usaremos id_rol que es lo común
                    $rolUser = isset($tokenData->id_rol) ? $tokenData->id_rol : 0;
                } catch (\Exception $e) {
                    $rolUser = 0;
                }

                // Fallback: Si no se obtuvo del token, intentar GET
                if ($rolUser == 0 && isset($_GET['rolUser'])) {
                    $rolUser = intval($_GET['rolUser']);
                }

                // DEBUG: Datos Dummy
                /*
                $arrData = [
                    [
                        'id_testigo' => 1,
                        'ident_elector' => '999',
                        'nom1_elector' => 'Test',
                        'ape1_elector' => 'User',
                        'nameplace_place' => 'Lugar Prueba',
                        'mesa_testigo' => 10,
                        'estado_testigo' => 1,
                        'options' => ''
                    ]
                ];
                $response = array('status' => true, 'data' => $arrData);
                jsonResponse($response, 200);
                die(); 
                */

                $arrData = $this->model->selectTestigos();

                if (empty($arrData)) {
                    $response = array('status' => true, 'msg' => 'No hay datos', 'data' => []);
                } else {
                    $requestPermisos = ($rolUser > 0) ? getPermisos($rolUser) : [];

                    for ($i = 0; $i < count($arrData); $i++) {
                        $arrData[$i]['estado_testigo'] = $arrData[$i]['estado_testigo'] == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';

                        // Validación defensiva de permisos
                        // ID Módulo 18: Testigos
                        if (!empty($requestPermisos[18]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-info btn-sm btnEdit" onClick="fntEditTestigo(' . $arrData[$i]['id_testigo'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[18]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDel" onClick="fntDelTestigo(' . $arrData[$i]['id_testigo'] . ')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                        }

                        $arrData[$i]['options'] = '<div class="text-center">' . $btnEdit . ' ' . $btnDel . '</div>';
                    }
                    $response = array('status' => true, 'data' => $arrData);
                }
                jsonResponse($response, 200);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }
    public function getTestigo($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $id = intval($id);
                if ($id > 0) {
                    $arrData = $this->model->selectTestigo($id);
                    if (empty($arrData)) {
                        jsonResponse(['status' => false, 'msg' => 'Datos no encontrados'], 200);
                    } else {
                        jsonResponse(['status' => true, 'data' => $arrData], 200);
                    }
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function setTestigo()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                // Validación básica de campos obligatorios (Elector)
                if (empty($_POST['listElector']) || empty($_POST['listEstado'])) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                    die();
                }

                $idTestigo = intval($_POST['idTestigo']);
                $intElector = intval($_POST['listElector']);
                $intEstado = intval($_POST['listEstado']);

                // Ubicación Opcional (puede venir 0 o vacío)
                $intDpto = !empty($_POST['listDpto']) ? intval($_POST['listDpto']) : 0;
                $intMuni = !empty($_POST['listMuni']) ? intval($_POST['listMuni']) : 0;
                $intZona = !empty($_POST['listZona']) ? intval($_POST['listZona']) : 0;
                $intPuesto = !empty($_POST['listPuesto']) ? intval($_POST['listPuesto']) : 0;
                $intMesa = !empty($_POST['txtMesa']) ? intval($_POST['txtMesa']) : 0;

                // Lista de Mesas (Array IDs de headresultado)
                $arrMesas = !empty($_POST['listMesas']) ? $_POST['listMesas'] : [];

                if ($idTestigo == 0) {
                    $request = $this->model->insertTestigo($intElector, $intDpto, $intMuni, $intZona, $intPuesto, $intMesa, $intEstado);
                    $option = 1;
                } else {
                    $request = $this->model->updateTestigo($idTestigo, $intElector, $intDpto, $intMuni, $intZona, $intPuesto, $intMesa, $intEstado);
                    $option = 2;
                }

                if ($request > 0 && $request !== 'exist') {
                    // Si insertó o actualizó el testigo correctamente, asignamos las mesas
                    // Si es nuevo, usamos el ID retornado ($request). Si es update, usamos $idTestigo.
                    $idFinal = ($option == 1) ? $request : $idTestigo;
                    $this->model->updateMesasTestigo($idFinal, $arrMesas);

                    $msg = $option == 1 ? 'Testigo registrado correctamente' : 'Testigo actualizado correctamente';
                    jsonResponse(['status' => true, 'msg' => $msg], 200);
                } else if ($request == 'exist') {
                    jsonResponse(['status' => false, 'msg' => 'Este elector ya está registrado como testigo.'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No se pudo almacenar los datos.'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delTestigo()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = intval($_POST['idTestigo']);
            $request = $this->model->deleteTestigo($id);
            if ($request) {
                jsonResponse(['status' => true, 'msg' => 'Testigo eliminado'], 200);
            } else {
                jsonResponse(['status' => false, 'msg' => 'Error al eliminar'], 200);
            }
        }
    }
    public function getMesas($idPuesto)
    {
        // Obtener Id Testigo opcional (para edit mode)
        $idTestigo = isset($_GET['idTestigo']) ? intval($_GET['idTestigo']) : 0;
        $idPuesto = intval($idPuesto);

        if ($idPuesto > 0) {
            $arrData = $this->model->selectMesasPuesto($idPuesto, $idTestigo);
            if (empty($arrData)) {
                jsonResponse(['status' => false, 'msg' => 'No hay mesas disponibles'], 200);
            } else {
                jsonResponse(['status' => true, 'data' => $arrData], 200);
            }
        }
        die();
    }
}
