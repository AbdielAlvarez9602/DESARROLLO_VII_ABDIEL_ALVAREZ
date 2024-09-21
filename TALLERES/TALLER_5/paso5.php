<?php

$jsonDatos = '
{
    "tienda": "ElectroTech",
    "productos": [
        {"id": 1, "nombre": "Laptop Gamer", "precio": 1200, "categorias": ["electrónica", "computadoras"]},
        {"id": 2, "nombre": "Smartphone 5G", "precio": 800, "categorias": ["electrónica", "celulares"]},
        {"id": 3, "nombre": "Auriculares Bluetooth", "precio": 150, "categorias": ["electrónica", "accesorios"]},
        {"id": 4, "nombre": "Smart TV 4K", "precio": 700, "categorias": ["electrónica", "televisores"]},
        {"id": 5, "nombre": "Tablet", "precio": 300, "categorias": ["electrónica", "computadoras"]}
    ],
    "clientes": [
        {"id": 101, "nombre": "Ana López", "email": "ana@example.com"},
        {"id": 102, "nombre": "Carlos Gómez", "email": "carlos@example.com"},
        {"id": 103, "nombre": "María Rodríguez", "email": "maria@example.com"}
    ]
}
';

$tiendaData = json_decode($jsonDatos, true);

function imprimirProductos($productos) {
    foreach ($productos as $producto) {
        echo "{$producto['nombre']} - \${$producto['precio']} - Categorías: " . implode(", ", $producto['categorias']) . "\n";
    }
}

echo "Productos de {$tiendaData['tienda']}:\n";
imprimirProductos($tiendaData['productos']);

$valorTotal = array_reduce($tiendaData['productos'], function($total, $producto) {
    return $total + $producto['precio'];
}, 0);

echo "\nValor total del inventario: \${$valorTotal}\n";

$productoMasCaro = array_reduce($tiendaData['productos'], function($max, $producto) {
    return ($producto['precio'] > $max['precio']) ? $producto : $max;
}, $tiendaData['productos'][0]);

echo "\nProducto más caro: {$productoMasCaro['nombre']} (\${$productoMasCaro['precio']})\n";

function filtrarPorCategoria($productos, $categoria) {
    return array_filter($productos, function($producto) use ($categoria) {
        return in_array($categoria, $producto['categorias']);
    });
}

$productosDeComputadoras = filtrarPorCategoria($tiendaData['productos'], "computadoras");
echo "\nProductos en la categoría 'computadoras':\n";
imprimirProductos($productosDeComputadoras);

$nuevoProducto = [
    "id" => 6,
    "nombre" => "Smartwatch",
    "precio" => 250,
    "categorias" => ["electrónica", "accesorios", "wearables"]
];
$tiendaData['productos'][] = $nuevoProducto;

$jsonActualizado = json_encode($tiendaData, JSON_PRETTY_PRINT);
echo "\nDatos actualizados de la tienda (JSON):\n$jsonActualizado\n";

function generarResumenVentas($tiendaData) {
    $ventas = [
        ["producto_id" => 1, "cliente_id" => 101, "cantidad" => 1, "fecha" => "2024-09-10"],
        ["producto_id" => 2, "cliente_id" => 102, "cantidad" => 2, "fecha" => "2024-09-11"],
        ["producto_id" => 3, "cliente_id" => 103, "cantidad" => 1, "fecha" => "2024-09-12"],
        ["producto_id" => 2, "cliente_id" => 101, "cantidad" => 1, "fecha" => "2024-09-13"],
        ["producto_id" => 5, "cliente_id" => 102, "cantidad" => 3, "fecha" => "2024-09-14"]
    ];

    $totalVentas = 0;
    $productosVendidos = [];
    $clientesCompra = [];

    foreach ($ventas as $venta) {
        $productoId = $venta['producto_id'];
        $clienteId = $venta['cliente_id'];
        $cantidad = $venta['cantidad'];

        $producto = array_filter($tiendaData['productos'], fn($p) => $p['id'] == $productoId);
        $producto = reset($producto); 
        $totalVentas += $producto['precio'] * $cantidad;

        if (!isset($productosVendidos[$productoId])) {
            $productosVendidos[$productoId] = 0;
        }
        $productosVendidos[$productoId] += $cantidad;

        if (!isset($clientesCompra[$clienteId])) {
            $clientesCompra[$clienteId] = 0;
        }
        $clientesCompra[$clienteId] += $cantidad;
    }

    $productoMasVendidoId = array_search(max($productosVendidos), $productosVendidos);
    $productoMasVendido = array_filter($tiendaData['productos'], fn($p) => $p['id'] == $productoMasVendidoId);
    $productoMasVendido = reset($productoMasVendido);

    $clienteMayorCompradorId = array_search(max($clientesCompra), $clientesCompra);
    $clienteMayorComprador = array_filter($tiendaData['clientes'], fn($c) => $c['id'] == $clienteMayorCompradorId);
    $clienteMayorComprador = reset($clienteMayorComprador);

    return [
        "total_ventas" => $totalVentas,
        "producto_mas_vendido" => $productoMasVendido['nombre'],
        "cliente_mayor_comprador" => $clienteMayorComprador['nombre']
    ];
}

$resumenVentas = generarResumenVentas($tiendaData);
echo "\nResumen de ventas:\n";
echo "Total de ventas: $" . $resumenVentas['total_ventas'] . "\n";
echo "Producto más vendido: " . $resumenVentas['producto_mas_vendido'] . "\n";
echo "Cliente que más ha comprado: " . $resumenVentas['cliente_mayor_comprador'] . "\n";

?>