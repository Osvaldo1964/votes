<?php
// Script de importación de puestos de votación (Versión Completa)
// Ejecutar desde la raíz del proyecto: php import_script.php

// 1. Cargar Configuración
require_once 'api-votes/Config/Config.php';

// 2. Conexión a Base de Datos
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos.\n";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}

// 3. Limpiar Tablas (Orden Importante por claves foráneas)
echo "Limpiando tablas antiguas...\n";
$pdo->exec("DELETE FROM mesas"); // O TRUNCATE si se desactivan FKs
$pdo->exec("ALTER TABLE mesas AUTO_INCREMENT = 1");
$pdo->exec("DELETE FROM puestos");
$pdo->exec("ALTER TABLE puestos AUTO_INCREMENT = 1");
$pdo->exec("DELETE FROM zones");
$pdo->exec("ALTER TABLE zones AUTO_INCREMENT = 1");
echo "Tablas limpiadas.\n";

// 4. Cargar Mapeo de Municipios desde Config.json
$jsonPath = 'api-votes/Json/Config.json';
if (!file_exists($jsonPath)) {
    die("Error: No se encuentra $jsonPath\n");
}

$jsonContent = file_get_contents($jsonPath);
$configData = json_decode($jsonContent, true);
$munisMap = []; // NombreMuni -> ['id' => idmuni, 'dpto' => id_dpto_muni]

foreach ($configData['munis'] as $muni) {
    $name = strtoupper(trim($muni['namemuni']));
    $munisMap[$name] = [
        'id' => $muni['idmuni'],
        'dpto' => $muni['dptomuni']
    ];
}
echo "Municipios cargados: " . count($munisMap) . "\n";

// 5. Leer CSV Completo
$csvPath = 'act_votos.csv';
if (!file_exists($csvPath)) {
    die("Error: No se encuentra el archivo $csvPath\n");
}
echo "Leyendo datos desde: $csvPath\n";

$handle = fopen($csvPath, "r");
$headers = fgetcsv($handle, 1000, ";"); // Leer encabezados

// Contadores
$newZones = 0;
$newPuestos = 0;
$newMesas = 0;
$rowNum = 0;

// Cache simple para evitar selects repetitivos
$zonesCache = []; // "MuniID_ZoneName" -> ZoneID

