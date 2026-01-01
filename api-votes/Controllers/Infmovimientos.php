<?php

class Infmovimientos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }
        try {
            $arrHeaders = getallheaders();
            fntAuthorization($arrHeaders);
        } catch (\Throwable $e) {
            jsonResponse(['status' => false, 'msg' => 'Error de autorización'], 401);
            die();
        }
    }

    public function getReporte()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (empty($_POST['fechaInicio']) || empty($_POST['fechaFin'])) {
                jsonResponse(['status' => false, 'msg' => 'Las fechas son obligatorias'], 400);
                die();
            }

            $fechaInicio = $_POST['fechaInicio'];
            $fechaFin = $_POST['fechaFin'];
            $conceptoId = isset($_POST['concepto']) ? intval($_POST['concepto']) : 0;

            $arrData = $this->model->selectMovimientosReporte($fechaInicio, $fechaFin, $conceptoId);

            // Calcular totales
            $totalIngresos = 0;
            $totalGastos = 0;

            foreach ($arrData as $key => $row) {
                // tipo_concepto: 1 = Ingreso, 2 = Gasto
                if ($row['tipo_concepto'] == 1) {
                    $totalIngresos += floatval($row['valor_movimiento']);
                } else {
                    $totalGastos += floatval($row['valor_movimiento']);
                }
                $arrData[$key]['valor_formato'] = formatMoney($row['valor_movimiento']);
                // Etiqueta visual para Tipo
                $arrData[$key]['tipo_label'] = ($row['tipo_concepto'] == 1)
                    ? '<span class="badge badge-success">Ingreso</span>'
                    : '<span class="badge badge-danger">Gasto</span>';
            }

            $balance = $totalIngresos - $totalGastos;

            $response = [
                'status' => true,
                'data' => $arrData,
                'resumen' => [
                    'ingresos' => formatMoney($totalIngresos),
                    'gastos' => formatMoney($totalGastos),
                    'balance' => formatMoney($balance),
                    'balance_val' => $balance
                ]
            ];

            jsonResponse($response, 200);
        }
        die();
    }
}
