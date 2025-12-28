<?php

class TercerosModel extends Mysql
{
    private $intIdTercero;
    private $strIdentificacion;
    private $strNombre;
    private $strTelefono;
    private $strEmail;
    private $strDireccion;
    private $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectTerceros()
    {
        // Extraer todos los terceros activos
        $sql = "SELECT id_tercero, ident_tercero, nombre_tercero, telefono_tercero, email_tercero, direccion_tercero, estado_tercero 
                FROM terceros 
                WHERE estado_tercero != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectTercero(int $idtercero)
    {
        // Buscar un tercero por ID
        $this->intIdTercero = $idtercero;
        $sql = "SELECT id_tercero, ident_tercero, nombre_tercero, telefono_tercero, email_tercero, direccion_tercero, estado_tercero 
                FROM terceros 
                WHERE id_tercero = $this->intIdTercero"; // Asumiendo que el controlador valida que sea INT
        $request = $this->select($sql, array());
        return $request;
    }

    public function insertTercero(string $identificacion, string $nombre, string $telefono, string $email, string $direccion)
    {
        $this->strIdentificacion = $identificacion;
        $this->strNombre = $nombre;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strDireccion = $direccion;
        $return = 0;

        // Validar si ya existe la identificación
        $sql = "SELECT * FROM terceros WHERE ident_tercero = '{$this->strIdentificacion}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO terceros(ident_tercero, nombre_tercero, telefono_tercero, email_tercero, direccion_tercero, estado_tercero) VALUES(?,?,?,?,?,?)";
            $arrData = array($this->strIdentificacion, $this->strNombre, $this->strTelefono, $this->strEmail, $this->strDireccion, 1);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function updateTercero(int $idtercero, string $identificacion, string $nombre, string $telefono, string $email, string $direccion)
    {
        $this->intIdTercero = $idtercero;
        $this->strIdentificacion = $identificacion;
        $this->strNombre = $nombre;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strDireccion = $direccion;

        // Validar que la identificación no pertenezca a otro ID diferente
        $sql = "SELECT * FROM terceros WHERE ident_tercero = '{$this->strIdentificacion}' AND id_tercero != $this->intIdTercero";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE terceros SET ident_tercero = ?, nombre_tercero = ?, telefono_tercero = ?, email_tercero = ?, direccion_tercero = ? WHERE id_tercero = $this->intIdTercero";
            $arrData = array($this->strIdentificacion, $this->strNombre, $this->strTelefono, $this->strEmail, $this->strDireccion);
            $request = $this->update($sql, $arrData);
        } else {
            $request = "exist";
        }
        return $request;
    }

    public function deleteTercero(int $idtercero)
    {
        $this->intIdTercero = $idtercero;
        // Borrado lógico: estado = 0
        $sql = "UPDATE terceros SET estado_tercero = ? WHERE id_tercero = $this->intIdTercero";
        $arrData = array(0);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
