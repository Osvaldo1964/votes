<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Usuario extends Controllers
{
    public function __construct()
    {
        parent::__construct();

        // 1. Manejo global de CORS Preflight (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }

        // 2. Identificar la ruta actual para excluir el login de la validación
        // Ajusta 'usuario/login' según tu sistema de rutas si es necesario
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $isLoginAction = (strpos($url, 'usuario/login') !== false);

        // 3. Validar token solo si NO es la acción de login
        if (!$isLoginAction) {
            try {
                $arrHeaders = getallheaders();
                fntAuthorization($arrHeaders);
            } catch (\Throwable $e) {
                $arrResponse = array('status' => false, 'msg' => 'Token inválido o expirado');
                jsonResponse($arrResponse, 401);
                die();
            }
        }
    }

    public function getUsuario($idusuario)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == "GET") {
                if (empty($idusuario) or !is_numeric($idusuario)) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $arrUser = $this->model->getUsuario($idusuario);
                if (empty($arrUser)) {
                    $response = array('status' => false, 'msg' => 'Registro no encontrado');
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrUser);
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

    public function setUsuario()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != "POST") {
                jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
                die();
            }

            if (empty($_POST)) {
                $_POST = json_decode(file_get_contents('php://input'), true);
            }

            // Validaciones básicas
            if (empty($_POST['txtNombre']) || !testString($_POST['txtNombre'])) {
                jsonResponse(['status' => false, 'msg' => 'Error en los nombres'], 200);
                die();
            }
            if (empty($_POST['txtEmail']) || !testString($_POST['txtEmail'])) {
                jsonResponse(['status' => false, 'msg' => 'Error en el email'], 200);
                die();
            }

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
                $msg = ($option == 1) ? 'Usuario registrado con éxito.' : 'Usuario actualizado con éxito.';
                jsonResponse(['status' => true, 'msg' => $msg], 200);
            } else if ($request === 'exist') {
                jsonResponse(['status' => false, 'msg' => '¡Atención! El email ya está registrado.'], 200);
            } else {
                jsonResponse(['status' => false, 'msg' => 'No es posible almacenar los datos.'], 200);
            }

        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function getUsers()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {

                // --- MEJORA DE SEGURIDAD ---
                // En lugar de $_GET, podrías obtener el ID del Token decodificado
                // Si ya tienes una variable global tras la validación, úsala:
                // $idUser = $_SESSION['idUser']; o extraerlo del payload del JWT.
                $rolUser = isset($_GET['rolUser']) ? intval($_GET['rolUser']) : 0;

                $arrData = $this->model->getUsuarios();

                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => '');
                } else {
                    // Consultamos los permisos del usuario que hace la petición
                    $requestPermisos = getPermisos($rolUser);
                    for ($i = 0; $i < count($arrData); $i++) {
                        $btnView = '';
                        $btnEdit = '';
                        $btnDel = '';

                        // Validamos cada permiso (r, u, d)
                        if (!empty($requestPermisos[2]['r_permiso'])) {
                            $btnView = '<button class="btn btn-info btn-sm btnViewUsuario" us="' . $arrData[$i]['id_usuario'] . '" title="Ver"><i class="fas fa-eye"></i></button>';
                        }
                        if (!empty($requestPermisos[2]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-primary btn-sm btnEditUsuario" us="' . $arrData[$i]['id_usuario'] . '" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[2]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDelUsuario" us="' . $arrData[$i]['id_usuario'] . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                        }

                        // Estado con Badge
                        $arrData[$i]['estado_usuario'] = ($arrData[$i]['estado_usuario'] == 1)
                            ? '<span class="badge badge-success">Activo</span>'
                            : '<span class="badge badge-danger">Inactivo</span>';

                        // Envolvemos en un div centrado para mejor visualización
                        $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDel . '</div>';
                    }
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrData);
                }
                jsonResponse($response, 200);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delUsuario($idusuario)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                if (empty($idusuario) or !is_numeric($idusuario)) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $buscar_usuario = $this->model->getUsuario($idusuario);
                if (empty($buscar_usuario)) {
                    jsonResponse(['status' => false, 'msg' => 'El usuario no existe'], 400);
                    die();
                }

                $request = $this->model->delUser($idusuario);
                if ($request) {
                    jsonResponse(['status' => true, 'msg' => 'Registro eliminado'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible eliminar el registro'], 200);
                }
            } else {
                jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function login()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $_POST = json_decode(file_get_contents('php://input'), true);
                if (empty($_POST['email_usuario']) || empty($_POST['password_usuario'])) {
                    jsonResponse(['status' => false, 'msg' => 'Error de datos'], 200);
                    die();
                }

                $strEmail = strClean($_POST['email_usuario']);
                $strPassword = hash("SHA256", $_POST['password_usuario']);
                $requestUser = $this->model->loginUser($strEmail, $strPassword);

                if (empty($requestUser)) {
                    jsonResponse(['status' => false, 'msg' => 'El usuario o la contraseña es incorrecto.'], 200);
                } else {
                    $tokenRequest = getTokenApi($requestUser); // Asegúrate que genere el payload con datos del usuario
                    if ($tokenRequest['status']) {
                        $arrAuth = $tokenRequest['data'];
                        $arrAuth['id_usuario'] = $requestUser['id_usuario'];
                        jsonResponse(['status' => true, 'msg' => '¡Bienvenido!', 'auth' => $arrAuth], 200);
                    } else {
                        jsonResponse(['status' => false, 'msg' => 'Error de autenticación'], 200);
                    }
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }
}