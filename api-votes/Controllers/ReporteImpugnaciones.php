<?php
class ReporteImpugnaciones extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getReporte()
    {
        $dpto = intval($_POST['dpto']);
        $muni = intval($_POST['muni']);
        $zona = $_POST['zona'];
        $puesto = $_POST['puesto'];
        $candidato = intval($_POST['candidato']);
        $porcentaje = floatval($_POST['porcentaje']);

        if ($dpto == 0 || $muni == 0 || $candidato == 0 || $porcentaje == 0) {
            $arrResponse = array('status' => false, 'msg' => 'Datos inválidos.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $arrData = $this->model->selectReporteImpugnaciones($dpto, $muni, $zona, $puesto, $candidato, $porcentaje);

        if (count($arrData) > 0) {
            $arrResponse = array('status' => true, 'data' => $arrData);
        } else {
            $arrResponse = array('status' => false, 'msg' => 'No se encontraron datos con los criterios seleccionados.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
