<?php
// Ejemplo de uso de json_encode() con un array simple
$frutas = ["manzana", "banana", "naranja"];
$jsonFrutas = json_encode($frutas);
echo "Array de frutas en JSON:</br>$jsonFrutas</br>";

// Ejemplo con un array asociativo
$persona = [
    "nombre" => "Juan",
    "edad" => 30,
    "ciudad" => "Madrid"
];
$jsonPersona = json_encode($persona);
echo "</br>Array asociativo de persona en JSON:</br>$jsonPersona</br>";

// Ejercicio: Crea un array con informacion sobre tu pelicula favorita
// (titulo, director, ano, actores principales) y conviertelo a JSON
$peliculaFavorita = [
    "titulo" => "El Senor de los Anillos: La Comunidad del Anillo",
    "director" => "Peter Jackson",
    "ano" => 2001,
    "actores" => [
        "Elijah Wood", 
        "Ian McKellen", 
        "Viggo Mortensen"
    ]
];
$jsonPelicula = json_encode($peliculaFavorita);
echo "</br>Informacion de tu pelicula favorita en JSON:</br>$jsonPelicula</br>";

// Bonus: Usa json_encode() con un objeto de clase personalizada
class Libro {
    public $titulo;
    public $autor;
    public $ano;
    
    public function __construct($titulo, $autor, $ano) {
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->ano = $ano;
    }
}

$miLibro = new Libro("Cien anos de soledad", "Gabriel Garcia Marquez", 1967);
$jsonLibro = json_encode($miLibro);
echo "</br>Objeto Libro en JSON:</br>$jsonLibro</br>";

// Extra: Uso de opciones en json_encode()
$datosConCaracteresEspeciales = [
    "nombre" => "Maria Jose",
    "descripcion" => "Le gusta el cafe y el te"
];
$jsonConOpciones = json_encode($datosConCaracteresEspeciales, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo "</br>JSON con opciones (caracteres Unicode y formato bonito):</br>$jsonConOpciones</br>";
?>
      
