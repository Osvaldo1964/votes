<?php
require_once "Config/Config.php";
require_once "Libraries/Core/Conexion.php";
require_once "Libraries/Core/Mysql.php";
require_once "Models/LugaresModel.php";

$model = new LugaresModel();
try {
    $data = $model->getPuestos(1); // Try some id
    echo "DATA: ";
    print_r($data);
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage();
}
