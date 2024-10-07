<?php
// Inicia la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "gestion_notas";

$conn = new mysqli($servername, $username, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los estudiantes
$sql = "SELECT id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, correo FROM estudiantes";
$result = $conn->query($sql);

// Verificar si la consulta tuvo éxito
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Estudiantes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        nav {
            background-color: #007bff;
            color: white;
        }

        nav a {
            color: white;
        }

        nav a:hover {
            color: #ffcc00; /* Color para el hover */
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .container {
            margin-top: 20px;
            background-color:#f8f9fa;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        th {
            background-color: #007bff;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
            border: 1px solid #dddddd;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Filas alternas con color de fondo */
        }

        tr:hover {
            background-color: #e9ecef; /* Color al pasar el mouse */
        }

        /* Estilos para el botón de cerrar sesión */
        .btn-logout {
            background-color: #dc3545; /* Color rojo para cerrar sesión */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-logout:hover {
            background-color: #c82333; /* Color más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand">
            <a class="navbar-brand" href="index.php">Gestión de Notas</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="buscar.php">Buscar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lista_estudiantes.php">Estudiantes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-logout" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>

    </nav>

    <div class="container mt-5">
        <h2>Lista de Estudiantes</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Primer Nombre</th>
                        <th>Segundo Nombre</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['primer_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['segundo_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['primer_apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['segundo_apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron estudiantes.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>

