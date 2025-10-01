<?php
// Paso 4: Ordenamiento y Filtrado Avanzado de Arreglos

// 1. Definir el arreglo de libros
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

// 2. Función para imprimir la biblioteca
function imprimirBiblioteca($libros) {
    if (empty($libros)) {
        echo "No se encontraron libros.\n";
        return;
    }
    foreach ($libros as $libro) {
        echo "{$libro['titulo']} - {$libro['autor']} ({$libro['año']}) - {$libro['genero']} - " . 
            ($libro['prestado'] ? "Prestado" : "Disponible") . "\n";
    }
    echo "\n";
}

echo "Biblioteca original:\n";
imprimirBiblioteca($biblioteca);

// 3. Ordenar libros por año de publicación (del más antiguo al más reciente)
usort($biblioteca, function($a, $b) {
    return $a['año'] - $b['año'];
});

echo "Libros ordenados por año de publicación (usort):\n";
imprimirBiblioteca($biblioteca);

// 4. Ordenar libros alfabéticamente por título
usort($biblioteca, function($a, $b) {
    return strcmp($a['titulo'], $b['titulo']);
});

echo "Libros ordenados alfabéticamente por título (usort):\n";
imprimirBiblioteca($biblioteca);

// 5. Filtrar libros disponibles (no prestados)
$librosDisponibles = array_filter($biblioteca, function($libro) {
    return !$libro['prestado'];
});

echo "Libros disponibles (array_filter):\n";
imprimirBiblioteca($librosDisponibles);

// 6. Filtrar libros por género
function filtrarPorGenero($libros, $genero) {
   
    return array_filter($libros, function($libro) use ($genero) {
        return strcasecmp($libro['genero'], $genero) === 0;
    });
}

$librosCienciaFiccion = filtrarPorGenero($biblioteca, "Ciencia ficción");
echo "Libros de Ciencia ficción (array_filter):\n";
imprimirBiblioteca($librosCienciaFiccion);

// 7. Obtener lista de autores únicos
$autores = array_unique(array_column($biblioteca, 'autor'));
sort($autores);

echo "Lista de autores (array_column, array_unique):\n";
foreach ($autores as $autor) {
    echo "- $autor\n";
}
echo "\n";

// 8. Calcular el año promedio de publicación
$añoPromedio = array_sum(array_column($biblioteca, 'año')) / count($biblioteca);
echo "Año promedio de publicación: " . round($añoPromedio, 2) . "\n\n";

// 9. Encontrar el libro más antiguo y el más reciente
$libroMasAntiguo = array_reduce($biblioteca, function($carry, $libro) {
    return (!$carry || $libro['año'] < $carry['año']) ? $libro : $carry;
});

$libroMasReciente = array_reduce($biblioteca, function($carry, $libro) {
    return (!$carry || $libro['año'] > $carry['año']) ? $libro : $carry;
});

echo "Libro más antiguo (array_reduce): {$libroMasAntiguo['titulo']} ({$libroMasAntiguo['año']})\n";
echo "Libro más reciente (array_reduce): {$libroMasReciente['titulo']} ({$libroMasReciente['año']})\n\n";

// --- TAREAS PENDIENTES IMPLEMENTADAS ---

// 10. TAREA: Implementa una función de búsqueda que permita buscar libros por título o autor
// La función debe ser capaz de manejar búsquedas parciales y no debe ser sensible a mayúsculas/minúsculas
function buscarLibros($biblioteca, $termino) {
    $termino_lower = strtolower($termino);

    // Usa array_filter para aplicar la lógica de búsqueda en cada elemento
    return array_filter($biblioteca, function($libro) use ($termino_lower) {
        $titulo_lower = strtolower($libro['titulo']);
        $autor_lower = strtolower($libro['autor']);

        // strpos verifica si la subcadena existe (coincidencia parcial)
        return strpos($titulo_lower, $termino_lower) !== false || 
               strpos($autor_lower, $termino_lower) !== false;
    });
}

// Ejemplo de uso de la función de búsqueda (descomenta para probar)
$resultadosBusqueda = buscarLibros($biblioteca, "quijote");
echo "Resultados de búsqueda para 'quijote' (Búsqueda por Título/Autor):\n";
imprimirBiblioteca($resultadosBusqueda);

$resultadosBusqueda2 = buscarLibros($biblioteca, "gabriel");
echo "Resultados de búsqueda para 'gabriel' (Búsqueda por Título/Autor):\n";
imprimirBiblioteca($resultadosBusqueda2);


// 11. TAREA: Crea una función que genere un reporte de la biblioteca
// El reporte debe incluir: número total de libros, número de libros prestados,
// número de libros por género, y el autor con más libros en la biblioteca
function generarReporteBiblioteca($biblioteca) {
    $reporte = [];

    $reporte['total_libros'] = count($biblioteca);

    $librosPrestados = array_filter($biblioteca, fn($libro) => $libro['prestado']); // PHP 7.4+ short syntax
    $reporte['libros_prestados'] = count($librosPrestados);
    $reporte['libros_disponibles'] = $reporte['total_libros'] - $reporte['libros_prestados'];

    $generos_conteo = [];
    $autores_conteo = [];
    foreach ($biblioteca as $libro) {

        $genero = $libro['genero'];
        $generos_conteo[$genero] = ($generos_conteo[$genero] ?? 0) + 1; // Null Coalescing Operator para inicializar

        $autor = $libro['autor'];
        $autores_conteo[$autor] = ($autores_conteo[$autor] ?? 0) + 1;
    }
    $reporte['libros_por_genero'] = $generos_conteo;

    $autor_mas_libros = key($autores_conteo); 
    $max_libros = current($autores_conteo); 

    foreach ($autores_conteo as $autor => $conteo) {
        if ($conteo > $max_libros) {
            $max_libros = $conteo;
            $autor_mas_libros = $autor;
        }
    }

    $reporte['autor_con_mas_libros'] = ($autor_mas_libros !== null) 
        ? "$autor_mas_libros ($max_libros libros)" 
        : "N/A";
    
    return $reporte;
}

echo "Reporte de la Biblioteca:\n";
print_r(generarReporteBiblioteca($biblioteca));

?>