while (($col = fgetcsv($handle, 1000, ";")) !== FALSE) {
    $rowNum++;

    // Mapeo (indices basados en CSV original)
    // 0:dd, 1:mm, 2:zz, 3:pp, 4:departamento, 5:municipio, 6:puesto, 
    // 7:mujeres, 8:hombres, 9:total, 10:mesas, 11:comuna, 12:dirección

    // Fix Encoding (Excel CSV is usually Windows-1252)
    $muniName = mb_convert_encoding(trim($col[5]), 'UTF-8', 'Windows-1252'); // was Latin1
    $muniName = strtoupper($muniName);

    $puestoCode = trim($col[3]);
    $puestoName = mb_convert_encoding(trim($col[6]), 'UTF-8', 'Windows-1252');
    $mesasCount = intval($col[10]);
    $comunaName = mb_convert_encoding(trim($col[11]), 'UTF-8', 'Windows-1252');

    // ...

    // Validar Municipio
    if (!isset($munisMap[$muniName])) {
        continue;
    }
    $muniData = $munisMap[$muniName];
    $muniID = $muniData['id'];
    $dptoID = $muniData['dpto'];

    // Gestionar Zona
    if (empty($comunaName)) {
        $comunaName = "ZONA " . trim($col[2]);
    }

    $zoneKey = $muniID . "_" . $comunaName;

    if (isset($zonesCache[$zoneKey])) {
        $zoneID = $zonesCache[$zoneKey];
    } else {
        $stmtZone = $pdo->prepare("SELECT `id_zone` FROM `zones` WHERE `name_zone` = ? AND `muni_zone` = ?");
        try {
            $stmtZone->execute([$comunaName, $muniID]);
        } catch (PDOException $e) {
            echo "Error SELECT Zone: " . $e->getMessage() . " Params: $comunaName, $muniID\n";
            exit;
        }
        $zoneRow = $stmtZone->fetch(PDO::FETCH_ASSOC);

        if ($zoneRow) {
            $zoneID = $zoneRow['id_zone'];
        } else {
            $stmtInsertZone = $pdo->prepare("INSERT INTO zones (name_zone, muni_zone, dpto_zone, codigo_zona, status_zone, created_zone) VALUES (?, ?, ?, ?, ?, NOW())");
            $defaultCodigo = ""; // No tenemos codigo en CSV
            $defaultStatus = 1;
            try {
                $stmtInsertZone->execute([$comunaName, $muniID, $dptoID, $defaultCodigo, $defaultStatus]);
                $zoneID = $pdo->lastInsertId();
                $newZones++;
            } catch (PDOException $e) {
                echo "\nError insertando Zona '$comunaName' (Muni: $muniID, Dpto: $dptoID): " . $e->getMessage() . "\n";
                // Optional: continue or exit? Let's exit to see the first error.
                exit;
            }
        }
        $zonesCache[$zoneKey] = $zoneID;
    }

    // Gestionar Puesto (puestos)
    // Al haber limpiado tablas, siempre insertaremos si no lo hemos procesado en este script.
    // Verificamos por si el CSV repite codigo de puesto (no deberia para insertar, pero si para mesas)

    $stmtPuesto = $pdo->prepare("SELECT id_puesto FROM puestos WHERE num_puesto = ? AND idzona_puesto = ?");
    $stmtPuesto->execute([$puestoCode, $zoneID]);
    $puestoRow = $stmtPuesto->fetch(PDO::FETCH_ASSOC);

    if ($puestoRow) {
        $puestoID = $puestoRow['id_puesto'];
    } else {
        $stmtInsertPuesto = $pdo->prepare("INSERT INTO puestos (nombre_puesto, num_puesto, idzona_puesto) VALUES (?, ?, ?)");
        $stmtInsertPuesto->execute([$puestoName, $puestoCode, $zoneID]);
        $puestoID = $pdo->lastInsertId();
        $newPuestos++;
    }

    // Gestionar Mesas (mesas) - Crear 1 a N
    // Borramos existentes para re-crear? Ya borramos todo al inicio.
    // Solo necesitamos saber si ya insertamos las mesas de este puesto en una vuelta anterior del loop
    // (si el CSV tuviera filas duplicadas por puesto).
    // Asumiremos que cada fila es un puesto único.
    // PERO: Si el codigo del puesto pasa verificacion, puede que ya tenga mesas.
    // Contamos actuales para saber desde donde arrancar si fuera update, pero aqui es clean insert.

    // Verificamos cuantas mesas tiene actualmente (por si acaso)
    $stmtCountMesas = $pdo->prepare("SELECT COUNT(*) FROM mesas WHERE id_puesto_mesa = ?");
    $stmtCountMesas->execute([$puestoID]);
    $currentMesas = $stmtCountMesas->fetchColumn();

    if ($currentMesas < $mesasCount) {
        $stmtInsertMesa = $pdo->prepare("INSERT INTO mesas (numero_mesa, id_puesto_mesa, estado_mesa) VALUES (?, ?, 1)");
        for ($i = $currentMesas + 1; $i <= $mesasCount; $i++) {
            $mesaNum = str_pad($i, 2, "0", STR_PAD_LEFT);
            $stmtInsertMesa->execute([$mesaNum, $puestoID]);
            $newMesas++;
        }
    }
}

fclose($handle);

echo "\n--- Resumen de Importación Completa ---\n";
echo "Filas Procesadas: $rowNum\n";
echo "Zonas Creadas: $newZones\n";
echo "Puestos Creados: $newPuestos\n";
echo "Mesas Creadas: $newMesas\n";
?>