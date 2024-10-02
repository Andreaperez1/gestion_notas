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
            background-color: #f8f9fa;
            color: #343a40;
        }

        .dashboard {
            margin-top: 50px;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .lead {
            font-weight: 300;
        }
    </style>
</head>

<body>
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

                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Exportar Notas de Mi Materia</h4>
                            <p>Descarga las notas de tus estudiantes en formato CSV.</p>
                            <a href="exportar_notas.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

                <!-- Opciones para el Estudiante -->
            <?php else: ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Matricularme</h4>
                            <p>Inscríbete en las materias disponibles.</p>
                            <a href="matricula_estudiante.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Exportar Notas</h4>
                            <p>Descarga todas tus notas en formato CSV.</p>
                            <a href="exportar_notas.php" class="btn btn-primary">Ir</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </div>
</body>

</html>
