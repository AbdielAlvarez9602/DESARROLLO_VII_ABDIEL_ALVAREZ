<?php
include 'funciones_tienda.php';

$productos = [
    'camisa' => 50,
    'pantalon' => 70,
    'zapatos' => 80,
    'calcetines' => 10,
    'gorra' => 25
];

$carrito = [
    'camisa' => 2,
    'pantalon' => 1,
    'zapatos' => 1,
    'calcetines' => 3,
    'gorra' => 0
];

$subtotal = 0;
foreach ($carrito as $producto => $cantidad) {
    if (isset($productos[$producto])) {
        $subtotal += $productos[$producto] * $cantidad;
    }
}

$descuento = calcular_descuento($subtotal);

$impuesto = aplicar_impuesto($subtotal);

$total = calcular_total($subtotal, $descuento, $impuesto);

echo "<h1>Resumen de la Compra</h1>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr><td><strong>Producto</strong></td><td><strong>Precio</strong></td><td><strong>Cantidad</strong></td><td><strong>Total</strong></td></tr>";

foreach ($carrito as $producto => $cantidad) {
    if ($cantidad > 0) {
        $precio = $productos[$producto];
        $total_producto = $precio * $cantidad;
        echo "<tr>";
        echo "<td>" . htmlspecialchars($producto) . "</td>";
        echo "<td>" . htmlspecialchars($precio) . "</td>";
        echo "<td>" . htmlspecialchars($cantidad) . "</td>";
        echo "<td>" . htmlspecialchars($total_producto) . "</td>";
        echo "</tr>";
    }
}

echo "<tr><td colspan='3'><strong>Subtotal</strong></td><td>" . htmlspecialchars($subtotal) . "</td></tr>";
echo "<tr><td colspan='3'><strong>Descuento Aplicado</strong></td><td>" . htmlspecialchars($descuento) . "</td></tr>";
echo "<tr><td colspan='3'><strong>Impuesto (7%)</strong></td><td>" . htmlspecialchars($impuesto) . "</td></tr>";
echo "<tr><td colspan='3'><strong>Total a Pagar</strong></td><td>" . htmlspecialchars($total) . "</td></tr>";
echo "</table>";
?>
