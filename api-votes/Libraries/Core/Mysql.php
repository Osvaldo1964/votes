<?php

class Mysql extends Conexion
{
    private $conexion;
    private $strquery;
    private $arrValues;

    public function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    // Inserta un registro
    public function insert(string $sql, array $arrValues)
    {
        $this->arrValues = $arrValues;

        try {
            $stmt = $this->conexion->prepare($sql);
            $resInsert = $stmt->execute($arrValues);

            if ($resInsert) {
                $lastInsert = $this->conexion->lastInsertId();
            } else {
                $lastInsert = 0;
            }
            $stmt->closeCursor();
            return $lastInsert;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage();
            return $response;
        }
    }

    //Devuelve todos los registros
    public function select_all(string $query)
    {
        try {
            $this->strquery = $query;
            $execute = $this->conexion->query($this->strquery);
            $request = $execute->fetchall(PDO::FETCH_ASSOC); //ARRAY
            $execute->closeCursor();
            return $request;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage();
            return $response;
        }
    }

    public function select(string $sql, array $arrValues)
    {
        $this->strquery = $sql;       // (Opcional) Guardar para depuración
        $this->arrValues = $arrValues; // (Opcional) Guardar para depuración

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($arrValues);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    //Actualiza registros
    public function update(string $query, array $arrValues)
    {
        try {
            $this->strquery = $query;
            $this->arrValues = $arrValues;
            $update = $this->conexion->prepare($this->strquery);
            $resUdpate = $update->execute($this->arrValues);
            $update->closeCursor();
            return $resUdpate;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage();
            return $response;
        }
    }

    //Eliminar un registros
    public function delete(string $query, array $arrValues)
    {
        try {
            $this->strquery = $query;
            $this->arrValues = $arrValues;
            $delete = $this->conexion->prepare($this->strquery);
            $del = $delete->execute($this->arrValues);
            return $del;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage();
            return $response;
        }
    }

    //Ejecuta Store Procedure
    public function call_execute(string $query, array $arrValues)
    {
        try {
            $this->strquery = $query;
            $this->arrValues = $arrValues;
            $query = $this->conexion->prepare($this->strquery);
            $query->execute($this->arrValues);
            $request = $query->fetchall(PDO::FETCH_ASSOC); //ARRAY
            $query->closeCursor();
            return $request;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage();
            return $response;
        }
    }
}
