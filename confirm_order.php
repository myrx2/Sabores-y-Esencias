<?php
session_start();
include('db.php');

// Verificar si el carrito no está vacío
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $all_items_in_stock = true;

    // Verificar si todos los productos del carrito tienen stock suficiente
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conexion->prepare("SELECT stock FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product['stock'] < $item['quantity']) {
            $all_items_in_stock = false;
            echo "<script>alert('No hay suficiente stock para el producto: " . $item['name'] . "');</script>";
            break;
        }
    }

    // Si hay suficiente stock para todos los productos, se actualiza la base de datos
    if ($all_items_in_stock) {
        foreach ($_SESSION['cart'] as $product_id => $item) {
            // Restar la cantidad comprada del stock
            $stmt = $conexion->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
            $stmt->execute([$item['quantity'], $product_id]);
        }

        // Vaciar el carrito después de la confirmación del pedido
        $_SESSION['cart'] = [];

        echo "<script>alert('Pedido confirmado. Gracias por su compra.');</script>";
    }
} else {
    echo "<script>alert('El carrito está vacío.');</script>";
}

// Redirigir a la página de productos o al inicio
header("Location: productos.php");
exit();
?>
