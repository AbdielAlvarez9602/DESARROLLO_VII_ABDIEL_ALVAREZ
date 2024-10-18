<?php
include 'config_sesion.php';

$productos = $_SESSION['productos'] ?? [];
$carrito = $_SESSION['carrito'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
</head>
<body>
    <h1>Tu Carrito</h1>
    <?php if (!empty($carrito)): ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
            <?php
            $total = 0;
            foreach ($carrito as $id => $cantidad):
                $producto = $productos[$id];
                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo $cantidad; ?></td>
                    <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <form action="eliminar_del_carrito.php" method="post" style="display:inline;">
                            <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                            <input type="submit" value="Eliminar">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td colspan="2">$<?php echo number_format($total, 2); ?></td>
            </tr>
        </table>
        <p><a href="checkout.php">Proceder al Pago</a></p>
    <?php else: ?>
        <p>Tu carrito está vacío.</p>
    <?php endif; ?>
    <p><a href="productos.php">Continuar Comprando</a></p>
</body>
</html>