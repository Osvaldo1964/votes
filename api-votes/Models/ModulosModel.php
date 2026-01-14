<?php

class ModulosModel extends Mysql
{
    public $intIdModulo;
    public $strTitulo;
    public $strDescripcion;
    public $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectModulos()
    {
        $sql = "SELECT * FROM modulos WHERE estado_modulo != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectModulo(int $idmodulo)
    {
        $this->intIdModulo = $idmodulo;
        $sql = "SELECT * FROM modulos WHERE id_modulo = ?";
        $arrData = array($this->intIdModulo);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function insertModulo(string $titulo, string $descripcion, int $status)
    {
        $this->strTitulo = $titulo;
        $this->strDescripcion = $descripcion;
        $this->intStatus = $status;

        $return = 0;
        $sql = "SELECT * FROM modulos WHERE titulo_modulo = '{$this->strTitulo}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO modulos(titulo_modulo, descript_modulo, estado_modulo) VALUES(?,?,?)";
            $arrData = array($this->strTitulo, $this->strDescripcion, $this->intStatus);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateModulo(int $idmodulo, string $titulo, string $descripcion, int $status)
    {
        $this->intIdModulo = $idmodulo;
        $this->strTitulo = $titulo;
        $this->strDescripcion = $descripcion;
        $this->intStatus = $status;

        $sql = "SELECT * FROM modulos WHERE titulo_modulo = '$this->strTitulo' AND id_modulo != $this->intIdModulo";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE modulos SET titulo_modulo = ?, descript_modulo = ?, estado_modulo = ? WHERE id_modulo = $this->intIdModulo";
            $arrData = array($this->strTitulo, $this->strDescripcion, $this->intStatus);
            $request = $this->update($sql, $arrData);
        } else {
            $request = "exist";
        }
        return $request;
    }

    public function deleteModulo(int $idmodulo)
    {
        $this->intIdModulo = $idmodulo;
        // Check for dependencies if any (e.g. Permisos)
        // Assuming we can mark as deleted (status 0) or delete row.
        // User SQL says status default 1. Standard is status 0 for deleted.

        $sql = "UPDATE modulos SET estado_modulo = 0 WHERE id_modulo = ?";
        $arrData = array($this->intIdModulo);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
