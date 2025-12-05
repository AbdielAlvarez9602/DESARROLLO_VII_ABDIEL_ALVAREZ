<?php
require_once "config_pdo.php";

try {
    echo "<h1>Resultados de Consultas Avanzadas (PDO)</h1>";
    echo "<hr>";

    $sql1 = "SELECT nombre, precio, stock 
             FROM productos 
             WHERE id NOT IN (SELECT DISTINCT producto_id FROM detalles_venta)";
    
    $stmt1 = $pdo->query($sql1);

    echo "<h3>1. Productos que nunca se han vendido:</h3>";
    echo "<ul>";
    $productos_sin_venta = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($productos_sin_venta) > 0) {
        foreach ($productos_sin_venta as $row) {
            echo "<li><strong>{$row['nombre']}</strong> - Precio: ${$row['precio']}</li>";
        }
    } else {
        echo "<li>Todos los productos se han vendido alguna vez.</li>";
    }
    echo "</ul>";
    echo "<hr>";

    $sql2 = "SELECT c.nombre, 
             COUNT(p.id) as cantidad_productos, 
             COALESCE(SUM(p.precio * p.stock), 0) as valor_total_inventario
             FROM categorias c
             LEFT JOIN productos p ON c.id = p.categoria_id
             GROUP BY c.id";

    $stmt2 = $pdo->query($sql2);

    echo "<h3>2. Inventario por Categoría:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Categoría</th><th>Items</th><th>Valor ($)</th></tr>";

    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td style='text-align:center'>{$row['cantidad_productos']}</td>";
        echo "<td>$" . number_format($row['valor_total_inventario'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<hr>";

    $cat_id = 4;

    $sql3 = "SELECT c.nombre, c.email
             FROM clientes c
             JOIN ventas v ON c.id = v.cliente_id
             JOIN detalles_venta dv ON v.id = dv.venta_id
             JOIN productos p ON dv.producto_id = p.id
             WHERE p.categoria_id = :cat_id
             GROUP BY c.id
             HAVING COUNT(DISTINCT p.id) = (
                 SELECT COUNT(*) FROM productos WHERE categoria_id = :cat_id2
             )";
    
    $stmt3 = $pdo->prepare($sql3);
    $stmt3->execute([':cat_id' => $cat_id, ':cat_id2' => $cat_id]);

    echo "<h3>3. Clientes que compraron toda la categoría ID $cat_id:</h3>";
    if ($stmt3->rowCount() > 0) {
        while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            echo "<div style='background-color:#eef; padding:10px; border-left: 5px solid blue; margin-bottom:5px;'>";
            echo "<strong>{$row['nombre']}</strong> ({$row['email']})";
            echo "</div>";
        }
    } else {
        echo "<p>Nadie posee la colección completa.</p>";
    }
    echo "<hr>";

    $sql4 = "SELECT p.nombre, 
             SUM(dv.subtotal) as total_vendido_producto,
             (SELECT SUM(subtotal) FROM detalles_venta) as venta_global_total,
             (SUM(dv.subtotal) * 100 / (SELECT SUM(subtotal) FROM detalles_venta)) as porcentaje
             FROM productos p
             JOIN detalles_venta dv ON p.id = dv.producto_id
             GROUP BY p.id
             ORDER BY porcentaje DESC";

    $stmt4 = $pdo->query($sql4);

    echo "<h3>4. Estadísticas de Ventas (Porcentaje):</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Producto</th><th>Total ($)</th><th>Participación (%)</th></tr>";

    while ($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>$" . number_format($row['total_vendido_producto'], 2) . "</td>";
        
        $width = $row['porcentaje'] * 2; 
        echo "<td>
                <div style='background-color:#ddd; width:100px; height:10px; display:inline-block;'>
                    <div style='background-color:green; width:{$row['porcentaje']}px; height:10px;'></div>
                </div> 
                " . number_format($row['porcentaje'], 1) . "%
              </td>";
        echo "</tr>";
    }
    echo "</table>";

} catch(PDOException $e) {
    echo "Error de Base de Datos: " . $e->getMessage();
}

$pdo = null;
?>