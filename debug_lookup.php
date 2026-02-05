<?php
// Adjust paths based on root execution from c:\xampp\htdocs\votes
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';

class Debugger extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        ob_start();
        echo "=== CHECKING SCHEMA ===\n";
        $this->checkSchema('places');
        $this->checkSchema('mesas');
        $this->checkSchema('puestos');

        echo "\n=== CHECKING TOTALS ===\n";
        $this->count('places');
        $this->count('zones');
        $this->count('puestos');
        $this->count('mesas');

        echo "\n=== CHECKING RELATIONSHIPS ===\n";
        // Check how many people in places have a valid mesa
        $sql = "SELECT COUNT(*) as count FROM places WHERE id_mesa_new IS NOT NULL AND id_mesa_new != 0";
        $res = $this->select($sql, []);
        echo "Places with id_mesa_new assigned: " . $res['count'] . "\n";

        // Check how many of those actually exist in mesas table
        $sql = "SELECT COUNT(*) as count FROM places p 
                INNER JOIN mesas m ON p.id_mesa_new = m.id_mesa";
        $res = $this->select($sql, []);
        echo "Places with VALID mesa (INNER JOIN): " . $res['count'] . "\n";

        // Difference
        $sql = "SELECT COUNT(*) as count FROM places p 
                WHERE p.id_mesa_new IS NOT NULL AND p.id_mesa_new != 0 
                AND p.id_mesa_new NOT IN (SELECT id_mesa FROM mesas)";
        $res = $this->select($sql, []);
        echo "Places with INVALID mesa reference (orphan FK): " . $res['count'] . "\n";

        echo "\n=== CHECKING MESAS -> PUESTOS ===\n";
        $sql = "SELECT COUNT(*) as count FROM mesas m
                WHERE m.id_puesto_mesa IS NOT NULL AND m.id_puesto_mesa != 0
                AND m.id_puesto_mesa NOT IN (SELECT id_puesto FROM puestos)";
        $res = $this->select($sql, []);
        echo "Mesas with INVALID puesto reference (orphan FK): " . $res['count'] . "\n";

        if ($res['count'] > 0) {
            $sql = "SELECT id_mesa, numero_mesa, id_puesto_mesa FROM mesas 
                     WHERE id_puesto_mesa NOT IN (SELECT id_puesto FROM puestos) LIMIT 5";
            $rows = $this->select_all($sql);
            foreach ($rows as $r) {
                echo "Orphan Mesa ID: " . $r['id_mesa'] . " refers to Puesto ID: " . $r['id_puesto_mesa'] . "\n";
            }
        }

        // Check if mesas table is empty?
        $sql = "SELECT COUNT(*) as count FROM mesas";
        $res = $this->select($sql, []);
        if ($res['count'] == 0) {
            echo "ALERT: Mesas table is EMPTY!\n";
        }

        // Check for duplicates
        echo "\n=== CHECKING DUPLICATES ===\n";
        $sql = "SELECT ident_place, COUNT(*) c FROM places GROUP BY ident_place HAVING c > 1 LIMIT 5";
        $res = $this->select_all($sql);
        if (!empty($res)) {
            echo "Found duplicate cedulas (showing first 5):\n";
            foreach ($res as $r) {
                echo "Cedula: " . $r['ident_place'] . " Count: " . $r['c'] . "\n";
            }
        } else {
            echo "No duplicate cedulas found.\n";
        }

        // Check format
        echo "\n=== CHECKING FORMAT ===\n";
        $res = $this->select_all("SELECT ident_place FROM places LIMIT 10");
        echo "Sample ident_place values: " . implode(", ", array_column($res, 'ident_place')) . "\n";

        $output = ob_get_clean();
        file_put_contents('debug_output.txt', $output);
        echo "Debug output written to debug_output.txt\n";
    }

    private function checkSchema($table)
    {
        $cols = $this->select_all("DESCRIBE $table");
        if (is_array($cols)) {
            echo "Table $table columns: " . implode(", ", array_column($cols, 'Field')) . "\n";
        } else {
            echo "Table $table error OR doesn't exist.\n";
        }
    }

    private function count($table)
    {
        $res = $this->select("SELECT COUNT(*) as c FROM $table", []);
        echo "Count $table: " . ($res ? $res['c'] : 'Error') . "\n";
    }
}

$dbg = new Debugger();
$dbg->run();
