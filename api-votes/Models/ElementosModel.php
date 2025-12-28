<?php

class ElementosModel extends Mysql
{
    private $intIdElemento;
    private $strNombre;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectElementos()
    {
        // Extraer todos los elementos activos
        $sql = "SELECT id_elemento, nombre_elemento, estado_elemento 
                FROM elementos 
                WHERE estado_elemento != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectElemento(int $idelemento)
    {
        // Buscar un elemento por ID
        $this->intIdElemento = $idelemento;
        $sql = "SELECT id_elemento, nombre_elemento, estado_elemento 
                FROM elementos 
                WHERE id_elemento = $this->intIdElemento";
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertElemento(string $nombre)
    {
        $this->strNombre = $nombre;
        $return = 0;

        // Validar duplicado por nombre exacto
        $sql = "SELECT * FROM elementos WHERE nombre_elemento = '{$this->strNombre}' AND estado_elemento != 0";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO elementos(nombre_elemento, estado_elemento) VALUES(?,?)";
            $arrData = array($this->strNombre, 1);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateElemento(int $idelemento, string $nombre)
    {
        $this->intIdElemento = $idelemento;
        $this->strNombre = $nombre;

        // Validar nombre duplicado en otro ID
        $sql = "SELECT * FROM elementos WHERE nombre_elemento = '{$this->strNombre}' AND id_elemento != $this->intIdElemento AND estado_elemento != 0";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE elementos SET nombre_elemento = ? WHERE id_elemento = $this->intIdElemento";
            $arrData = array($this->strNombre);
            $request = $this->update($sql, $arrData);
        } else {
            $request = "exist";
        }
        return $request;
    }

    public function deleteElemento(int $idelemento)
    {
        $this->intIdElemento = $idelemento;
        // Borrado lógico
        $sql = "UPDATE elementos SET estado_elemento = ? WHERE id_elemento = $this->intIdElemento";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
