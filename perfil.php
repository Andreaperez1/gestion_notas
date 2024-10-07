<?php
session_start();

// Verificar si el usuario está logueado como profesor o estudiante
if (!isset($_SESSION['profesor_id']) && !isset($_SESSION['estudiante_id'])) {
    header('Location: login.php');
    exit();
}

// Determinar si es profesor o estudiante
$esProfesor = isset($_SESSION['profesor_id']);
$nombreUsuario = $esProfesor ? $_SESSION['nombre_profesor'] : $_SESSION['nombre_estudiante'];

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

// Obtener los datos del usuario
$idUsuario = $esProfesor ? $_SESSION['profesor_id'] : $_SESSION['estudiante_id'];
$tabla = $esProfesor ? "profesores" : "estudiantes";
$sql = "SELECT * FROM $tabla WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Gestión de Notas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container perfil-container">
        <h2>Perfil de <?php echo $esProfesor ? 'Profesor' : 'Estudiante'; ?></h2>
        <form>
            <div class="form-group">
                <label for="foto">Foto de Perfil</label>
                <?php if (!empty($usuario['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de Perfil" class="img-fluid" style="width: 150px; height: auto;">
                <?php else: ?>
                    <p>No hay foto de perfil disponible.</p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" value="<?php echo htmlspecialchars($usuario['primer_nombre'] . ' ' . $usuario['segundo_nombre'] . ' ' . $usuario['primer_apellido'] . ' ' . $usuario['segundo_apellido']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="text" class="form-control" id="cedula" value="<?php echo htmlspecialchars($usuario['cedula']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="rol">Rol</label>
                <input type="text" class="form-control" id="rol" value="<?php echo $esProfesor ? 'Profesor' : 'Estudiante'; ?>" disabled>
            </div>
            <div class="form-group">
                <a href="editar_perfil.php" class="btn btn-primary">Editar Perfil</a>
                <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
