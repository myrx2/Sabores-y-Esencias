<?php
session_start();
include('db.php');

// Verificar si el usuario ha iniciado sesión y si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Inicializar variables para la búsqueda
$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';

// Obtener productos de la base de datos con filtrado por nombre
$sql = "SELECT * FROM products WHERE 1=1"; 

if (!empty($searchName)) {
    $sql .= " AND name LIKE :searchName"; 
}

$stmt = $conexion->prepare($sql);

// Asignar parámetros de búsqueda
if (!empty($searchName)) {
    $searchNameParam = "%" . $searchName . "%"; 
    $stmt->bindParam(':searchName', $searchNameParam);
}

// Ejecutar la consulta
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Actualizar datos del producto o eliminarlo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Obtener datos del formulario
    $new_name = isset($_POST['new_name']) ? $_POST['new_name'] : null;
    $new_price = isset($_POST['new_price']) ? floatval($_POST['new_price']) : null;
    $new_stock = isset($_POST['new_stock']) ? intval($_POST['new_stock']) : null;
    $new_image = isset($_FILES['new_image']) ? $_FILES['new_image'] : null; // Capturar la imagen

    // Verificar que al menos un campo nuevo esté establecido
    if ($new_name !== null || $new_price !== null || $new_stock !== null || ($new_image && $new_image['error'] === UPLOAD_ERR_OK)) {
        $update_fields = [];
        $update_values = [];

        if ($new_name !== null) {
            $update_fields[] = "name = ?";
            $update_values[] = $new_name;
        }
        if ($new_price !== null) {
            $update_fields[] = "price = ?";
            $update_values[] = $new_price;
        }
        if ($new_stock !== null) {
            $update_fields[] = "stock = ?";
            $update_values[] = $new_stock;
        }

        // Manejo de la carga de imagen
        if ($new_image && $new_image['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/"; 
            $target_file = $target_dir . basename($new_image["name"]); 
            move_uploaded_file($new_image["tmp_name"], $target_file); 
            $update_fields[] = "image_path = ?"; 
            $update_values[] = $target_file; 
        }

        $update_values[] = $product_id;
        if (count($update_fields) > 0) {
            $stmt = $conexion->prepare("UPDATE products SET " . implode(", ", $update_fields) . " WHERE product_id = ?");
            $stmt->execute($update_values);
            echo "<script>alert('Producto actualizado correctamente.');</script>";
        }
    }

    // Eliminar producto
    if (isset($_POST['delete'])) {
        $stmt = $conexion->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        echo "<script>alert('Producto eliminado correctamente.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Stock de Productos</title>
    <link rel="stylesheet" href="./stylos/manage_products.css"> 
</head>

<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <h2>Gestionar Stock de Productos</h2>

    <!-- Formulario de búsqueda -->
    <form  method="GET" action="">
        <label for="search_name">Buscar por Nombre:</label>
        <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($searchName); ?>">
        <button type="submit">Buscar</button>
    </form>

    <div class="products-container">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Precio: $<?php echo number_format($product['price'], 2); ?></p>
                    <p>Stock actual: <?php echo $product['stock']; ?></p>

                    <!-- Mostrar imagen del producto -->
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 200px; object-fit: cover; margin-bottom: 10px;" class="product-image">
                    <?php endif; ?>

                    <!-- Formulario para editar los datos del producto y stock -->
                    <form method="POST" class="edit-form" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <label for="new_name">Nombre:</label>
                        <input type="text" name="new_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <label for="new_price">Precio:</label>
                        <input type="number" name="new_price" value="<?php echo number_format($product['price'], 2); ?>" min="0" step="0.01">
                        <label for="new_stock">Nuevo stock:</label>
                        <input type="number" name="new_stock" value="<?php echo $product['stock']; ?>" min="0">
                        <label for="new_image">Imagen:</label>
                        <input type="file" name="new_image" accept="image/*"> 
                        <button type="submit">Actualizar Datos y Stock</button>
                    </form>

                    <!-- Botón para eliminar el producto -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <button type="submit" name="delete" class="delete-button" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar Producto</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron productos que coincidan con los criterios de búsqueda.</p>
        <?php endif; ?>
    </div>
</body>

</html>
