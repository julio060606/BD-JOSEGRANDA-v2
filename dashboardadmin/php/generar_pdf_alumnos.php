<?php
require('fpdf.php');        // Asegúrate de tener FPDF
include("../../conexion/conexion.php");    // Debe exponer $conn (PostgreSQL)

// ------------ utilidades ------------
function es($txt) {
    // FPDF (Core) trabaja en ISO-8859-1; convertimos desde UTF-8
    return utf8_decode((string)$txt);
}
function fecha_larga($isoDate) {
    if (!$isoDate) return '';
    // Formato: 26 de septiembre de 2025
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $t = strtotime($isoDate);
    if ($t === false) return $isoDate;
    $d = (int)date('j', $t);
    $m = $meses[(int)date('n', $t)-1];
    $y = date('Y', $t);
    return "$d de $m de $y";
}

// ------------ validar input ------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID del alumno no especificado');
}
$id_alumno = (int)$_GET['id'];

// ------------ consulta segura ------------
$sql = "SELECT a.id_alumno, a.nombres, a.apellidos, a.dni, a.fecha_nacimiento,
               p.nombres AS padre_nombres, p.apellidos AS padre_apellidos
        FROM alumno a
        LEFT JOIN padre p ON a.id_padre = p.id_padre
        WHERE a.id_alumno = $1";
$res = pg_query_params($conn, $sql, [$id_alumno]);
if (!$res) {
    die('Error en la consulta.');
}
$alumno = pg_fetch_assoc($res);
if (!$alumno) {
    die('Alumno no encontrado');
}
$nombreCompleto = strtoupper(trim(($alumno['nombres'] ?? '').' '.($alumno['apellidos'] ?? '')));

// ------------ clase PDF personalizada ------------
class PDF extends FPDF {
    function Header() {
        // Banner (opcional): si existe, se muestra arriba
        if (file_exists('banner_encabezado.png')) {
            // ancho de la página ~210mm; ajustamos alto del banner a 20mm
            $this->Image('banner_encabezado.png', 10, 10, 190, 0, 'PNG');
            $this->Ln(25);
        } else {
            // Logo + título si no hay banner
            if (file_exists('logo_colegio.png')) {
                $this->Image('logo_colegio.png', 10, 10, 20, 0, 'PNG');
            }
            $this->SetFont('Arial','B',14);
            $this->Cell(0,6, es('I.E.E. JOSÉ GRANDA'), 0, 1, 'R');
            $this->SetFont('Arial','',10);
            $this->SetTextColor(80,80,80);
            $this->Cell(0,5, es('Reporte del Alumno'), 0, 1, 'R');
            $this->Ln(5);
        }

        // Línea de separación
        $this->SetDrawColor(28,71,128);
        $this->SetLineWidth(0.6);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(6);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetDrawColor(230,230,230);
        $this->SetLineWidth(0.4);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->SetY(-16);

        $this->SetFont('Arial','I',8);
        $this->SetTextColor(120,120,120);
        $this->Cell(0,5, es('I.E.E. JOSÉ GRANDA • Sistema Académico'), 0, 1, 'C');
        $this->Cell(0,5, es('Página '.$this->PageNo().'/{nb}'), 0, 0, 'C');
    }

    // Bloque de título grande
    function Titulo($txt) {
        $this->SetFont('Arial','B',16);
        $this->SetTextColor(28,71,128);
        $this->Cell(0,10, es($txt), 0, 1, 'C');
        $this->Ln(2);
    }

    // Tabla bonita de dos columnas (etiqueta/valor)
    function InfoRow($label, $value, $fill=false) {
        $this->SetFont('Arial','',11);
        $this->SetDrawColor(220,220,220);
        if ($fill) $this->SetFillColor(246,249,255); else $this->SetFillColor(255,255,255);

        // etiqueta
        $this->SetTextColor(60,60,60);
        $this->Cell(50, 8, es($label), 1, 0, 'L', $fill);

        // valor
        $this->SetTextColor(20,20,20);
        $this->Cell(0, 8, es($value), 1, 1, 'L', $fill);
    }
}

// ------------ crear PDF ------------
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetTitle(es('Reporte del Alumno - '.$nombreCompleto));
$pdf->SetAuthor(es('I.E.E. José Granda'));
$pdf->SetSubject(es('Ficha del alumno'));

$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 22);

// Título central
$pdf->Titulo('Reporte del Alumno');

// Si hay logo, lo re-colocamos pequeño en esquina
if (file_exists('logo_colegio.png')) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Image('logo_colegio.png', 12, $y, 18, 0, 'PNG');
    $pdf->Ln(2);
}

// Subtítulo con nombre del alumno
$pdf->SetFont('Arial','B',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,8, es($nombreCompleto), 0, 1, 'L');
$pdf->Ln(2);

// Bloque de datos
$padre = trim(($alumno['padre_nombres'] ?? '').' '.($alumno['padre_apellidos'] ?? ''));
if ($padre === '') $padre = '—';

$fechaNac = fecha_larga($alumno['fecha_nacimiento'] ?? '');

$pdf->InfoRow('ID:',             $alumno['id_alumno'], false);
$pdf->InfoRow('Nombres:',        $alumno['nombres'],   true);
$pdf->InfoRow('Apellidos:',      $alumno['apellidos'], false);
$pdf->InfoRow('DNI:',            $alumno['dni'],       true);
$pdf->InfoRow('Fecha Nac.:',     $fechaNac,            false);
$pdf->InfoRow('Padre/Madre:',    $padre,               true);

// Nota final / sello de fecha
$pdf->Ln(8);
$pdf->SetFont('Arial','I',9);
$pdf->SetTextColor(100,100,100);
$pdf->MultiCell(0,6, es('Este documento es una constancia informativa generada por el sistema académico. '
    .'Para uso institucional.'));

$pdf->Ln(2);
$pdf->SetTextColor(28,71,128);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,6, es('Generado el '.fecha_larga(date('Y-m-d'))), 0, 1, 'R');

// Espacio para firma
$pdf->Ln(20);
$pdf->Cell(0, 10, es('Firma del Responsable'), 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, '____________________________', 0, 1, 'C');
$pdf->Cell(0, 10, 'Nombre y Firma', 0, 1, 'C');

// ---------- Registrar actividad ----------
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
if ($id_usuario) {
    $desc = "Generó PDF del alumno {$nombreCompleto} (ID: {$id_alumno})";
    $sql_act = "INSERT INTO historial_actividad (id_usuario, descripcion) VALUES ($1, $2)";
    pg_query_params($conn, $sql_act, [$id_usuario, $desc]);
}


// Descargar automáticamente
$filename = 'reporte_alumno_'.$id_alumno.'.pdf';
$pdf->Output('D', $filename);
?>
