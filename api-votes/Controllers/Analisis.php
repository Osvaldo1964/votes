<?php
class Analisis extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCandidatos()
    {
        $arrData = $this->model->selectCandidatos();
        if (empty($arrData)) {
            $arrResponse = array("status" => false, "msg" => 'No hay candidatos activos');
        } else {
            $arrResponse = array("status" => true, "data" => $arrData);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getReporte()
    {
        $dpto = $_POST['dpto'] ?? '';
        $muni = $_POST['muni'] ?? '';
        $zona = $_POST['zona'] ?? '';
        $puesto = $_POST['puesto'] ?? '';
        $idCandidato = $_POST['idCandidato'] ?? '';

        if (empty($dpto) || empty($muni) || empty($idCandidato)) {
            $arrResponse = array("status" => false, "msg" => 'Faltan datos obligatorios (Ubicación o Candidato)');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $arrData = $this->model->selectReporteAnalisis($dpto, $muni, $zona, $puesto, $idCandidato);

        // Calcular Totales
        $totCenso = 0;
        $totPotencial = 0;
        $totTestigos = 0;
        $totE14 = 0;
        $totDiferencia = 0;

        foreach ($arrData as &$row) {
            $e14 = intval($row['votos_e14']);
            $testigos = intval($row['mis_testigos']);

            // Diferencia: (Testigos - E14). 
            // Positivo significa que reportaron más votos de testigos que los que aparecieron en el E-14 (Fuga/Fraude en contra)
            // Negativo significa que aparecieron más votos en E-14 que los que reportaron los testigos (Sorpresa positiva o error de testigos).
            $row['diferencia'] = $testigos - $e14;

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
