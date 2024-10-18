<?php
session_start();
include('db.php');

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Inicializar variables para el filtrado
$searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : '';
$searchAmount = isset($_GET['search_amount']) ? $_GET['search_amount'] : '';
$searchUser = isset($_GET['search_user']) ? $_GET['search_user'] : '';

// Preparar la consulta SQL
$sql = "SELECT orders.order_id, orders.total_amount, orders.created_at, users.username 
        FROM orders 
        JOIN users ON orders.user_id = users.user_id";

// Agregar condiciones de búsqueda
$conditions = [];
if (!empty($searchDate)) {
    $conditions[] = "DATE(orders.created_at) = :searchDate";
}
if (!empty($searchAmount)) {
    $conditions[] = "orders.total_amount = :searchAmount";
}
if (!empty($searchUser)) {
    $conditions[] = "users.username LIKE :searchUser"; // Usamos LIKE para permitir búsquedas parciales
}
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY orders.created_at DESC";
$stmt = $conexion->prepare($sql);

// Vincular parámetros si existen
if (!empty($searchDate)) {
    $stmt->bindParam(':searchDate', $searchDate);
}
if (!empty($searchAmount)) {
    $stmt->bindParam(':searchAmount', $searchAmount);
}
if (!empty($searchUser)) {
    $searchUserParam = "%" . $searchUser . "%"; // Agregar comodines para búsquedas parciales
    $stmt->bindParam(':searchUser', $searchUserParam);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos Realizados</title>
    <link rel="stylesheet" href="./stylos/view_orders.css"> 
</head>
<body>
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <h2>Pedidos Realizados</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="">
        <label for="search_user">Buscar por Usuario:</label>
        <input type="text" id="search_user" name="search_user" value="<?php echo htmlspecialchars($searchUser); ?>">

        <label for="search_date">Buscar por Fecha:</label>
        <input type="date" id="search_date" name="search_date" value="<?php echo htmlspecialchars($searchDate); ?>">
        
        <button type="submit">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID del Pedido</th>
                <th>Usuario</th>
                <th>Monto Total</th>
                <th>Fecha</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                        <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>">Ver Detalles</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay pedidos realizados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
