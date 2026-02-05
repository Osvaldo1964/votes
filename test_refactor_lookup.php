<?php
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';
require_once 'api-votes/Models/LugaresModel.php';

// Mock helpers
if (!function_exists('strClean')) {
    function strClean($s)
    {
        return $s;
    }
}

class TestRefactor extends Mysql
{
    public function run()
    {
        $model = new LugaresModel();

        // Test Case: Cedula 73111404 -> Mesa 459 -> Puesto 26 (I.E.D.HUGO J. BERMUDEZ) -> Zona 4
        $idZona = 4;
        $idPuesto = 26; // The ID we want to test

        echo "=== TESTING getMesas WITH ID ===\n";
        echo "Input: Zona=$idZona, PuestoID=$idPuesto\n";

        $mesas = $model->getMesas($idZona, $idPuesto);

        if (empty($mesas)) {
            echo "FAIL: No mesas found for Puesto ID $idPuesto\n";
        } else {
            echo "PASS: Found " . count($mesas) . " mesas.\n";
            echo "Sample Mesa: ID=" . $mesas[0]['id_mesa'] . " Name=" . $mesas[0]['nombre_mesa'] . "\n";

            // Verify if Mesa 459 is in the list
            $found = false;
            foreach ($mesas as $m) {
                if ($m['id_mesa'] == 459)
                    $found = true;
            }
            echo "Target Mesa 459 found in list? " . ($found ? "YES" : "NO") . "\n";
        }
    }
}

$t = new TestRefactor();
$t->run();
