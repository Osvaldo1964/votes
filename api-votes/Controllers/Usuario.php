<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Usuario extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuario($idusuario)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "GET") {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================

                if (empty($idusuario) or !is_numeric($idusuario)) {
                    $response = array('status' => false, 'msg' => 'Error en los parametros');
                    jsonResponse($response, 400);
                    die();
                }
                $arrUser = $this->model->getUsuario($idusuario);
                if (empty($arrUser)) {
                    $response = array('status' => false, 'msg' => 'Registro no encontrado');
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrUser);
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

    public function setUsuario()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method != "POST") {
                jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
                die();
            }

            // 1. Validar token
            $arrHeaders = getallheaders();
            $auth = fntAuthorization($arrHeaders); // Asegúrate de validar el resultado de $auth

            if (empty($_POST)) {
                $_POST = json_decode(file_get_contents('php://input'), true);
            }
            // 3. Validaciones
            if (empty($_POST['txtNombre']) || !testString($_POST['txtNombre'])) {
                jsonResponse(['status' => false, 'msg' => 'Error en los nombres'], 200);
                die();
            }
            if (empty($_POST['txtApellido']) || !testString($_POST['txtApellido'])) {
                jsonResponse(['status' => false, 'msg' => 'Error en los apellidos'], 200);
                die();
            }
            if (empty($_POST['txtTelefono'])) {
                jsonResponse(['status' => false, 'msg' => 'Error en el telefono'], 200);
                die();
            }
            if (empty($_POST['txtEmail']) || !testString($_POST['txtEmail'])) {
                jsonResponse(['status' => false, 'msg' => 'Error en el email'], 200);
                die();
            }
            // 4. Limpieza de datos
            $idusuario = intval($_POST['idUsuario']);
            $strNombres = ucwords(strClean($_POST['txtNombre']));
            $strApellidos = ucwords(strClean($_POST['txtApellido']));
            $strTelefono = strClean($_POST['txtTelefono']);
            $strEmail = strClean(strtolower($_POST['txtEmail']));
            $intRolUsuario = intval($_POST['listRolid']);
            $intStatus = intval($_POST['listStatus']);

            if ($idusuario == 0) {
                $option = 1;
                $strPassword = empty($_POST['txtPassword']) ? hash("SHA256", passGenerator()) : hash("SHA256", $_POST['txtPassword']);
                $request = $this->model->setUser($strNombres, $strApellidos, $strTelefono, $strEmail, $strPassword, $intRolUsuario, $intStatus);
            } else {
                $option = 2;
                $strPassword = empty($_POST['txtPassword']) ? "" : hash("SHA256", $_POST['txtPassword']);
                $request = $this->model->putUser($idusuario, $strNombres, $strApellidos, $strTelefono, $strEmail, $strPassword, $intRolUsuario, $intStatus);
            }

            if ($request > 0) {
                if ($option == 1) {
                    $response = array('status' => true, 'msg' => 'Usuario registrado con éxito.');
                } else {
                    $response = array('status' => true, 'msg' => 'Usuario actualizado con éxito.');
                }
            } else if ($request === 'exist') {
                $response = array('status' => false, 'msg' => '¡Atención! El email ya está registrado.');
            } else {
                $response = array('status' => false, 'msg' => 'No es posible almacenar los datos en este momento.');
            }
            jsonResponse($response, 200);
            die();

        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function updateUser($idusuario)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "PUT") {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($idusuario) or !is_numeric($idusuario)) {
                    $response = array('status' => false, 'msg' => 'Error en los parametros');
                    $code = 400;
                    jsonResponse($response, $code);
                    die();
                }

                if (empty($data['txtNombre']) or !testString($data['txtNombre'])) {
                    $response = array('status' => false, 'msg' => 'Error en los nombres');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($data['txtApellido']) or !testString($data['txtApellido'])) {
                    $response = array('status' => false, 'msg' => 'Error en los apellidos');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($data['txtTelefono'])) {
                    $response = array('status' => false, 'msg' => 'Error en el telefono');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($data['txtEmail']) or !testEmail($data['txtEmail'])) {
                    $response = array('status' => false, 'msg' => 'Error en el email');
                    jsonResponse($response, 200);
                    die();
                }

                $strNombres = ucwords(strClean($data['txtNombre']));
                $strApellidos = ucwords(strClean($data['txtApellido']));
                $strTelefono = strClean($data['txtTelefono']);
                $strEmail = strClean($data['txtEmail']);
                $strPassword = !empty($data['txtPassword']) ? hash("SHA256", $data['txtPassword']) : "";

                $buscar_usuario = $this->model->getUsuario($idusuario);
                if (empty($buscar_usuario)) {
                    $response = array('status' => false, 'msg' => 'El usuario no existe');
                    $code = 400;
                    jsonResponse($response, $code);
                    die();
                }

                $request = $this->model->putUser(
                    $idusuario,
                    $strNombres,
                    $strApellidos,
                    $strEmail,
                    $strPassword
                );
                if ($request > 0) {
                    $arrUser = array(
                        'idusuario' => $idusuario,
                        'nombres' => $strNombres,
                        'apellidos' => $strApellidos,
                        'email' => $strEmail
                    );
                    $response = array('status' => true, 'msg' => 'Datos actualizados correctamente', 'data' => $arrUser);
                } else {
                    $response = array('status' => false, 'msg' => 'El email ya existe');
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

    public function getUsers()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "GET") {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================
                $arrData = $this->model->getUsuarios();
                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => '');
                } else {
                    for ($i = 0; $i < count($arrData); $i++) {
                        if ($arrData[$i]['estado_usuario'] == 1) {
                            $arrData[$i]['estado_usuario'] = '<span class="badge badge-success">Activo</span>';
                        } else {
                            $arrData[$i]['estado_usuario'] = '<span class="badge badge-danger">Inactivo</span>';
                        }
                        $btnView = '';
                        $btnEdit = '';
                        $btnDelete = '';
                        $arrData[$i]['options'] = '<div class="text-center">
                                                <button class="btn btn-info btn-sm btnViewUsuario" us="' . $arrData[$i]['id_usuario'] . '" title="Ver Usuario"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-primary btn-sm btnEditUsuario" us="' . $arrData[$i]['id_usuario'] . '" title="Editar Usuario"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm btnDelUsuario" us="' . $arrData[$i]['id_usuario'] . '" title="Eliminar Usuario"><i class="fas fa-trash-alt"></i></button>
                                            </div>';
                    }
                    $response = array('status' => true, 'msg' => 'Datos encontrados ', 'data' => $arrData);
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

    public function delUsuario($idusuario)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "PUT") {
                //================= Validar token ===================
                $arrHeaders = getallheaders();
                $reesponse = fntAuthorization($arrHeaders);
                //====================================================

                if (empty($idusuario) or !is_numeric($idusuario)) {
                    $response = array('status' => false, 'msg' => 'Error en los parametros');
                    jsonResponse($response, 400);
                    die();
                }

                $buscar_usuario = $this->model->getUsuario($idusuario);
                if (empty($buscar_usuario)) {
                    $response = array('status' => false, 'msg' => 'El usuario no existe o ya fue eliminado');
                    jsonResponse($response, 400);
                    die();
                }
                $request = $this->model->delUser($idusuario);
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

    public function login()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "POST") {
                $_POST = json_decode(file_get_contents('php://input'), true);
                if (empty($_POST['email_usuario']) || empty($_POST['password_usuario'])) {
                    $response = array('status' => false, 'msg' => 'Error de datos');
                    jsonResponse($response, 200);
                    die();
                }
                $strEmail = strClean($_POST['email_usuario']);
                $strPassword = hash("SHA256", $_POST['password_usuario']);
                $requestUser = $this->model->loginUser($strEmail, $strPassword);

                if (empty($requestUser)) {
                    $response = array('status' => false, 'msg' => 'El usuario o la contraseña es incorrecto.');
                } else {
                    $tokenRequest = getTokenApi();

                    if ($tokenRequest['status']) {
                        $arrAuth = $tokenRequest['data'];
                        $arrAuth['id_usuario'] = $requestUser['id_usuario'];
                        $code = 200;
                        $arrResponse = array('status' => true, 'msg' => '¡Bienvenido al sistema!', 'auth' => $arrAuth);
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Error de autenticación');
                        $code = 200;
                    }
                }
                jsonResponse($arrResponse, $code);
                die();
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
}
