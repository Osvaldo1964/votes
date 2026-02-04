<?php
require_once 'api-votes/Config/Config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    echo "Zones: " . $pdo->query('SELECT COUNT(*) FROM zones')->fetchColumn() . "\n";
    echo "Puestos: " . $pdo->query('SELECT COUNT(*) FROM puestos')->fetchColumn() . "\n";
    echo "Mesas: " . $pdo->query('SELECT COUNT(*) FROM mesas')->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>