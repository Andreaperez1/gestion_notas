<?php
session_start();

if (!isset($_SESSION['profesor_id']) && !isset($_SESSION['estudiante_id'])) {
    header('Location: login.php');
    exit();
}

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
            e.primer_nombre, e.primer_apellido, p.nombre, m.nombre";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error al preparar la consulta para profesor: ' . $conn->error);
    }
    $stmt->bind_param("i", $usuarioId);
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
    if (!$stmt) {
        die('Error al preparar la consulta para estudiante: ' . $conn->error);
    }
    $stmt->bind_param("i", $usuarioId);
}

if (!$stmt->execute()) {
    die('Error al ejecutar la consulta: ' . $stmt->error);
}
$result = $stmt->get_result();

require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

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

$html = '<h1 style="text-align:center;">Reporte de Notas</h1>';

$notasPorMateria = [];
while ($data = $result->fetch_assoc()) {
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
}

foreach ($notasPorMateria as $materiaPeriodo => $info) {
    $html .= '<h3>Materia: ' . htmlspecialchars($info['materia']) . ' - Periodo: ' . htmlspecialchars($info['periodo']) . '</h3>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th>Nombre Estudiante</th>
                <th>Apellido Estudiante</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($info['notas'] as $nota) {
        $html .= '<tr>
            <td>' . htmlspecialchars($info['nombre']) . '</td>
            <td>' . htmlspecialchars($info['apellido']) . '</td>
            <td>' . htmlspecialchars($nota) . '</td>
        </tr>';
    }
    
    $promedio = round(array_sum($info['notas']) / count($info['notas']), 2);
    $estado = ($promedio >= 3.5) ? 'Aprobado' : 'Reprobado';
    
    $html .= '</tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align:right;"><strong>Promedio:</strong></td>
                <td>' . $promedio . '</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;"><strong>Estado:</strong></td>
                <td>' . $estado . '</td>
            </tr>
        </tfoot>
    </table><br>';
}

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('notas_' . ($esProfesor ? 'profesor' : 'estudiante') . '_' . date('Y-m-d_H-i') . '.pdf', 'D');

$conn->close();
?>
