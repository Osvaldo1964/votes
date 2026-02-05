<?php
require_once "Config/Config.php";
require_once "Libraries/Core/Conexion.php";
require_once "Libraries/Core/Mysql.php";

$conn = new Conexion();
$db = $conn->connect();
$tables = ['headresultado', 'places', 'zones'];

foreach ($tables as $table) {
    echo "--- TABLE: $table ---\n";
    $stmt = $db->query("DESCRIBE $table");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "\n";
}
