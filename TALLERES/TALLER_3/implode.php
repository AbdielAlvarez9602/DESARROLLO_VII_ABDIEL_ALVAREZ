<?php
// Ejemplo de uso de implode()
$frutas = ["Manzana", "Naranja", "Plátano", "Uva"];
$frase = implode(", ", $frutas);

echo "Array de frutas:<br>";
print_r($frutas);
echo "<br>Frase creada: $frase<br>";

// Ejercicio: Crea un array con los nombres de 5 países que te gustaría visitar
$paises = ["Japón", "Italia", "Australia", "Canadá", "España"];
$listaPaises = implode("-", $paises);

echo "<br>Mi lista de países para visitar: $listaPaises<br>";

// Bonus: Usa implode() con un array asociativo
$persona = [
    "nombre" => "Juan",
    "edad" => 30,
    "ciudad" => "Madrid"
];

$valoresPersona = array_values($persona);
$infoPersona = implode(" | ", $valoresPersona);

echo "<br>Información de la persona: $infoPersona<br>";
?>

      
