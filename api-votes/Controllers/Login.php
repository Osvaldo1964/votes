<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Login extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loginUser()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "POST") {
                //$_POST = json_decode(file_get_contents('php://input'), true);

                if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
                    $response = array('status' => false, 'msg' => 'Error de datos');
                    jsonResponse($response, 200);
                    die();
                }
                $strEmail = strClean($_POST['txtEmail']);
                $strPassword = hash("SHA256", $_POST['txtPassword']);
                $requestUser = $this->model->loginUser($strEmail, $strPassword);
                if (empty($requestUser)) {
                    $response = array('status' => false, 'msg' => 'El usuario o la contraseña es incorrecto.');
                    $code = 400;
                } else {
                    $tokenRequest = getTokenApi();

                    if ($tokenRequest['status']) {
                        $arrAuth = $tokenRequest['data'];
                        $arrAuth['id_usuario'] = $requestUser['id_usuario'];
                        $arrAuth['nombre_usuario'] = $requestUser['nombres_usuario'] . ' ' . $requestUser['apellidos_usuario'];
                        $arrAuth['email_usuario'] = $strEmail;
                        $arrAuth['rol_usuario'] = $requestUser['rol_usuario'];
                        $arrAuth['telefono_usuario'] = $requestUser['telefono_usuario'];
                        $arrAuth['nombre_rol'] = $requestUser['nombre_rol'];
                        $_SESSION['login'] = true;
                        $code = 200;
                        $response = array('status' => true, 'msg' => '¡Bienvenido al sistema!', 'auth' => $arrAuth);
                    } else {
                        $response = array('status' => false, 'msg' => 'Error de autenticación');
                        $code = 200;
                    }
                }
                jsonResponse($response, $code);
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

    public function resetPass()
    {
        if ($_POST) {
            //error_reporting(0);

            if (empty($_POST['txtEmailReset'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                $token = token();
                $strEmail = strtolower(strClean($_POST['txtEmailReset']));
                $arrData = $this->model->getUserEmail($strEmail);

                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Usuario no existente.');
                } else {
                    $idpersona = $arrData['id_usuario'];
                    $nombreUsuario = $arrData['nombres_usuario'] . ' ' . $arrData['apellidos_usuario'];

                    $url_recovery = 'http://app-votes.com/login/confirmUser/' . $strEmail . '/' . $token;
                    $requestUpdate = $this->model->setTokenUser($idpersona, $token);

                    $dataUsuario = array(
                        'nombreUsuario' => $nombreUsuario,
                        'email' => $strEmail,
                        'asunto' => 'Recuperar cuenta - ' . NOMBRE_REMITENTE,
                        'url_recovery' => $url_recovery
                    );
                    if ($requestUpdate) {
                        $sendEmail = sendEmail($dataUsuario, 'email_cambioPassword');

                        if ($sendEmail) {
                            $arrResponse = array(
                                'status' => true,
                                'msg' => 'Se ha enviado un email a tu cuenta de correo para cambiar tu contraseña.'
                            );
                        } else {
                            $arrResponse = array(
                                'status' => false,
                                'msg' => 'No es posible realizar el proceso, intenta más tarde.'
                            );
                        }
                    } else {
                        $arrResponse = array(
                            'status' => false,
                            'msg' => 'No es posible realizar el proceso, intenta más tarde.'
                        );
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
