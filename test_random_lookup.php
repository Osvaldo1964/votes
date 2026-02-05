<?php
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';
require_once 'api-votes/Models/PublicoModel.php';

// Mock helpers if needed, but PublicoModel only uses strClean?
// Actually PublicoModel doesn't seem to use strClean inside the query method, the Controller does.
// But the model extends Mysql.

class Tester
{
    public function run()
    {
        $model = new PublicoModel();

        // Get 5 random cedulas
        $mysql = new Mysql();
        $random_users = $mysql->select_all("SELECT ident_place, id_mesa_new FROM places ORDER BY RAND() LIMIT 5");

        echo "Testing 5 random Lookups:\n";
        foreach ($random_users as $u) {
            $cedula = $u['ident_place'];
            echo "Checking Cedula: $cedula (Mesa New: " . $u['id_mesa_new'] . ")... ";

            $result = $model->selectConsultaPublica($cedula);

            if (!empty($result)) {
                echo "FOUND! Data: " . print_r($result, true) . "\n";
            } else {
                echo "NOT FOUND (Empty Result)!\n";
            }
            echo "------------------------------------------------\n";
        }
    }
}

$t = new Tester();
$t->run();
