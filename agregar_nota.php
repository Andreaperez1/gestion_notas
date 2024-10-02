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

// Obtener los estudiantes matriculados
$sql_estudiantes = "SELECT em.id AS id_matriculados, e.primer_nombre, e.primer_apellido 
                    FROM matriculadosmaterias em
                    JOIN Estudiantes e ON em.id_estudiante = e.id
                    WHERE em.id_profesor = ?";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
$stmt_estudiantes->bind_param("i", $_SESSION['profesor_id']);
$stmt_estudiantes->execute();
$result_estudiantes = $stmt_estudiantes->get_result();

// Obtener los periodos disponibles
$sql_periodos = "SELECT id, nombre FROM periodo";
$result_periodos = $conn->query($sql_periodos);

// Obtener las materias disponibles
$sql_materias = "SELECT id, nombre FROM materias";
$result_materias = $conn->query($sql_materias);

// Manejar la inserción de la nueva nota
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_matriculados = $_POST['id_matriculados'];
    $id_periodo = $_POST['id_periodo'];
    $id_materia = $_POST['id_materia'];
    $nota = $_POST['nota'];
    $fecha = date('Y-m-d');

    // Insertar la nueva nota en la base de datos
    $sql_insert = "INSERT INTO detalle_notas (id_matriculados, id_periodo, nota, fecha) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iids", $id_matriculados, $id_periodo, $nota, $fecha);

    if ($stmt_insert->execute()) {
        echo "<div class='alert alert-success'>Nota agregada exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al agregar la nota: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nota</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Agregar Nota</h2>

        <!-- Botón para regresar al menú -->
        <a href="gestionar_notas.php" class="btn btn-secondary mb-3">Regresar a Gestionar Notas</a>

        <form action="" method="POST">
            <div class="form-group">
                <label for="id_matriculados">Estudiante:</label>
                <select class="form-control" name="id_matriculados" required>
                    <option value="">Seleccionar estudiante</option>
                    <?php while ($row = $result_estudiantes->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_matriculados']; ?>">
                            <?php echo $row['primer_nombre'] . " " . $row['primer_apellido']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_periodo">Periodo:</label>
                <select class="form-control" name="id_periodo" required>
                    <option value="">Seleccionar periodo</option>
                    <?php while ($row = $result_periodos->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_materia">Materia:</label>
                <select class="form-control" name="id_materia" required>
                    <option value="">Seleccionar materia</option>
                    <?php while ($row = $result_materias->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nota">Nota:</label>
                <input type="number" step="0.01" class="form-control" name="nota" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Nota</button>
        </form>
    </div>
</body>
</html>

<?php
$stmt_estudiantes->close();
$conn->close();
?>
