
<?php
// Ejemplo de uso de explode()
$frase = "Manzana,Naranja,Plátano,Uva";
$frutas = explode(",", $frase);

echo "Frase original: $frase<br>";
echo "Array de frutas:<br>";
print_r($frutas);

// Ejercicio: Crea una variable con una lista de tus 5 películas favoritas separadas por comas (,)
// y usa explode() para convertirla en un array
$misPeliculas = "El aro,Superman,Barbie,Flash,El conjuro"; // Asegúrate de usar comas como separador
$arrayPeliculas = explode(",", $misPeliculas);

echo "<br>Mis películas favoritas:<br>";
print_r($arrayPeliculas);

// Bonus: Usa explode() con un límite
$texto = "Uno,Dos,Tres,Cuatro,Cinco";
$array = explode("()", $texto, 2);

echo "<br>Texto original: $texto<br>";
echo "Array con límite:<br>";
print_r($array);
?>

      
