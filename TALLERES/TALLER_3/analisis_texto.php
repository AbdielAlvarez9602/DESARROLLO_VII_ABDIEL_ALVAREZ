<?php

include 'utilidades_texto.php';

$frases = [
    "Mejor tarde que nunca",
    "A mal tiempo buena cara",
    "No dejes para manana lo que puedes hacer hoy"
];

echo "<table>";
echo "<tr><td>Frase</td><td>Número de Palabras</td><td>Número de Vocales</td><td>Palabras Invertidas</td></tr>";

foreach ($frases as $frase) {
    $num_palabras = contar_palabras($frase);
    $num_vocales = contar_vocales($frase);
    $frase_invertida = invertir_palabras($frase);

    echo "<tr>";
    echo "<td>" . htmlspecialchars($frase) . "</td>";
    echo "<td>" . htmlspecialchars($num_palabras) . "</td>";
    echo "<td>" . htmlspecialchars($num_vocales) . "</td>";
    echo "<td>" . htmlspecialchars($frase_invertida) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
