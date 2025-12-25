<?php

class Lideres extends Controllers
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

    public function getLider($idlider)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                if (empty($idlider) or !is_numeric($idlider)) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $arrLider = $this->model->selectLider($idlider);
                if (empty($arrLider)) {
                    $response = array('status' => false, 'msg' => 'Registro no encontrado');
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrLider);
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

    public function getLideres()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                // Obtener ID del usuario para validar botones de permisos r, u, d
                $rolUser = isset($_GET['rolUser']) ? intval($_GET['rolUser']) : 0;

                $arrData = $this->model->selectLideres();

                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => "");
                } else {
                    // Consultamos permisos (El módulo de Roles suele ser el ID 1 o similar en tu tabla)
                    $requestPermisos = getPermisos($rolUser);

                    for ($i = 0; $i < count($arrData); $i++) {
                        // Formatear Badge de Estado
                        $arrData[$i]['estado_lider'] = ($arrData[$i]['estado_lider'] == 1)
                            ? '<span class="badge badge-success">Activo</span>'
                            : '<span class="badge badge-danger">Inactivo</span>';

                        // Lógica de botones según permisos
                        $btnView = '';
                        $btnEdit = '';
                        $btnDel = '';

                        // Asumiendo que validas permisos para el módulo de Roles
                        // Si tu tabla de permisos usa r_permiso, u_permiso, etc.
                        if (!empty($requestPermisos[3]['r_permiso'])) {
                            $btnView = '<button class="btn btn-info btn-sm btnView" can="' . $arrData[$i]['id_lider'] . '" title="View"><i class="fas fa-eye"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-primary btn-sm btnEdit" can="' . $arrData[$i]['id_lider'] . '" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDel" can="' . $arrData[$i]['id_lider'] . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                        }

                        $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDel . '</div>';
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

    public function setLider()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (
                    empty($_POST['ident_lider']) || empty($_POST['ape1_lider']) || empty($_POST['ape2_lider']) ||
                    empty($_POST['nom1_lider']) || empty($_POST['nom2_lider']) || empty($_POST['telefono_lider']) ||
                    empty($_POST['email_lider']) || empty($_POST['dpto_lider']) || empty($_POST['muni_lider']) ||
                    empty($_POST['direccion_lider'])
                ) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                    die();
                }
                $intIdLider = intval($_POST['id_lider']);
                $strCedula = strClean($_POST['ident_lider']);
                $strApe1 = strtoupper(strClean($_POST['ape1_lider']));
                $strApe2 = strtoupper(strClean($_POST['ape2_lider']));
                $strNom1 = strtoupper(strClean($_POST['nom1_lider']));
                $strNom2 = strtoupper(strClean($_POST['nom2_lider']));
                $strTelefono = strClean($_POST['telefono_lider']);
                $strEmail = strClean($_POST['email_lider']);
                $intDpto = intval($_POST['dpto_lider']);
                $intMuni = intval($_POST['muni_lider']);
                $strDireccion = strtolower(strClean($_POST['direccion_lider']));
                $intEstado = intval($_POST['estado_lider']) == 0 ? 1 : intval($_POST['estado_lider']);

                if ($intIdLider == 0) {
                    $request_lider = $this->model->insertLider(
                        $strCedula,
                        $strApe1,
                        $strApe2,
                        $strNom1,
                        $strNom2,
                        $strTelefono,
                        $strEmail,
                        $intDpto,
                        $intMuni,
                        $strDireccion,
                        $intEstado
                    );
                    $option = 1;
                } else {
                    $request_lider = $this->model->updateLider(
                        $intIdLider,
                        $strCedula,
                        $strApe1,
                        $strApe2,
                        $strNom1,
                        $strNom2,
                        $strTelefono,
                        $strEmail,
                        $intDpto,
                        $intMuni,
                        $strDireccion,
                        $intEstado
                    );
                    $option = 2;
                }

                if ($request_lider > 0) {
                    $msg = ($option == 1) ? 'Datos guardados correctamente' : 'Datos actualizados correctamente.';
                    jsonResponse(['status' => true, 'msg' => $msg], 200);
                } else if ($request_lider == 'exist') {
                    jsonResponse(['status' => false, 'msg' => '¡Atención! El lider ya existe.'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible realizar la acción'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delLider()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                $data = json_decode(file_get_contents("php://input"), true);
                $idrol = isset($data['idrol']) ? intval($data['idrol']) : 0;

                if ($idrol <= 0) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $buscar_rol = $this->model->selectLider($idrol);
                if (empty($buscar_rol)) {
                    jsonResponse(['status' => false, 'msg' => 'El lider no existe'], 400);
                    die();
                }

                $requestDelete = $this->model->deleteLider($idrol);
                if ($requestDelete == "ok") {
                    jsonResponse(['status' => true, 'msg' => 'Registro eliminado'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible eliminar el lider (tiene votos asociados o no existe)'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function getSelectLideres()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $arrData = $this->model->selectLideres();
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

    public function getJsons()
    {
        // Ruta hacia donde guardaste el archivo json
        $jsonPath = "Json/Config.json";

        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            echo $jsonContent; // Esto ya devuelve el JSON tal cual
        } else {
            echo json_encode(["status" => false, "msg" => "No se encontró el archivo de opciones"]);
        }
        die();
    }
}
