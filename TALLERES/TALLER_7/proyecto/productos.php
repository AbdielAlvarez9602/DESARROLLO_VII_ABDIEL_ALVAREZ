<?php
include 'config_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
</head>
<body>
    <h2>Lista de Productos</h2>
    
    <?php
    if(isset($_COOKIE['nombre_usuario'])) {
        echo "<p>Bienvenido de vuelta, " . htmlspecialchars($_COOKIE['nombre_usuario']) . "!</p>"; 
    }
    ?>

    <p><a href="ver_carrito.php">Ver Carrito</a></p>
    
    <table border="1" style="width: 50%;">
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Acción</th>
        </tr>
        <?php foreach ($productos as $id => $producto): ?>
        <tr>
            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
            <td>
                <form method="post" action="agregar_al_carrito.php">
                    <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                    <input type="number" name="cantidad" value="1" min="1" required style="width: 50px;">
                    <input type="submit" value="Añadir">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>