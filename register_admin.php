<?php
session_start();
include('db.php');

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Inicializar variables para mensajes
$error = ""; 
$success = ""; 

// Lógica para registrar un nuevo administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin'; 

    // Verificar si el usuario ya existe
    $stmt = $conexion->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "El usuario o el email ya están en uso. Por favor, elige otro.";
    } else {
        // Insertar nuevo administrador
        $stmt = $conexion->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role])) {
            $success = "Administrador creado con éxito"; 
            // Redirigir a panel de administración
            header("Location: admin.php?success=" . urlencode($success));
            exit();
        } else {
            $error = "Error en el registro";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Administrador</title>
    <link rel="stylesheet" href="./stylos/register_admin.css"> 
</head>
<body>

    <!-- Navbar -->
    <?php include('navbar.php'); ?>
    <h2>Registrar Nuevo Administrador</h2>
    
    <div class="container-form">
        
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Usuario:</label>
            <input type="text" name="username" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Contraseña:</label>
            <input type="password" name="password" required><br>
            <button type="submit" name="register_admin">Registrar</button>
        </form>
    </div>
</body>
</html>
