<?php
session_start();
include('db.php');

// Inicializar el carrito si no está configurado
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obtener productos de la base de datos
$stmt = $conexion->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensaje de estado
$message = "";

// Agregar productos al carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Usar una transacción
    $conexion->beginTransaction();
    try {
        // Verificar y actualizar el stock en una sola consulta
        $stmt = $conexion->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?");
        $stmt->execute([$quantity, $product_id, $quantity]);

        // Verificar si la actualización fue exitosa
        if ($stmt->rowCount() > 0) {
            // Si el producto ya existe en el carrito, sumar la cantidad
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                // Si no existe, agregarlo con la cantidad seleccionada
                $stmt = $conexion->prepare("SELECT * FROM products WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image_path'] // Añadir la ruta de la imagen al carrito
                ];
            }
            $message = "Producto agregado al carrito."; // Mensaje de éxito
        } else {
            $message = "No hay suficiente stock."; // Mensaje de error
        }

        // Confirmar la transacción
        $conexion->commit();
    } catch (Exception $e) {
        // Si hay un error, revertir la transacción
        $conexion->rollBack();
        $message = "Error al agregar el producto: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="./stylos/products.css"> <!-- Enlace a la hoja de estilos externa -->
</head>

<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <h1>Lista de Productos</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <!-- Contenedor para los productos -->
    <div class="products-container">
        <?php foreach ($products as $product): ?>
            <div class="product">

                <?php if (!empty($product['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        style="width: 100%; height: 200px; object-fit: cover; margin-bottom: 10px;"
                        class="product-image">
                <?php endif; ?>


                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>Precio: $<?php echo number_format($product['price'], 2); ?></p>
                <p>Stock: <?php echo $product['stock']; ?></p>

                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <label>Cantidad:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                    <button type="submit">Agregar al Carrito</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>