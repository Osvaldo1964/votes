<?php
class Place extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function place($idelector)
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $response = [];
            if ($method == "GET") {
                if (empty($idelector)) {
                    $response = array('status' => false, 'msg' => 'Error en los parametros');
                    jsonResponse($response, 400);
                    die();
                }
                $arrUser = $this->model->getPlace($idelector);
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
    public function getValidaPlace($idelector)
    {
        if (empty($idelector)) {
            $arrResponse = array("status" => false, "msg" => "Identificación vacía");
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        // Usamos el metodo existente getPlace del modelo
        // Nota: Asegúrate de que el modelo PlaceModel tenga getPlace
        $request = $this->model->getPlace($idelector);

        if (empty($request)) {
            $arrResponse = array("status" => false, "msg" => "Esta identificación NO está habilitada para votar.");
        } else {
            $arrResponse = array("status" => true, "msg" => "Identificación válida.", "data" => $request);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
