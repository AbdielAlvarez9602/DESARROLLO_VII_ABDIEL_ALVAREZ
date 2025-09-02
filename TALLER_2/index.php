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
                    
<?php
$nombre = "Juan";
$edad = 25;

// Usando echo
echo "Hola, mundo!<br>";
echo "Mi nombre es $nombre<br>";

// Usando print
print "Tengo $edad años<br>";

// Usando printf (permite formateo)
printf("Me llamo %s y tengo %d años<br>", $nombre, $edad);

// Usando var_dump (útil para debugging)
var_dump($nombre);
echo "<br>";
?>

<?php
// Definición de variables
$nombre = "María";
$edad = 30;
$ciudad = "Madrid";

// Definición de constante
define("PROFESION", "Ingeniera");

// Creación de mensaje usando diferentes métodos de concatenación e impresión
$mensaje1 = "Hola, mi nombre es " . $nombre . " y tengo " . $edad . " años.";
$mensaje2 = "Vivo en $ciudad y soy " . PROFESION . ".";

echo $mensaje1 . "<br>";
print($mensaje2 . "<br>");

printf("En resumen: %s, %d años, %s, %s<br>", $nombre, $edad, $ciudad, PROFESION);

echo "<br>Información de debugging:<br>";
var_dump($nombre);
echo "<br>";
var_dump($edad);
echo "<br>";
var_dump($ciudad);
echo "<br>";
var_dump(PROFESION);
echo "<br>";
?>