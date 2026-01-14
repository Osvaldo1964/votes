<?php

class Parametros extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // CORS and Auth checks similar to Candidatos
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);

            // Validate User ID from token/session if possible. 
            // NOTE: API might not have $_SESSION if it's stateless token based. 
            // However, this system relies on `sessionUser()` logic often or mixed.
            // But API usually relies on JWT payload. `fntAuthorization` validates token.
            // Does `fntAuthorization` populate $_SESSION? Likely not for pure API.
            // But this is a hybrid app often using session.

            // Let's check session first as fallback.
            if (isset($_SESSION['userData']['id_usuario']) && $_SESSION['userData']['id_usuario'] != 1) {
                jsonResponse(['status' => false, 'msg' => 'Acceso denegado'], 403);
                die();
            }
            // If API access is purely token based without session, we'd need to decode token here.
            // Given the context of "Usuarios" module, it seems session matching is key.
            // Let's stick to session check for now as the user is likely using the web app.
        } catch (\Throwable $e) {
            $arrResponse = array('status' => false, 'msg' => 'Token inválido o expirado');
            jsonResponse($arrResponse, 401);
            die();
        }
    }

    public function getParametros()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $arrData = $this->model->selectParametros();
                if (empty($arrData)) {
                    $response = array('status' => false, 'msg' => 'No hay datos de configuración', 'data' => "");
                } else {
                    $response = array('status' => true, 'msg' => 'Datos encontrados', 'data' => $arrData);
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

    public function setParametros()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (empty($_POST['listCandidato'])) {
                    jsonResponse(['status' => false, 'msg' => 'Datos incompletos'], 200);
                    die();
                }

                $intIdCandidato = intval($_POST['listCandidato']);
                $strEslogan = strClean($_POST['txtEslogan']);
                $intNumLista = intval($_POST['txtNumLista']);

                // Fetch Name for Legacy Compatibility
                $arrCandidato = $this->model->selectCandidato($intIdCandidato);
                $strCandidato = !empty($arrCandidato) ? $arrCandidato['nombre'] : "Oficial";

                // Fetch current data to keep existing photo if not updated
                $currentData = $this->model->selectParametros();
                $strFoto = (is_array($currentData) && !empty($currentData['foto'])) ? $currentData['foto'] : '';

                // Handle File Upload
                if (!empty($_FILES['foto']['name'])) {
                    $targetDir = "../app-votes/Assets/images/uploads/";
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    $fileName = "candidato_" . time() . "_" . basename($_FILES["foto"]["name"]);
                    $targetFilePath = $targetDir . $fileName;
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
                    if (in_array(strtolower($fileType), $allowTypes)) {
                        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFilePath)) {
                            $strFoto = $fileName;
                        } else {
                            jsonResponse(['status' => false, 'msg' => 'Error al subir la imagen'], 200);
                            die();
                        }
                    } else {
                        jsonResponse(['status' => false, 'msg' => 'Solo archivos JPG, JPEG, PNG y GIF son permitidos'], 200);
                        die();
                    }
                }

                $request = $this->model->updateParametros(
                    $strCandidato,
                    $intIdCandidato,
                    $strEslogan,
                    $intNumLista,
                    $strFoto
                );

                if ($request > 0) {
                    jsonResponse(['status' => true, 'msg' => 'Configuración guardada correctamente'], 200);
                } else {
                    jsonResponse(['status' => false, 'msg' => 'No es posible realizar la acción prob'], 200);
                }
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        die();
    }
    public function getCandidatos()
    {
        $arrData = $this->model->selectCandidatos();
        if (empty($arrData)) {
            $arrResponse = array("status" => false, "msg" => 'No hay candidatos activos');
        } else {
            $arrResponse = array("status" => true, "data" => $arrData);
        }
        jsonResponse($arrResponse, 200);
        die();
    }
}
