<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['error'] = "Necesitas iniciar sesión.";
    header('Location: login.php');
    exit;
}

// Verifica el rol del usuario
$rol = $_SESSION['rol'];
$current_page = basename($_SERVER['PHP_SELF']);

if (($current_page === 'admin.php' && $rol !== 'admin') || 
    ($current_page === 'pos.php' && !in_array($rol, ['admin', 'user']))) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: index.php'); // Redirige al inicio o a otra página
    exit;
}
?>
