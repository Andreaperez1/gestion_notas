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

// Consulta SQL para obtener las notas de los estudiantes con la materia
$sql = "SELECT dn.id, em.id AS id_matriculados, e.primer_nombre, e.primer_apellido, m.nombre AS materia, dn.nota, p.nombre AS periodo
        FROM detalle_notas dn
        JOIN matriculadosmaterias em ON dn.id_matriculados = em.id
        JOIN Estudiantes e ON em.id_estudiante = e.id
        JOIN materias m ON em.id_materia = m.id
        JOIN periodo p ON dn.id_periodo = p.id
        WHERE em.id_profesor = ?
        ORDER BY e.primer_apellido, e.primer_nombre, m.nombre, p.nombre"; // Ordenar por apellido, nombre, materia, y periodo

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Verificar si la preparación fue exitosa
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Asociar el parámetro
$stmt->bind_param("i", $_SESSION['profesor_id']); // Filtrar por el ID del profesor
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Notas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            color: #007bff;
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
        }
        .btn-warning, .btn-danger {
            margin-right: 5px; /* Espacio entre los botones */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestionar Notas</h2>

        <a href="dashboard.php" class="btn btn-secondary mb-3">Regresar al Menú</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Materia</th> 
                    <th>Periodo</th>
                    <th>Nota</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['primer_nombre'] . " " . $row['primer_apellido']); ?></td>
                        <td><?php echo htmlspecialchars($row['materia']); ?></td>
                        <td><?php echo htmlspecialchars($row['periodo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nota']); ?></td>
                        <td>
                            <a href="editar_nota.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="eliminar_nota.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar esta nota?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="agregar_nota.php" class="btn btn-primary">Agregar Nueva Nota</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
