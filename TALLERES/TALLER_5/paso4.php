<?php

$biblioteca = [
    [
        "titulo" => "Cien años de soledad",
        "autor" => "Gabriel García Márquez",
        "año" => 1967,
        "genero" => "Realismo mágico",
        "prestado" => true
    ],
    [
        "titulo" => "1984",
        "autor" => "George Orwell",
        "año" => 1949,
        "genero" => "Ciencia ficción",
        "prestado" => false
    ],
    [
        "titulo" => "El principito",
        "autor" => "Antoine de Saint-Exupéry",
        "año" => 1943,
        "genero" => "Literatura infantil",
        "prestado" => true
    ],
    [
        "titulo" => "Don Quijote de la Mancha",
        "autor" => "Miguel de Cervantes",
        "año" => 1605,
        "genero" => "Novela",
        "prestado" => false
    ],
    [
        "titulo" => "Orgullo y prejuicio",
        "autor" => "Jane Austen",
        "año" => 1813,
        "genero" => "Novela romántica",
        "prestado" => true
    ]
];

function imprimirBiblioteca($libros) {
    foreach ($libros as $libro) {
        echo "{$libro['titulo']} - {$libro['autor']} ({$libro['año']}) - {$libro['genero']} - " . 
             ($libro['prestado'] ? "Prestado" : "Disponible") . "\n";
    }
    echo "\n";
}

echo "Biblioteca original:\n";
imprimirBiblioteca($biblioteca);

usort($biblioteca, function($a, $b) {
    return $a['año'] - $b['año'];
});

echo "Libros ordenados por año de publicación:\n";
imprimirBiblioteca($biblioteca);

usort($biblioteca, function($a, $b) {
    return strcmp($a['titulo'], $b['titulo']);
});

echo "Libros ordenados alfabéticamente por título:\n";
imprimirBiblioteca($biblioteca);

$librosDisponibles = array_filter($biblioteca, function($libro) {
    return !$libro['prestado'];
});

echo "Libros disponibles:\n";
imprimirBiblioteca($librosDisponibles);

function filtrarPorGenero($libros, $genero) {
    return array_filter($libros, function($libro) use ($genero) {
        return strcasecmp($libro['genero'], $genero) === 0;
    });
}

$librosCienciaFiccion = filtrarPorGenero($biblioteca, "Ciencia ficción");
echo "Libros de Ciencia ficción:\n";
imprimirBiblioteca($librosCienciaFiccion);

$autores = array_unique(array_column($biblioteca, 'autor'));
sort($autores);

echo "Lista de autores:\n";
foreach ($autores as $autor) {
    echo "- $autor\n";
}
echo "\n";

$añoPromedio = array_sum(array_column($biblioteca, 'año')) / count($biblioteca);
echo "Año promedio de publicación: " . round($añoPromedio, 2) . "\n\n";

$libroMasAntiguo = array_reduce($biblioteca, function($carry, $libro) {
    return (!$carry || $libro['año'] < $carry['año']) ? $libro : $carry;
});

$libroMasReciente = array_reduce($biblioteca, function($carry, $libro) {
    return (!$carry || $libro['año'] > $carry['año']) ? $libro : $carry;
});

echo "Libro más antiguo: {$libroMasAntiguo['titulo']} ({$libroMasAntiguo['año']})\n";
echo "Libro más reciente: {$libroMasReciente['titulo']} ({$libroMasReciente['año']})\n\n";

function buscarLibros($biblioteca, $termino) {
    $termino = strtolower($termino); 
    return array_filter($biblioteca, function($libro) use ($termino) {
        return (strpos(strtolower($libro['titulo']), $termino) !== false || 
                strpos(strtolower($libro['autor']), $termino) !== false);
    });
}

$resultadosBusqueda = buscarLibros($biblioteca, "quijote");
echo "Resultados de búsqueda para 'quijote':\n";
imprimirBiblioteca($resultadosBusqueda);

function generarReporteBiblioteca($biblioteca) {
    $reporte = [];

    $reporte['total_libros'] = count($biblioteca);

    $reporte['libros_prestados'] = count(array_filter($biblioteca, function($libro) {
        return $libro['prestado'];
    }));

    $generos = array_count_values(array_column($biblioteca, 'genero'));
    $reporte['libros_por_genero'] = $generos;

    $autores = array_count_values(array_column($biblioteca, 'autor'));
    arsort($autores); 
    $reporte['autor_con_mas_libros'] = array_key_first($autores);

    return $reporte;
}

echo "Reporte de la Biblioteca:\n";
print_r(generarReporteBiblioteca($biblioteca));
?>
