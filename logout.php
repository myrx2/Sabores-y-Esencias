<?php
session_start();
session_destroy(); // Destruye la sesión actual
header("Location: page.php"); // Redirige al usuario a la página de inicio
exit();
?>
