<?php
function leerInventario($archivo) {
    if (!file_exists($archivo)) {
        echo "El archivo $archivo no existe.<br>";
        return [];
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true);
}

function ordenarInventarioPorNombre(&$inventario) {
    usort($inventario, function($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });
}

function mostrarResumen($inventario) {
    echo "<br>Resumen del Inventario:<br>";
    foreach ($inventario as $producto) {
        echo "- {$producto['nombre']} | Precio: $" . number_format($producto['precio'], 2) . " | Cantidad: {$producto['cantidad']}<br>";
    }
}

function calcularValorTotal($inventario) {
    return array_sum(array_map(function($producto) {
        return $producto['precio'] * $producto['cantidad'];
    }, $inventario));
}

function productosStockBajo($inventario) {
    return array_filter($inventario, function($producto) {
        return $producto['cantidad'] < 5;
    });
}

$archivo = "inventario.json";

$inventario = leerInventario($archivo);

ordenarInventarioPorNombre($inventario);

mostrarResumen($inventario);

$valorTotal = calcularValorTotal($inventario);
echo "<br>Valor total del inventario: $" . number_format($valorTotal, 2) . "<br>";

$stockBajo = productosStockBajo($inventario);
echo "<br>Productos con stock bajo (menos de 5 unidades):<br>";
if (count($stockBajo) > 0) {
    foreach ($stockBajo as $producto) {
        echo "- {$producto['nombre']} ({$producto['cantidad']} unidades)<br>";
    }
} else {
    echo "No hay productos con stock bajo.<br>";
}
?>