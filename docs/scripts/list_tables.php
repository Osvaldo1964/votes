<?php
require_once "Config/Config.php";
require_once "Libraries/Core/Conexion.php";
require_once "Libraries/Core/Mysql.php";

$conn = new Conexion();
$db = $conn->connect();
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "[$table]\n";
}
