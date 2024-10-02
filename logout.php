<?php
session_start();

// Destruir todas las sesiones
session_unset();
session_destroy();

// Redirigir al login después de cerrar la sesión
header('Location: login.php');
exit();
