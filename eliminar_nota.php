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

    // Eliminar la nota
    $sql = "DELETE FROM detalle_notas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $successMessage = "Nota eliminada con éxito"; // Mensaje de éxito
    } else {
        $errorMessage = "Error al eliminar la nota: " . $conn->error; // Mensaje de error
    }
} else {
    die("ID de nota no proporcionado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Nota</title>
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
            color: #dc3545; /* Color rojo para el título */
        }
        .alert {
            margin-top: 20px;
        }
        .btn-secondary {
            margin-bottom: 20px; /* Espacio debajo del botón de regresar */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Eliminar Nota</h2>
        
        <a href="gestionar_notas.php" class="btn btn-secondary">Regresar</a>

        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
