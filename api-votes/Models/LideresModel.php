<?php

class LideresModel extends Mysql
{
    private $intIdLider;
    private $strNombres;
    private $strApellidos;
    private $strTelefono;
    private $strEmail;
    private $strPassword;
    private $intRolUsuario;
    private $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function getLider(int $idlider)
    {
        $this->intIdLider = $idlider;
        $sql = "SELECT u.id_lider,u.nombres_lider,u.apellidos_lider,u.telefono_lider,u.email_lider,u.rol_lider,u.estado_lider,r.nombre_rol
							FROM lideres u
                            INNER JOIN roles r ON u.rol_lider = r.id_rol
                            WHERE u.id_lider = :iduser AND u.estado_lider != :status ";
        $arrData = array(":iduser" => $this->intIdLider, ":status" => 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }
    public function getLiderById(int $idlider)
    {
        $this->intIdLider = $idlider;
        $sql = "SELECT u.id_lider,u.nombres_lider,u.apellidos_lider,u.telefono_lider,u.email_lider,u.rol_lider,u.estado_lider,r.nombre_rol
                            FROM lideres u
                            INNER JOIN roles r ON u.rol_lider = r.id_rol
                            WHERE u.id_lider = :iduser AND u.estado_lider != :status ";
        $arrData = array(":iduser" => $this->intIdLider, ":status" => 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }
    public function setLider(string $nombres, string $apellidos, string $telefono, string $email, string $password, int $rolusuario)
    {
        $this->strNombres = $nombres;
        $this->strApellidos = $apellidos;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolUsuario = $rolusuario;
        $return = 0;

        $sql = "SELECT email_lider FROM lideres WHERE email_lider = '{$this->strEmail}' AND estado_lider != 0";

        if (empty($request)) {
            $sql_insert = "INSERT INTO lideres(nombres_lider, apellidos_lider, telefono_lider, email_lider, password_lider,rol_lider)
                       VALUES(?, ?, ?, ?, ?, ?)";

            $arrData = array(
                $this->strNombres,
                $this->strApellidos,
                $this->strTelefono,
                $this->strEmail,
                $this->strPassword,
                $this->intRolUsuario
            );

            $request_insert = $this->insert($sql_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = 'exist';
        }
        return $return;
    }

    public function putLider(int $idlider, string $nombres, string $apellidos, string $telefono, string $email, string $password, int $rolusuario, int $status)
    {
        $this->intIdLider = $idlider;
        $this->strNombres = $nombres;
        $this->strApellidos = $apellidos;
        $this->strTelefono = $telefono; // Faltaba asignar
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolUsuario = $rolusuario;
        $this->intStatus = $status;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM lideres WHERE email_lider = ? AND id_lider != ? AND estado_lider != 0";
        $arrParams = array($this->strEmail, $this->intIdLider);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            if ($this->strPassword == "") {
                // UPDATE sin contraseña
                $sql = "UPDATE lideres SET nombres_lider = ?, apellidos_lider = ?, telefono_lider = ?, email_lider = ?, rol_lider = ?, estado_lider = ? 
                    WHERE id_lider = ?";
                $arrData = array(
                    $this->strNombres,
                    $this->strApellidos,
                    $this->strTelefono,
                    $this->strEmail,
                    $this->intRolUsuario,
                    $this->intStatus,
                    $this->intIdLider
                );
            } else {
                // UPDATE con contraseña
                $sql = "UPDATE lideres SET nombres_lider = ?, apellidos_lider = ?, telefono_lider = ?, email_lider = ?, password_lider = ?, rol_lider = ?, estado_lider = ? 
                    WHERE id_lider = ?";
                $arrData = array(
                    $this->strNombres,
                    $this->strApellidos,
                    $this->strTelefono,
                    $this->strEmail,
                    $this->strPassword,
                    $this->intRolUsuario,
                    $this->intStatus,
                    $this->intIdLider
                );
            }

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function getLideres()
    {
        $sql = "SELECT u.id_lider, u.nombres_lider, u.apellidos_lider,
                            u.telefono_lider, u.email_lider,u.rol_lider,u.estado_lider,r.nombre_rol
							FROM lideres u 
                            inner join roles r on u.rol_lider = r.id_rol
                            WHERE u.estado_usuario != 0 ORDER BY u.id_usuario DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function delLider($idlider)
    {
        $this->intIdLider = $idlider;
        $sql = "UPDATE lideres SET estado_lider = :estado WHERE id_lider = :id ";
        $arrData = array(":estado" => 0, ":id" => $this->intIdLider);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
