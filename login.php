<?php
session_start();

// Verificar si ya está logueado
if (isset($_SESSION['profesor_id']) || isset($_SESSION['estudiante_id'])) {
    header('Location: dashboard.php'); // Redirigir al dashboard si ya está logueado
    exit();
}

// Verificar si hay un mensaje de éxito y mostrarlo
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success text-center">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo
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

// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identificador = $_POST['identificador'];
    $password = $_POST['password'];
    $rol = $_POST['rol']; 

    if ($rol === 'profesor') {
        // Consulta para el profesor, usando correo
        $sql = "SELECT id, primer_nombre, primer_apellido, password FROM Profesores WHERE correo='$identificador'";
    } elseif ($rol === 'estudiante') {
        // Consulta para el estudiante, usando la cédula
        $sql = "SELECT id, primer_nombre, primer_apellido, cedula FROM Estudiantes WHERE cedula='$identificador'";
    } else {
        $error = "Por favor selecciona un rol válido.";
    }

    // Verificar si se ha establecido la consulta SQL
    if (isset($sql)) {
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verificar la cédula como contraseña para estudiantes
            if ($rol === 'estudiante' && $password === $row['cedula']) {
                // Guardar información del estudiante en la sesión
                $_SESSION['estudiante_id'] = $row['id'];
                $_SESSION['nombre_estudiante'] = $row['primer_nombre'] . ' ' . $row['primer_apellido'];

                header('Location: dashboard.php');
                exit();
            }
            // Verificar la contraseña para profesores
            elseif ($rol === 'profesor' && password_verify($password, $row['password'])) {
                // Guardar información del profesor en la sesión
                $_SESSION['profesor_id'] = $row['id'];
                $_SESSION['nombre_profesor'] = $row['primer_nombre'] . ' ' . $row['primer_apellido'];

                // Redirigir al dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "El identificador no está registrado.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn {
            width: 100%;
            margin-top: 10px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="identificador">Correo Electrónico o Cédula:</label>
                <input type="text" class="form-control" name="identificador" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select class="form-control" name="rol" required>
                    <option value="">Seleccionar rol</option>
                    <option value="profesor">Profesor</option>
                    <option value="estudiante">Estudiante</option>
                </select>
            </div>
            
            <a href="registrar.php" class="btn btn-danger">Registrarse</a>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
