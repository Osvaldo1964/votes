<?php
class Dashboard extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getResumen()
    {
        try {
            // Limpiar cualquier warning previo
            ob_start();

            $totalElectores = $this->model->selectTotalElectores();
            $totalLideres = $this->model->selectTotalLideres();
            $totalVotos = $this->model->selectTotalVotos();
            $topLideres = $this->model->selectTopLideres();
            $distMunicipios = $this->model->selectDistribucionMunicipios();
            $meta = $this->model->selectMetaGlobal();

            // Calculo Porcentaje Meta
            $porcentajeMeta = ($meta > 0) ? round(($totalElectores / $meta) * 100, 1) : 0;

            $data = array(
                'total_electores' => $totalElectores,
                'total_lideres' => $totalLideres,
                'total_votos' => $totalVotos, // Votos marcados en Monitor (Día D)
                'meta_global' => $meta,
                'porcentaje_meta' => $porcentajeMeta,
                'top_lideres' => $topLideres,
                'dist_municipios' => $distMunicipios
            );

            // Descartar salida sucia
            ob_clean();

            $arrResponse = array("status" => true, "data" => $data);
        } catch (Exception $e) {
            ob_clean();
            $arrResponse = array("status" => false, "msg" => "Error en servidor: " . $e->getMessage());
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
