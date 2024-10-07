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

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];

    // Manejar la subida de la foto
    $foto = $usuario['foto']; // Mantener la foto actual
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/"; // Asegúrate de que esta carpeta tenga permisos de escritura
        $target_file = $target_dir . basename($_FILES['foto']['name']);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $target_file; // Actualizar la ruta de la foto
        }
    }

    // Actualizar la información en la base de datos
    $sql_update = "UPDATE $tabla SET primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?, segundo_apellido = ?, cedula = ?, correo = ?, foto = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssisi", $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $cedula, $correo, $foto, $idUsuario);
    $stmt_update->execute();

    // Redirigir al perfil después de la actualización
    header('Location: perfil.php');
    exit();
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Gestión de Notas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container perfil-container">
        <h2>Editar Perfil de <?php echo $esProfesor ? 'Profesor' : 'Estudiante'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="foto">Foto de Perfil</label>
                <?php if (!empty($usuario['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de Perfil" class="img-fluid" style="width: 150px; height: auto;">
                <?php else: ?>
                    <p>No hay foto de perfil disponible.</p>
                <?php endif; ?>
                <input type="file" class="form-control" id="foto" name="foto">
            </div>
            <div class="form-group">
                <label for="primer_nombre">Primer Nombre</label>
                <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" value="<?php echo htmlspecialchars($usuario['primer_nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="segundo_nombre">Segundo Nombre</label>
                <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre" value="<?php echo htmlspecialchars($usuario['segundo_nombre']); ?>">
            </div>
            <div class="form-group">
                <label for="primer_apellido">Primer Apellido</label>
                <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" value="<?php echo htmlspecialchars($usuario['primer_apellido']); ?>" required>
            </div>
            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido</label>
                <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido" value="<?php echo htmlspecialchars($usuario['segundo_apellido']); ?>">
            </div>
            <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo htmlspecialchars($usuario['cedula']); ?>" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="perfil.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
