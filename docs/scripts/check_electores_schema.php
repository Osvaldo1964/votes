<?php
require_once "Config/Config.php";
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$pdo = new PDO($dsn, DB_USER, DB_PASSWORD);

echo "--- TABLE: electores ---\n";
$stmt = $pdo->query("DESCRIBE electores");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
