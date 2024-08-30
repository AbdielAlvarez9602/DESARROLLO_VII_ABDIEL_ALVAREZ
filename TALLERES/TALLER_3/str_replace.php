<?php
// Ejemplo de uso de str_replace()
$frase = "El gato negro saltó sobre el perro negro";
$fraseModificada = str_replace("negro", "blanco", $frase);

echo "Frase original: $frase<br>";
echo "Frase modificada: $fraseModificada<br>";

// Ejercicio: Crea una variable con una frase que contenga al menos tres veces la palabra "PHP"
// y usa str_replace() para cambiar "PHP" por "JavaScript"
$miFrase = "Compre un abrigo negro y unos guantes negros para el invierno"; // Reemplaza esto con tu frase
$miFraseModificada = str_replace("negro", "blanco", $miFrase);

echo "<br>Mi frase original: $miFrase<br>";
echo "Mi frase modificada: $miFraseModificada<br>";

// Bonus: Usa str_replace() para reemplazar múltiples palabras a la vez
$texto = "Manzanas y naranjas son frutas. Me gustan las manzanas y las naranjas.";
$buscar = ["Manzanas", "naranjas"];
$reemplazar = ["Peras", "uvas"];
$textoModificado = str_replace($buscar, $reemplazar, $texto);

echo "<br>Texto original: $texto<br>";
echo "Texto modificado: $textoModificado<br>";

$textoNuevo = "El cielo está azul y el mar también. El azul del cielo es precioso y el azul del mar es relajante.";
$buscarNuevo = ["azul", "mar"];
$reemplazarNuevo = ["verde", "océano"];
$textoNuevoModificado = str_replace($buscarNuevo, $reemplazarNuevo, $textoNuevo);

echo "<br>Texto original nuevo: $textoNuevo<br>";
echo "Texto modificado nuevo: $textoNuevoModificado<br>";
?>     
