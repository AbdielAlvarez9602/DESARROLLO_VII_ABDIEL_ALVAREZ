<?php

$ciudades = ["Nueva York", "Tokio", "Londres", "París", "Sídney", "Río de Janeiro", "Moscú", "Berlín", "Ciudad del Cabo", "Toronto"];

echo "Ciudades originales:\n";
print_r($ciudades);

array_push($ciudades, "Dubái", "Singapur");

array_splice($ciudades, 2, 1);

array_splice($ciudades, 4, 0, "Mumbai");

echo "\nCiudades modificadas:\n";
print_r($ciudades);

function imprimirCiudadesOrdenadas($arr) {
    $ordenado = $arr;
    sort($ordenado);
    echo "Ciudades en orden alfabético:\n";
    foreach ($ordenado as $ciudad) {
        echo "- $ciudad\n";
    }
}

imprimirCiudadesOrdenadas($ciudades);

function contarCiudadesPorInicial($arr, $letra) {
    $contador = 0;
    foreach ($arr as $ciudad) {
        if (strtoupper($ciudad[0]) == strtoupper($letra)) {
            $contador++;
        }
    }
    return $contador;
}

$letra = 'S';
echo "\nNúmero de ciudades que comienzan con la letra '$letra': " . contarCiudadesPorInicial($ciudades, $letra) . "\n";
?>
