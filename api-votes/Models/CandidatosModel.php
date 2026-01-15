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
    private $intDpto;
    private $intMuni;
    private $intCurul;
    private $intPartido;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectCandidato(int $idcandidato)
    {
        $this->intIdCandidato = $idcandidato;
        $sql = "SELECT id_candidato,ident_candidato,ape1_candidato,ape2_candidato,nom1_candidato,nom2_candidato,
                        telefono_candidato,email_candidato,dpto_candidato,muni_candidato,direccion_candidato,curul_candidato,partido_candidato,estado_candidato
                        FROM candidatos
                        WHERE id_candidato = ? AND estado_candidato != ? ";
        $arrData = array($this->intIdCandidato, 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function insertCandidato(
        string $cedula,
        string $ape1,
        string $ape2,
        string $nom1,
        string $nom2,
        string $telefono,
        string $email,
        int $dpto,
        int $muni,
        string $direccion,
        int $curul,
        int $partido,
        int $estado
    ) {
        $this->strCedula = $cedula;
        $this->strApe1 = $ape1;
        $this->strApe2 = $ape2;
        $this->strNom1 = $nom1;
        $this->strNom2 = $nom2;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->intDpto = $dpto;
        $this->intMuni = $muni;
        $this->strDireccion = $direccion;
        $this->intCurul = $curul;
        $this->intPartido = $partido;
        $this->intEstado = $estado;
        $return = 0;

        $sql = "SELECT ident_candidato FROM candidatos WHERE ident_candidato = ? AND estado_candidato != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (empty($request)) {
            $sql_insert = "INSERT INTO candidatos(ident_candidato, ape1_candidato, ape2_candidato, nom1_candidato,
                                     nom2_candidato, telefono_candidato, email_candidato, dpto_candidato, muni_candidato, direccion_candidato,
                                     curul_candidato, partido_candidato, estado_candidato)
                       VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $arrData = array(
                $this->strCedula,
                $this->strApe1,
                $this->strApe2,
                $this->strNom1,
                $this->strNom2,
                $this->strTelefono,
                $this->strEmail,
                $this->intDpto,
                $this->intMuni,
                $this->strDireccion,
                $this->intCurul,
                $this->intPartido,
                $this->intEstado
            );

            $request_insert = $this->insert($sql_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = 'exist';
        }
        return $return;
    }

    public function updateCandidato(
        int $idcandidato,
        string $cedula,
        string $ape1,
        string $ape2,
        string $nom1,
        string $nom2,
        string $telefono,
        string $email,
        int $dpto,
        int $muni,
        string $direccion,
        int $curul,
        int $partido,
        int $estado
    ) {
        $this->intIdCandidato = $idcandidato;
        $this->strCedula = $cedula;
        $this->strApe1 = $ape1;
        $this->strApe2 = $ape2;
        $this->strNom1 = $nom1;
        $this->strNom2 = $nom2;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->intDpto = $dpto;
        $this->intMuni = $muni;
        $this->strDireccion = $direccion;
        $this->intCurul = $curul;
        $this->intPartido = $partido;
        $this->intEstado = $estado;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM candidatos WHERE email_candidato = ? AND id_candidato != ? AND estado_candidato != 0";
        $arrParams = array($this->strEmail, $this->intIdCandidato);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            $sql = "UPDATE candidatos SET ident_candidato = ?, ape1_candidato = ?, ape2_candidato = ?, nom1_candidato = ?, nom2_candidato = ?,
                         telefono_candidato = ?, email_candidato = ?, dpto_candidato = ?, muni_candidato = ?, direccion_candidato = ?, curul_candidato = ?,
                         partido_candidato = ?, estado_candidato = ? 
                    WHERE id_candidato = ?";
            $arrData = array(
                $this->strCedula,
                $this->strApe1,
                $this->strApe2,
                $this->strNom1,
                $this->strNom2,
                $this->strTelefono,
                $this->strEmail,
                $this->intDpto,
                $this->intMuni,
                $this->strDireccion,
                $this->intCurul,
                $this->intPartido,
                $this->intEstado,
                $this->intIdCandidato
            );

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function selectCandidatos()
    {
        $sql = "SELECT c.id_candidato,c.ident_candidato, c.ape1_candidato, c.ape2_candidato,
                            c.nom1_candidato, c.nom2_candidato,c.telefono_candidato,
                            c.email_candidato, c.dpto_candidato, c.muni_candidato, c.direccion_candidato, c.curul_candidato,c.partido_candidato,c.estado_candidato
							FROM candidatos c 
                            WHERE c.estado_candidato != 0 ORDER BY c.id_candidato DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function deleteCandidato($idcandidato)
    {
        $this->intIdCandidato = $idcandidato;
        $sql = "UPDATE candidatos SET estado_candidato = ? WHERE id_candidato = ? ";
        $arrData = array(0, $this->intIdCandidato);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
