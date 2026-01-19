<?php

class Publico extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        // NOTA: No llamamos a fntAuthorization() aquí porque este controlador es PÚBLICO.
    }

    public function consultarPuesto($cedula)
    {
        if (empty($cedula)) {
            $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            jsonResponse($arrResponse, 200);
            die();
        }

        $strCedula = strClean($cedula);
        $arrData = $this->model->selectConsultaPublica($strCedula);

        if (empty($arrData) || !is_array($arrData)) {
            $arrResponse = array('status' => false, 'msg' => 'Cédula no encontrada en el censo.');
        } else {
            // Manejo de array de resultados (legacy behavior)
            $voterData = isset($arrData[0]) ? $arrData[0] : $arrData;

            $dataResponse = array(
                'cedula' => $voterData['identificacion'] ?? '',
                'nombre' => $voterData['nombres'] ?? '', // Ahora viene concatenado desde el modelo
                'departamento' => $voterData['departamento'] ?? 'Magdalena',
                'municipio' => $voterData['municipio'] ?? 'Santa Marta',
                'puesto' => $voterData['puesto'] ?? 'No asignado',
                'mesa' => $voterData['mesa'] ?? '00',
                'direccion' => $voterData['direccion_puesto'] ?? ''
            );
            $arrResponse = array('status' => true, 'data' => $dataResponse);
        }

        jsonResponse($arrResponse, 200);
    }

    public function registrarVoto()
    {
        // Soporte para POST (json o form-data) y GET básico
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            $cedula = $input['cedula'] ?? $_POST['cedula'] ?? '';
            // Nuevos parámetros Geolocalización
            $lat = $input['lat'] ?? $_POST['lat'] ?? '';
            $lon = $input['lon'] ?? $_POST['lon'] ?? '';
        } else {
            $cedula = $_GET['cedula'] ?? '';
            $lat = '';
            $lon = '';
        }

        if (empty($cedula)) {
            $arrResponse = array('status' => false, 'msg' => 'Se requiere el número de cédula.');
            jsonResponse($arrResponse, 200);
            die();
        }

        $strCedula = strClean($cedula);
        $result = $this->model->updateVotoPublico($strCedula, $lat, $lon);

        if ($result == "ok") {
            $arrResponse = array('status' => true, 'msg' => '¡Voto registrado exitosamente!');
        } elseif ($result == "already_voted") {
            $arrResponse = array('status' => false, 'msg' => 'Esta cédula ya registra un voto.');
        } elseif ($result == "not_found") {
            $arrResponse = array('status' => false, 'msg' => 'Cédula no encontrada en la base de datos de electores.');
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Error al registrar el voto.');
        }

        jsonResponse($arrResponse, 200);
    }
    public function getCertificado($cedula)
    {
        if (empty($cedula)) {
            die("Cédula requerida.");
        }

        $strCedula = strClean($cedula);

        // 1. Obtener Datos
        $arrData = $this->model->selectConsultaPublica($strCedula);

        if (empty($arrData)) {
            die("Datos no encontrados para esta cédula.");
        }

        // Si vienes de model->selectConsultaPublica, puede que sea un array de array
        $voterData = isset($arrData[0]) ? $arrData[0] : $arrData;

        // Validar si ya votó (opcional, pero recomendado)
        // En este punto asumimos que si pide certificado es porque acaba de votar o ya votó.

        // 2. Preparar Librerías
        require_once 'Libraries/fpdf/fpdf.php';
        require_once 'Libraries/phpqrcode/qrlib.php';

        $nombre = mb_convert_case($voterData['nombres'], MB_CASE_UPPER, "UTF-8");
        $lugar = mb_convert_case($voterData['puesto'], MB_CASE_UPPER, "UTF-8");
        $mesa = $voterData['mesa'];
        $fecha = date("d/m/Y H:i A"); // Fecha actual de generación

        // 3. Generar QR
        $tempDir = sys_get_temp_dir();
        // Contenido del QR: URL validación (cuando exista) o JSON datos
        $codeContents = "CERTIFICADO DE VOTACION\nCC: $strCedula\nNombre: $nombre\nMesa: $mesa\nFecha: $fecha\nValidado: SI";
        $qrFile = $tempDir . '/qr_' . md5($strCedula) . '.png';
        QRcode::png($codeContents, $qrFile, QR_ECLEVEL_L, 3);

        // 4. Crear PDF
        $pdf = new FPDF('P', 'mm', array(100, 160));
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // Borde
        $pdf->SetDrawColor(0, 50, 100);
        $pdf->SetLineWidth(1);
        $pdf->Rect(5, 5, 90, 150);

        // LOGO EMBEBIDO (Solución definitiva para producción)
        // Intentamos cargar desde archivo generado o fallback
        $logoDataFile = 'Libraries/LogoData.php';
        if (file_exists($logoDataFile)) {
            require_once $logoDataFile;
            if (isset($logoB64)) {
                $logoData = base64_decode($logoB64);
                $tempLogo = $tempDir . '/logo_chadan_temp.jpg';
                file_put_contents($tempLogo, $logoData);
                $pdf->Image($tempLogo, 35, 10, 30);
                // Limpieza opcional inmediata o por cron
                // unlink($tempLogo);
            }
        } else {
            // Fallback local
            $logoPath = '../app-votes/Assets/images/logo_chadan.jpg';
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 35, 10, 30);
            }
        }
        $pdf->Ln(25);

        // Título
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('CERTIFICADO DE VOTACION'), 0, 1, 'C');
        $pdf->Ln(2);

        // Datos
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, utf8_decode('Cédula:'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, $strCedula, 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, utf8_decode('Elector:'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(0, 5, utf8_decode($nombre), 0, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, utf8_decode('Lugar de Votación:'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->MultiCell(0, 5, utf8_decode($lugar), 0, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, utf8_decode('Mesa:'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, $mesa, 0, 1, 'C');
        $pdf->Ln(2);

        // QR
        $pdf->Image($qrFile, 30, $pdf->GetY(), 40, 40);

        // Pie
        $pdf->SetY(145);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->Cell(0, 5, utf8_decode('Generado el: ' . $fecha), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Sistema de Votación 2026'), 0, 1, 'C');

        // Output
        // 'D' fuerza la descarga, lo cual suele disparar la pregunta "¿Desea descargar?" en móviles.
        $pdf->Output('D', 'certificado_' . $strCedula . '.pdf');

        // Clean
        unlink($qrFile);
    }
}
