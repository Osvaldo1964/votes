<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Usuario extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function usuario($idusuario)
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

            // 2. CORRECCIÓN: No uses json_decode si envías FormData desde JS.
            // PHP llena automáticamente la variable $_POST cuando usas FormData.
            // Si quieres soportar ambos, podrías hacer un check, pero lo normal es:
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
            if (empty($_POST['txtPassword'])) {
                jsonResponse(['status' => false, 'msg' => 'El password es requerido'], 200);
                die();
            }

            // 4. Limpieza de datos
            $strNombres = ucwords(strClean($_POST['txtNombre']));
            $strApellidos = ucwords(strClean($_POST['txtApellido']));
            $strTelefono = strClean($_POST['txtTelefono']);
            $strEmail = strClean(strtolower($_POST['txtEmail']));
            $intRolUsuario = intval($_POST['listRolid']);
            $strPassword = hash("SHA256", $_POST['txtPassword']);

            // 5. Llamada al modelo
            $request = $this->model->setUser($strNombres, $strApellidos, $strTelefono, $strEmail, $strPassword, $intRolUsuario);
            if ($request > 0) {
                // Si es un número (ID), la inserción fue exitosa
                $response = array('status' => true, 'msg' => 'Usuario registrado con éxito.');
            } else if ($request == 'exist') {
                // Si el modelo devolvió la palabra "exist"
                $response = array('status' => false, 'msg' => '¡Atención! El email ya está registrado, intente con otro.');
            } else {
                // Si hubo un error técnico (devolvió 0 o false)
                $response = array('status' => false, 'msg' => 'No es posible almacenar los datos en este momento.');
            }
            jsonResponse($response, 200);
            die();

        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function actualizar($idusuario)
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

                if (empty($data['nombres']) or !testString($data['nombres'])) {
                    $response = array('status' => false, 'msg' => 'Error en los nombres');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($data['apellidos']) or !testString($data['apellidos'])) {
                    $response = array('status' => false, 'msg' => 'Error en los apellidos');
                    jsonResponse($response, 200);
                    die();
                }
                if (empty($data['email']) or !testEmail($data['email'])) {
                    $response = array('status' => false, 'msg' => 'Error en el email');
                    jsonResponse($response, 200);
                    die();
                }

                $strNombres = ucwords(strClean($data['nombres']));
                $strApellidos = ucwords(strClean($data['apellidos']));
                $strEmail = strClean($data['email']);
                $strPassword = !empty($data['password']) ? hash("SHA256", $data['password']) : "";

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
                                                <button class="btn btn-info btn-sm btnPermisosRol" rl="' . $arrData[$i]['id_usuario'] . '" title="Ver Rol"><i class="fas fa-key"></i></button>
                                                <button class="btn btn-primary btn-sm btnEditRol" rl="' . $arrData[$i]['id_usuario'] . '" title="Editar Rol"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm btnDelRol" rl="' . $arrData[$i]['id_usuario'] . '" title="Eliminar Rol"><i class="fas fa-trash-alt"></i></button>
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

    public function eliminar($idusuario)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "DELETE") {
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
                $request = $this->model->deleteUsuario($idusuario);
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
