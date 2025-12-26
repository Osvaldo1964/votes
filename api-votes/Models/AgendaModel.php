<?php

class AgendaModel extends Mysql
{
    public $intIdAgenda;
    public $strTitulo;
    public $strDescripcion;
    public $strFechaInicio;
    public $strFechaFin;
    public $strColor;
    public $intStatus;
    public $intUsuarioId;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectEventos()
    {
        // Puedes filtrar por usuario si lo deseas usando WHERE usuario_id = X
        $sql = "SELECT id, title, start, end, color, description FROM agenda WHERE status != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectEvento(int $id)
    {
        $this->intIdAgenda = $id;
        $sql = "SELECT * FROM agenda WHERE id = $this->intIdAgenda";
        $request = $this->select($sql);
        return $request;
    }

    public function insertEvento(string $titulo, string $inicio, string $fin, string $descripcion, string $color)
    {
        $this->strTitulo = $titulo;
        $this->strFechaInicio = $inicio;
        $this->strFechaFin = $fin;
        $this->strDescripcion = $descripcion;
        $this->strColor = $color;
        $this->intStatus = 1;

        $query_insert = "INSERT INTO agenda(title, start, end, description, color, status) VALUES(?,?,?,?,?,?)";
        $arrData = array($this->strTitulo, $this->strFechaInicio, $this->strFechaFin, $this->strDescripcion, $this->strColor, $this->intStatus);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function updateEvento(int $id, string $titulo, string $inicio, string $fin, string $descripcion, string $color)
    {
        $this->intIdAgenda = $id;
        $this->strTitulo = $titulo;
        $this->strFechaInicio = $inicio;
        $this->strFechaFin = $fin;
        $this->strDescripcion = $descripcion;
        $this->strColor = $color;

        $sql = "UPDATE agenda SET title = ?, start = ?, end = ?, description = ?, color = ? WHERE id = ?";
        $arrData = array($this->strTitulo, $this->strFechaInicio, $this->strFechaFin, $this->strDescripcion, $this->strColor, $this->intIdAgenda);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function deleteEvento(int $id)
    {
        $this->intIdAgenda = $id;
        // Borrado lógico o físico. Aquí físico según ejemplo simple, o lógico si prefieres:
        // $sql = "UPDATE agenda SET status = ? WHERE id = ?"; ($arrData = array(0, $id))
        $sql = "DELETE FROM agenda WHERE id = ?";
        $arrData = array($this->intIdAgenda);
        $request = $this->delete($sql, $arrData); // Asumiendo que tienes método delete, si no update con status 0
        return $request;
    }

    // Método alternativo para borrado lógico si tu framework prefiere no borrar filas
    public function disableEvento(int $id)
    {
        $this->intIdAgenda = $id;
        $sql = "UPDATE agenda SET status = 0 WHERE id = $this->intIdAgenda";
        $request = $this->update($sql, array()); // Ajustar según tu método update base
        return $request;
    }
}
