<?php

function leerInventario($archivo) {
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true);
}

function ordenarInventarioPorNombre(&$inventario) {
    usort($inventario, function($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });
}

function mostrarResumenInventario($inventario) {
    echo "Resumen del Inventario:\n";
    foreach ($inventario as $producto) {
        echo "Nombre: " . $producto['nombre'] . ", Precio: $" . number_format($producto['precio'], 2) . ", Cantidad: " . $producto['cantidad'] . "\n";
    }
}

function calcularValorTotal($inventario) {
    $valores = array_map(function($producto) {
        return $producto['precio'] * $producto['cantidad'];
    }, $inventario);
    return array_sum($valores);
}

function informeStockBajo($inventario, $umbral = 5) {
    $productosBajos = array_filter($inventario, function($producto) use ($umbral) {
        return $producto['cantidad'] < $umbral;
    });
    return $productosBajos;
}

$archivo = 'inventario.json';

$inventario = leerInventario($archivo);

ordenarInventarioPorNombre($inventario);

mostrarResumenInventario($inventario);

$valorTotal = calcularValorTotal($inventario);
echo "\nValor total del inventario: $" . number_format($valorTotal, 2) . "\n";

$productosBajos = informeStockBajo($inventario);
echo "\nProductos con stock bajo:\n";
foreach ($productosBajos as $producto) {
    echo "Nombre: " . $producto['nombre'] . ", Cantidad: " . $producto['cantidad'] . "\n";
}
?>
