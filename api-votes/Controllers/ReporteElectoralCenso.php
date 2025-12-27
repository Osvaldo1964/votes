<?php

class ReporteElectoralCenso extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // Validar Token JWT si es necesario, asumimos middleware o herencia validada
    }

    public function getZonas($idMuni)
    {
        if (empty($idMuni)) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos');
        } else {
            $arrData = $this->model->selectZonas($idMuni);
            if (empty($arrData)) {
                $arrResponse = array("status" => false, "msg" => 'Datos no encontrados');
            } else {
                $arrResponse = array("status" => true, "data" => $arrData);
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getPuestos($idZona)
    {
        if (empty($idZona)) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos');
        } else {
            $arrData = $this->model->selectPuestos($idZona);
            if (empty($arrData)) {
                $arrResponse = array("status" => false, "msg" => 'Datos no encontrados');
            } else {
                $arrResponse = array("status" => true, "data" => $arrData);
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getMesas()
    {
        // Recibimos JSON body o POST normal. Asumamos POST params o GET params.
        // Dado que puede tener caracteres especiales el nombre del puesto, mejor POST.
        $idZona = $_POST['idZona'] ?? '';
        $nombrePuesto = $_POST['nombrePuesto'] ?? '';

        if (empty($idZona) || empty($nombrePuesto)) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos');
        } else {
            $arrData = $this->model->selectMesas($idZona, $nombrePuesto);
            if (empty($arrData)) {
                $arrResponse = array("status" => false, "msg" => 'Datos no encontrados');
            } else {
                $arrResponse = array("status" => true, "data" => $arrData);
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generarReporte()
    {
        // POST request con los filtros
        $input = json_decode(file_get_contents("php://input"), true);

        $dpto = $input['dpto'] ?? '';
        $muni = $input['muni'] ?? '';
        $zona = $input['zona'] ?? '';
        $puesto = $input['puesto'] ?? '';
        $mesa = $input['mesa'] ?? '';
        $tipoReporte = $input['tipoReporte'] ?? 'detallado'; // Default detallado

        if (empty($dpto) || empty($muni)) {
            $arrResponse = array("status" => false, "msg" => 'Debe seleccionar al menos Departamento y Municipio');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $arrData = $this->model->selectReporteCenso($dpto, $muni, $zona, $puesto, $mesa, $tipoReporte);

        // Calculamos porcentaje y totales
        $totalPotencial = 0;
        $totalVotos = 0;

        foreach ($arrData as &$row) {
            $potencial = intval($row['potencial']);
            $votos = intval($row['mis_votos']);
            $row['porcentaje'] = ($potencial > 0) ? round(($votos / $potencial) * 100, 2) : 0;

            $totalPotencial += $potencial;
            $totalVotos += $votos;
        }

        $resumen = [
            'total_potencial' => $totalPotencial,
            'total_mis_votos' => $totalVotos,
            'porcentaje_global' => ($totalPotencial > 0) ? round(($totalVotos / $totalPotencial) * 100, 2) : 0
        ];

        $arrResponse = array(
            "status" => true,
            "data" => $arrData,
            "resumen" => $resumen,
            "debug_params" => [
                "tipoReporteRecibido" => $tipoReporte,
                "zona" => $zona,
                "puesto" => $puesto
            ]
        );
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
