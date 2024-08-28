<?php
// 1. Patrón de triángulo rectángulo usando asteriscos (*)
echo "Patrón de triángulo rectángulo:\r\n";
for ($i = 1; $i <= 5; $i++) {
    echo str_repeat("*", $i) . "\r\n";
}

echo "\r\n"; // Separación entre secciones

// 2. Secuencia de números del 1 al 20, mostrando solo los números impares
echo "Secuencia de números impares del 1 al 20:\r\n";
$num = 1;
while ($num <= 20) {
    if ($num % 2 != 0) { // Si el número es impar
        echo $num . "\r\n";
    }
    $num++;
}

echo "\r\n"; // Separación entre secciones

// 3. Contador regresivo desde 10 hasta 1, saltando el número 5
echo "Contador regresivo desde 10 hasta 1 (saltando el número 5):\r\n";
$counter = 10;
do {
    if ($counter == 5) {
        $counter--; // Disminuir el contador y continuar el bucle sin mostrar 5
        continue;
    }
    echo $counter . "\r\n";
    $counter--;
} while ($counter >= 1);

echo "\r\n"; // Salto de línea final

?>
