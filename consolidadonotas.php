<?php
session_start();

// Verificar si el profesor está logueado
if (!isset($_SESSION['profesor_id'])) {
    header('Location: login.php'); // Redirigir al login si no está logueado
    exit();
}

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "gestion_notas";
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el consolidado de notas
$sql_consolidado = "SELECT 
                        em.id AS id_matriculados, 
                        e.primer_nombre, 
                        e.primer_apellido,
                        AVG(dn.nota) AS promedio,
                        p.id AS id_periodo,
                        p.nombre AS periodo
                    FROM 
                        detalle_notas dn
                    JOIN 
                        matriculadosmaterias em ON dn.id_matriculados = em.id
                    JOIN 
                        Estudiantes e ON em.id_estudiante = e.id
                    JOIN 
                        periodo p ON dn.id_periodo = p.id
                    WHERE 
                        em.id_profesor = ?
                    GROUP BY 
                        em.id, p.id";

$stmt = $conn->prepare($sql_consolidado);
$stmt->bind_param("i", $_SESSION['profesor_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consolidado de Notas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Consolidado de Notas</h2>

        <!-- Botón para regresar al menú -->
        <a href="dashboard.php" class="btn btn-secondary mb-3">Regresar al Menú</a>

        <!-- Tabla de consolidado de notas -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Periodo</th>
                    <th>Promedio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['primer_nombre'] . " " . $row['primer_apellido']; ?></td>
                        <td><?php echo $row['periodo']; ?></td>
                        <td><?php echo number_format($row['promedio'], 2); ?></td>
                        <td><?php echo ($row['promedio'] >= 3) ? 'Aprobado' : 'Reprobado'; ?></td> <!-- Cambia esta lógica si es necesario -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
