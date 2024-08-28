<?php

$nombre_completo = "Abdiel Saul Alvarez Rodriguez";
$edad = 28;
$correo = "abdielalvarez9602@gmail.com";
$telefono = "6102-1166";

define("OCUPACION", "Estudiante");

echo "<p>Nombre completo: " . $nombre_completo . "</p>";
print "<p>Edad: " . $edad . " años</p>";
printf("<p>Correo electrónico: %s</p>", $correo);
echo "<p>Teléfono: $telefono</p>";
echo "<p>Ocupación: " . OCUPACION . "</p>";

echo "<p>Detalles de las variables:</p>";
var_dump($nombre_completo);
var_dump($edad);
var_dump($correo);
var_dump($telefono);
var_dump(OCUPACION);
?>
