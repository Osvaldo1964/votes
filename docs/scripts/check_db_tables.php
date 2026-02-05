<?php
require_once "Config/Config.php";
require_once "Libraries/Core/Conexion.php";
require_once "Libraries/Core/Mysql.php";

$conn = new Conexion();
$db = $conn->connect();
$stmt = $db->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . "\n";
}
