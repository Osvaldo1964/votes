<?php
// Script to generate migration SQL for puestos and mesas
// Usage: php generate_migration_sql.php

require_once 'api-votes/Config/Config.php';
// We need a simple connection, not the full framework
$host = DB_HOST;
$db = DB_NAME;
$user = DB_USER;
$pass = DB_PASSWORD;
$charset = DB_CHARSET;

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$outputFile = 'migration_payload.sql';
$handle = fopen($outputFile, 'w');

if (!$handle) {
    die("Cannot open file:  $outputFile");
}

fwrite($handle, "-- Migration script for Puestos and Mesas\n");
fwrite($handle, "-- Generated on " . date('Y-m-d H:i:s') . "\n\n");
fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

$tables = ['puestos', 'mesas'];

foreach ($tables as $table) {
    echo "Processing $table...\n";
    fwrite($handle, "-- Table: $table\n");
    fwrite($handle, "TRUNCATE TABLE `$table`;\n");

    $stmt = $pdo->query("SELECT * FROM `$table`");
    $rows = $stmt->fetchAll();

    if (count($rows) > 0) {
        $cols = array_keys($rows[0]);
        $colNames = implode("`, `", $cols);

        $batchSize = 100;
        $total = count($rows);
        $batches = ceil($total / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $offset = $i * $batchSize;
            $batch = array_slice($rows, $offset, $batchSize);

            $values = [];
            foreach ($batch as $row) {
                $rowValues = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $rowValues[] = "NULL";
                    } else {
                        $rowValues[] = $pdo->quote($val);
                    }
                }
                $values[] = "(" . implode(", ", $rowValues) . ")";
            }

            $sql = "INSERT INTO `$table` (`$colNames`) VALUES \n" . implode(",\n", $values) . ";\n";
            fwrite($handle, $sql);
        }
    }
    fwrite($handle, "\n");
}

fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
fwrite($handle, "-- End of script\n");
fclose($handle);

echo "Done! File $outputFile created.\n";
