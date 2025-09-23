<?php
// 1. Crear un arreglo de 10 nombres de ciudades
$ciudades = ["Nueva York", "Tokio", "Londres", "París", "Sídney", "Río de Janeiro", "Moscú", "Berlín", "Ciudad del Cabo", "Toronto"];

// 2. Imprimir el arreglo original
echo "Ciudades originales:\n";
print_r($ciudades);

// 3. Agregar 2 ciudades más al final del arreglo
array_push($ciudades, "Dubái", "Singapur");

// 4. Eliminar la tercera ciudad del arreglo
array_splice($ciudades, 2, 1);

// 5. Insertar una nueva ciudad en la quinta posición
array_splice($ciudades, 4, 0, "Mumbai");

// 6. Imprimir el arreglo modificado
echo "\nCiudades modificadas:\n";
print_r($ciudades);

// 7. Crear una función que imprima las ciudades en orden alfabético
function imprimirCiudadesOrdenadas($arr) {
    $ordenado = $arr;
    sort($ordenado);
    echo "Ciudades en orden alfabético:\n";
    foreach ($ordenado as $ciudad) {
        echo "- $ciudad\n";
    }
}

// 8. Llamar a la función
imprimirCiudadesOrdenadas($ciudades);


function contarCiudadesPorInicial($arr, $letra) {
    $contador = 0;
    foreach ($arr as $ciudad) {
        if (strpos(strtolower($ciudad), strtolower($letra)) === 0) {
            $contador++;
        }
    }
    return $contador;
}

echo "\nEl número de ciudades que comienzan con 'S' es: " . contarCiudadesPorInicial($ciudades, 'S') . "\n";
echo "El número de ciudades que comienzan con 'M' es: " . contarCiudadesPorInicial($ciudades, 'M') . "\n";

?>