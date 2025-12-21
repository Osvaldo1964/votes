<?php

class UsuarioModel extends Mysql
{
    private $intIdUsuario;
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

    public function getUsuario(int $idusuario)
    {
        $this->intIdUsuario = $idusuario;
        $sql = "SELECT u.id_usuario,u.nombres_usuario,u.apellidos_usuario,u.telefono_usuario,u.email_usuario,u.rol_usuario,u.estado_usuario,r.nombre_rol
							FROM usuarios u
                            INNER JOIN roles r ON u.rol_usuario = r.id_rol
                            WHERE u.id_usuario = :iduser AND u.estado_usuario != :status ";
        $arrData = array(":iduser" => $this->intIdUsuario, ":status" => 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function setUser(string $nombres, string $apellidos, string $telefono, string $email, string $password, int $rolusuario)
    {
        $this->strNombres = $nombres;
        $this->strApellidos = $apellidos;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolUsuario = $rolusuario;
        $return = 0;

        $sql = "SELECT email_usuario FROM usuarios WHERE email_usuario = '{$this->strEmail}' AND estado_usuario != 0";

        if (empty($request)) {
            $sql_insert = "INSERT INTO usuarios(nombres_usuario, apellidos_usuario, telefono_usuario, email_usuario, password_usuario,rol_usuario)
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

    public function putUser(int $idusuario, string $nombres, string $apellidos, string $telefono, string $email, string $password, int $rolusuario, int $status)
    {
        $this->intIdUsuario = $idusuario;
        $this->strNombres = $nombres;
        $this->strApellidos = $apellidos;
        $this->strTelefono = $telefono; // Faltaba asignar
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolUsuario = $rolusuario;
        $this->intStatus = $status;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM usuarios WHERE email_usuario = ? AND id_usuario != ? AND estado_usuario != 0";
        $arrParams = array($this->strEmail, $this->intIdUsuario);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            if ($this->strPassword == "") {
                // UPDATE sin contraseña
                $sql = "UPDATE usuarios SET nombres_usuario = ?, apellidos_usuario = ?, telefono_usuario = ?, email_usuario = ?, rol_usuario = ?, estado_usuario = ? 
                    WHERE id_usuario = ?";
                $arrData = array(
                    $this->strNombres,
                    $this->strApellidos,
                    $this->strTelefono,
                    $this->strEmail,
                    $this->intRolUsuario,
                    $this->intStatus,
                    $this->intIdUsuario
                );
            } else {
                // UPDATE con contraseña
                $sql = "UPDATE usuarios SET nombres_usuario = ?, apellidos_usuario = ?, telefono_usuario = ?, email_usuario = ?, password_usuario = ?, rol_usuario = ?, estado_usuario = ? 
                    WHERE id_usuario = ?";
                $arrData = array(
                    $this->strNombres,
                    $this->strApellidos,
                    $this->strTelefono,
                    $this->strEmail,
                    $this->strPassword,
                    $this->intRolUsuario,
                    $this->intStatus,
                    $this->intIdUsuario
                );
            }

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function getUsuarios()
    {
        $sql = "SELECT u.id_usuario, u.nombres_usuario, u.apellidos_usuario,
                            u.telefono_usuario, u.email_usuario,u.rol_usuario,u.estado_usuario,r.nombre_rol
							FROM usuarios u 
                            inner join roles r on u.rol_usuario = r.id_rol
                            WHERE u.estado_usuario != 0 ORDER BY u.id_usuario DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function delUser($idusuario)
    {
        $this->intIdUsuario = $idusuario;
        $sql = "UPDATE usuarios SET estado_usuario = :estado WHERE id_usuario = :id ";
        $arrData = array(":estado" => 0, ":id" => $this->intIdUsuario);
        $request = $this->update($sql, $arrData);
        return $request;
    }

}
