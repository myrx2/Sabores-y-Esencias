<?php
session_start();
include('db.php');

// Verificar si el usuario ha iniciado sesión y si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Agregar un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['stock'])) {
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // Insertar el nuevo producto en la base de datos
    $stmt = $conexion->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
    $stmt->execute([$name, $price, $stock]);

    echo "<script>alert('Producto agregado correctamente.'); window.location.href = 'add_product.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Nuevo Producto</title>
    <link rel="stylesheet" href="./stylos/add_product.css"> 
</head>

<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <h2>Agregar Nuevo Producto</h2>

    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="name">Nombre del Producto:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="price">Precio:</label>
                <input type="number" name="price" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" name="stock" min="0" required>
            </div>

            <button type="submit">Agregar Producto</button>
        </form>
    </div>
</body>

</html>