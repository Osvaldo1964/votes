<?php

class InfmovimientosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectMovimientosReporte(string $fechaInicio, string $fechaFin, int $conceptoId)
    {
        $sql = "SELECT m.id_movimiento, 
                       m.fecha_movimiento, 
                       m.tercero_movimiento,
                       m.concepto_movimiento,
                       m.tipo_movimiento, 
                       m.obs_movimiento, 
                       m.valor_movimiento, 
                       t.nombre_tercero, 
                       c.nombre_concepto,
                       c.tipo_concepto 
                FROM movimientos m
                LEFT JOIN terceros t ON m.tercero_movimiento = t.id_tercero
                LEFT JOIN conceptos c ON m.concepto_movimiento = c.id_concepto
                WHERE m.estado_movimiento != 0 
                AND m.fecha_movimiento BETWEEN '$fechaInicio' AND '$fechaFin'";

        if ($conceptoId > 0) {
            $sql .= " AND m.concepto_movimiento = $conceptoId";
        }

        $sql .= " ORDER BY m.fecha_movimiento ASC";

        $request = $this->select_all($sql);
        return $request;
    }
}
