<?php

class ElectoresModel extends Mysql
{
    private $intIdElector;
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
    private $intLider;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectElector(int $idelector)
    {
        $this->intIdElector = $idelector;
        $sql = "SELECT c.id_elector,c.ident_elector,c.ape1_elector,c.ape2_elector,c.nom1_elector,c.nom2_elector,
                        c.telefono_elector,c.email_elector,c.dpto_elector,c.muni_elector,c.direccion_elector, c.lider_elector,c.estado_elector,
                        l.nom1_lider, l.ape1_lider
                        FROM electores c
                        LEFT JOIN lideres l ON c.lider_elector = l.id_lider
                        WHERE c.id_elector = ? AND c.estado_elector != ? ";
        $arrData = array($this->intIdElector, 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function insertElector(
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
        int $lider,
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
        $this->intLider = $lider;
        $this->intEstado = $estado;
        $return = 0;

        $sql = "SELECT ident_elector FROM electores WHERE ident_elector = ? AND estado_elector != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (empty($request)) {
            $sql_insert = "INSERT INTO electores(ident_elector, ape1_elector, ape2_elector, nom1_elector,
                                     nom2_elector, telefono_elector, email_elector, dpto_elector, muni_elector, direccion_elector, lider_elector,
                                     estado_elector)
                       VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                $this->intLider,
                $this->intEstado
            );

            $request_insert = $this->insert($sql_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = 'exist';
        }
        return $return;
    }

    public function updateElector(
        int $idelector,
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
        int $lider,
        int $estado
    ) {
        $this->intIdElector = $idelector;
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
        $this->intLider = $lider;
        $this->intEstado = $estado;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM electores WHERE email_elector = ? AND id_elector != ? AND estado_elector != 0";
        $arrParams = array($this->strEmail, $this->intIdElector);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            $sql = "UPDATE electores SET ident_elector = ?, ape1_elector = ?, ape2_elector = ?, nom1_elector = ?, nom2_elector = ?,
                         telefono_elector = ?, email_elector = ?, dpto_elector = ?, muni_elector = ?, direccion_elector = ?, lider_elector = ?, estado_elector = ? 
                    WHERE id_elector = ?";
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
                $this->intLider,
                $this->intEstado,
                $this->intIdElector
            );

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function selectElectores()
    {
        $sql = "SELECT c.id_elector,c.ident_elector, c.ape1_elector, c.ape2_elector,
                            c.nom1_elector, c.nom2_elector,c.telefono_elector,
                            c.email_elector, c.dpto_elector, c.muni_elector, c.direccion_elector,
                            c.lider_elector, c.estado_elector,
                            l.nom1_lider, l.ape1_lider 
							FROM electores c
                            LEFT JOIN lideres l ON c.lider_elector = l.id_lider
                            WHERE c.estado_elector != 0 ORDER BY c.id_elector DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function deleteElector($idelector)
    {
        $this->intIdElector = $idelector;
        $sql = "UPDATE electores SET estado_elector = ? WHERE id_elector = ? ";
        $arrData = array(0, $this->intIdElector);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function selectPlace(string $id_elector)
    {
        // CAST a ambos lados para asegurar comparación numérica estricta
        // query optimizada copiada de PlaceModel para traer nombres de zona
        $sql = "SELECT d.name_department, m.name_municipality, z.name_zone, p.nameplace_place, p.ape1_place, p.ape2_place, p.nom1_place, p.nom2_place, p.mesa_place, p.ident_place 
                FROM places p
                INNER JOIN departments d ON p.iddpto_place = d.id_department
                INNER JOIN municipalities m ON p.idmuni_place = m.id_municipality
                INNER JOIN zones z ON p.idzona_place = z.id_zone
                WHERE CAST(p.ident_place AS UNSIGNED) = ?";

        $arrData = array((int)$id_elector);
        $request = $this->select($sql, $arrData);
        return $request;
    }
    public function updatePollElector(string $identificacion)
    {
        $this->strCedula = $identificacion;

        // 1. Buscar elector y verificar estado actual del voto
        $sql = "SELECT id_elector, poll_elector FROM electores WHERE ident_elector = ? AND estado_elector != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (empty($request)) {
            return "not_found";
        }

        if ($request['poll_elector'] == 1) {
            return "voted";
        }

        // 2. Registrar el voto (cambiar a 1)
        $sql_update = "UPDATE electores SET poll_elector = ? WHERE id_elector = ?";
        $arrUpdate = array(1, $request['id_elector']);
        $request_update = $this->update($sql_update, $arrUpdate);

        return $request_update;
    }

    public function selectElectorByIdent(string $identificacion)
    {
        $this->strCedula = $identificacion;
        $sql = "SELECT id_elector FROM electores WHERE ident_elector = ? AND estado_elector != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);
        return $request;
    }
}
