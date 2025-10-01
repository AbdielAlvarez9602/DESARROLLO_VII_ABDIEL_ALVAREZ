<?php
// 1. Crear un string JSON con datos de una tienda en línea
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

// 2. Convertir el JSON a un arreglo asociativo de PHP
$tiendaData = json_decode($jsonDatos, true);

// 3. Función para imprimir los productos
function imprimirProductos($productos) {
    foreach ($productos as $producto) {
        echo "{$producto['nombre']} - {$producto['precio']} - Categorías: " . implode(", ", $producto['categorias']) . "\n";
    }
}

echo "Productos de {$tiendaData['tienda']}:\n";
imprimirProductos($tiendaData['productos']);

// 4. Calcular el valor total del inventario
$valorTotal = array_reduce($tiendaData['productos'], function($total, $producto) {
    return $total + $producto['precio'];
}, 0);

echo "\nValor total del inventario: $$valorTotal\n";

// 5. Encontrar el producto más caro
$productoMasCaro = array_reduce($tiendaData['productos'], function($max, $producto) {
    return ($producto['precio'] > $max['precio']) ? $producto : $max;
}, $tiendaData['productos'][0]);

echo "\nProducto más caro: {$productoMasCaro['nombre']} ({$productoMasCaro['precio']})\n";

// 6. Filtrar productos por categoría
function filtrarPorCategoria($productos, $categoria) {
    return array_filter($productos, function($producto) use ($categoria) {
        return in_array($categoria, $producto['categorias']);
    });
}

$productosDeComputadoras = filtrarPorCategoria($tiendaData['productos'], "computadoras");
echo "\nProductos en la categoría 'computadoras':\n";
imprimirProductos($productosDeComputadoras);

// 7. Agregar un nuevo producto
$nuevoProducto = [
    "id" => 6,
    "nombre" => "Smartwatch",
    "precio" => 250,
    "categorias" => ["electrónica", "accesorios", "wearables"]
];
$tiendaData['productos'][] = $nuevoProducto;

// 8. Convertir el arreglo actualizado de vuelta a JSON
$jsonActualizado = json_encode($tiendaData, JSON_PRETTY_PRINT);
echo "\nDatos actualizados de la tienda (JSON):\n$jsonActualizado\n";

// TAREA: Implementa una función que genere un resumen de ventas
// Crea un arreglo de ventas (producto_id, cliente_id, cantidad, fecha)
// y genera un informe que muestre:
// - Total de ventas
// - Producto más vendido
// - Cliente que más ha comprado
// Tu código aquí

// Arreglo de ventas simuladas
$ventas = [
    ["producto_id" => 1, "cliente_id" => 101, "cantidad" => 1, "fecha" => "2023-10-01"], 
    ["producto_id" => 2, "cliente_id" => 102, "cantidad" => 2, "fecha" => "2023-10-02"], 
    ["producto_id" => 3, "cliente_id" => 101, "cantidad" => 5, "fecha" => "2023-10-03"], 
    ["producto_id" => 4, "cliente_id" => 103, "cantidad" => 1, "fecha" => "2023-10-04"], 
    ["producto_id" => 2, "cliente_id" => 103, "cantidad" => 1, "fecha" => "2023-10-05"], 
    ["producto_id" => 6, "cliente_id" => 102, "cantidad" => 3, "fecha" => "2023-10-06"],
    ["producto_id" => 1, "cliente_id" => 103, "cantidad" => 2, "fecha" => "2023-10-07"], 
];

function generarResumenVentas($tiendaData, $ventas) {

    $productos = array_column($tiendaData['productos'], null, 'id'); 
    $clientes = array_column($tiendaData['clientes'], null, 'id'); 
    $resumen = array_reduce($ventas, function($carry, $venta) use ($productos) {
        $producto = $productos[$venta['producto_id']] ?? null;
        
        if ($producto) {
            $costo_venta = $producto['precio'] * $venta['cantidad'];
            
            $carry['total_ventas'] += $costo_venta;

            $prod_id = $venta['producto_id'];
            $carry['conteo_productos'][$prod_id] = 
                ($carry['conteo_productos'][$prod_id] ?? 0) + $venta['cantidad'];

            $clie_id = $venta['cliente_id'];
            $carry['gasto_clientes'][$clie_id] = 
                ($carry['gasto_clientes'][$clie_id] ?? 0) + $costo_venta;
        }
        return $carry;
    }, [
        'total_ventas' => 0,
        'conteo_productos' => [], 
        'gasto_clientes' => [] 
    ]);

    $producto_mas_vendido_id = null;
    $max_cantidad = 0;
    if (!empty($resumen['conteo_productos'])) {
        $max_cantidad = max($resumen['conteo_productos']);
        $producto_mas_vendido_id = array_keys($resumen['conteo_productos'], $max_cantidad)[0];
    }
    
    $cliente_top_id = null;
    $max_gasto = 0;
    if (!empty($resumen['gasto_clientes'])) {
        $max_gasto = max($resumen['gasto_clientes']);
        $cliente_top_id = array_keys($resumen['gasto_clientes'], $max_gasto)[0];
    }

    $reporteFinal = [
        "Total de ventas" => "\$" . number_format($resumen['total_ventas'], 2),
        "Producto más vendido" => [
            "Nombre" => $productos[$producto_mas_vendido_id]['nombre'] ?? 'N/A',
            "Cantidad Vendida" => $max_cantidad
        ],
        "Cliente que más ha comprado" => [
            "Nombre" => $clientes[$cliente_top_id]['nombre'] ?? 'N/A',
            "Gasto Total" => "\$" . number_format($max_gasto, 2)
        ]
    ];

    return $reporteFinal;
}

echo "\n--- Resumen de Ventas Generado ---\n";
$reporteVentas = generarResumenVentas($tiendaData, $ventas);
print_r($reporteVentas);

?>