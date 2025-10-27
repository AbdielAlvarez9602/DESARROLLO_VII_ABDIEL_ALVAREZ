<?php
include 'config_sesion.php';

$resumen_compra = $_SESSION['carrito'];
$total_final = 0;

foreach ($resumen_compra as $item) {
    $total_final += $item['precio'] * $item['cantidad'];
}

$_SESSION['carrito'] = [];

$nombre_usuario = "Cliente_Invitado";

setcookie("nombre_usuario", $nombre_usuario, [
    'expires' => time() + 86400,
    'path' => '/',
    // 'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
    <h2>¡Gracias por tu compra!</h2>
    <p>Tu pedido ha sido procesado con éxito.</p>
    
    <h3>Resumen de la Compra</h3>
    
    <?php if (empty($resumen_compra)): ?>
        <p>No se pudo generar el resumen, el carrito estaba vacío.</p>
    <?php else: ?>
        <table border="1" style="width: 50%;">
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($resumen_compra as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" style="text-align: right;"><strong>Total Final:</strong></td>
                <td><strong>$<?php echo number_format($total_final, 2); ?></strong></td>
            </tr>
        </table>
    <?php endif; ?>
    
    <p>Hemos guardado tu nombre para saludarte la próxima vez. <a href="productos.php">Volver a la tienda</a></p>
</body>
</html>