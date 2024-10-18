<?php
include 'config_sesion.php';

// Lista de productos (en un caso real, esto vendría de una base de datos)
$productos = [
    1 => ['nombre' => 'Producto 1', 'precio' => 10.00],
    2 => ['nombre' => 'Producto 2', 'precio' => 15.50],
    3 => ['nombre' => 'Producto 3', 'precio' => 7.99],
    4 => ['nombre' => 'Producto 4', 'precio' => 12.30],
    5 => ['nombre' => 'Producto 5', 'precio' => 20.00],
];

// Guardar la lista de productos en la sesión para acceder desde otros scripts
$_SESSION['productos'] = $productos;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
</head>
<body>
    <h1>Productos Disponibles</h1>
    <ul>
        <?php foreach ($productos as $id => $producto): ?>
            <li>
                <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong> - $<?php echo number_format($producto['precio'], 2); ?>
                <form action="agregar_al_carrito.php" method="post" style="display:inline;">
                    <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                    <input type="number" name="cantidad" value="1" min="1" required>
                    <input type="submit" value="Añadir al carrito">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <p><a href="ver_carrito.php">Ver Carrito</a></p>
</body>
</html>