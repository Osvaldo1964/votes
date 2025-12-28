<?php

class ConceptosModel extends Mysql
{
    private $intIdConcepto;
    private $strNombre;
    private $intTipo;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectConceptos()
    {
        // Extraer todos los conceptos activos
        $sql = "SELECT id_concepto, nombre_concepto, tipo_concepto, estado_concepto 
                FROM conceptos 
                WHERE estado_concepto != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectConcepto(int $idconcepto)
    {
        // Buscar un concepto por ID
        $this->intIdConcepto = $idconcepto;
        $sql = "SELECT id_concepto, nombre_concepto, tipo_concepto, estado_concepto 
                FROM conceptos 
                WHERE id_concepto = $this->intIdConcepto";
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertConcepto(string $nombre, int $tipo)
    {
        $this->strNombre = $nombre;
        $this->intTipo = $tipo;
        $return = 0;

        // Validar duplicado por nombre exacto
        $sql = "SELECT * FROM conceptos WHERE nombre_concepto = '{$this->strNombre}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO conceptos(nombre_concepto, tipo_concepto, estado_concepto) VALUES(?,?,?)";
            $arrData = array($this->strNombre, $this->intTipo, 1);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateConcepto(int $idconcepto, string $nombre, int $tipo)
    {
        $this->intIdConcepto = $idconcepto;
        $this->strNombre = $nombre;
        $this->intTipo = $tipo;

        // Validar nombre duplicado en otro ID
        $sql = "SELECT * FROM conceptos WHERE nombre_concepto = '{$this->strNombre}' AND id_concepto != $this->intIdConcepto";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE conceptos SET nombre_concepto = ?, tipo_concepto = ? WHERE id_concepto = $this->intIdConcepto";
            $arrData = array($this->strNombre, $this->intTipo);
            $request = $this->update($sql, $arrData);
        } else {
            $request = "exist";
        }
        return $request;
    }

    public function deleteConcepto(int $idconcepto)
    {
        $this->intIdConcepto = $idconcepto;
        // Borrado lógico
        $sql = "UPDATE conceptos SET estado_concepto = ? WHERE id_concepto = $this->intIdConcepto";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
