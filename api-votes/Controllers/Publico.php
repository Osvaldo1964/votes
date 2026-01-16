<?php

class Publico extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // NOTA: No llamamos a fntAuthorization() aquí porque este controlador es PÚBLICO.
    }

    public function consultarPuesto($cedula)
    {
        if (empty($cedula)) {
            $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            jsonResponse($arrResponse, 200);
            die();
        }

        $strCedula = strClean($cedula);
        $arrData = $this->model->selectConsultaPublica($strCedula);

        if (empty($arrData) || !is_array($arrData)) {
            $arrResponse = array('status' => false, 'msg' => 'Cédula no encontrada en el censo.');
        } else {
            // Manejo de array de resultados (legacy behavior)
            $voterData = isset($arrData[0]) ? $arrData[0] : $arrData;

            $dataResponse = array(
                'cedula' => $voterData['identificacion'] ?? '',
                'nombre' => $voterData['nombres'] ?? '', // Ahora viene concatenado desde el modelo
                'departamento' => $voterData['departamento'] ?? 'Magdalena',
                'municipio' => $voterData['municipio'] ?? 'Santa Marta',
                'puesto' => $voterData['puesto'] ?? 'No asignado',
                'mesa' => $voterData['mesa'] ?? '00',
                'direccion' => $voterData['direccion_puesto'] ?? ''
            );
            $arrResponse = array('status' => true, 'data' => $dataResponse);
        }

        jsonResponse($arrResponse, 200);
    }

    public function registrarVoto()
    {
        // Soporte para POST (json o form-data) y GET básico
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            $cedula = $input['cedula'] ?? $_POST['cedula'] ?? '';
        } else {
            $cedula = $_GET['cedula'] ?? '';
        }

        if (empty($cedula)) {
            $arrResponse = array('status' => false, 'msg' => 'Se requiere el número de cédula.');
            jsonResponse($arrResponse, 200);
            die();
        }

        $strCedula = strClean($cedula);
        $result = $this->model->updateVotoPublico($strCedula);

        if ($result == "ok") {
            $arrResponse = array('status' => true, 'msg' => '¡Voto registrado exitosamente!');
        } elseif ($result == "already_voted") {
            $arrResponse = array('status' => false, 'msg' => 'Esta cédula ya registra un voto.');
        } elseif ($result == "not_found") {
            $arrResponse = array('status' => false, 'msg' => 'Cédula no encontrada en la base de datos de electores.');
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Error al registrar el voto.');
        }

        jsonResponse($arrResponse, 200);
    }
}
