<?php
session_start();

// Verificar si el profesor está logueado
if (!isset($_SESSION['profesor_id'])) {
    header('Location: login.php'); // Redirigir al login si no está logueado
    exit();
}

require 'vendor/autoload.php'; // Asegúrate de que la ruta es correcta
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

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

// Inicializar variables para mensajes
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $file = $_FILES['file']['tmp_name'];

    // Validar si el archivo es Excel
    try {
        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        // Contador para registrar cuántos estudiantes se han subido
        $contador = 0;

        // Preparar la consulta para verificar la existencia del estudiante
        $stmt_check = $conn->prepare("SELECT * FROM Estudiantes WHERE cedula = ?");
        $stmt_insert = $conn->prepare("INSERT INTO Estudiantes (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula) VALUES (?, ?, ?, ?, ?)");

        // Recorrer las filas del Excel
        foreach ($worksheet->getRowIterator() as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getValue();
            }

            // Comprobar que hay suficientes celdas
            if (count($cells) >= 5) {
                $primer_nombre = $cells[0];
                $segundo_nombre = $cells[1];
                $primer_apellido = $cells[2];
                $segundo_apellido = $cells[3];
                $cedula = $cells[4];

                // Verificar si el estudiante ya está en la base de datos
                $stmt_check->bind_param("s", $cedula);
                $stmt_check->execute();
                $result = $stmt_check->get_result();

                if ($result->num_rows == 0) {
                    // Insertar estudiante si no existe
                    $stmt_insert->bind_param("sssss", $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $cedula);
                    if ($stmt_insert->execute()) {
                        $contador++;
                    }
                }
            }
        }

        if ($contador > 0) {
            $mensaje = "Se han subido $contador estudiantes exitosamente.";
        } else {
            $error = "No se ha subido ningún estudiante. Verifica si ya existen en la base de datos.";
        }

    } catch (Exception $e) {
        $error = "Error al procesar el archivo: " . $e->getMessage();
    }

} else {
    if (isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0) {
        $error = "Error al subir el archivo.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Estudiantes</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Subir Estudiantes</h2>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Seleccionar archivo Excel (.xls o .xlsx):</label>
                <input type="file" class="form-control" name="file" accept=".xls,.xlsx" required>
            </div>
            <button type="submit" class="btn btn-primary">Subir</button>
            <button onclick="history.back()" type="button" class="btn btn-secondary">Regresar</button>
        </form>
    </div>
</body>
</html>
