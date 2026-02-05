<?php
require_once "Config/Config.php";
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$pdo = new PDO($dsn, DB_USER, DB_PASSWORD);

$count = $pdo->query("SELECT COUNT(*) FROM bodyresultado")->fetchColumn();
echo "BodyResultado COUNT: $count\n";

$countMesas = $pdo->query("SELECT COUNT(*) FROM mesas")->fetchColumn();
echo "Mesas COUNT: $countMesas\n";

$countZones = $pdo->query("SELECT COUNT(*) FROM zones WHERE muni_zone = 569")->fetchColumn();
echo "Santa Marta Zones COUNT: $countZones\n";
