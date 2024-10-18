<!-- navbar.php -->
<link rel="stylesheet" href="./stylos/navbar.css"> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2c2c2c;">
    <div class="container-fluid">
        <!-- Logotipo -->
        <a class="navbar-brand logo" href="page.php">🍽️ Sabores & Esencias</a>
        
        <!-- Botón de menú para pantallas pequeñas -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Enlaces de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Si el usuario no ha iniciado sesión -->
                <?php if (!isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                <?php else: ?>
                    <!-- Si el usuario es administrador -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="register_admin.php">Registrar Nuevo Administrador</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_orders.php">Ver Pedidos Realizados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_product.php">Agregar Producto</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_stock.php">Gestionar Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <!-- Opciones disponibles para usuarios normales -->
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">Lista de Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">Carrito de Compras</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_orders.php">Pedidos Realizados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="comentarios.php">Comentarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
