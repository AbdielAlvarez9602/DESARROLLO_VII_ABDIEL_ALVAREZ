<?php

include 'funciones_gimnasio.php';

$membresias = [
    'basica' => 80,
    'premium' => 120,
    'vip' => 180,
    'familiar' => 250,
    'corporativa' => 300
];

$miembros = [
    'Juan Pérez' => ['tipo' => 'premium', 'antiguedad' => 15],
    'Ana García' => ['tipo' => 'basica', 'antiguedad' => 2],
    'Carlos López' => ['tipo' => 'vip', 'antiguedad' => 30],
    'María Rodríguez' => ['tipo' => 'familiar', 'antiguedad' => 8],
    'Luis Martínez' => ['tipo' => 'corporativa', 'antiguedad' => 18]
];

echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>Nombre</th>
        <th>Tipo de Membresía</th>
        <th>Antigüedad (meses)</th>
        <th>Cuota Base ($)</th>
        <th>Descuento (%)</th>
        <th>Monto Descuento ($)</th>
        <th>Seguro Médico ($)</th>
        <th>Cuota Final ($)</th>
      </tr>";

foreach ($miembros as $nombre => $info) {
    $tipo = $info['tipo'];
    $antiguedad = $info['antiguedad'];
    $cuota_base = $membresias[$tipo];

    $descuento_pct = calcular_promocion($antiguedad);
    $monto_descuento = $cuota_base * ($descuento_pct / 100);
    $seguro = calcular_seguro_medico($cuota_base);
    $cuota_final = calcular_cuota_final($cuota_base, $descuento_pct, $seguro);}

    echo "<tr>";
    echo "<td>$nombre</td>";
    echo "<td>" . ucfirst($tipo) . "</td>";
    echo "<td>$antiguedad</td>";
    echo "<td>$cuota_base</td>";
    echo// filepath: c:\laragon\www\PARCIALES\PARCIAL_1\gestionar_membresias.php
            "<td>$descuento_pct%</td>";
    echo "<td>$monto_descuento</td>";
    echo "<td>$seguro</td>";
    echo "<td>$cuota_final</td>";
    echo "</tr>";
}

echo "</table>";
?>