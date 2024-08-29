<?php
echo "Patrón de triángulo rectángulo:\r\n";
for ($i = 1; $i <= 5; $i++) {
    echo str_repeat("*", $i) . "\r\n";
}

echo "\r\n"; 

echo "Secuencia de números impares del 1 al 20:\r\n";
$num = 1;
while ($num <= 20) {
    if ($num % 2 != 0) { 
        echo $num . "\r\n";
    }
    $num++;
}

echo "\r\n";

echo "Contador regresivo desde 10 hasta 1 (saltando el número 5):\r\n";
$counter = 10;
do {
    if ($counter == 5) {
        $counter--;
        continue;
    }
    echo $counter . "\r\n";
    $counter--;
} while ($counter >= 1);

echo "\r\n"; 

?>
