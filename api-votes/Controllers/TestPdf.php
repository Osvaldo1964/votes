<?php

class TestPdf extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // 1. Cargar librerías manualmente
        require_once 'Libraries/fpdf/fpdf.php';
        // PHPQRCode suele tener un archivo qrlib.php principal
        include 'Libraries/phpqrcode/qrlib.php';

        // 2. Datos de prueba
        $nombre = "JUAN CARLOS PEREZ GOMEZ";
        $cedula = "1.020.304.506";
        $lugar = "INSTITUCION EDUCATIVA CENTRAL";
        $mesa = "05";
        $fecha = date("d/m/Y H:i:s");

        // 3. Generar QR Temporal
        $tempDir = sys_get_temp_dir();
        $codeContents = "CERTIFICADO DE VOTACION\nCC: $cedula\nNombre: $nombre\nMesa: $mesa\nFecha: $fecha\nValidado: SI";
        $qrFile = $tempDir . '/qr_' . md5($cedula) . '.png';

        QRcode::png($codeContents, $qrFile, QR_ECLEVEL_L, 3);

        // 4. Crear PDF con FPDF
        // Tamaño personalizado tipo "recibo" un poco mas alto para que quepa todo en una pagina sin salto auto
        $pdf = new FPDF('P', 'mm', array(100, 160));
        $pdf->SetAutoPageBreak(false); // Evitar salto de pagina automatico
        $pdf->AddPage();

        // Borde decorativo
        $pdf->SetDrawColor(0, 50, 100);
        $pdf->SetLineWidth(1);
        $pdf->Rect(5, 5, 90, 150);

        // LOGO
        // Ajusta la ruta si es necesario. Como estamos en api-votes/Controllers, salir a raiz y entrar a app-votes
        $logoPath = '../app-votes/Assets/images/logo_chadan.jpg';
        if (file_exists($logoPath)) {
            // x, y, w
            $pdf->Image($logoPath, 35, 10, 30);
            $pdf->Ln(25); // Espacio despues del logo
        } else {
            $pdf->Ln(10);
        }

        // Título
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('CERTIFICADO DE VOTACION'), 0, 1, 'C');
        $pdf->Ln(2);

        // Datos
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, utf8_decode('Cédula:'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, $cedula, 0, 1, 'C');
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

        // Insertar QR
        $pdf->Image($qrFile, 30, $pdf->GetY(), 40, 40);

        // Pie de pagina
        $pdf->SetY(145);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->Cell(0, 5, utf8_decode('Generado el: ' . $fecha), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Sistema de Votación 2026'), 0, 1, 'C');

        // Output clean
        // F = guardar local, I = mostrar en navegador, D = descargar
        $pdf->Output('I', 'certificado.pdf');

        // Limpieza
        unlink($qrFile);
    }
}
