<?php
session_start();
include('db.php');

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Verificar si se ha pasado un order_id
if (!isset($_GET['order_id'])) {
    header("Location: admin.php");
    exit();
}

$order_id = $_GET['order_id'];

// Obtener detalles del pedido
$stmt = $conexion->prepare("SELECT order_items.product_id, order_items.quantity, order_items.subtotal, products.name 
                             FROM order_items 
                             JOIN products ON order_items.product_id = products.product_id 
                             WHERE order_items.order_id = ?");
$stmt->execute([$order_id]);
$order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener información del pedido
$stmt = $conexion->prepare("SELECT total_amount, created_at FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_info = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Pedido</title>
    <link rel="stylesheet" type="text/css" href="./stylos/order_details.css">
</head>
<body>
    <div class="container">
        <h2>Detalles del Pedido #<?php echo htmlspecialchars($order_id); ?></h2>
        <div class="order-summary">
            <p><strong>Total:</strong> $<?php echo number_format($order_info['total_amount'], 2); ?></p>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($order_info['created_at']); ?></p>
        </div>

        <div class="product-list">
            <h3>Productos en este pedido:</h3>
            <ul>
                <?php if (count($order_details) > 0): ?>
                    <?php foreach ($order_details as $detail): ?>
                        <li class="product-item">
                            <strong>ID del Producto:</strong> <?php echo htmlspecialchars($detail['product_id']); ?> <br>
                            <strong>Nombre:</strong> <?php echo htmlspecialchars($detail['name']); ?> <br>
                            <strong>Cantidad:</strong> <?php echo htmlspecialchars($detail['quantity']); ?> <br>
                            <strong>Subtotal:</strong> $<?php echo number_format($detail['subtotal'], 2); ?>
                        </li>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No hay detalles para este pedido.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="actions">
            <a href="view_orders.php" class="button">Volver a Órdenes</a>
        </div>
    </div>
</body>
</html>

