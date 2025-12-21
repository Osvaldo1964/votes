<?php

class Permisos extends Controllers
{
	public function __construct()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			die(); // Finaliza la ejecución para OPTIONS con un status 200 implícito
		}
		try {
			$arrHeaders = getallheaders();
			fntAuthorization($arrHeaders);
		} catch (\Throwable $e) {
			$arrResponse = array('status' => false, 'msg' => 'Token inválido o expirado');
			jsonResponse($arrResponse, 401); // <-- CAMBIAR A 401
			die();
		}
		parent::__construct();
	}

	public function getPermisosRol($idrol)
	{
		try {
			$method = $_SERVER['REQUEST_METHOD'];
			$response = [];
			if ($method == "GET") {

				if (empty($idrol) or !is_numeric($idrol)) {
					$response = array('status' => false, 'msg' => 'Error en los parametros');
					jsonResponse($response, 400);
					die();
				}

				$arrModulos = $this->model->selectModulos();
				$arrPermisosRol = $this->model->selectPermisosRol($idrol);
				$arrPermisos = array('r' => 0, 'w' => 0, 'u' => 0, 'd' => 0);
				$arrPermisoRol = array('idrol' => $idrol);

				if (empty($arrPermisosRol)) {
					for ($i = 0; $i < count($arrModulos); $i++) {
						$arrModulos[$i]['permisos'] = $arrPermisos;
					}
				} else {
					for ($i = 0; $i < count($arrModulos); $i++) {
						$arrPermisos = array('r' => 0, 'w' => 0, 'u' => 0, 'd' => 0);
						if (isset($arrPermisosRol[$i])) {
							$arrPermisos = array(
								'r' => $arrPermisosRol[$i]['r_permiso'],
								'w' => $arrPermisosRol[$i]['w_permiso'],
								'u' => $arrPermisosRol[$i]['u_permiso'],
								'd' => $arrPermisosRol[$i]['d_permiso']
							);
						}
						$arrModulos[$i]['permisos'] = $arrPermisos;
					}
				}
				$arrPermisosRol['modulos'] = $arrModulos;


				if (empty($arrModulos)) {
					$response = array('status' => false, 'msg' => 'Registro no encontrado');
				} else {
					$response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrPermisosRol['modulos']);
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

	public function setPermisos()
	{
		if ($_POST) {
			$intIdrol = intval($_POST['idrol']);
			$modulos = $_POST['modulos'];

			$this->model->deletePermisos($intIdrol);
			foreach ($modulos as $modulo) {
				$idModulo = $modulo['idmodulo'];
				$r = empty($modulo['r']) ? 0 : 1;
				$w = empty($modulo['w']) ? 0 : 1;
				$u = empty($modulo['u']) ? 0 : 1;
				$d = empty($modulo['d']) ? 0 : 1;
				$requestPermiso = $this->model->insertPermisos($intIdrol, $idModulo, $r, $w, $u, $d);
			}
			if ($requestPermiso > 0) {
				$arrResponse = array('status' => true, 'msg' => 'Permisos asignados correctamente.');
			} else {
				$arrResponse = array("status" => false, "msg" => 'No es posible asignar los permisos.');
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}

}
?>