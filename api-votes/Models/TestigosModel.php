<?php

class TestigosModel extends Mysql
{
    private $intIdTestigo;
    private $intElector;
    private $intDpto;
    private $intMuni;
    private $intZona;
    private $intPuesto;
    private $intMesa;
    private $intEstado;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectTestigos()
    {
        // Join con electores para traer datos personales
        // Join con lugares para traer nombres de ubicación de TRABAJO (si están definidos)
        $sql = "SELECT t.id_testigo, t.elector_testigo, t.dpto_testigo, t.muni_testigo, t.zona_testigo, t.puesto_testigo, t.mesa_testigo, t.estado_testigo,
                       e.ident_elector, e.nom1_elector, e.nom2_elector, e.ape1_elector, e.ape2_elector, e.telefono_elector,
                       d.name_department, m.name_municipality, z.name_zone, p.nameplace_place
                FROM testigos t
                INNER JOIN electores e ON t.elector_testigo = e.id_elector
                LEFT JOIN departments d ON t.dpto_testigo = d.id_department
                LEFT JOIN municipalities m ON t.muni_testigo = m.id_municipality
                LEFT JOIN zones z ON t.zona_testigo = z.id_zone
                LEFT JOIN places p ON t.puesto_testigo = p.id_place
                WHERE t.estado_testigo != 0
                ORDER BY t.id_testigo DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectTestigo(int $idtestigo)
    {
        $this->intIdTestigo = $idtestigo;
        $sql = "SELECT * FROM testigos WHERE id_testigo = ?";
        $arrData = array($this->intIdTestigo);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function insertTestigo(int $elector, int $dpto, int $muni, int $zona, int $puesto, int $mesa, int $estado)
    {
        $this->intElector = $elector;
        $this->intDpto = $dpto;
        $this->intMuni = $muni;
        $this->intZona = $zona;
        $this->intPuesto = $puesto; // Ojo: en la tabla testigos dice 'puesto_testigo bigint'. Asumo es ID de places o ID Puesto.
        // En MonitorModel usaste 'nameplace_place' string. En Testigos parece ser ID por ser bigint.
        // Si no tienes una tabla 'puestos' separada y usas 'places' como puesto:
        // Asumiremos que puesto_testigo guarda el ID de la tabla PLACES que representa al puesto.
        $this->intMesa = $mesa;
        $this->intEstado = $estado;

        // Validar si el elector YA es testigo activo
        $sql = "SELECT id_testigo FROM testigos WHERE elector_testigo = ? AND estado_testigo != 0";
        $arrParams = array($this->intElector);
        $request = $this->select($sql, $arrParams);

        if (empty($request)) {
            $query_insert = "INSERT INTO testigos(elector_testigo, dpto_testigo, muni_testigo, zona_testigo, puesto_testigo, mesa_testigo, estado_testigo) VALUES(?,?,?,?,?,?,?)";
            $arrData = array($this->intElector, $this->intDpto, $this->intMuni, $this->intZona, $this->intPuesto, $this->intMesa, $this->intEstado);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    public function updateTestigo(int $idtestigo, int $elector, int $dpto, int $muni, int $zona, int $puesto, int $mesa, int $estado)
    {
        $this->intIdTestigo = $idtestigo;
        $this->intElector = $elector;
        $this->intDpto = $dpto;
        $this->intMuni = $muni;
        $this->intZona = $zona;
        $this->intPuesto = $puesto;
        $this->intMesa = $mesa;
        $this->intEstado = $estado;

        // Validar duplicado (mismo elector otro ID)
        $sql = "SELECT id_testigo FROM testigos WHERE elector_testigo = ? AND id_testigo != ? AND estado_testigo != 0";
        $arrParams = array($this->intElector, $this->intIdTestigo);
        $request = $this->select($sql, $arrParams);

        if (empty($request)) {
            $sql = "UPDATE testigos SET elector_testigo = ?, dpto_testigo = ?, muni_testigo = ?, zona_testigo = ?, puesto_testigo = ?, mesa_testigo = ?, estado_testigo = ? WHERE id_testigo = ?";
            $arrData = array($this->intElector, $this->intDpto, $this->intMuni, $this->intZona, $this->intPuesto, $this->intMesa, $this->intEstado, $this->intIdTestigo);
            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function deleteTestigo(int $idtestigo)
    {
        $this->intIdTestigo = $idtestigo;
        $sql = "UPDATE testigos SET estado_testigo = ? WHERE id_testigo = ?";
        $arrData = array(0, $this->intIdTestigo);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
