<?php
require_once "Config/Config.php";
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$pdo = new PDO($dsn, DB_USER, DB_PASSWORD);

$stmt = $pdo->query("SELECT * FROM zones WHERE muni_zone = 569");
$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($zones, JSON_PRETTY_PRINT);
