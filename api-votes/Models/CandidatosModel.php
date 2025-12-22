<?php

class CandidatosModel extends Mysql
{
    private $intIdCandidato;
    private $strCedula;
    private $strApe1;
    private $strApe2;
    private $strNom1;
    private $strNom2;
    private $strTelefono;
    private $strEmail;
    private $strDireccion;
    private $strCurul;
    private $strPartido;
    private $intEstado;

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

    public function setCandidato(string $cedula, string $ape1, string $ape2, string $nom1, string $nom2, string $telefono, string $email, string $direccion, int $curul, int $partido, int $estado)
    {
        $this->strCedula = $cedula;
        $this->strApe1 = $ape1;
        $this->strApe2 = $ape2;
        $this->strNom1 = $nom1;
        $this->strNom2 = $nom2;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strDireccion = $direccion;
        $this->strCurul = $curul;
        $this->strPartido = $partido;
        $this->intEstado = $estado;
        $return = 0;

        $sql = "SELECT ident_candidato FROM candidatos WHERE ident_candidato = ? AND estado_candidato != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (empty($request)) {
            $sql_insert = "INSERT INTO candidatos(ident_candidato, ape1_candidato, ape2_candidato, nom1_candidato, nom2_candidato, telefono_candidato, email_candidato, direccion_candidato, curul_candidato, partido_candidato, estado_candidato)
                       VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $arrData = array(
                "ident_candidato" => $this->strCedula,
                "ape1_candidato" => $this->strApe1,
                "ape2_candidato" => $this->strApe2,
                "nom1_candidato" => $this->strNom1,
                "nom2_candidato" => $this->strNom2,
                "telefono_candidato" => $this->strTelefono,
                "email_candidato" => $this->strEmail,
                "direccion_candidato" => $this->strDireccion,
                "curul_candidato" => $this->strCurul,
                "partido_candidato" => $this->strPartido,
                "estado_candidato" => $this->intEstado
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
