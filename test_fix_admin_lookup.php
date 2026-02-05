<?php
// Simulate Admin Context
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';
require_once 'api-votes/Models/ElectoresModel.php';

// Mock strClean as it is a helper in Controllers (or helpers file)
// We need to verify if strClean is available. It's usually in helpers.
// If not available, we define a dummy one.
if (!function_exists('strClean')) {
    function strClean($str)
    {
        return $str;
    } // Dummy
}

class TestFix extends Mysql
{
    public function run($cedula)
    {
        $model = new ElectoresModel();

        // This simulates what getValidaElector does:
        // 1. model->selectPlace
        $requestPlace = $model->selectPlace($cedula);

        echo "=== RESULTS FOR CEDULA $cedula ===\n";
        echo "Puesto (Name expected): " . $requestPlace['nombre_puesto'] . "\n";
        echo "Puesto ID (Internal): " . $requestPlace['id_puesto_mesa'] . "\n";
        echo "Mesa ID: " . $requestPlace['id_mesa_new'] . "\n";

        if (!empty($requestPlace['nombre_puesto']) && !is_numeric($requestPlace['nombre_puesto'])) {
            echo "PASS: Puesto name is present and is text.\n";
        } else {
            echo "FAIL: Puesto name is missing or numeric (ID).\n";
        }
    }
}

$t = new TestFix();
$t->run('73111404');
