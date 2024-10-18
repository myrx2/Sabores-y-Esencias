<?php
session_start();
include('db.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se ha pasado el order_id
if (!isset($_GET['order_id'])) {
    echo "No se ha especificado un ID de pedido.";
    exit();
}

$order_id = $_GET['order_id'];

// Obtener detalles del pedido
$stmt = $conexion->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener items del pedido
$stmt = $conexion->prepare("SELECT order_items.*, products.name FROM order_items 
                             JOIN products ON order_items.product_id = products.product_id 
                             WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si el pedido existe
if (!$order) {
    echo "Pedido no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles del Pedido</title>
    <link rel="stylesheet" type="text/css" href="./stylos/details_myorders.css">
</head>

<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Detalles del Pedido</h2>
        <p><strong>Monto Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>

        <h3>Items del Pedido</h3>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($order_items) > 0): ?>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No hay items para este pedido.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="actions">
            <a href="my_orders.php" class="button">Volver a Mis Pedidos</a>
        </div>
    </div>
</body>

</html>