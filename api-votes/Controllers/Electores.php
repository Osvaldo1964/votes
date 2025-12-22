<?php

class Electores extends Controllers
{
    public function __construct()
    {
        parent::__construct();

        // 1. Manejo global de CORS Preflight (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }

        // 2. Validar token para todos los métodos de Roles
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);
        } catch (\Throwable $e) {
            $arrResponse = array('status' => false, 'msg' => 'Token inválido o expirado');
            jsonResponse($arrResponse, 401);
            die();
        }
    }

    public function getElector($idelector)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                if (empty($idelector) or !is_numeric($idelector)) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $arrElector = $this->model->getElector($idelector);
                if (empty($arrElector)) {
                    $response = array('status' => false, 'msg' => 'Registro no encontrado');
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrElector);
                }
                jsonResponse($response, 200);
            } else {
                jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function getElectores()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                // Obtener ID del usuario para validar botones de permisos r, u, d
                $rolUser = isset($_GET['rolUser']) ? intval($_GET['rolUser']) : 0;

                $arrData = $this->model->getRoles();

                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => "");
                } else {
                    // Consultamos permisos (El módulo de Roles suele ser el ID 1 o similar en tu tabla)
                    $requestPermisos = getPermisos($rolUser);

                    for ($i = 0; $i < count($arrData); $i++) {
                        // Formatear Badge de Estado
                        $arrData[$i]['estado_elector'] = ($arrData[$i]['estado_elector'] == 1)
                            ? '<span class="badge badge-success">Activo</span>'
                            : '<span class="badge badge-danger">Inactivo</span>';

                        // Lógica de botones según permisos
                        $btnPerm = '';
                        $btnEdit = '';
                        $btnDel = '';

                        // Asumiendo que validas permisos para el módulo de Roles
                        // Si tu tabla de permisos usa r_permiso, u_permiso, etc.
                        if (!empty($requestPermisos[3]['r_permiso'])) {
                            $btnPerm = '<button class="btn btn-info btn-sm btnPermisosElectores" rl="' . $arrData[$i]['id_elector'] . '" title="Permisos"><i class="fas fa-key"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-primary btn-sm btnEditElectores" rl="' . $arrData[$i]['id_elector'] . '" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDelElectores" rl="' . $arrData[$i]['id_elector'] . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                        }

                        $arrData[$i]['options'] = '<div class="text-center">' . $btnPerm . ' ' . $btnEdit . ' ' . $btnDel . '</div>';
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

    public function setElector()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (empty($_POST['txtNombre']) || empty($_POST['txtDescripcion'])) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                    die();
                }

                $intIdElector = intval($_POST['idelector']);
                $strNombre = strClean($_POST['txtNombre']);
                $strDescript = strClean($_POST['txtDescripcion']);
                $listEstado = intval($_POST['listStatus']);

                if ($intIdElector == 0) {
                    $request_rol = $this->model->insertElector($strNombre, $strDescript, $listEstado);
                    $option = 1;
                } else {
                    $request_rol = $this->model->updateElector($intIdElector, $strNombre, $strDescript, $listEstado);
                    $option = 2;
                }

                if ($request_rol > 0) {
                    $msg = ($option == 1) ? 'Datos guardados correctamente' : 'Datos actualizados correctamente.';
                    jsonResponse(['status' => true, 'msg' => $msg], 200);
                } else if ($request_rol == 'exist') {
                    jsonResponse(['status' => false, 'msg' => '¡Atención! El rol ya existe.'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible realizar la acción'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delElector()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                $data = json_decode(file_get_contents("php://input"), true);
                $idelector = isset($data['idelector']) ? intval($data['idelector']) : 0;

                if ($idrol <= 0) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $buscar_elector = $this->model->getElector($idelector);
                if (empty($buscar_elector)) {
                    jsonResponse(['status' => false, 'msg' => 'El elector no existe'], 400);
                    die();
                }

                $requestDelete = $this->model->deleteElector($idelector);
                if ($requestDelete == "ok") {
                    jsonResponse(['status' => true, 'msg' => 'Registro eliminado'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible eliminar el rol (tiene usuarios asociados o no existe)'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function getSelectElectores()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $arrData = $this->model->getElectores();
                if (count($arrData) > 0) {
                    jsonResponse(['status' => true, 'data' => $arrData], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No hay datos'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }
}