<?php
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";
require_once "api-votes/Models/AnalisisModel.php";

$model = new AnalisisModel();
// Simulate params: Dpto=MAGDALENA(id?), Muni=SANTA MARTA(id?), Zona=SURORIENTAL, Puesto=RODRIGO DE BASTIDAS
// Assuming IDs. Need to be robust. 
// Let's rely on the previous debug output for IDs if possible, or try broad search.

// Try with empty filters first (should fail or return everything depending on implementation, 
// actually the model checks empty strings but let's try passing what the controller expects)
// The Controller checks required Dpto/Muni.
// Let's verify SQL syntax only.

// Fetch one valid muni/zone/puesto to test
$db = new Mysql();
$z = $db->select("SELECT id_zone, muni_zone FROM zones LIMIT 1", array());
$idZona = $z['id_zone'];
$idMuni = $z['muni_zone'];

echo "Testing AnalisisModel with Muni: $idMuni, Zona: $idZona\n";
try {
    $res = $model->selectReporteAnalisis(1, $idMuni, $idZona, "todos");
    if (is_array($res)) {
        echo "Success. Rows returned: " . count($res) . "\n";
        if (count($res) > 0)
            print_r($res[0]);
    } else {
        echo "Error: Result is not array.\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
?>