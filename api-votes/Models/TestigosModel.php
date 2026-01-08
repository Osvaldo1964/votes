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
        // También liberamos las mesas que tuviera
        $sqlRelease = "UPDATE headresultado SET testigo_headresultado = 0 WHERE testigo_headresultado = ?";
        $this->update($sqlRelease, array($this->intIdTestigo));
        return $request;
    }

    public function selectMesasPuesto(int $idPuesto, int $idTestigo = 0)
    {
        // 1. Obtener detalles del Puesto (agrupador)
        $sqlPuesto = "SELECT idzona_place, nameplace_place FROM places WHERE id_place = $idPuesto";
        $infoPuesto = $this->select($sqlPuesto, array());

        if (empty($infoPuesto)) return [];

        $zona = $infoPuesto['idzona_place'];
        $nombre = $infoPuesto['nameplace_place'];
        // Escapar comillas simple en el nombre para evitar error SQL
        $nombre = str_replace("'", "\'", $nombre);

        // 2. Buscar HeadResultados (Mesas) que coincidan con la ubicación
        // Deben estar LIBRES (0 o NULL) O asignadas a ESTE testigo ($idTestigo)
        $sql = "SELECT h.id_headresultado, p.mesa_place, h.testigo_headresultado
                    FROM headresultado h
                    INNER JOIN places p ON h.place_headresultado = p.id_place
                    WHERE p.idzona_place = $zona 
                    AND p.nameplace_place = '$nombre'
                    AND (h.testigo_headresultado IS NULL OR h.testigo_headresultado = 0 OR h.testigo_headresultado = $idTestigo)
                    ORDER BY CAST(p.mesa_place AS UNSIGNED) ASC";

        $request = $this->select_all($sql);
        return $request;
    }

    public function updateMesasTestigo(int $idTestigo, array $arrMesas)
    {
        // 1. Liberar mesas previamente asignadas a este testigo
        // Esto cubre el caso de desmarcar mesas o cambiar de puesto (libera las del puesto anterior)
        $sqlClean = "UPDATE headresultado SET testigo_headresultado = 0 WHERE testigo_headresultado = ?";
        $this->update($sqlClean, array($idTestigo));

        // 2. Asignar las nuevas mesas seleccionadas
        if (!empty($arrMesas)) {
            foreach ($arrMesas as $idHead) {
                // Validar que el idHead sea numerico para evitar inyeccion
                $idHead = intval($idHead);
                if ($idHead > 0) {
                    $sqlUpdate = "UPDATE headresultado SET testigo_headresultado = ? WHERE id_headresultado = ?";
                    $this->update($sqlUpdate, array($idTestigo, $idHead));
                }
            }
        }
    }
}
