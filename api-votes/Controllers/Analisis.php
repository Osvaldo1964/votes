<?php
class Analisis extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }



    public function getReporte()
    {
        $dpto = $_POST['dpto'] ?? '';
        $muni = $_POST['muni'] ?? '';
        $zona = $_POST['zona'] ?? '';
        $puesto = $_POST['puesto'] ?? '';

        if (empty($dpto) || empty($muni)) {
            $arrResponse = array("status" => false, "msg" => 'Faltan datos obligatorios (Ubicación)');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $arrData = $this->model->selectReporteAnalisis($dpto, $muni, $zona, $puesto);

        // Calcular Totales
        $totCenso = 0;
        $totPotencial = 0;
        $totTestigos = 0;
        $totE14 = 0;
        $totDiferencia = 0;

        foreach ($arrData as &$row) {
            $e14 = intval($row['votos_e14']);
            $testigos = intval($row['mis_testigos']);

            // Diferencia: (E14 - Reales/Testigos). 
            // Positivo: SUPERADO (Ganancia, más votos oficiales que los reportados).
            // Negativo: FALTANTE (Pérdida, menos votos oficiales que los reportados).
            $row['diferencia'] = $e14 - $testigos;

            if ($row['diferencia'] > 0) {
                $row['estado'] = '<span class="badge badge-success">SUPERADO</span>';
            } elseif ($row['diferencia'] < 0) {
                $row['estado'] = '<span class="badge badge-danger">FALTANTE</span>';
            } else {
                $row['estado'] = '<span class="badge badge-info">META</span>';
            }

            $totCenso += intval($row['censo_mesa']);
            $totPotencial += intval($row['mi_potencial']);
            $totTestigos += $testigos;
            $totE14 += $e14;
            $totDiferencia += $row['diferencia'];
        }

        $resumen = [
            'total_censo' => $totCenso,
            'total_potencial' => $totPotencial,
            'total_testigos' => $totTestigos,
            'total_e14' => $totE14,
            'total_diferencia' => $totDiferencia
        ];

        $arrResponse = array("status" => true, "data" => $arrData, "resumen" => $resumen);
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
