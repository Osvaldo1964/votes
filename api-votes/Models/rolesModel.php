<?php

class RolesModel extends Mysql
{
    private $intIdRol;
    private $strNombre;
    private $strDescripcion;
    private $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function setRol(string $nombre, string $descripcion, string $estado)
    {
        $return = "";
        $this->strNombre = $nombre;
        $this->strDescripcion = $descripcion;
        $this->intStatus = $estado;

        $sql = "SELECT * FROM roles WHERE nombre_rol = ?";
        $arrParams = array($this->strNombre);
        $request = $this->select($sql, $arrParams);
        if (empty($request)) {
            $query_insert = "INSERT INTO roles(nombre_rol, descript_rol, status_rol) VALUES(?, ?, ?)";

            $arrData = array(
                $this->strNombre,
                $this->strDescripcion,
                $this->intStatus
            );

            $request_rol = $this->insert($query_insert, $arrData);
            $return = $request_rol;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateRol(int $idrol, string $nombres, string $descripcion, string $estado)
    {
        $this->intIdRol = $idrol;
        $this->strNombre = $nombres;
        $this->strDescripcion = $descripcion;
        $this->intStatus = $estado;

        // Verificar si el rol ya existe
        $sql = "SELECT * FROM roles WHERE nombre_rol = ? AND id_rol != ?";
        $arrParams = array($this->strNombre, $this->intIdRol);
        $request_rol = $this->select($sql, $arrParams);

        if (empty($request_rol)) {
            $sql = "UPDATE roles SET nombre_rol = ?, descript_rol = ?, status_rol = ? WHERE id_rol = ? ";

            $arrParams = array($this->strNombre, $this->strDescripcion, $this->intStatus, $this->intIdRol);
            $request = $this->update($sql, $arrParams);
            return $request;
        } else {
            return 'exist';
        }
    }

    public function getRol(int $idrol)
    {
        $this->intIdRol = $idrol;
        $sql = "SELECT * FROM roles WHERE id_rol = ?";
        $arrData = array($this->intIdRol);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function getRoles()
    {
        $sql = "SELECT * FROM roles WHERE status_rol != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function deleteRol(int $idrol)
    {
        // verificar si el rol está asignado a algún usuario
        $this->intIdRol = $idrol;
        $sql = "UPDATE roles SET status_rol = ? WHERE id_rol = ? ";
        $arrData = array("0", $this->intIdRol);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
