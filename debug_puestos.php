<?php
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';

class DebugPuestos extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        ob_start();
        echo "=== CHECKING DUPLICATE PUESTO NAMES ===\n";

        $sql = "SELECT nombre_puesto, idzona_puesto, COUNT(*) as c, GROUP_CONCAT(id_puesto) as ids
                FROM puestos 
                GROUP BY nombre_puesto, idzona_puesto 
                HAVING c > 1";

        $rows = $this->select_all($sql);

        if (empty($rows)) {
            echo "No duplicate Puesto names within the same Zone found.\n";
        } else {
            echo "FOUND DUPLICATES:\n";
            foreach ($rows as $r) {
                echo "Zona: " . $r['idzona_puesto'] . " | Name: " . $r['nombre_puesto'] .
                    " | Count: " . $r['c'] . " | IDs: " . $r['ids'] . "\n";
            }
        }

        echo "\n=== CHECKING SPECIFIC PUESTO (FROM CEDULA 73111404) ===\n";
        $sql = "SELECT m.id_mesa, m.id_puesto_mesa, p.nombre_puesto, p.idzona_puesto 
                FROM mesas m 
                JOIN puestos p ON m.id_puesto_mesa = p.id_puesto
                WHERE m.id_mesa = 459";
        $mesa = $this->select($sql, []);

        if ($mesa) {
            echo "Mesa 459 belongs to Puesto ID: " . $mesa['id_puesto_mesa'] . "\n";
            echo "Puesto Name: " . $mesa['nombre_puesto'] . "\n";
            echo "Zona ID: " . $mesa['idzona_puesto'] . "\n";

            $name = $mesa['nombre_puesto'];
            $zone = $mesa['idzona_puesto'];
            $sqlDup = "SELECT * FROM puestos WHERE nombre_puesto = '$name' AND idzona_puesto = $zone";
            $dups = $this->select_all($sqlDup);
            echo "Puestos with this name in this zone:\n";
            foreach ($dups as $d) {
                echo " - ID: " . $d['id_puesto'] . " Name: " . $d['nombre_puesto'] . "\n";
            }
        } else {
            echo "Mesa 459 NOT FOUND.\n";
        }

        file_put_contents('debug_puestos_out.txt', ob_get_clean());
    }
}

$dbg = new DebugPuestos();
$dbg->run();
