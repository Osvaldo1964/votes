<?php

class MovimientosModel extends Mysql
{
    private $intIdMovimiento;
    private $strFecha;
    private $intTerceroId;
    private $intConceptoId;
    private $intTipoMovimiento; // Ahora almacena "Norma Contable" (1 o 2)
    private $strObservacion;
    private $decValor;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectMovimientos()
    {
        // NOTA: Traemos el tipo_concepto de la tabla conceptos para saber si es Ingreso/Gasto visualmente
        $sql = "SELECT m.id_movimiento, 
                       m.fecha_movimiento, 
                       m.tercero_movimiento,
                       m.concepto_movimiento,
                       m.tipo_movimiento, 
                       m.obs_movimiento, 
                       m.valor_movimiento, 
                       m.estado_movimiento,
                       t.nombre_tercero, 
                       c.nombre_concepto,
                       c.tipo_concepto as tipo_operacion -- Esto define si es Ingreso (1) o Gasto (2)
                FROM movimientos m
                LEFT JOIN terceros t ON m.tercero_movimiento = t.id_tercero
                LEFT JOIN conceptos c ON m.concepto_movimiento = c.id_concepto
                WHERE m.estado_movimiento != 0
                ORDER BY m.id_movimiento DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectMovimiento(int $idmovimiento)
    {
        $this->intIdMovimiento = $idmovimiento;
        $sql = "SELECT m.*, 
                       c.tipo_concepto as tipo_operacion
                FROM movimientos m
                LEFT JOIN conceptos c ON m.concepto_movimiento = c.id_concepto
                WHERE m.id_movimiento = $this->intIdMovimiento";
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertMovimiento(string $fecha, int $tercero, int $concepto, int $tipo, string $observacion, float $valor)
    {
        $this->strFecha = $fecha;
        $this->intTerceroId = $tercero;
        $this->intConceptoId = $concepto;
        $this->intTipoMovimiento = $tipo; // Norma contable
        $this->strObservacion = $observacion;
        $this->decValor = $valor;

        $query_insert = "INSERT INTO movimientos(fecha_movimiento, tercero_movimiento, concepto_movimiento, tipo_movimiento, obs_movimiento, valor_movimiento, estado_movimiento) VALUES(?,?,?,?,?,?,?)";
        $arrData = array($this->strFecha, $this->intTerceroId, $this->intConceptoId, $this->intTipoMovimiento, $this->strObservacion, $this->decValor, 1);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function updateMovimiento(int $id, string $fecha, int $tercero, int $concepto, int $tipo, string $observacion, float $valor)
    {
        $this->intIdMovimiento = $id;
        $this->strFecha = $fecha;
        $this->intTerceroId = $tercero;
        $this->intConceptoId = $concepto;
        $this->intTipoMovimiento = $tipo;
        $this->strObservacion = $observacion;
        $this->decValor = $valor;

        $sql = "UPDATE movimientos SET fecha_movimiento = ?, tercero_movimiento = ?, concepto_movimiento = ?, tipo_movimiento = ?, obs_movimiento = ?, valor_movimiento = ? WHERE id_movimiento = $this->intIdMovimiento";
        $arrData = array($this->strFecha, $this->intTerceroId, $this->intConceptoId, $this->intTipoMovimiento, $this->strObservacion, $this->decValor);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function deleteMovimiento(int $id)
    {
        $this->intIdMovimiento = $id;
        $sql = "UPDATE movimientos SET estado_movimiento = ? WHERE id_movimiento = $this->intIdMovimiento";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // Listas para llenar selects en el controlador si fuera necesario desde el backend
    // aunque generalmente se cargan desde endpoints específicos de Terceros y Conceptos
    public function selectTerceros()
    {
        $sql = "SELECT id_tercero, nombre_tercero FROM terceros WHERE estado_tercero != 0 ORDER BY nombre_tercero ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectConceptos()
    {
        // Aquí traemos el tipo para validaciones
        $sql = "SELECT id_concepto, nombre_concepto, tipo_concepto FROM conceptos WHERE estado_concepto != 0 ORDER BY nombre_concepto ASC";
        $request = $this->select_all($sql);
        return $request;
    }
}
