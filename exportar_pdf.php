<?php
session_start();

// Verificar si el usuario está logueado como profesor o estudiante
if (!isset($_SESSION['profesor_id']) && !isset($_SESSION['estudiante_id'])) {
    header('Location: login.php'); // Redirigir al login si no está logueado
    exit();
}

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
            periodos p ON dn.id_periodo = p.id
        WHERE 
            em.id_profesor = ? 
        ORDER BY 
            e.primer_nombre, e.primer_apellido, p.nombre, m.nombre";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error al preparar la consulta para profesor: ' . $conn->error);
    }
    $stmt->bind_param("i", $usuarioId);
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
            periodos p ON dn.id_periodo = p.id
        WHERE 
            e.id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error al preparar la consulta para estudiante: ' . $conn->error);
    }
    $stmt->bind_param("i", $usuarioId);
}

// Ejecutar la consulta y obtener resultados
if (!$stmt->execute()) {
    die('Error al ejecutar la consulta: ' . $stmt->error);
}
$result = $stmt->get_result();

// Incluir TCPDF
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Crear un nuevo documento PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Notas');
$pdf->SetHeaderData('', 0, 'Reporte de Notas', '');
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Encabezados de la tabla
$html = '<h1>Notas</h1>';
$html .= '<table border="1" cellpadding="5">
<tr>
<th>Nombre Estudiante</th>
<th>Apellido Estudiante</th>
<th>Materia</th>
<th>Periodo</th>
<th>Nota</th>
</tr>';

// Inicializar un arreglo para acumular notas por materia
$notasPorMateria = [];

// Rellenar los datos
while ($data = $result->fetch_assoc()) {
    // Acumular notas por materia
    $key = $data['materia'] . '|' . $data['periodo'];
    if (!isset($notasPorMateria[$key])) {
        $notasPorMateria[$key] = [
            'nombre' => $data['primer_nombre'],
            'apellido' => $data['primer_apellido'],
            'notas' => [],
            'materia' => $data['materia'],
            'periodo' => $data['periodo'],
        ];
    }
    $notasPorMateria[$key]['notas'][] = $data['nota'];

    // Escribir cada nota en la tabla
    $html .= '<tr>
        <td>' . htmlspecialchars($data['primer_nombre']) . '</td>
        <td>' . htmlspecialchars($data['primer_apellido']) . '</td>
        <td>' . htmlspecialchars($data['materia']) . '</td>
        <td>' . htmlspecialchars($data['periodo']) . '</td>
        <td>' . htmlspecialchars($data['nota']) . '</td>
    </tr>';
}

// Calcular y mostrar promedios por materia y periodo
foreach ($notasPorMateria as $materiaPeriodo => $info) {
    $promedio = round(array_sum($info['notas']) / count($info['notas']), 2);
    $estado = ($promedio >= 3.5) ? 'Aprobado' : 'Reprobado';

    $html .= '<tr>
        <td colspan="2" style="text-align:right;"><strong>Promedio ' . htmlspecialchars($info['materia']) . ' (' . htmlspecialchars($info['periodo']) . '):</strong></td>
        <td>' . $promedio . '</td>
        <td>' . $estado . '</td>
    </tr>';
}

// Cerrar la tabla
$html .= '</table>';

// Escribir el contenido HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y salir
$pdf->Output('notas_' . ($esProfesor ? 'profesor' : 'estudiante') . '_' . date('Y-m-d_H-i') . '.pdf', 'D');

// Cerrar la conexión a la base de datos
$conn->close();
?>
