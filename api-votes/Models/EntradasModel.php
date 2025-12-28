<?php

class EntradasModel extends Mysql
{
    private $intIdEntrada;
    private $strFecha;
    private $intTerceroId;
    private $strFactura;
    private $intElementoId;
    private $decCantidad;
    private $decUnitario;
    private $decTotal;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectEntradas()
    {
        // Join con Terceros y Elementos para mostrar nombres
        $sql = "SELECT e.id_entrada, 
                       e.fecha_entrada, 
                       t.nombre_tercero, 
                       e.factura_entrada, 
                       el.nombre_elemento, 
                       e.cantidad_entrada, 
                       e.unitario_entrada, 
                       e.total_entrada, 
                       e.estado_entrada
                FROM entradas e
                INNER JOIN terceros t ON e.tercero_entrada = t.id_tercero
                INNER JOIN elementos el ON e.elemento_entrada = el.id_elemento
                WHERE e.estado_entrada != 0
                ORDER BY e.id_entrada DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectEntrada(int $identrada)
    {
        $this->intIdEntrada = $identrada;
        $sql = "SELECT * FROM entradas WHERE id_entrada = $this->intIdEntrada";
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertEntrada(string $fecha, int $tercero, string $factura, int $elemento, float $cantidad, float $unitario, float $total)
    {
        $this->strFecha = $fecha;
        $this->intTerceroId = $tercero;
        $this->strFactura = $factura;
        $this->intElementoId = $elemento;
        $this->decCantidad = $cantidad;
        $this->decUnitario = $unitario;
        $this->decTotal = $total;

        $query_insert = "INSERT INTO entradas(fecha_entrada, tercero_entrada, factura_entrada, elemento_entrada, cantidad_entrada, unitario_entrada, total_entrada, estado_entrada) VALUES(?,?,?,?,?,?,?,?)";
        $arrData = array($this->strFecha, $this->intTerceroId, $this->strFactura, $this->intElementoId, $this->decCantidad, $this->decUnitario, $this->decTotal, 1);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function updateEntrada(int $id, string $fecha, int $tercero, string $factura, int $elemento, float $cantidad, float $unitario, float $total)
    {
        $this->intIdEntrada = $id;
        $this->strFecha = $fecha;
        $this->intTerceroId = $tercero;
        $this->strFactura = $factura;
        $this->intElementoId = $elemento;
        $this->decCantidad = $cantidad;
        $this->decUnitario = $unitario;
        $this->decTotal = $total;

        $sql = "UPDATE entradas SET fecha_entrada = ?, tercero_entrada = ?, factura_entrada = ?, elemento_entrada = ?, cantidad_entrada = ?, unitario_entrada = ?, total_entrada = ? WHERE id_entrada = $this->intIdEntrada";
        $arrData = array($this->strFecha, $this->intTerceroId, $this->strFactura, $this->intElementoId, $this->decCantidad, $this->decUnitario, $this->decTotal);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function deleteEntrada(int $id)
    {
        $this->intIdEntrada = $id;
        $sql = "UPDATE entradas SET estado_entrada = ? WHERE id_entrada = $this->intIdEntrada";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // Métodos para llenar los combos
    public function selectTerceros()
    {
        $sql = "SELECT id_tercero, nombre_tercero FROM terceros WHERE estado_tercero != 0 ORDER BY nombre_tercero ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectElementos()
    {
        $sql = "SELECT id_elemento, nombre_elemento FROM elementos WHERE estado_elemento != 0 ORDER BY nombre_elemento ASC";
        $request = $this->select_all($sql);
        return $request;
    }
}
