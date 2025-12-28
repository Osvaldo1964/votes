<?php

class SalidasModel extends Mysql
{
    private $intIdSalida;
    private $strFecha;
    private $intLiderId;
    private $intElementoId;
    private $decCantidad;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectSalidas()
    {
        // Join con Lideres y Elementos
        $sql = "SELECT s.id_salida, 
                       s.fecha_salida, 
                       CONCAT(l.nom1_lider, ' ', l.ape1_lider) as nombre_lider,
                       el.nombre_elemento, 
                       s.cantidad_salida, 
                       s.estado_salida
                FROM salidas s
                INNER JOIN lideres l ON s.lider_salida = l.id_lider
                INNER JOIN elementos el ON s.elemento_salida = el.id_elemento
                WHERE s.estado_salida != 0
                ORDER BY s.id_salida DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectSalida(int $idsalida)
    {
        $this->intIdSalida = $idsalida;
        $sql = "SELECT * FROM salidas WHERE id_salida = $this->intIdSalida";
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertSalida(string $fecha, int $lider, int $elemento, float $cantidad)
    {
        $this->strFecha = $fecha;
        $this->intLiderId = $lider;
        $this->intElementoId = $elemento;
        $this->decCantidad = $cantidad;

        $query_insert = "INSERT INTO salidas(fecha_salida, lider_salida, elemento_salida, cantidad_salida, estado_salida) VALUES(?,?,?,?,?)";
        $arrData = array($this->strFecha, $this->intLiderId, $this->intElementoId, $this->decCantidad, 1);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function updateSalida(int $id, string $fecha, int $lider, int $elemento, float $cantidad)
    {
        $this->intIdSalida = $id;
        $this->strFecha = $fecha;
        $this->intLiderId = $lider;
        $this->intElementoId = $elemento;
        $this->decCantidad = $cantidad;

        $sql = "UPDATE salidas SET fecha_salida = ?, lider_salida = ?, elemento_salida = ?, cantidad_salida = ? WHERE id_salida = $this->intIdSalida";
        $arrData = array($this->strFecha, $this->intLiderId, $this->intElementoId, $this->decCantidad);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function deleteSalida(int $id)
    {
        $this->intIdSalida = $id;
        $sql = "UPDATE salidas SET estado_salida = ? WHERE id_salida = $this->intIdSalida";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // LISTAS PARA COMBOS

    // Select de Líderes incluyendo conteo de Electores Activos
    public function selectLideresMetrics()
    {
        $sql = "SELECT l.id_lider, 
                       CONCAT(l.nom1_lider, ' ', l.ape1_lider) as nombre_lider,
                       COUNT(e.id_elector) as total_electores
                FROM lideres l
                LEFT JOIN electores e ON l.id_lider = e.lider_elector AND e.estado_elector != 0
                WHERE l.estado_lider != 0
                GROUP BY l.id_lider
                ORDER BY nombre_lider ASC";
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
