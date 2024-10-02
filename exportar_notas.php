<?php
session_start();

// Verificar si el usuario está logueado como profesor o estudiante
if (!isset($_SESSION['profesor_id']) && !isset($_SESSION['estudiante_id'])) {
    header('Location: login.php'); // Redirigir al login si no está logueado
    exit();
}

// Incluir PhpSpreadsheet
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "gestion_notas";
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el rol del usuario
$esProfesor = isset($_SESSION['profesor_id']);
$usuarioId = $esProfesor ? $_SESSION['profesor_id'] : $_SESSION['estudiante_id'];

// Crear un nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer los encabezados de la hoja
$sheet->setCellValue('A1', 'Nombre Estudiante');
$sheet->setCellValue('B1', 'Apellido Estudiante');
$sheet->setCellValue('C1', 'Materia');
$sheet->setCellValue('D1', 'Periodo');
$sheet->setCellValue('E1', 'Nota');
$sheet->setCellValue('F1', 'Promedio Final'); // Encabezado para el promedio

// Consulta SQL según el rol del usuario
if ($esProfesor) {
    // Si es profesor, obtiene todas las notas de sus estudiantes
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
            periodo p ON dn.id_periodo = p.id
        WHERE 
            em.id_profesor = ?
        ORDER BY 
            e.primer_nombre, e.primer_apellido, m.nombre, p.nombre";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['profesor_id']);
} else {
    // Si es estudiante, obtiene solo sus propias notas
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
            periodo p ON dn.id_periodo = p.id
        WHERE 
            e.id = ?"; // Filtra por el ID del estudiante

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['estudiante_id']);
}

$stmt->execute();
$result = $stmt->get_result();

// Variables para el promedio
$currentMateria = "";
$sumNotas = 0;
$countNotas = 0;
$row = 2; // Empezar en la segunda fila

// Rellenar los datos
while ($data = $result->fetch_assoc()) {
    // Si cambiamos de materia, calcula y escribe el promedio anterior
    if ($currentMateria !== $data['materia'] && $currentMateria !== "") {
        // Escribir el promedio en la siguiente fila
        $sheet->setCellValue('F' . $row, round($sumNotas / $countNotas, 2)); // Escribir el promedio redondeado
        $row++; // Avanzar una fila para dejar un espacio

        // Reiniciar los acumuladores
        $sumNotas = 0;
        $countNotas = 0;
    }

    // Escribir datos en la hoja de cálculo
    $sheet->setCellValue('A' . $row, $data['primer_nombre']);
    $sheet->setCellValue('B' . $row, $data['primer_apellido']);
    $sheet->setCellValue('C' . $row, $data['materia']);
    $sheet->setCellValue('D' . $row, $data['periodo']);
    $sheet->setCellValue('E' . $row, $data['nota']);

    // Acumular para el promedio
    $sumNotas += $data['nota'];
    $countNotas++;

    $row++;
    $currentMateria = $data['materia']; // Actualizar la materia actual
}

// Escribir el último promedio si hay datos
if ($countNotas > 0) {
    $sheet->setCellValue('F' . $row, round($sumNotas / $countNotas, 2)); // Escribir el promedio redondeado
}

// Agregar una fila vacía al final para un mejor espaciado
$row++;
// Crear el archivo Excel
try {
    $writer = new Xlsx($spreadsheet);
    $filename = 'notas_' . ($esProfesor ? 'profesor' : 'estudiante') . '_' . date('Y-m-d_H-i') . '.xlsx';

    // Configurar la cabecera para la descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Guardar el archivo en la salida
    $writer->save('php://output');
    exit; // Terminar el script después de la exportación
} catch (Exception $e) {
    echo 'Error al generar el archivo Excel: ', $e->getMessage();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Notas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h2 {
            margin-bottom: 20px;
        }
        .container {
            margin-top: 50px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Exportar Notas</h2>
        <a href="gestionar_notas.php" class="btn btn-secondary mb-3">Regresar a Gestionar Notas</a>
        <p>Las notas se están generando. Si la descarga no comienza automáticamente, por favor <a href="gestionar_notas.php">haga clic aquí</a>.</p>
    </div>
</body>
</html>
