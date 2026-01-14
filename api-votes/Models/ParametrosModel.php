<?php

class ParametrosModel extends Mysql
{
    private $strCandidato;
    public $intIdCandidato;
    private $strIdentificacion;
    private $strDireccion;
    private $strTelefono;
    private $strEmail;
    private $intCurul;
    private $intPartido;
    private $strEslogan;
    private $intNumLista;
    private $strFoto;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectParametros()
    {
        // New Schema: canditado (bigint) is the FK. Columns like id_parametros, id_candidato are gone.
        $sql = "SELECT p.canditado as id_candidato, p.eslogan, p.numlista, p.foto,
                       c.ident_candidato, c.telefono_candidato, c.email_candidato, c.direccion_candidato, 
                       c.curul_candidato, c.partido_candidato,
                       CONCAT(c.nom1_candidato, ' ', c.nom2_candidato, ' ', c.ape1_candidato, ' ', c.ape2_candidato) as nombre_oficial
                FROM parametros p
                LEFT JOIN candidatos c ON p.canditado = c.id_candidato
                LIMIT 1";
        $request = $this->select($sql, array());
        return $request;
    }

    public function updateParametros(string $candidato, int $idCandidato, string $eslogan, int $numlista, string $foto)
    {
        // In new schema, 'canditado' column holds the ID. The string 'candidato' var is unused/legacy.
        $this->intIdCandidato = $idCandidato;
        $this->strEslogan = $eslogan;
        $this->intNumLista = $numlista;
        $this->strFoto = $foto;

        // Check if a record exists
        $sqlCheck = "SELECT COUNT(*) as count FROM parametros";
        $requestCheck = $this->select($sqlCheck, array());

        if ($requestCheck['count'] > 0) {
            // Update existing record. PK is missing/not needed as per user "always 1 reg".
            // We update 'canditado' column with the ID.
            $sql = "UPDATE parametros SET canditado = ?, eslogan = ?, numlista = ?, foto = ?";
            $arrData = array($this->intIdCandidato, $this->strEslogan, $this->intNumLista, $this->strFoto);
            $request = $this->update($sql, $arrData);
        } else {
            // Insert new record
            $sql = "INSERT INTO parametros (canditado, eslogan, numlista, foto) VALUES (?, ?, ?, ?)";
            $arrData = array($this->intIdCandidato, $this->strEslogan, $this->intNumLista, $this->strFoto);
            $request = $this->insert($sql, $arrData);
        }
        return $request;
    }
    public function selectCandidatos()
    {
        $sql = "SELECT id_candidato, 
                       CONCAT(nom1_candidato, ' ', nom2_candidato, ' ', ape1_candidato, ' ', ape2_candidato) as nombre
                FROM candidatos 
                WHERE estado_candidato != 0
                ORDER BY nombre ASC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectCandidato(int $id)
    {
        $sql = "SELECT CONCAT(nom1_candidato, ' ', nom2_candidato, ' ', ape1_candidato, ' ', ape2_candidato) as nombre
                FROM candidatos 
                WHERE id_candidato = $id";
        $request = $this->select($sql, array());
        return $request;
    }
}
