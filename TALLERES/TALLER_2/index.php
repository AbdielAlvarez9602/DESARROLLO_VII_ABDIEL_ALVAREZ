<?php
$nombre = "Juan";
$edad = 25;
$altura = 1.75;
$esEstudiante = true;

echo "Nombre: $nombre<br>";
echo "Edad: $edad<br>";
echo "Altura: $altura<br>";
echo "¿Es estudiante? " . ($esEstudiante ? "Sí" : "No");
?>


<?php
$nombre = "Juan";
$edad = 25;

// Concatenación usando el operador .
$presentacion1 = "Hola, mi nombre es " . $nombre . " y tengo " . $edad . " años.";

// Concatenación dentro de comillas dobles
$presentacion2 = "Hola, mi nombre es $nombre y tengo $edad años.";

// Definición de una constante
define("SALUDO", "¡Bienvenido!");

// Concatenación con constante
$mensaje = SALUDO . " " . $nombre;

echo $presentacion1 . "<br>";
echo $presentacion2 . "<br>";
echo $mensaje . "<br>";
?>
                    
