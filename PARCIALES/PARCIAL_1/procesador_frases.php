<?php
include 'operaciones_cadena.php';

$frases = [
    "tres por tres es nueve",
    "El sol brilla en el cielo azul",
    "Este parcial esta bien dificil",
    "La vida es bella y corta"
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Frase original</th><th>Capitalizada</th><th>Conteo de palabras</th></tr>";

foreach ($frases as $frase) {
    $capitalizada = capitalizar_palabras($frase);
    $conteo = contar_palabras_repetidas($frase);
    $conteo_str = "";
    foreach ($conteo as $palabra => $cantidad) {
        $conteo_str .= "$palabra: $cantidad<br>";
    }

    echo "<tr>";
    echo "<td>$frase</td>";
    echo "<td>$capitalizada</td>";
    echo "<td>$conteo_str</td>";
    echo "</tr>";
}

echo "</table>";
?>