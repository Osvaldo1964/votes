<?php

class Candidatos extends Controllers
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

    public function getCandidato($idrol)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                if (empty($idrol) or !is_numeric($idrol)) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $arrRol = $this->model->selectCandidato($idrol);
                if (empty($arrRol)) {
                    $response = array('status' => false, 'msg' => 'Registro no encontrado');
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrRol);
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

    public function getCandidatos()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                // Obtener ID del usuario para validar botones de permisos r, u, d
                $rolUser = isset($_GET['rolUser']) ? intval($_GET['rolUser']) : 0;

                $arrData = $this->model->selectCandidatos();

                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos para mostrar', 'data' => "");
                } else {
                    // Consultamos permisos (El módulo de Roles suele ser el ID 1 o similar en tu tabla)
                    $requestPermisos = getPermisos($rolUser);

                    for ($i = 0; $i < count($arrData); $i++) {
                        // Formatear Badge de Estado
                        $arrData[$i]['estado_candidato'] = ($arrData[$i]['estado_candidato'] == 1)
                            ? '<span class="badge badge-success">Activo</span>'
                            : '<span class="badge badge-danger">Inactivo</span>';

                        // Lógica de botones según permisos
                        $btnView = '';
                        $btnEdit = '';
                        $btnDel = '';

                        // Asumiendo que validas permisos para el módulo de Roles
                        // Si tu tabla de permisos usa r_permiso, u_permiso, etc.
                        if (!empty($requestPermisos[3]['r_permiso'])) {
                            $btnView = '<button class="btn btn-info btn-sm btnView" can="' . $arrData[$i]['id_candidato'] . '" title="View"><i class="fas fa-eye"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['u_permiso'])) {
                            $btnEdit = '<button class="btn btn-primary btn-sm btnEdit" can="' . $arrData[$i]['id_candidato'] . '" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (!empty($requestPermisos[3]['d_permiso'])) {
                            $btnDel = '<button class="btn btn-danger btn-sm btnDel" can="' . $arrData[$i]['id_candidato'] . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
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

    public function setCandidato()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (
                    empty($_POST['txtCedula']) || empty($_POST['txtApe1']) || empty($_POST['txtApe2']) ||
                    empty($_POST['txtNom1']) || empty($_POST['txtNom2']) || empty($_POST['txtTelefono']) ||
                    empty($_POST['txtEmail']) || empty($_POST['txtDireccion']) || empty($_POST['listCurul']) ||
                    empty($_POST['listPartido']) || empty($_POST['listEstado'])
                ) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                    die();
                }

                $intIdCandidato = intval($_POST['idCandidato']);
                $strCedula = strClean($_POST['txtCedula']);
                $strApe1 = strClean($_POST['txtApe1']);
                $strApe2 = strClean($_POST['txtApe2']);
                $strNom1 = strClean($_POST['txtNom1']);
                $strNom2 = strClean($_POST['txtNom2']);
                $strTelefono = strClean($_POST['txtTelefono']);
                $strEmail = strClean($_POST['txtEmail']);
                $strDireccion = strClean($_POST['txtDireccion']);
                $listCurul = intval($_POST['listCurul']);
                $listPartido = intval($_POST['listPartido']);
                $listEstado = intval($_POST['listEstado']);

                if ($intIdCandidato == 0) {
                    $request_candidato = $this->model->insertCandidato($strCedula, $strApe1, $strApe2, $strNom1, $strNom2, $strTelefono, $strEmail, $strDireccion, $listCurul, $listPartido, $listEstado);
                    $option = 1;
                } else {
                    $request_candidato = $this->model->updateCandidato($intIdCandidato, $strNom1, $strNom2, $strTelefono, $strEmail, $strDireccion, $listCurul, $listPartido, $listEstado);
                    $option = 2;
                }

                if ($request_candidato > 0) {
                    $msg = ($option == 1) ? 'Datos guardados correctamente' : 'Datos actualizados correctamente.';
                    jsonResponse(['status' => true, 'msg' => $msg], 200);
                } else if ($request_candidato == 'exist') {
                    jsonResponse(['status' => false, 'msg' => '¡Atención! El candidato ya existe.'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible realizar la acción'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }

    public function delCandidato()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                $data = json_decode(file_get_contents("php://input"), true);
                $idrol = isset($data['idrol']) ? intval($data['idrol']) : 0;

                if ($idrol <= 0) {
                    jsonResponse(['status' => false, 'msg' => 'Error en los parámetros'], 400);
                    die();
                }

                $buscar_rol = $this->model->selectCandidato($idrol);
                if (empty($buscar_rol)) {
                    jsonResponse(['status' => false, 'msg' => 'El candidato no existe'], 400);
                    die();
                }

                $requestDelete = $this->model->deleteCandidato($idrol);
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

    public function getSelectCandidatos()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $arrData = $this->model->selectCandidatos();
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
}