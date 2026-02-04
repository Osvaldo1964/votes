<?php
require_once 'api-votes/Config/Config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Columns in `zones` table:\n";
    $stmt = $pdo->query("DESCRIBE zones");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 6) . str_pad("Default", 10) . "\n";
    foreach ($columns as $col) {
        echo str_pad($col['Field'], 20) . str_pad($col['Type'], 20) . str_pad($col['Null'], 6) . str_pad($col['Default'] ?? 'N/A', 10) . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>