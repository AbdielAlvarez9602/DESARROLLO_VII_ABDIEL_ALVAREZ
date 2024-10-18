<?php
include 'config_sesion.php';

// Procesar el checkout solo si el carrito no está vacío
if (!empty($_SESSION['carrito'])) {
    // Aquí podrías procesar el pago y guardar la orden en una base de datos

    // Pedir el nombre del usuario si no está en la cookie
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre_usuario = filter_input(INPUT_POST, 'nombre_usuario', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($nombre_usuario) {
            // Guardar el nombre del usuario en una cookie por 24 horas
            setcookie('nombre_usuario', $nombre_usuario, time() + 86400, '/', '', false, true);
            $mensaje = "Gracias por tu compra, $nombre_usuario.";
            // Vaciar el carrito
            unset($_SESSION['carrito']);
        } else {
            $error = 'Por favor, ingresa tu nombre.';
        }
    }
} else {
    die('Tu carrito está vacío.');
}

// Obtener el nombre del usuario de la cookie si existe
$nombre_usuario = $_COOKIE['nombre_usuario'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
    <h1>Finalizar Compra</h1>
    <?php if (isset($mensaje)): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
        <p><a href="productos.php">Volver a la tienda</a></p>
    <?php else: ?>
        <?php if ($nombre_usuario): ?>
            <p>Gracias por regresar, <?php echo htmlspecialchars($nombre_usuario); ?>.</p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="checkout.php" method="post">
            <label for="nombre_usuario">Ingresa tu nombre para completar la compra:</label><br>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required><br><br>
            <input type="submit" value="Completar Compra">
        </form>
    <?php endif; ?>
</body>
</html>
