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
    private $intInsc;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectElector(int $idelector)
    {
        $this->intIdElector = $idelector;
        $sql = "SELECT c.id_elector, c.ident_elector, c.ape1_elector, c.ape2_elector, c.nom1_elector, c.nom2_elector,
                        c.telefono_elector, c.email_elector, c.dpto_elector, c.muni_elector, c.direccion_elector, c.lider_elector, c.estado_elector, c.insc_elector,
                        l.nom1_lider, l.ape1_lider,
                        p.id_mesa_new,
                        d.name_department, m.name_municipality
                        FROM electores c
                        LEFT JOIN lideres l ON c.lider_elector = l.id_lider
                        LEFT JOIN places p ON c.ident_elector = p.ident_place
                        LEFT JOIN departments d ON c.dpto_elector = d.id_department
                        LEFT JOIN municipalities m ON c.muni_elector = m.id_municipality
                        WHERE c.id_elector = ? AND c.estado_elector != ? ";
        $arrData = array($this->intIdElector, 0);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    // ... (skipping insertElector/updateElector in replacement block, focusing on selectElector first, then selectElectores but I need multiple chunks or one big update?)
    // I'll do one big update or two chunks. Let's do two chunks for safety if they are far apart.
    // Actually, selectElector is lines 24-36. selectElectores is lines 160-172. They are far.
    // I will use multi_replace for this tool? Ah, the available tool is 'replace_file_content' (single block) or 'multi_replace...'.
    // I should use multi_replace_file_content.



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
        int $estado,
        int $insc
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
        $this->intInsc = $insc;
        $return = 0;

        $sql = "SELECT ident_elector FROM electores WHERE ident_elector = ? AND estado_elector != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (empty($request)) {
            $sql_insert = "INSERT INTO electores(ident_elector, ape1_elector, ape2_elector, nom1_elector,
                                     nom2_elector, telefono_elector, email_elector, dpto_elector, muni_elector, direccion_elector, lider_elector,
                                     estado_elector, insc_elector)
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
                $this->intLider,
                $this->intEstado,
                $this->intInsc
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
        int $estado,
        int $insc
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
        $this->intInsc = $insc;

        // 1. Validar si el email ya existe en OTRO usuario
        $sql = "SELECT * FROM electores WHERE email_elector = ? AND id_elector != ? AND estado_elector != 0";
        $arrParams = array($this->strEmail, $this->intIdElector);
        $request = $this->select_all_prepare($sql, $arrParams); // Usa una función que acepte parámetros

        if (empty($request)) {
            $sql = "UPDATE electores SET ident_elector = ?, ape1_elector = ?, ape2_elector = ?, nom1_elector = ?, nom2_elector = ?,
                         telefono_elector = ?, email_elector = ?, dpto_elector = ?, muni_elector = ?, direccion_elector = ?, lider_elector = ?, estado_elector = ?, insc_elector = ? 
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
                $this->intInsc,
                $this->intIdElector
            );

            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    public function selectElectores($filterMesa = 'all')
    {
        $whereExtra = "";
        // Usamos EXISTS/NOT EXISTS con subconsultas para evitar el colapso (CORS pending) provocado por el LEFT JOIN en la nube.
        if ($filterMesa === 'without') {
            $whereExtra = " AND NOT EXISTS (SELECT 1 FROM places p WHERE p.ident_place = c.ident_elector AND p.id_mesa_new IS NOT NULL) ";
        } else if ($filterMesa === 'with') {
            $whereExtra = " AND EXISTS (SELECT 1 FROM places p WHERE p.ident_place = c.ident_elector AND p.id_mesa_new IS NOT NULL) ";
        }

        $sql = "SELECT c.id_elector, c.ident_elector, c.ape1_elector, c.ape2_elector,
                            c.nom1_elector, c.nom2_elector, c.telefono_elector,
                            c.email_elector, c.dpto_elector, c.muni_elector, c.direccion_elector,
                            c.lider_elector, c.estado_elector, c.insc_elector,
                            l.nom1_lider, l.ape1_lider,
                            d.name_department, m.name_municipality
                            FROM electores c
                            LEFT JOIN lideres l ON c.lider_elector = l.id_lider
                            LEFT JOIN departments d ON c.dpto_elector = d.id_department
                            LEFT JOIN municipalities m ON c.muni_elector = m.id_municipality
                            WHERE c.estado_elector != 0 {$whereExtra} 
                            ORDER BY c.id_elector DESC ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectElectoresSelect()
    {
        $sql = "SELECT id_elector, ident_elector, nom1_elector, nom2_elector, ape1_elector, ape2_elector
                FROM electores 
                WHERE estado_elector != 0
                ORDER BY ape1_elector ASC, nom1_elector ASC";
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
        $sql = "SELECT p.ident_place, p.ape1_place, p.ape2_place, p.nom1_place, p.nom2_place, p.id_mesa_new,
                       me.id_puesto_mesa, pu.nombre_puesto, pu.idzona_puesto, z.muni_zone, m.id_department_municipality as dpto_zone
                FROM places p
                LEFT JOIN mesas me ON p.id_mesa_new = me.id_mesa
                LEFT JOIN puestos pu ON me.id_puesto_mesa = pu.id_puesto
                LEFT JOIN zones z ON pu.idzona_puesto = z.id_zone
                LEFT JOIN municipalities m ON z.muni_zone = m.id_municipality
                WHERE p.ident_place = ?";

        $arrData = array($id_elector);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    public function getUbicacionByMesa(int $idMesa)
    {
        $sql = "SELECT me.id_mesa, me.id_puesto_mesa, pu.nombre_puesto, pu.idzona_puesto, z.muni_zone, m.id_department_municipality as dpto_zone
                FROM mesas me
                INNER JOIN puestos pu ON me.id_puesto_mesa = pu.id_puesto
                INNER JOIN zones z ON pu.idzona_puesto = z.id_zone
                INNER JOIN municipalities m ON z.muni_zone = m.id_municipality
                WHERE me.id_mesa = ?";
        $res = $this->select($sql, array($idMesa));
        if ($res) {
            return [
                'status' => true,
                'ids' => [
                    'dpto' => $res['dpto_zone'],
                    'muni' => $res['muni_zone'],
                    'zone' => $res['idzona_puesto'],
                    'puesto' => $res['nombre_puesto'],
                    'mesa' => $res['id_mesa']
                ]
            ];
        }
        return ['status' => false];
    }

    public function insertOrUpdatePlace(string $ident, string $ape1, string $ape2, string $nom1, string $nom2, int $idMesa)
    {
        $sql = "SELECT id_place FROM places WHERE ident_place = ?";
        $exists = $this->select($sql, array($ident));

        if ($exists) {
            $sql_upd = "UPDATE places SET ape1_place=?, ape2_place=?, nom1_place=?, nom2_place=?, id_mesa_new=? WHERE id_place=?";
            $this->update($sql_upd, array($ape1, $ape2, $nom1, $nom2, $idMesa, $exists['id_place']));
        } else {
            $sql_ins = "INSERT INTO places (ident_place, ape1_place, ape2_place, nom1_place, nom2_place, id_mesa_new) VALUES (?,?,?,?,?,?)";
            $this->insert($sql_ins, array($ident, $ape1, $ape2, $nom1, $nom2, $idMesa));
        }
    }

    public function updatePollElector(string $identificacion, string $lat = "", string $lon = "")
    {
        $this->strCedula = $identificacion;
        $lat = !empty($lat) ? $lat : NULL;
        $lon = !empty($lon) ? $lon : NULL;

        // 1. Verificar si existe en ELECTORES
        $sql = "SELECT id_elector, poll_elector FROM electores WHERE ident_elector = ? AND estado_elector != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);

        if (!empty($request)) {
            // A. YA EXISTE EN ELECTORES
            $estadoVoto = intval($request['poll_elector']);
            if ($estadoVoto >= 1) {
                return "voted";
            }
            // Actualizar Voto + Coordenadas
            $sql_update = "UPDATE electores SET poll_elector = ?, lati_elector = ?, long_elector = ? WHERE id_elector = ?";
            $arrUpdate = array(1, $lat, $lon, $request['id_elector']);
            $request_update = $this->update($sql_update, $arrUpdate);
            return $request_update;
        } else {
            // B. NO EXISTE EN ELECTORES -> BUSCAR EN CENSO (PLACES)
            // Necesitamos los IDs (dpto, muni) para insertar en electores.
            // Hacemos el JOIN completo para obtener esos IDs desde la jerarquia nueva.

            $sqlPlaceRaw = "SELECT p.ape1_place, p.ape2_place, p.nom1_place, p.nom2_place,
                                   d.id_department, m.id_municipality
                            FROM places p
                            INNER JOIN mesas me ON p.id_mesa_new = me.id_mesa
                            INNER JOIN puestos pu ON me.id_puesto_mesa = pu.id_puesto
                            INNER JOIN zones z ON pu.idzona_puesto = z.id_zone
                            LEFT JOIN municipalities m ON z.muni_zone = m.id_municipality
                            LEFT JOIN departments d ON m.id_department_municipality = d.id_department
                            WHERE p.ident_place = ?";

            $arrPlaceRaw = $this->select($sqlPlaceRaw, array($this->strCedula));

            if (empty($arrPlaceRaw)) {
                return "not_found";
            }

            // Mapeo de datos
            $dpto = !empty($arrPlaceRaw['id_department']) ? $arrPlaceRaw['id_department'] : 0; // Fallback 0
            $muni = !empty($arrPlaceRaw['id_municipality']) ? $arrPlaceRaw['id_municipality'] : 0;

            $nom1 = $arrPlaceRaw['nom1_place'];
            $nom2 = $arrPlaceRaw['nom2_place'];
            $ape1 = $arrPlaceRaw['ape1_place'];
            $ape2 = $arrPlaceRaw['ape2_place'];

            $telefono = "";
            $email = "";
            $direccion = "";
            $lider = 0;
            $estado = 1;
            $insc = 0;


            $insert = $this->insertElector(
                $this->strCedula,
                $ape1,
                $ape2,
                $nom1,
                $nom2,
                $telefono,
                $email,
                $dpto,
                $muni,
                $direccion,
                $lider,
                $estado,
                $insc
            );

            if ($insert > 0 && $insert != 'exist') {
                // D. MARCAR VOTO AL RECIEN CREADO
                // Tambien actualizamos coordenadas aqui
                $sql_update = "UPDATE electores SET poll_elector = ?, lati_elector = ?, long_elector = ? WHERE id_elector = ?";
                $arrUpdate = array(1, $lat, $lon, $insert); // $insert es el ID nuevo
                $request_update = $this->update($sql_update, $arrUpdate);
                return $request_update;
            } else {
                return false;
            }
        }
    }

    public function selectElectorByIdent(string $identificacion)
    {
        $this->strCedula = $identificacion;
        $sql = "SELECT id_elector, poll_elector FROM electores WHERE ident_elector = ? AND estado_elector != 0";
        $arrData = array($this->strCedula);
        $request = $this->select($sql, $arrData);
        return $request;
    }
}
