<?php
session_start();

// Verificar si ya está logueado
if (isset($_SESSION['profesor_id'])) {
    header('Location: dashboard.php'); // Redirigir al dashboard si ya está logueado
    exit();
}

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password_db = ""; // Cambié el nombre de la variable para evitar confusión con la contraseña
$dbname = "gestion_notas";
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash de la contraseña

    // Verificar si el correo ya está registrado
    $sql = "SELECT * FROM Profesores WHERE correo='$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $error = "El correo ya está registrado.";
    } else {
        // Insertar el nuevo profesor en la base de datos
        $sql = "INSERT INTO Profesores (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, correo, password) 
                VALUES ('$primer_nombre', '$segundo_nombre', '$primer_apellido', '$segundo_apellido', '$cedula', '$correo', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Establecer un mensaje de éxito en la sesión
            $_SESSION['success_message'] = "Profesor registrado con éxito.";
            // Redirigir al login después del registro exitoso
            header('Location: login.php');
            exit();
        } else {
            $error = "Error al registrar el profesor: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Profesor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
</head>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
        }

        .alert {
            margin-bottom: 20px;

        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Registrar Profesor</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="primer_nombre">Primer Nombre:</label>
                <input type="text" class="form-control" name="primer_nombre" required>
            </div>
            <div class="form-group">
                <label for="segundo_nombre">Segundo Nombre:</label>
                <input type="text" class="form-control" name="segundo_nombre">
            </div>
            <div class="form-group">
                <label for="primer_apellido">Primer Apellido:</label>
                <input type="text" class="form-control" name="primer_apellido" required>
            </div>
            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" class="form-control" name="segundo_apellido">
            </div>
            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" class="form-control" name="cedula" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" class="form-control" name="correo" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Registrar</button>
        </form>
    </div>
</body>

</html>