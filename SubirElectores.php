<?php
/**
 * SubirElectores.php
 * Importación masiva de Líderes y Electores desde CSV
 *
 * Estructura del CSV (separador: ;):
 * [0]  CED_LIDER       [1]  NOM1_LIDER     [2]  NOM2_LIDER    [3]  APE1_LIDER
 * [4]  APE2_LIDER      [5]  TEL_LIDER      [6]  EMAIL_LIDER   [7]  DPTO_LIDER
 * [8]  MUNI_LIDER      [9]  DIRECCION_LIDER
 * [10] CED_ELECTOR     [11] NOM1_ELECTOR   [12] NOM2_ELECTOR  [13] APE1_ELECTOR
 * [14] APE2_ELECTOR    [15] SEXO_ELECTOR   [16] TEL_ELECTOR   [17] EMAIL_ELECTOR
 * [18] DIR_ELECTOR     [19] COD_DEP (ignorado) [20] COD_MUNI (ignorado)
 * [21] NUMPUESTO       [22] MESA
 *
 * Reglas:
 * - Si CED_ELECTOR vacía → descarta toda la fila (no inserta ni líder ni elector)
 * - Si NUMPUESTO o MESA vacíos → inserta elector sin registro en places (se edita luego)
 * - Si COD_MUNI vacío → usa MUNI_LIDER para el elector
 * - Líderes duplicados (misma CED_LIDER) → se ignoran (INSERT IGNORE)
 * - Electores duplicados (mismo CED_ELECTOR) → se ignoran (INSERT IGNORE)
 */

header('Content-Type: text/html; charset=utf-8');

// ─── Configuración ────────────────────────────────────────────────────────────
$host = 'localhost';
$db = 'db-votes';
$user = 'root';
$pass = '';
$csvFile = __DIR__ . '/cargar_elector.csv';
$sep = ';';
// ──────────────────────────────────────────────────────────────────────────────

