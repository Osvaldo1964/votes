<?php
class Roles extends Controllers
{

    public function __construct()
    {

        try {
            //================= Validar token ===================
            $arrHeaders = getallheaders();
            //dep($arrHeaders);exit;
            //$arrHeaders['Authorization'] = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6NCwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1hQGVtcHJlc2ExLmNvbSIsImlhdCI6MTc2NTc1NjEyMywiZXhwIjoxNzY1NzU5NzIzfQ.zyCETn0dGyn89uiRBucutDVomgewgliT7lYsxxxIcQgJBU6sPCuu-ksc1wW-nRGMku1Yk-btyyjwhpipyUm0wQ';
            $reesponse = fntAuthorization($arrHeaders);
            //====================================================
        } catch (\Throwable $e) {
            $arrResponse = array('status' => false, 'msg' => $e->getMessage());
            jsonResponse($arrResponse, 400);
            die();
        }
        parent::__construct();
    }

    public function getRol($idrol)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "GET") {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================

                if (empty($idrol) or !is_numeric($idrol)) {
                    $response = array('status' => false, 'msg' => 'Error en los parametros');
                    jsonResponse($response, 400);
                    die();
                }
                $arrRol = $this->model->getRol($idrol);
                if (empty($arrRol)) {
                    $response = array('status' => false, 'msg' => 'Registro no encontrado');
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrRol);
                }
                $code = 200;
            } else {
                $response = array('status' => false, 'msg' => 'Error en la solicitud ' . $method);
                $code = 400;
            }
            jsonResponse($response, $code);
            die();
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => $e->getMessage());
            jsonResponse($arrResponse, 400);
        }
        die();
    }

    public function getRoles()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "GET") {
                $arrData = $this->model->getRoles();
                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => "");
                } else {
                    for ($i = 0; $i < count($arrData); $i++) {
                        if ($arrData[$i]['status_rol'] == 1) {
                            $arrData[$i]['status_rol'] = '<span class="badge badge-success">Activo</span>';
                        } else {
                            $arrData[$i]['status_rol'] = '<span class="badge badge-danger">Inactivo</span>';
                        }
                        $btnView = '';
                        $btnEdit = '';
                        $btnDelete = '';
                        $arrData[$i]['options'] = '<div class="text-center">
                                                <button class="btn btn-info btn-sm btnPermisosRol" rl="' . $arrData[$i]['id_rol'] . '" title="Ver Rol"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-primary btn-sm btnEditRol" rl="' . $arrData[$i]['id_rol'] . '" title="Editar Rol"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm btnDelRol" rl="' . $arrData[$i]['id_rol'] . '" title="Eliminar Rol"><i class="fas fa-trash-alt"></i></button>
                                            </div>';
                    }
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrData);
                }
                $code = 200;
            } else {
                $response = array('status' => false, 'msg' => 'Error en la solicitud ' . $method);
                $code = 400;
            }
            jsonResponse($response, $code);
            die();
        } catch (\Throwable $th) {
            echo "Error en el proceso: " . $e->getMessage();
        }
        die();
    }

    public function setRol()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "POST") {
                //$_POST = json_decode(file_get_contents('php://input'), true);

                if (empty($_POST['txtNombre'])) {
                    $response = array('status' => false, 'msg' => 'El nombre es cvcvcvcvrequerido');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($_POST['txtDescripcion'])) {
                    $response = array('status' => false, 'msg' => 'La descripcion es requerida');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($_POST['listStatus']) or !is_numeric($_POST['listStatus'])) {
                    $response = array('status' => false, 'msg' => 'El estado es requerido');
                    jsonResponse($response, 200);
                    die();
                }

                $intIdRol = intval($_POST['idRol']);
                $strNombre = strClean($_POST['txtNombre']);
                $strDescript = strClean($_POST['txtDescripcion']);
                $listEstado = strClean($_POST['listStatus']);

                if ($intIdRol == 0) {
                    // Crear
                    $request_rol = $this->model->setRol($strNombre, $strDescript, $listEstado);
                    $option = 1;
                } else {
                    // Actualizar
                    $request_rol = $this->model->updateRol($intIdRol, $strNombre, $strDescript, $listEstado);
                    $option = 2;
                }

                if ($request_rol > 0) {
                    if ($option == 1) {
                        $response = array('status' => true, 'msg' => 'Datos guardados correctamente', 'data' => $request_rol);
                    } else {
                        $response = array('status' => true, 'msg' => 'Datos actualizados correctamente.', 'data' => $request_rol);
                    }
                } else if ($request_rol == 'exist') {
                    $response = array('status' => false, 'msg' => '¡Atención! El rol ya existe.');
                } else {
                    $response = array('status' => false, 'msg' => 'No es posible crear el rol', 'msg_tecnico' => $request_rol);
                }
                $code = 200;
            } else {
                $response = array('status' => false, 'msg' => 'Error en la solicitud ' . $method);
                $code = 400;
            }
            jsonResponse($response, $code);
            die();
        } catch (Exception $e) {
            echo "Error en el proceso: " . $e->getMessage();
        }
        die();
    }

    public function delRol($irol)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "PUT") {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================
                $data = json_decode(file_get_contents("php://input"), true);
                $idrol = $data['idrol'];
                if (empty($idrol) or !is_numeric($idrol)) {
                    $response = array('status' => false, 'msg' => 'Error en los parametros');
                    jsonResponse($response, 400);
                    die();
                }

                $buscar_rol = $this->model->getRol($idrol);
                if (empty($buscar_rol)) {
                    $response = array('status' => false, 'msg' => 'El rol no existe o ya fue eliminado');
                    jsonResponse($response, 400);
                    die();
                }
                $request = $this->model->deleteRol($idrol);
                if ($request) {
                    $response = array('status' => true, 'msg' => 'Registro eliminado');
                } else {
                    $response = array('status' => false, 'msg' => 'No es posible eliminar el registro');
                }
                $code = 200;
            } else {
                $response = array('status' => false, 'msg' => 'Error en la solicitud ' . $method);
                $code = 400;
            }
            jsonResponse($response, $code);
            die();
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => $e->getMessage());
            jsonResponse($arrResponse, 400);
        }
        die();
    }
}
