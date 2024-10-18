<?php
session_start();
include('db.php'); // Asegúrate de incluir tu archivo de conexión a la base de datos

// Procesar el formulario de envío de comentarios cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    // Si se envía un nuevo comentario
    if (isset($_POST['comentario'])) {
        $user_id = $_SESSION['user_id']; // ID del usuario que deja el comentario
        $comentario = trim($_POST['comentario']); // Obtener el comentario y eliminar espacios

        // Validar que el comentario no esté vacío
        if (!empty($comentario)) {
            // Insertar el comentario en la base de datos
            $stmt = $conexion->prepare("INSERT INTO comentarios (user_id, comentario) VALUES (:user_id, :comentario)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':comentario', $comentario);

            if ($stmt->execute()) {
                $success_message = "Comentario enviado con éxito.";
            } else {
                $error_message = "Error al enviar el comentario. Inténtalo de nuevo.";
            }
        } else {
            $error_message = "Por favor, escribe un comentario.";
        }
    }

    // Procesar el formulario de eliminación cuando se envíe
    if (isset($_POST['delete_comentario'])) {
        $comentario_id = $_POST['comentario_id']; // ID del comentario a eliminar

        // Verifica si el usuario tiene permiso para eliminar el comentario
        $stmt = $conexion->prepare("DELETE FROM comentarios WHERE id = :comentario_id AND user_id = :user_id");
        $stmt->bindParam(':comentario_id', $comentario_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success_message = "Comentario eliminado con éxito.";
        } else {
            $error_message = "Error al eliminar el comentario. Inténtalo de nuevo.";
        }
    }
}

// Obtener solo los comentarios del usuario actual
$stmt = $conexion->prepare("SELECT c.*, u.username FROM comentarios c JOIN users u ON c.user_id = u.user_id WHERE c.user_id = :user_id ORDER BY fecha DESC");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios</title>
    <link rel="stylesheet" href="./stylos/comentarios.css">
</head>
<body>
     <!-- Navbar -->
     <?php include('navbar.php'); ?>

    <!-- Mensajes de éxito o error -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <h1>Deja tu Comentario</h1>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" action="">
            <textarea name="comentario" placeholder="Escribe tu comentario aquí..." required></textarea>
            <button type="submit">Enviar Comentario</button>
        </form>
    <?php else: ?>
        <p>Debes <a href="login.php">iniciar sesión</a> para dejar un comentario.</p>
    <?php endif; ?>

    <h2>Comentarios Recientes</h2>
    <div class="comentarios-list">
        <?php if (count($comentarios) > 0): ?>
            <?php foreach ($comentarios as $comentario): ?>
                <div class="comentario">
                    <p><strong><?php echo htmlspecialchars($comentario['username']); ?></strong> (<?php echo htmlspecialchars($comentario['fecha']); ?>):</p>
                    <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                    <?php if ($comentario['user_id'] == $_SESSION['user_id']): // Solo muestra el botón de eliminar si el usuario es el autor del comentario ?>
                        <form method="POST" action="">
                            <input type="hidden" name="comentario_id" value="<?php echo htmlspecialchars($comentario['id']); ?>">
                            <button type="submit" name="delete_comentario">Eliminar Comentario</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay comentarios disponibles.</p>
        <?php endif; ?>
    </div>

</body>
</html>