// Contadores
$stats = [
    'filas_leidas' => 0,
    'descartadas_sin_ced' => 0,
    'lideres_insertados' => 0,
    'lideres_duplicados' => 0,
    'electores_insertados' => 0,
    'electores_duplicados' => 0,
    'places_insertados' => 0,
    'places_sin_puesto' => 0,
    'places_mesa_no_encontrada' => 0,
    'errores' => [],
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8mb4");
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// ─── Prepared Statements ──────────────────────────────────────────────────────

// Verificar si lider ya existe
$stmtCheckLider = $pdo->prepare("SELECT id_lider FROM lideres WHERE ident_lider = ? LIMIT 1");

// Insertar líder
$stmtInsLider = $pdo->prepare("
    INSERT IGNORE INTO lideres
        (ident_lider, nom1_lider, nom2_lider, ape1_lider, ape2_lider,
         telefono_lider, email_lider, dpto_lider, muni_lider, direccion_lider, estado_lider)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
");

// Verificar si elector ya existe
$stmtCheckElector = $pdo->prepare("SELECT id_elector FROM electores WHERE ident_elector = ? LIMIT 1");

// Insertar elector (zona_elector y barrio_elector en 0 si no hay puesto)
$stmtInsElector = $pdo->prepare("
    INSERT IGNORE INTO electores
        (ident_elector, nom1_elector, nom2_elector, ape1_elector, ape2_elector,
         sexo_elector, telefono_elector, email_elector, direccion_elector,
         lider_elector, dpto_elector, muni_elector, zona_elector, barrio_elector,
         insc_elector, poll_elector, long_elector, lati_elector, estado_elector)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, 1)
");

// Buscar id_mesa dado num_puesto y numero_mesa
$stmtBuscaMesa = $pdo->prepare("
    SELECT m.id_mesa, p.idzona_puesto
    FROM mesas m
    INNER JOIN puestos p ON m.id_puesto_mesa = p.id_puesto
    WHERE p.id_puesto = ?
      AND m.numero_mesa = ?
    LIMIT 1
");

// Insertar en places
$stmtInsPlace = $pdo->prepare("
    INSERT IGNORE INTO places
        (ident_place, ape1_place, ape2_place, nom1_place, nom2_place, id_mesa_new)
    VALUES (?, ?, ?, ?, ?, ?)
");

// ─── Procesamiento del CSV ────────────────────────────────────────────────────

if (!file_exists($csvFile)) {
    die("Error: No se encontró el archivo $csvFile");
}

$handle = fopen($csvFile, 'r');
// Detectar BOM UTF-8 y saltar encabezado
$firstLine = fgets($handle);
rewind($handle);
// Si tiene BOM, lo quitamos
if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
    fread($handle, 3);
}
// Saltar encabezado
fgetcsv($handle, 0, $sep);

$pdo->beginTransaction();
$batchSize = 500;
$batchCount = 0;

while (($row = fgetcsv($handle, 0, $sep)) !== false) {
    $stats['filas_leidas']++;

    // Normalizar: limpiar espacios
    $row = array_map('trim', $row);

    // Mapear columnas
    $cedLider = $row[0] ?? '';
    $nom1Lider = mb_strtoupper($row[1] ?? '', 'UTF-8');
    $nom2Lider = mb_strtoupper($row[2] ?? '', 'UTF-8');
    $ape1Lider = mb_strtoupper($row[3] ?? '', 'UTF-8');
    $ape2Lider = mb_strtoupper($row[4] ?? '', 'UTF-8');
    $telLider = $row[5] ?? '';
    $emailLider = $row[6] ?? '';
    $dptoLider = intval($row[7] ?? 0);
    $muniLider = intval($row[8] ?? 0);
    $dirLider = $row[9] ?? '';

    $cedElector = $row[10] ?? '';
    $nom1Elector = mb_strtoupper($row[11] ?? '', 'UTF-8');
    $nom2Elector = mb_strtoupper($row[12] ?? '', 'UTF-8');
    $ape1Elector = mb_strtoupper($row[13] ?? '', 'UTF-8');
    $ape2Elector = mb_strtoupper($row[14] ?? '', 'UTF-8');
    $sexoElector = mb_strtoupper($row[15] ?? '', 'UTF-8');
    $telElector = substr($row[16] ?? '', 0, 10);
    $emailElector = $row[17] ?? '';
    $dirElector = $row[18] ?? '';
    // $row[19] COD_DEP  → ignorado
    // $row[20] COD_MUNI → ignorado
    $numPuesto = $row[21] ?? '';
    $numMesa = ($row[22] !== '' && $row[22] !== null)
        ? str_pad(intval($row[22]), 2, '0', STR_PAD_LEFT)
        : '';

    // ── Regla 1: descartar fila si CED_ELECTOR está vacía ──
    if ($cedElector === '') {
        $stats['descartadas_sin_ced']++;
        continue;
    }

    try {
        // ── Paso 1: Insertar/obtener Líder ──────────────────────────────────
        $idLider = null;

        if ($cedLider !== '') {
            $stmtCheckLider->execute([$cedLider]);
            $liderExistente = $stmtCheckLider->fetchColumn();

            if ($liderExistente) {
                $idLider = $liderExistente;
                $stats['lideres_duplicados']++;
            } else {
                $stmtInsLider->execute([
                    $cedLider,
                    $nom1Lider,
                    $nom2Lider,
                    $ape1Lider,
                    $ape2Lider,
                    $telLider,
                    $emailLider,
                    $dptoLider,
                    $muniLider,
                    $dirLider
                ]);
                $idLider = $pdo->lastInsertId();
                $stats['lideres_insertados']++;
            }
        }

        // ── Paso 2: Insertar/obtener Elector ────────────────────────────────
        $stmtCheckElector->execute([$cedElector]);
        $electorExistente = $stmtCheckElector->fetchColumn();

        if ($electorExistente) {
            $stats['electores_duplicados']++;
            // Aun si el elector existe, verificar si tiene place
            $idElector = $electorExistente;
        } else {
            // zona_elector la obtenemos del puesto si existe, sino 0
            $zonaElector = 0;

            $stmtInsElector->execute([
                $cedElector,
                $nom1Elector,
                $nom2Elector,
                $ape1Elector,
                $ape2Elector,
                $sexoElector,
                $telElector,
                $emailElector,
                $dirElector,
                $idLider ?? 0,
                $dptoLider,  // reutiliza dpto del líder
                $muniLider,  // reutiliza muni del líder
                $zonaElector
            ]);
            $idElector = $pdo->lastInsertId();
            $stats['electores_insertados']++;
        }

        // ── Paso 3: Insertar en places (si hay NUMPUESTO y MESA) ────────────
        if ($numPuesto === '' || $numMesa === '') {
            $stats['places_sin_puesto']++;
            // No insertamos en places, se completará manualmente
        } else {
            // Buscar id_mesa
            $stmtBuscaMesa->execute([$numPuesto, $numMesa]);
            $mesaData = $stmtBuscaMesa->fetch(PDO::FETCH_ASSOC);

            if (!$mesaData) {
                $stats['places_mesa_no_encontrada']++;
                $stats['errores'][] = "Fila {$stats['filas_leidas']}: Mesa no encontrada para puesto=$numPuesto, mesa=$numMesa (elector: $cedElector)";
            } else {
                // Verificar si ya existe en places
                $existePlace = $pdo->prepare("SELECT id_place FROM places WHERE ident_place = ? LIMIT 1");
                $existePlace->execute([$cedElector]);
                if (!$existePlace->fetchColumn()) {
                    $stmtInsPlace->execute([
                        $cedElector,
                        $ape1Elector,
                        $ape2Elector,
                        $nom1Elector,
                        $nom2Elector,
                        $mesaData['id_mesa']
                    ]);
                    $stats['places_insertados']++;

                    // Actualizar zona_elector en electores con la zona del puesto
                    if (!empty($mesaData['idzona_puesto'])) {
                        $pdo->prepare("UPDATE electores SET zona_elector = ? WHERE ident_elector = ?")
                            ->execute([$mesaData['idzona_puesto'], $cedElector]);
                    }
                }
            }
        }

    } catch (Exception $e) {
        $stats['errores'][] = "Fila {$stats['filas_leidas']}: " . $e->getMessage();
    }

    // Commit por lotes para no sobrecargar memoria
    $batchCount++;
    if ($batchCount >= $batchSize) {
        $pdo->commit();
        $pdo->beginTransaction();
        $batchCount = 0;
    }
}

// Commit final
$pdo->commit();
fclose($handle);

// ─── Reporte HTML ─────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultado de Importación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f4f4;
        }

        h1 {
            color: #2c3e50;
        }

        .box {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        .ok {
            color: #27ae60;
            font-weight: bold;
        }

        .warn {
            color: #e67e22;
            font-weight: bold;
        }

        .err {
            color: #e74c3c;
            font-weight: bold;
        }

        .error-list {
            background: #ffeaea;
            border-left: 4px solid #e74c3c;
            padding: 12px;
            margin-top: 10px;
            font-size: 13px;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <h1>📊 Resultado de Importación Masiva</h1>

    <div class="box">
        <h2>Resumen</h2>
        <table>
            <tr>
                <th>Concepto</th>
                <th>Cantidad</th>
            </tr>
            <tr>
                <td>Filas leídas del CSV</td>
                <td>
                    <?= $stats['filas_leidas'] ?>
                </td>
            </tr>
            <tr>
                <td>Descartadas (sin cédula elector)</td>
                <td class="warn">
                    <?= $stats['descartadas_sin_ced'] ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Líderes</strong></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Insertados</td>
                <td class="ok">
                    <?= $stats['lideres_insertados'] ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Ya existían (duplicados, omitidos)</td>
                <td class="warn">
                    <?= $stats['lideres_duplicados'] ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Electores</strong></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Insertados</td>
                <td class="ok">
                    <?= $stats['electores_insertados'] ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Ya existían (duplicados, omitidos)</td>
                <td class="warn">
                    <?= $stats['electores_duplicados'] ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Places (asignación de mesa)</strong></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Insertados correctamente</td>
                <td class="ok">
                    <?= $stats['places_insertados'] ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Sin puesto/mesa en CSV (se editarán manualmente)</td>
                <td class="warn">
                    <?= $stats['places_sin_puesto'] ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;Puesto/Mesa no encontrado en BD</td>
                <td class="err">
                    <?= $stats['places_mesa_no_encontrada'] ?>
                </td>
            </tr>
            <tr>
                <td><strong>Errores totales</strong></td>
                <td class="err">
                    <?= count($stats['errores']) ?>
                </td>
            </tr>
        </table>
    </div>

    <?php if (!empty($stats['errores'])): ?>
        <div class="box">
            <h2>⚠️ Detalle de Errores</h2>
            <div class="error-list">
                <?php foreach ($stats['errores'] as $err): ?>
                    <p>
                        <?= htmlspecialchars($err) ?>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="box" style="background:#eafaf1;">
        <p class="ok">✅ Importación completada el
            <?= date('d/m/Y H:i:s') ?>
        </p>
    </div>
</body>

</html>