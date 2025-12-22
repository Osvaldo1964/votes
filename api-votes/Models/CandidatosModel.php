<?php

class CandidatosModel extends Mysql
{
    private $intIdCandidato;
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

    public function getCandidato(int $idcandidato)
    {
        $this->intIdCandidato = $idcandidato;
        $sql = "SELECT u.id_candidato,u.nombres_candidato,u.apellidos_candidato,u.telefono_candidato,u.email_candidato,u.rol_candidato,u.estado_candidato,r.nombre_rol
							FROM candidatos u
                            INNER JOIN roles r ON u.rol_candidato = r.id_rol
                            WHERE u.id_candidato = :iduser AND u.estado_candidato != :status ";
        $arrData = array(":iduser" => $this->intIdCandidato, ":status" => 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function setCandidato(string $nombres, string $apellidos, string $telefono, string $email, string $password, int $rolusuario)
    {
        $this->strNombres = $nombres;
        $this->strApellidos = $apellidos;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolUsuario = $rolusuario;
        $return = 0;

        $sql = "SELECT email_candidato FROM candidatos WHERE email_candidato = '{$this->strEmail}' AND estado_candidato != 0";

        if (empty($request)) {
            $sql_insert = "INSERT INTO candidatos(nombres_candidato, apellidos_candidato, telefono_candidato, email_candidato, password_candidato,rol_candidato)
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

    public function putCandidato(int $idcandidato, string $nombres, string $apellidos, string $telefono, string $email, string $password, int $rolusuario, int $status)
    {
        $this->intIdCandidato = $idcandidato;
        $this->strNombres = $nombres;
        $this->strApellidos = $apellidos;
        $this->strTelefono = $telefono; // Faltaba asignar
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolUsuario = $rolusuario;
        $this->intStatus = $status;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM candidatos WHERE email_candidato = ? AND id_candidato != ? AND estado_candidato != 0";
        $arrParams = array($this->strEmail, $this->intIdCandidato);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            if ($this->strPassword == "") {
                // UPDATE sin contraseña
                $sql = "UPDATE candidatos SET nombres_candidato = ?, apellidos_candidato = ?, telefono_candidato = ?, email_candidato = ?, rol_candidato = ?, estado_candidato = ? 
                    WHERE id_candidato = ?";
                $arrData = array(
                    $this->strNombres,
                    $this->strApellidos,
                    $this->strTelefono,
                    $this->strEmail,
                    $this->intRolUsuario,
                    $this->intStatus,
                    $this->intIdCandidato
                );
            } else {
                // UPDATE con contraseña
                $sql = "UPDATE candidatos SET nombres_candidato = ?, apellidos_candidato = ?, telefono_candidato = ?, email_candidato = ?, password_candidato = ?, rol_candidato = ?, estado_candidato = ? 
                    WHERE id_candidato = ?";
                $arrData = array(
                    $this->strNombres,
                    $this->strApellidos,
                    $this->strTelefono,
                    $this->strEmail,
                    $this->strPassword,
                    $this->intRolUsuario,
                    $this->intStatus,
                    $this->intIdCandidato
                );
            }

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function selectCandidatos()
    {
        $sql = "SELECT c.id_candidato, c.ape1_candidato, c.ape2_candidato,
                            c.nom1_candidato, c.nom2_candidato,c.telefono_candidato,
                            c.email_candidato, c.direccion_candidato, c.curul_candidato,c.partido_candidato,c.estado_candidato
							FROM candidatos c 
                            WHERE c.estado_candidato != 0 ORDER BY c.id_candidato DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function delCandidato($idcandidato)
    {
        $this->intIdCandidato = $idcandidato;
        $sql = "UPDATE candidatos SET estado_candidato = :estado WHERE id_candidato = :id ";
        $arrData = array(":estado" => 0, ":id" => $this->intIdCandidato);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
