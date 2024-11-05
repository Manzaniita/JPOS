<?php
require 'auth.php'; // Verifica si el usuario está autenticado

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol']; // Asegúrate de que el rol también esté en la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="css/styles.css"> </head>
<body>
    <div class="container">
        <h1>Bienvenido, <?= htmlspecialchars($usuario); ?>!</h1>

        <nav>
            <?php if ($rol === 'admin'): ?>
                <a href="admin.php" class="button">Panel de Administración</a>
                <a href="pos.php" class="button">Vender</a>
                <a href="obtener_ventas.php" class="button">Ventas</a>
            <?php endif; ?> 
            <a href="logout.php" class="button">Cerrar sesión</a>
            <a href="update.php" class="button">Actualizar mis datos</a>
        </nav>

        </div>
</body>
</html>
