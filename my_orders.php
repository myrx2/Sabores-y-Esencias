<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inicializar la variable de filtro de fecha
$searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : '';

// Modificar la consulta para agregar el filtro de fecha
$sql = "SELECT * FROM orders WHERE user_id = ?";

// Si se ha seleccionado una fecha, agregarla a la consulta
if (!empty($searchDate)) {
    $sql .= " AND DATE(created_at) = :searchDate";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $conexion->prepare($sql);

// Asignar parámetros de búsqueda
$params = [$_SESSION['user_id']];
if (!empty($searchDate)) {
    $stmt->bindParam(':searchDate', $searchDate);
}

// Ejecutar la consulta
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <link rel="stylesheet" href="./stylos/my_orders.css">
</head>

<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Mis Pedidos</h1>

        <!-- Formulario de búsqueda por fecha -->
        <form method="GET" action="">
            <label for="search_date">Filtrar por Fecha:</label>
            <input type="date" id="search_date" name="search_date" value="<?php echo htmlspecialchars($searchDate); ?>">
            <button type="submit">Buscar</button>
        </form>

        <?php if (empty($orders)): ?>
            <p>No tienes pedidos realizados en la fecha seleccionada.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Compra n°</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_orders = count($orders); // Total de pedidos para usar en la numeración
                    ?>
                    <?php foreach ($orders as $index => $order): ?>
                        <tr>
                            <td><?php echo $total_orders - $index; ?></td> <!-- Mostrar número de compra en orden descendente -->
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                            <td><a href="details_myorders.php?order_id=<?php echo $order['order_id']; ?>" class="details-link">Ver Detalles</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>