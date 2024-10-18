<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante - Sistema de Gestión de Pedidos</title>
    <link rel="stylesheet" href="./stylos/index.css"> 
</head>
<body>

    <!-- Navbar -->
    <?php include('navbar.php'); ?>

   <!-- Contenido principal -->
<div class="content">
    <?php 
    if (isset($_SESSION['username'])) {
        // Verifica si el usuario es administrador
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $admin_name = $_SESSION['username']; // Nombre del administrador
            echo "<h1>Bienvenido, $admin_name!</h1>";
            echo "<p>Has accedido al Panel de Control. Aquí puedes gestionar pedidos y productos de manera eficiente.</p>";
        } else {
            // Mensaje para un usuario normal
            echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['username']) . "!</h1>";
            echo "<p>Disfruta de nuestros deliciosos platos y gestiona tus pedidos fácilmente.</p>";
        }
    } else {
        // Mensaje general si no ha iniciado sesión
        echo "<h1>Bienvenido al Sistema de Gestión de Pedidos</h1>";
        echo "<p>Disfruta de nuestros deliciosos platos y gestiona tus pedidos fácilmente.</p>";
    }
    ?>
</div>


</body>
</html>
