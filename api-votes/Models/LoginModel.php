<?php

class LoginModel extends Mysql
{
    private $intIdUsuario;
    private $strEmail;
    private $strPassword;
    private $intStatus;
    private $strUsuario;

    public function __construct()
    {
        parent::__construct();
    }

    public function loginUser(string $email, string $password)
    {
        $this->strEmail = $email;
        $this->strPassword = $password;
        $sql = "SELECT * FROM usuarios INNER JOIN roles ON usuarios.rol_usuario = roles.id_rol WHERE 
            usuarios.email_usuario = BINARY ? AND usuarios.password_usuario = BINARY ? AND usuarios.estado_usuario != 0";
        $arrData = array($this->strEmail, $this->strPassword);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function getUserEmail(string $strEmail)
    {
        $this->strUsuario = $strEmail;
        $sql = "SELECT id_usuario,nombres_usuario,apellidos_usuario,estado_usuario FROM usuarios WHERE 
					email_usuario = ? and  
					estado_usuario = 1 ";
        $arrData = array($this->strUsuario);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function setTokenUser(int $idpersona, string $token)
    {
        $this->intIdUsuario = $idpersona;
        $this->strToken = $token;
        $sql = "UPDATE persona SET token = ? WHERE idpersona = $this->intIdUsuario ";
        $arrData = array($this->strToken);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function getUsuario(string $email, string $token)
    {
        $this->strUsuario = $email;
        $this->strToken = $token;
        $sql = "SELECT idpersona FROM persona WHERE 
					email_user = '$this->strUsuario' and 
					token = '$this->strToken' and 					
					status = 1 ";
        $request = $this->select($sql);
        return $request;
    }

    public function insertPassword(int $idPersona, string $password)
    {
        $this->intIdUsuario = $idPersona;
        $this->strPassword = $password;
        $sql = "UPDATE persona SET password = ?, token = ? WHERE idpersona = $this->intIdUsuario ";
        $arrData = array($this->strPassword, "");
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function permisosModulo(int $idrol)
    {
        $this->intRolid = $idrol;
        $sql = "SELECT p.id_permiso,
						   p.modulo_permiso,
						   m.titulo_modulo as modulo,
						   p.r_permiso,
						   p.w_permiso,
						   p.u_permiso,
						   p.d_permiso 
					FROM permisos p 
					INNER JOIN modulos m
					ON p.modulo_permiso = m.id_modulo
					WHERE p.rol_permiso = $this->intRolid";
        $request = $this->select_all($sql);
        $arrPermisos = array();
        for ($i = 0; $i < count($request); $i++) {
            $arrPermisos[$request[$i]['modulo_permiso']] = $request[$i];
        }
        return $arrPermisos;
    }
}
