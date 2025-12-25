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

                $arrElector = $this->model->selectElector($idelector);
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

                $arrData = $this->model->selectElectores();

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
                        $btnView = '';
                        $btnEdit = '';
                        $btnDel = '';

                        // Asumiendo que validas permisos para el módulo de Roles
                        // Si tu tabla de permisos usa r_permiso, u_permiso, etc.
                        if (!empty($requestPermisos[3]['r_permiso'])) {
                            $btnView = '<button class="btn btn-info btn-sm btnView" can="' . $arrData[$i]['id_elector'] . '" title="View"><i class="fas fa-eye"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-primary btn-sm btnEdit" can="' . $arrData[$i]['id_elector'] . '" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDel" can="' . $arrData[$i]['id_elector'] . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
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

    public function setElector()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (
                    empty($_POST['ident_elector']) || empty($_POST['ape1_elector']) ||
                    empty($_POST['nom1_elector']) || empty($_POST['telefono_elector']) ||
                    empty($_POST['email_elector']) || empty($_POST['dpto_elector']) || empty($_POST['muni_elector']) ||
                    empty($_POST['direccion_elector']) || empty($_POST['lider_elector']) || empty($_POST['estado_elector'])
                ) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                    die();
                }
                $intIdElector = intval($_POST['id_elector']);
                $strCedula = strClean($_POST['ident_elector']);
                $strApe1 = strtoupper(strClean($_POST['ape1_elector']));
                $strApe2 = strtoupper(strClean($_POST['ape2_elector']));
                $strNom1 = strtoupper(strClean($_POST['nom1_elector']));
                $strNom2 = strtoupper(strClean($_POST['nom2_elector']));
                $strTelefono = strClean($_POST['telefono_elector']);
                $strEmail = strClean($_POST['email_elector']);
                $intDpto = intval($_POST['dpto_elector']);
                $intMuni = intval($_POST['muni_elector']);
                $strDireccion = strtolower(strClean($_POST['direccion_elector']));
                $intLider = intval($_POST['lider_elector']); // Capturar el Lider
                $intEstado = intval($_POST['estado_elector']) == 0 ? 1 : intval($_POST['estado_elector']);

                if ($intIdElector == 0) {
                    $request_elector = $this->model->insertElector(
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
                        $intLider,
                        $intEstado
                    );
                    $option = 1;
                } else {
                    $request_elector = $this->model->updateElector(
                        $intIdElector,
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
                        $intLider,
                        $intEstado
                    );
                    $option = 2;
                }
                if ($request_elector > 0) {
                    $msg = ($option == 1) ? 'Datos guardados correctamente' : 'Datos actualizados correctamente.';
                    jsonResponse(['status' => true, 'msg' => $msg], 200);
                } else if ($request_elector == 'exist') {
                    jsonResponse(['status' => false, 'msg' => '¡Atención! El elector ya existe.'], 200);
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
                $id_elector = isset($data['id_elector']) ? intval($data['id_elector']) : 0;

                if ($id_elector <= 0) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $buscar_elector = $this->model->selectElector($id_elector);
                if (empty($buscar_elector)) {
                    jsonResponse(['status' => false, 'msg' => 'El elector no existe'], 400);
                    die();
                }

                $requestDelete = $this->model->deleteElector($id_elector);
                if ($requestDelete == "ok") {
                    jsonResponse(['status' => true, 'msg' => 'Registro eliminado'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible eliminar el candidato (tiene votos asociados o no existe)'], 200);
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
                $arrData = $this->model->selectElectores();
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
