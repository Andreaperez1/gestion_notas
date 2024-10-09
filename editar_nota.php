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

// Verificar si se ha pasado el ID de la nota
if (isset($_GET['id'])) {
    $id = $_GET['id'];

   // Modificar la consulta para editar
$sql = "SELECT dn.nota, em.id_estudiante, m.nombre AS materia, p.nombre AS periodo
FROM detalle_notas dn
JOIN matriculadosmaterias em ON dn.id_matriculados = em.id
JOIN materias m ON em.id_materia = m.id
JOIN periodos p ON dn.id_periodo = p.id
WHERE dn.id = ?";

// Prepara y ejecuta la consulta
$stmt = $conn->prepare($sql);

// Verifica si la consulta se preparó correctamente
if (!$stmt) {
die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
$notaData = $result->fetch_assoc();
} else {
die("Nota no encontrada.");
}

} else {
    die("ID de nota no proporcionado.");
}

// Manejar la actualización de la nota
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevaNota = $_POST['nota'];
    
    $sql = "UPDATE detalle_notas SET nota = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $nuevaNota, $id);
    
    if ($stmt->execute()) {
        header('Location: gestionar_notas.php?success=Nota actualizada con éxito'); // Redirigir después de actualizar
        exit();
    } else {
        echo "Error al actualizar la nota: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Nota</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Fondo gris claro */
        }
        .container {
            margin-top: 50px;
            background-color: #ffffff; /* Fondo blanco para el contenedor */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px; /* Espaciado interno más amplio */
        }
        h2 {
            color: #007bff; /* Color azul */
        }
        .btn-secondary {
            margin-bottom: 20px; /* Espacio debajo del botón de regresar */
        }
        .form-group label {
            font-weight: bold; /* Resaltar las etiquetas */
        }
        .form-control {
            border-radius: 5px; /* Bordes redondeados para los inputs */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Nota</h2>
        
        <a href="gestionar_notas.php" class="btn btn-secondary mb-3">Regresar</a>

        <form method="POST">
            <div class="form-group">
                <label for="nota">Nueva Nota</label>
                <input type="number" step="0.01" class="form-control" id="nota" name="nota" value="<?php echo htmlspecialchars($notaData['nota']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Nota</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
