<?php

class Monitor extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
    }

    public function getStats()
    {
        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            jsonResponse(['status' => false, 'msg' => 'Método no permitido'], 405);
            die();
        }

        try {
            // Validar Token
            $arrHeaders = getallheaders();
            $tokenData = fntAuthorization($arrHeaders);

            // Obtener datos
            $idZona = intval($_POST['idZona'] ?? 0);
            $puesto = strClean($_POST['puesto'] ?? '');

            if ($idZona <= 0 || empty($puesto)) {
                jsonResponse(['status' => false, 'msg' => 'Seleccione Zona y Puesto'], 400);
                die();
            }

            $arrData = $this->model->selectMonitorMesa($idZona, $puesto);

            if (empty($arrData)) {
                jsonResponse(['status' => true, 'msg' => 'No se encontraron mesas para este puesto', 'data' => []], 200);
            } else {
                jsonResponse(['status' => true, 'msg' => 'Datos obtenidos', 'data' => $arrData], 200);
            }
        } catch (Exception $e) {
            jsonResponse(['status' => false, 'msg' => 'Error servidor: ' . $e->getMessage()], 500);
        }
    }
}
