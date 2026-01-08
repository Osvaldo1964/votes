<?php
class ReporteTestigos extends Controllers
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
            $arrResponse = array("status" => false, "msg" => 'Seleccione al menos Departamento y Municipio.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $arrData = $this->model->selectReporteTestigos($dpto, $muni, $zona, $puesto);

        if (empty($arrData)) {
            $arrResponse = array("status" => false, "msg" => 'No se encontraron testigos con los filtros seleccionados.');
        } else {
            $arrResponse = array("status" => true, "data" => $arrData);
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
