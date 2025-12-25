<?php

class LideresModel extends Mysql
{
    private $intIdLider;
    private $strCedula;
    private $strApe1;
    private $strApe2;
    private $strNom1;
    private $strNom2;
    private $strTelefono;
    private $strEmail;
    private $intDpto;
    private $intMuni;
    private $strDireccion;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectLider(int $idlider)
    {
        $this->intIdLider = $idlider;
        $sql = "SELECT id_lider,ident_lider,ape1_lider,ape2_lider,nom1_lider,nom2_lider,
                        telefono_lider,email_lider,dpto_lider,muni_lider,direccion_lider,estado_lider
                        FROM lideres
                        WHERE id_lider = ? AND estado_lider != ? ";
        $arrData = array($this->intIdLider, 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function insertLider(
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
        $this->intEstado = $estado;
        $return = 0;

        $sql = "SELECT ident_lider FROM lideres WHERE ident_lider = ? AND estado_lider != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (empty($request)) {
            $sql_insert = "INSERT INTO lideres(ident_lider, ape1_lider, ape2_lider, nom1_lider,
                                     nom2_lider, telefono_lider, email_lider, dpto_lider, muni_lider, direccion_lider,
                                     estado_lider)
                       VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                $this->intEstado
            );

            $request_insert = $this->insert($sql_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = 'exist';
        }
        return $return;
    }

    public function updateLider(
        int $idlider,
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
        int $estado
    ) {
        $this->intIdLider = $idlider;
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
        $this->intEstado = $estado;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM lideres WHERE email_lider = ? AND id_lider != ? AND estado_lider != 0";
        $arrParams = array($this->strEmail, $this->intIdLider);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            $sql = "UPDATE lideres SET ident_lider = ?, ape1_lider = ?, ape2_lider = ?, nom1_lider = ?, nom2_lider = ?,
                         telefono_lider = ?, email_lider = ?, dpto_lider = ?, muni_lider = ?, direccion_lider = ?, estado_lider = ? 
                    WHERE id_lider = ?";
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
                $this->intEstado,
                $this->intIdLider
            );

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function selectLideres()
    {
        $sql = "SELECT c.id_lider,c.ident_lider, c.ape1_lider, c.ape2_lider,
                            c.nom1_lider, c.nom2_lider,c.telefono_lider,
                            c.email_lider, c.dpto_lider, c.muni_lider, c.direccion_lider,c.estado_lider
							FROM lideres c 
                            WHERE c.estado_lider != 0 ORDER BY c.id_lider DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function deleteLider($idlider)
    {
        $this->intIdLider = $idlider;
        $sql = "UPDATE lideres SET estado_lider = ? WHERE id_lider = ? ";
        $arrData = array(0, $this->intIdLider);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
