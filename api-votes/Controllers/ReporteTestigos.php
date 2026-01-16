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
        if (!is_array($arrData)) $arrData = [];

        $arrSinAsignar = $this->model->selectMesasSinAsignar($dpto, $muni, $zona, $puesto);
        if (!is_array($arrSinAsignar)) $arrSinAsignar = [];

        // Si hay mesas sin asignar, las agregamos al final del array principal
        if (!empty($arrSinAsignar)) {
            foreach ($arrSinAsignar as $key => $value) {
                $arrSinAsignar[$key]['is_unassigned'] = true;
            }
            $arrData = array_merge($arrData, $arrSinAsignar);
        }

        if (empty($arrData)) {
            $arrResponse = array("status" => false, "msg" => 'No se encontraron datos con los filtros seleccionados.');
        } else {
            $arrResponse = array("status" => true, "data" => $arrData);
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
