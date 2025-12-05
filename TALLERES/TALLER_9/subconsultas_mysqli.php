<?php

require_once "config_mysqli.php";

echo "<h1>Resultados de Consultas Avanzadas (MySQLi)</h1>";
echo "<hr>";

$sql1 = "SELECT nombre, precio, stock 
         FROM productos 
         WHERE id NOT IN (SELECT DISTINCT producto_id FROM detalles_venta)";

$result1 = mysqli_query($conn, $sql1);

echo "<h3>1. Productos que nunca se han vendido:</h3>";
if ($result1 && mysqli_num_rows($result1) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result1)) {
        echo "<li><strong>{$row['nombre']}</strong> - Precio: ${$row['precio']} (Stock: {$row['stock']})</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Todos los productos han tenido al menos una venta.</p>";
}
echo "<hr>";

$sql2 = "SELECT c.nombre, 
         COUNT(p.id) as cantidad_productos, 
         COALESCE(SUM(p.precio * p.stock), 0) as valor_total_inventario
         FROM categorias c
         LEFT JOIN productos p ON c.id = p.categoria_id
         GROUP BY c.id";

$result2 = mysqli_query($conn, $sql2);

echo "<h3>2. Inventario por Categoría:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Categoría</th><th>Cant. Productos</th><th>Valor Inventario</th></tr>";

while ($row = mysqli_fetch_assoc($result2)) {
    echo "<tr>";
    echo "<td>{$row['nombre']}</td>";
    echo "<td style='text-align: center;'>{$row['cantidad_productos']}</td>";
    echo "<td>$" . number_format($row['valor_total_inventario'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<hr>";

$categoria_objetivo_id = 4; 

$sql3 = "SELECT c.nombre, c.email
         FROM clientes c
         JOIN ventas v ON c.id = v.cliente_id
         JOIN detalles_venta dv ON v.id = dv.venta_id
         JOIN productos p ON dv.producto_id = p.id
         WHERE p.categoria_id = $categoria_objetivo_id
         GROUP BY c.id
         HAVING COUNT(DISTINCT p.id) = (
             SELECT COUNT(*) FROM productos WHERE categoria_id = $categoria_objetivo_id
         )";

$result3 = mysqli_query($conn, $sql3);

echo "<h3>3. Clientes VIP (Compraron todos los productos de la categoría ID $categoria_objetivo_id):</h3>";
if ($result3 && mysqli_num_rows($result3) > 0) {
    while ($row = mysqli_fetch_assoc($result3)) {
        echo "<p><strong>Cliente:</strong> {$row['nombre']} ({$row['email']}) <br> <em>¡Ha completado la colección!</em></p>";
    }
} else {
    echo "<p>Ningún cliente ha comprado todos los productos de esta categoría.</p>";
}
echo "<hr>";

// ---------------------------------------------------------
// TAREA 4: Porcentaje de ventas de cada producto respecto al total
// ---------------------------------------------------------
// Calculamos la venta total del producto y la dividimos por la venta global (subconsulta)
$sql4 = "SELECT p.nombre, 
         SUM(dv.subtotal) as total_vendido_producto,
         (SELECT SUM(subtotal) FROM detalles_venta) as venta_global_total,
         (SUM(dv.subtotal) * 100 / (SELECT SUM(subtotal) FROM detalles_venta)) as porcentaje
         FROM productos p
         JOIN detalles_venta dv ON p.id = dv.producto_id
         GROUP BY p.id
         ORDER BY porcentaje DESC";

$result4 = mysqli_query($conn, $sql4);

echo "<h3>4. Rendimiento de Ventas por Producto:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Producto</th><th>Total Vendido ($)</th><th>% del Total Global</th></tr>";

while ($row = mysqli_fetch_assoc($result4)) {
    echo "<tr>";
    echo "<td>{$row['nombre']}</td>";
    echo "<td>$" . number_format($row['total_vendido_producto'], 2) . "</td>";
    echo "<td><strong>" . number_format($row['porcentaje'], 2) . "%</strong></td>";
    echo "</tr>";
}
echo "</table>";

// Cerrar conexión
mysqli_close($conn);
?>