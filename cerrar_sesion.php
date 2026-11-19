
<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Volver a la página principal
header("Location: index.php");
exit();
?>
