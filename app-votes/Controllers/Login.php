<?php

class Login extends Controllers
{
	public function __construct()
	{
		session_start();
		if (isset($_SESSION['login'])) {
			header('Location: ' . base_url() . '/dashboard');
		}
		parent::__construct();
	}

	public function login()
	{
		$data['page_tag'] = "Login - Sistema Electoral";
		$data['page_title'] = "Login";
		$data['page_name'] = "login";
		$data['page_functions_js'] = "functions_login.js";
		$this->views->getView($this, "login", $data);
	}


	public function confirmUser(string $params)
	{

		if (empty($params)) {
			header('Location: ' . base_url());
		} else {
			$arrParams = explode(',', $params);
			$strEmail = strClean($arrParams[0]);
			$strToken = strClean($arrParams[1]);
			$arrResponse = $this->model->getUsuario($strEmail, $strToken);
			if (empty($arrResponse)) {
				header("Location: " . base_url());
			} else {
				$data['page_tag'] = "Cambiar contraseña";
				$data['page_name'] = "cambiar_contrasenia";
				$data['page_title'] = "Cambiar Contraseña";
				$data['email'] = $strEmail;
				$data['token'] = $strToken;
				$data['idpersona'] = $arrResponse['idpersona'];
				$data['page_functions_js'] = "functions_login.js";
				$this->views->getView($this, "cambiar_password", $data);
			}
		}
		die();
	}

	public function setPassword()
	{

		if (empty($_POST['idUsuario']) || empty($_POST['txtEmail']) || empty($_POST['txtToken']) || empty($_POST['txtPassword']) || empty($_POST['txtPasswordConfirm'])) {

			$arrResponse = array(
				'status' => false,
				'msg' => 'Error de datos'
			);
		} else {
			$intIdpersona = intval($_POST['idUsuario']);
			$strPassword = $_POST['txtPassword'];
			$strPasswordConfirm = $_POST['txtPasswordConfirm'];
			$strEmail = strClean($_POST['txtEmail']);
			$strToken = strClean($_POST['txtToken']);

			if ($strPassword != $strPasswordConfirm) {
				$arrResponse = array(
					'status' => false,
					'msg' => 'Las contraseñas no son iguales.'
				);
			} else {
				$arrResponseUser = $this->model->getUsuario($strEmail, $strToken);
				if (empty($arrResponseUser)) {
					$arrResponse = array(
						'status' => false,
						'msg' => 'Erro de datos.'
					);
				} else {
					$strPassword = hash("SHA256", $strPassword);
					$requestPass = $this->model->insertPassword($intIdpersona, $strPassword);

					if ($requestPass) {
						$arrResponse = array(
							'status' => true,
							'msg' => 'Contraseña actualizada con éxito.'
						);
					} else {
						$arrResponse = array(
							'status' => false,
							'msg' => 'No es posible realizar el proceso, intente más tarde.'
						);
					}
				}
			}
		}
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function crearSesion()
	{
		// 1. Recibir los datos enviados por JS
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);

		if ($data && isset($data['access_token'])) {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}

			// 2. Decodificar el JWT para obtener la expiración (exp)
			$tokenParts = explode('.', $data['access_token']);
			if (count($tokenParts) === 3) {
				$payload = base64_decode($tokenParts[1]);
				$payloadData = json_decode($payload, true);

				if (isset($payloadData['exp'])) {
					// Guardamos el timestamp de expiración en la sesión
					$_SESSION['timeout'] = $payloadData['exp'];
				}
			}

			// 3. Cargar las variables de sesión normales
			$_SESSION['login'] = true;
			$_SESSION['idUser'] = $data['id_usuario'];
			$_SESSION['userToken'] = $data['access_token'];
			$_SESSION['userData'] = $data;
			echo json_encode(['status' => true]);
		} else {
			echo json_encode(['status' => false, 'msg' => 'Datos insuficientes']);
		}
		die();
	}
}
