<?php
session_start();
include('db.php');

// Confirmar pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    if (!empty($_SESSION['cart'])) {
        $user_id = $_SESSION['user_id'];
        $total_amount = 0;

        // Iniciar transacción
        $conexion->beginTransaction();
        try {
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt = $conexion->prepare("SELECT stock FROM products WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product && $product['stock'] >= $item['quantity']) {
                    $total_amount += $item['price'] * $item['quantity'];

                    // Restar el stock
                    $stmt = $conexion->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
                    $stmt->execute([$item['quantity'], $product_id]);
                } else {
                    throw new Exception('No hay suficiente stock para el producto: ' . $item['name']);
                }
            }

            // Insertar el pedido
            $stmt = $conexion->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
            $stmt->execute([$user_id, $total_amount]);
            $order_id = $conexion->lastInsertId();

            // Insertar los detalles del pedido
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt = $conexion->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->execute([$order_id, $product_id, $item['quantity'], $subtotal]);
            }

            // Limpiar el carrito
            $_SESSION['cart'] = [];
            $conexion->commit();
            echo "<script>alert('Pedido realizado con éxito.'); window.location.href = 'index.php';</script>";
        } catch (Exception $e) {
            $conexion->rollBack();
            echo "<script>alert('Error al realizar el pedido: " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('El carrito está vacío.');</script>";
    }
}
?>
