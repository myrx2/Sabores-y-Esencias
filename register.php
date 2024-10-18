<?php
session_start();
include('db.php');

$error = ""; // Variable para almacenar mensajes de error
$success = ""; // Variable para almacenar mensajes de éxito

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = 'user'; // Solo se registran usuarios

    // Verificar si el usuario ya existe
    $stmt = $conexion->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "El usuario o el email ya están en uso. Por favor, elige otro.";
    } else {
        // Insertar nuevo usuario
        $stmt = $conexion->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role])) {
            $success = "Usuario creado con éxito"; // Mensaje de éxito
            // Redirigir a inicio de sesión
            header("Location: login.php?success=" . urlencode($success));
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
    <title>Registro</title>
    <link rel="stylesheet" href="./stylos/register.css"> 
</head>
<body>
    <form method="POST">
        <h2>Registro</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <label>Usuario:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Contraseña:</label>
        <input type="password" name="password" required>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>

