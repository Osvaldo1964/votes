<?php
require_once "api-votes/Config/Config.php";
require_once "api-votes/Libraries/Core/Conexion.php";
require_once "api-votes/Libraries/Core/Mysql.php";
require_once "api-votes/Models/LugaresModel.php";

echo "Instantiating LugaresModel...\n";
$model = new LugaresModel();
echo "Calling getMisVotos(1)...\n";
try {
    $res = $model->getMisVotos(1);
    print_r($res);
    echo "Success!\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>