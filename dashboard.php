<?php
session_start();

// Verificar si el usuario está logueado como profesor o estudiante
if (!isset($_SESSION['profesor_id']) && !isset($_SESSION['estudiante_id'])) {
    // Si no está logueado, redirigir al login
    header('Location: login.php');
    exit();
}

$esProfesor = isset($_SESSION['profesor_id']);
$nombreUsuario = $esProfesor ? $_SESSION['nombre_profesor'] : $_SESSION['nombre_estudiante'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Notas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fa;
            /* Color de fondo más suave */
            color: #343a40;
        }

        .navbar {
            background-color: #007bff;
            /* Color de fondo de la navbar */
        }

        .navbar a {
            color: white;
            /* Color de los enlaces de navegación */
        }

        .navbar a:hover {
            color: #ffc107;
            /* Color al pasar el ratón por encima de los enlaces */
        }

        .dashboard {
            margin-top: 50px;
            padding: 30px;
            /* Aumentar el padding */
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard h2 {
            color: #007bff;
            /* Color del título */
            margin-bottom: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 25px;
            /* Aumentar el padding */
        }

        .btn-primary {
            background-color: #007bff;
            /* Color del botón */
            border-color: #007bff;
            border-radius: 5px;
            /* Esquinas redondeadas */
        }

        .btn-danger {
            background-color: #dc3545;
            /* Color del botón de peligro */
            border-color: #dc3545;
        }

        .lead {
            font-weight: 300;
        }

        .text-center {
            margin-bottom: 20px;
            /* Espacio entre los textos centrados */
        }

        .row>div {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Centrar contenido de cada tarjeta */
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Barra de Menú centrada -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">Gestión de Notas</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav"> 
                    <li class="nav-item">
                        <a class="nav-link" href="lista_estudiantes.php">Estudiantes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Más opciones
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="exportar_notas.php">Exportar a Excel</a>
                            <a class="dropdown-item" href="exportar_pdf.php">Descargar PDF</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido del Dashboard -->
    <div class="container dashboard">
        <div class="text-center">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
            <p class="lead"><?php echo $esProfesor ? "Panel de Profesor" : "Panel de Estudiante"; ?></p>
        </div>

        <div class="row mt-4">
            <!-- Opciones para el Profesor -->
            <?php if ($esProfesor): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Subir Estudiantes</h4>
                            <p>Sube estudiantes a tu clase para gestionar notas y participación.</p>
                            <a href="subir_estudiantes.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Gestionar Notas</h4>
                            <p>Administra las notas de tus estudiantes y mantén los registros al día.</p>
                            <a href="gestionar_notas.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Exportar Notas de Mi Materia</h4>
                            <p>Descarga las notas de tus estudiantes en formato CSV.</p>
                            <a href="exportar_notas.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Exportar Notas PDF</h4>
                            <p>Descarga todas tus notas en formato PDF.</p>
                            <a href="exportar_pdf.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

            <!-- Opciones para el Estudiante -->
            <?php else: ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Matricularme</h4>
                            <p>Inscríbete en las materias disponibles.</p>
                            <a href="matricula_estudiante.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Exportar Notas Excel</h4>
                            <p>Descarga todas tus notas en formato CSV.</p>
                            <a href="exportar_notas.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Exportar Notas PDF</h4>
                            <p>Descarga todas tus notas en formato PDF.</p>
                            <a href="exportar_pdf.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>



</html>