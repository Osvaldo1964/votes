<?php
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';

class DebugSpecific extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($cedula)
    {
        echo "=== DEBUGGING CEDULA: $cedula ===\n";

        // 1. Get Place Data
        $sql = "SELECT * FROM places WHERE ident_place = '$cedula'";
        $place = $this->select($sql, []);

        if (empty($place)) {
            echo "Cedula NOT FOUND in places table.\n";
            return;
        }

        echo "Found in Places:\n";
        print_r($place);

        $idMesa = $place['id_mesa_new'];
        echo "Mesa ID (from places): " . ($idMesa ? $idMesa : "NULL") . "\n";

        if (!$idMesa) {
            echo "STOP: No mesa assigned.\n";
            return;
        }

        // 2. Get Mesa Data
        $sql = "SELECT * FROM mesas WHERE id_mesa = $idMesa";
        $mesa = $this->select($sql, []);

        if (empty($mesa)) {
            echo "STOP: Mesa ID $idMesa NOT FOUND in mesas table.\n";
            return;
        }

        echo "Found in Mesas:\n";
        print_r($mesa);

        $idPuesto = $mesa['id_puesto_mesa'];
        echo "Puesto ID (from mesas): " . ($idPuesto ? $idPuesto : "NULL") . "\n";

        if (!$idPuesto) {
            echo "STOP: No puesto assigned to mesa.\n";
            return;
        }

        // 3. Get Puesto Data
        $sql = "SELECT * FROM puestos WHERE id_puesto = $idPuesto";
        $puesto = $this->select($sql, []);

        if (empty($puesto)) {
            echo "STOP: Puesto ID $idPuesto NOT FOUND in puestos table.\n";
            return;
        }

        echo "Found in Puestos:\n";
        print_r($puesto);

        $idZona = $puesto['idzona_puesto'];
        echo "Zona ID (from puestos): " . ($idZona ? $idZona : "NULL") . "\n";

        if (!$idZona) {
            echo "STOP: No zona assigned to puesto.\n";
            return;
        }

        // 4. Get Zona Data
        $sql = "SELECT * FROM zones WHERE id_zone = $idZona";
        $zona = $this->select($sql, []);

        if (empty($zona)) {
            echo "STOP: Zona ID $idZona NOT FOUND in zones table.\n";
            return;
        }

        echo "Found in Zones:\n";
        print_r($zona);

        $idMuni = $zona['muni_zone'];
        echo "Municipality ID (from zones): " . ($idMuni ? $idMuni : "NULL") . "\n";

        // 5. Get Municipality Data
        $sql = "SELECT * FROM municipalities WHERE id_municipality = $idMuni";
        $muni = $this->select($sql, []);

        if (empty($muni)) {
            echo "STOP: Municipality ID $idMuni NOT FOUND in municipalities table.\n";
            return;
        }

        echo "Found in Municipalities:\n";
        print_r($muni);

        echo "=== CHAIN COMPLETE ===\n";
    }
}

$dbg = new DebugSpecific();
$dbg->run('73111404');
