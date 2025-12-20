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
