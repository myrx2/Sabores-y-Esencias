<?php
session_start();
include('db.php');

// Inicializar variables
$message = "";

// Guardar pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    if (!empty($_SESSION['cart'])) {
        $user_id = $_SESSION['user_id']; 
        $total_amount = 0;

        // Calcular el total
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Iniciar transacción
        $conexion->beginTransaction();
        try {
            // Insertar el pedido
            $stmt = $conexion->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
            $stmt->execute([$user_id, $total_amount]);
            $order_id = $conexion->lastInsertId(); // Obtener el ID del pedido insertado

            // Insertar los detalles del pedido
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt = $conexion->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->execute([$order_id, $product_id, $item['quantity'], $subtotal]);
            }

            // Limpiar el carrito
            $_SESSION['cart'] = [];
            // Confirmar la transacción
            $conexion->commit();

            // Establecer mensaje de éxito en la sesión
            $_SESSION['success_message'] = "Gracias por su compra.";
            $_SESSION['modal'] = true; // Mostrar modal

            // Redireccionar
            header("Location: cart.php");
            exit();
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conexion->rollBack();
            $message = "Error al realizar el pedido: " . htmlspecialchars($e->getMessage()); // Mensaje de error
        }
    } else {
        $message = "El carrito está vacío."; 
    }
}

// Eliminar producto del carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); 
        $message = "Producto eliminado del carrito."; 
    }
}

// Vaciar el carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empty_cart'])) {
    $_SESSION['cart'] = []; 
    $message = "El carrito ha sido vaciado."; 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="./stylos/cart.css"> <!-- Enlace a la hoja de estilos externa -->
    <link rel="stylesheet" href="./stylos/modal.css"> <!-- Enlace a la hoja de estilos del modal -->
</head>
<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>
    
    <h1>Carrito de Compras</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="cart-container" id="cart-content">
        <?php if (empty($_SESSION['cart'])): ?>
            <p class="empty-cart-message">El carrito está vacío.</p>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                <div class="cart-item">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p>Precio: $<?php echo number_format($item['price'], 2); ?></p>
                    <p>Cantidad: <?php echo htmlspecialchars($item['quantity']); ?></p>
                    <p>Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    <form method="POST" class="remove-item-form">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" name="remove_item" class="button">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <h3 class="total-amount">Total: $<?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
                echo number_format($total, 2);
            ?></h3>
            <form method="POST" class="confirm-order-form">
                <button type="submit" name="confirm_order" class="button">Confirmar Pedido</button>
            </form>
            <form method="POST" class="empty-cart-form">
                <button type="submit" name="empty_cart" class="button" style="background-color: red;">Vaciar Carrito</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <?php if (isset($_SESSION['modal'])): ?>
        <div class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="closeModal()">&times;</span>
                <h2><?php echo htmlspecialchars($_SESSION['success_message']); ?></h2>
                <button onclick="closeModal()">Cerrar</button>
            </div>
        </div>
        <script>
            document.getElementById('cart-content').style.display = 'none'; // Ocultar el contenido del carrito
        </script>
        <?php unset($_SESSION['modal']); ?> <!-- Limpiar variable de sesión -->
    <?php endif; ?>

    <div class="navigation-buttons">
        <a href="index.php" class="button">Volver al Inicio</a>
        <a href="products.php" class="button">Volver a la lista de productos</a>
    </div>

    <script>
        function closeModal() {
            // Redirigir a la página de inicio
            window.location.href = "index.php"; // Cambia esto si necesitas otra URL
        }
    </script>
</body>
</html>
