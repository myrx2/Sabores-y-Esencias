<?php
session_start();
include('db.php');

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Restaurante</title>
    <link rel="stylesheet" href="./stylos/admin.css"> 
</head>
<body>

    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Contenido principal -->
    <div class="content">
        <h1>Panel de Administración</h1>

        <div class="actions">
            <a href="register_admin.php">Registrar Nuevo Administrador</a>
            <a href="view_orders.php">Ver Pedidos Realizados</a>
            <a href="add_product.php">Agregar Producto</a>
            <a href="manage_stock.php">Gestionar Productos</a>
            <a href="index.php">Volver</a>
        </div>
    </div>

</body>
</html>
