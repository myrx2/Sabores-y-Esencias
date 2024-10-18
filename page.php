<?php
session_start();
include('db.php');

// Obtener productos de la base de datos
$stmt = $conexion->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener comentarios de la base de datos
$stmt_comments = $conexion->prepare("SELECT c.*, u.username FROM comentarios c JOIN users u ON c.user_id = u.user_id ORDER BY fecha DESC");
$stmt_comments->execute();
$comentarios = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante - Sistema de Gestión de Pedidos</title>
    <link rel="stylesheet" href="./stylos/page.css">
</head>
<body>

    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Banner -->
    <div class="banner">
        <img src="./images/tabla1.jpg" alt="Deliciosos Platos" class="banner-image">
        <div class="banner-text">
            <h1>Bienvenido a Nuestro Restaurante</h1>
            <p>Disfruta de una experiencia gastronómica única.</p>
            <a href="#productos" class="btn-banner">Ver Menú</a>
        </div>
    </div>

    <!-- Información sobre el restaurante -->
    <div class="content">
        <h1>Sobre Nosotros</h1>
        <p>
            Nuestro restaurante ofrece una amplia variedad de platos que combinan los sabores tradicionales con un toque moderno. 
            Nos enorgullece utilizar ingredientes frescos y de alta calidad para ofrecerte una experiencia culinaria excepcional.
        </p>
        <p>
            Ya sea que estés buscando un lugar para una cena romántica, una reunión familiar o simplemente quieras disfrutar de una comida deliciosa, 
            ¡estamos aquí para ti!
        </p>
    </div>

    <!-- Sucursales -->
    <div class="branches-section">
        <h2>Nuestras Sucursales</h2>
        <div class="branches-container">
            <div class="branch">
                <h3>Sucursal 1</h3>
                <p>Dirección: Av. Siempre Viva 123, Ciudad, País</p>
                <p>Teléfono: (123) 456-7890</p>
            </div>
            <div class="branch">
                <h3>Sucursal 2</h3>
                <p>Dirección: Calle Falsa 456, Ciudad, País</p>
                <p>Teléfono: (234) 567-8901</p>
            </div>
            <div class="branch">
                <h3>Sucursal 3</h3>
                <p>Dirección: Av. Secundaria 789, Ciudad, País</p>
                <p>Teléfono: (345) 678-9012</p>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="products-section" id="productos">
        <h2>Nuestros Productos</h2>
        <div class="products-container">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         style="width: 100%; height: 200px; object-fit: cover; margin-bottom: 10px;" 
                         class="product-image" 
                         onclick="<?php echo isset($_SESSION['user_id']) ? 'window.location.href=\'products.php\';' : 'showModal();'; ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Precio: $<?php echo number_format($product['price'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sección de Comentarios -->
    <div class="comments-section">
        <h2>Comentarios de Nuestros Clientes</h2>
        <div class="comments-container">
            <?php foreach ($comentarios as $comentario): ?>
                <div class="comment-card">
                    <div class="comment-header">
                        <strong><?php echo htmlspecialchars($comentario['username']); ?></strong>
                        <span class="comment-date">(<?php echo htmlspecialchars($comentario['fecha']); ?>)</span>
                    </div>
                    <div class="comment-body">
                        <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sección de contacto -->
    <div class="contact-section">
        <h2>Contacto</h2>
        <p>Si tienes alguna pregunta o deseas hacer una reservación, no dudes en contactarnos:</p>
        <p>Email: contacto@restaurante.com</p>
        <p>Teléfono: (123) 456-7890</p>
    </div>

    <!-- Modal -->
    <div class="modal" id="login-modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2>Inicia Sesión</h2>
            <p>Debes iniciar sesión para agregar productos al carrito.</p>
            <a href="login.php" class="btn-banner">Iniciar Sesión</a>
        </div>
    </div>

    <!-- Footer (opcional) -->
    <footer>
        <p>&copy; 2024 Restaurante. Todos los derechos reservados.</p>
    </footer>

    <script>
        function showModal() {
            document.getElementById('login-modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('login-modal').style.display = 'none';
        }

        // Cierra el modal si el usuario hace clic fuera de él
        window.onclick = function(event) {
            if (event.target === document.getElementById('login-modal')) {
                closeModal();
            }
        };
        
        // Función para agregar productos al carrito
        function addToCart(productId) {
            console.log("Producto " + productId + " agregado al carrito.");
        }
    </script>

</body>
</html>

