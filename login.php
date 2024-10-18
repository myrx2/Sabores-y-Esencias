<?php
// Iniciar la sesión para poder almacenar información del usuario
session_start();
include('db.php');

$error = ""; // Variable para almacenar mensajes de error

// Procesar el formulario de inicio de sesión cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener las credenciales del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparar y ejecutar la consulta para buscar al usuario
    $stmt = $conexion->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y si la contraseña es correcta
    if ($user && password_verify($password, $user['password'])) {
        // Almacenar datos del usuario en la sesión
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php"); 
        exit(); 
    } else {
        $error = "Usuario o contraseña incorrectos."; 
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Para asegurar que sea responsivo -->
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="./stylos/login.css"> 
</head>
<body>
    <div class="login-container"> 
        <h2>Iniciar Sesión</h2>
        <!-- Mostrar mensaje de error si existe -->
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <!-- Formulario para iniciar sesión -->
        <form method="POST">
            <label>Usuario:</label>
            <input type="text" name="username" required> 
            <label>Contraseña:</label>
            <input type="password" name="password" required> 
            <button type="submit">Iniciar Sesión</button> 
        </form>
        <div class="navigation-buttons"> 
            <a href="register.php">¿No tienes una cuenta? Regístrate aquí</a> 
            <a href="page.php" class="button">Volver al Inicio</a> 
        </div>
    </div>
</body>
</html>

