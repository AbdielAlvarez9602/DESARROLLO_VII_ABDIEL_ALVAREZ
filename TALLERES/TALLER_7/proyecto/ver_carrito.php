<?php
include 'config_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu Carrito</title>
</head>
<body>
    <h2>Tu Carrito de Compras</h2>
    <p><a href="productos.php">Seguir Comprando</a></p>
    
    <?php if (empty($_SESSION['carrito'])): ?>
        <p>Tu carrito está vacío.</p>
    <?php else: ?>
        <table border="1" style="width: 70%;">
            <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
            <?php 
            $total_compra = 0;
            foreach ($_SESSION['carrito'] as $id => $item): 
                $subtotal = $item['precio'] * $item['cantidad'];
                $total_compra += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td> 
                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
                <td>
                    <a href="eliminar_del_carrito.php?id=<?php echo $id; ?>">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>$<?php echo number_format($total_compra, 2); ?></strong></td>
                <td></td>
            </tr>
        </table>
        <p><a href="checkout.php">Proceder al Pago (Checkout)</a></p>
    <?php endif; ?>
</body>
</html>