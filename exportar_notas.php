<?php
session_start();

// Verificar si el usuario está logueado como profesor o estudiante
if (!isset($_SESSION['profesor_id']) && !isset($_SESSION['estudiante_id'])) {
    header('Location: login.php');
    exit();
}

// Incluir PhpSpreadsheet
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "gestion_notas";
$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$esProfesor = isset($_SESSION['profesor_id']);
$usuarioId = $esProfesor ? $_SESSION['profesor_id'] : $_SESSION['estudiante_id'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados de la hoja
$sheet->setCellValue('A1', 'Nombre Estudiante');
$sheet->setCellValue('B1', 'Apellido Estudiante');
$sheet->setCellValue('C1', 'Materia');
$sheet->setCellValue('D1', 'Periodo');
$sheet->setCellValue('E1', 'Nota');
$sheet->setCellValue('F1', 'Promedio Periodo');
$sheet->setCellValue('G1', 'Estado');

if ($esProfesor) {
    $sql = "
        SELECT 
            e.primer_nombre, 
            e.primer_apellido, 
            m.nombre AS materia, 
            p.nombre AS periodo, 
            dn.nota
        FROM 
            detalle_notas dn
        JOIN 
            matriculadosmaterias em ON dn.id_matriculados = em.id
        JOIN 
            Estudiantes e ON em.id_estudiante = e.id
        JOIN 
            materias m ON em.id_materia = m.id
        JOIN 
            periodos p ON dn.id_periodo = p.id
        WHERE 
            em.id_profesor = ?
        ORDER BY 
            e.primer_nombre, e.primer_apellido, m.nombre, p.nombre";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['profesor_id']);
} else {
    $sql = "
        SELECT 
            e.primer_nombre, 
            e.primer_apellido, 
            m.nombre AS materia, 
            p.nombre AS periodo, 
            dn.nota
        FROM 
            detalle_notas dn
        JOIN 
            matriculadosmaterias em ON dn.id_matriculados = em.id
        JOIN 
            Estudiantes e ON em.id_estudiante = e.id
        JOIN 
            materias m ON em.id_materia = m.id
        JOIN 
            periodos p ON dn.id_periodo = p.id
        WHERE 
            e.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['estudiante_id']);
}

$stmt->execute();
$result = $stmt->get_result();

$currentMateria = "";
$currentPeriodo = "";
$sumNotas = 0;
$countNotas = 0;
$row = 2;

while ($data = $result->fetch_assoc()) {
    // Escribir las notas por periodo y materia
    if (($currentMateria !== $data['materia'] || $currentPeriodo !== $data['periodo']) && $currentMateria !== "") {
        $promedio = round($sumNotas / $countNotas, 2);
        $estado = ($promedio >= 3.5) ? 'Aprobado' : 'Reprobado';
        $sheet->setCellValue('F' . $row, $promedio);
        $sheet->setCellValue('G' . $row, $estado);
        $row++;
        
        // Reiniciar acumuladores para el nuevo periodo
        $sumNotas = 0;
        $countNotas = 0;
    }

    // Rellenar cada fila con las notas
    $sheet->setCellValue('A' . $row, $data['primer_nombre']);
    $sheet->setCellValue('B' . $row, $data['primer_apellido']);
    $sheet->setCellValue('C' . $row, $data['materia']);
    $sheet->setCellValue('D' . $row, $data['periodo']);
    $sheet->setCellValue('E' . $row, $data['nota']);

    $sumNotas += $data['nota'];
    $countNotas++;
    $currentMateria = $data['materia'];
    $currentPeriodo = $data['periodo'];
    $row++;
}

// Calcular y agregar el promedio del último grupo de datos
if ($countNotas > 0) {
    $promedio = round($sumNotas / $countNotas, 2);
    $estado = ($promedio >= 3.5) ? 'Aprobado' : 'Reprobado';
    $sheet->setCellValue('F' . $row, $promedio);
    $sheet->setCellValue('G' . $row, $estado);
}

try {
    $writer = new Xlsx($spreadsheet);
    $filename = 'notas_' . ($esProfesor ? 'profesor' : 'estudiante') . '_' . date('Y-m-d_H-i') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Expires: 0');
    ob_clean();
    flush();
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    echo 'Error al generar el archivo Excel: ', $e->getMessage();
}

$conn->close();
?>
