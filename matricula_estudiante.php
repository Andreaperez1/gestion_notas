<?php
session_start();

// Verificar si el estudiante está logueado
if (!isset($_SESSION['estudiante_id'])) {
    header('Location: login.php');
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

// Obtener la lista de materias y profesores
$sql_materias_profesores = "
    SELECT m.id AS materia_id, m.nombre AS materia_nombre, p.id AS profesor_id, p.primer_nombre, p.primer_apellido 
    FROM materias m
    JOIN Profesores p ON p.id = m.id";  // Asumiendo que cada materia tiene un profesor asociado
$result_materias_profesores = $conn->query($sql_materias_profesores);

// Manejar la inserción de la matrícula
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_estudiante = $_SESSION['estudiante_id'];
    $id_materia = $_POST['id_materia'];
    $id_profesor = $_POST['id_profesor'];
    $fecha = date('Y-m-d');

    // Insertar la matrícula en la base de datos
    $sql_insert = "INSERT INTO matriculadosmaterias (id_estudiante, id_materia, id_profesor, fecha) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiis", $id_estudiante, $id_materia, $id_profesor, $fecha);

    if ($stmt_insert->execute()) {
        echo "<div class='alert alert-success'>Te has matriculado exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al matricular: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matricularse en Materias</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fa; /* Color de fondo más suave */
            font-family: 'Arial', sans-serif; /* Tipografía moderna */
        }

        .container {
            margin-top: 50px;
            background-color: #ffffff; /* Fondo blanco para el contenedor */
            border-radius: 10px; /* Bordes redondeados */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Sombra suave */
            padding: 30px; /* Espaciado interno más amplio */
        }

        h2 {
            color: #007bff; /* Color azul para el título */
            font-weight: bold; /* Hacer el título más destacado */
            margin-bottom: 30px; /* Espaciado inferior para separar del contenido */
            text-align: center; /* Centrar el título */
        }

        .alert {
            margin-bottom: 20px; /* Espaciado entre alertas */
        }

        .form-group label {
            font-weight: bold; /* Negrita para las etiquetas */
            color: #333; /* Color de texto oscuro */
        }

        .btn-primary {
            background-color: #007bff; /* Color de fondo personalizado */
            border-color: #007bff; /* Color del borde */
            padding: 10px 20px; /* Espaciado interno */
            border-radius: 5px; /* Bordes redondeados para botones */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Color de fondo al pasar el mouse */
            border-color: #004085; /* Color del borde al pasar el mouse */
        }

        .btn-secondary {
            background-color: #6c757d; /* Color de fondo secundario */
            border-color: #6c757d; /* Color del borde secundario */
            padding: 10px 20px; /* Espaciado interno */
            border-radius: 5px; /* Bordes redondeados */
        }

        .btn-secondary:hover {
            background-color: #5a6268; /* Color de fondo al pasar el mouse */
            border-color: #545b62; /* Color del borde al pasar el mouse */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Matricularse en Materias</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="id_materia">Materia:</label>
                <select class="form-control" name="id_materia" required>
                    <option value="">Seleccionar materia</option>
                    <?php while ($row = $result_materias_profesores->fetch_assoc()): ?>
                        <option value="<?php echo $row['materia_id']; ?>">
                            <?php echo $row['materia_nombre'] . " - Profesor: " . $row['primer_nombre'] . " " . $row['primer_apellido']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_profesor">Profesor:</label>
                <select class="form-control" name="id_profesor" required>
                    <option value="">Seleccionar profesor</option>
                    <?php 
                    $result_materias_profesores->data_seek(0); // Reset pointer to re-use result
                    while ($row = $result_materias_profesores->fetch_assoc()): ?>
                        <option value="<?php echo $row['profesor_id']; ?>">
                            <?php echo $row['primer_nombre'] . " " . $row['primer_apellido']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Matricularme</button>
            <a href="dashboard.php" class="btn btn-secondary mb-3">Regresar al Menú</a>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
