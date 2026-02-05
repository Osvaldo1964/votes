<?php
$csvPath = 'act_votos.csv';
$handle = fopen($csvPath, "r");
$headers = fgetcsv($handle, 1000, ";");

$zones = [];

while (($col = fgetcsv($handle, 1000, ";")) !== FALSE) {
    if (count($col) < 12) continue;

    $muniName = strtoupper(trim(mb_convert_encoding($col[5], 'UTF-8', 'Windows-1252')));
    if ($muniName === 'SANTA MARTA') {
        $zz = trim($col[2]);
        $comunaName = trim(mb_convert_encoding($col[11], 'UTF-8', 'Windows-1252'));

        if (empty($comunaName)) {
            $comunaName = "ZONA " . $zz;
        }

        $key = $zz . " | " . $comunaName;
        if (!isset($zones[$key])) {
            $zones[$key] = 0;
        }
        $zones[$key]++;
    }
}
fclose($handle);

ksort($zones);
$output = "Zonas encontradas para SANTA MARTA en CSV:\n";
foreach ($zones as $key => $count) {
    $output .= "$key (Filas: $count)\n";
}

$output .= "\nTotal zonas unicas (ZZ + Comuna): " . count($zones) . "\n";
file_put_contents('analyze_results.txt', $output);
