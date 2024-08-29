<?php
function obtenerLibros() {
    return [
        [
            'titulo' => 'El Quijote',
            'autor' => 'Miguel de Cervantes',
            'anio_publicacion' => 1605,
            'genero' => 'Novela',
            'descripcion' => 'La historia del ingenioso hidalgo Don Quijote de la Mancha.'
        ],
        [
            'titulo' => 'Cien años de soledad',
            'autor' => 'Gabriel García Márquez',
            'anio_publicacion' => 1967,
            'genero' => 'Realismo mágico',
            'descripcion' => 'La historia de la familia Buendía en el ficticio pueblo de Macondo.'
        ],
        [
            'titulo' => '1984',
            'autor' => 'George Orwell',
            'anio_publicacion' => 1949,
            'genero' => 'Distopía',
            'descripcion' => 'Una novela sobre un futuro totalitario y la vigilancia estatal.'
        ],
        [
            'titulo' => 'Orgullo y prejuicio',
            'autor' => 'Jane Austen',
            'anio_publicacion' => 1813,
            'genero' => 'Novela romántica',
            'descripcion' => 'Una historia sobre el amor y las relaciones sociales en la Inglaterra del siglo XIX.'
        ],
        [
            'titulo' => 'El Hobbit',
            'autor' => 'J.R.R. Tolkien',
            'anio_publicacion' => 1937,
            'genero' => 'Fantástico',
            'descripcion' => 'Las aventuras de Bilbo Baggins en la Tierra Media.'
        ]
    ];
}

function mostrarDetallesLibro($libro) {
    return "<div class='libro'>
                <h2>{$libro['titulo']}</h2>
                <p><strong>Autor:</strong> {$libro['autor']}</p>
                <p><strong>Año de Publicación:</strong> {$libro['anio_publicacion']}</p>
                <p><strong>Género:</strong> {$libro['genero']}</p>
                <p><strong>Descripción:</strong> {$libro['descripcion']}</p>
            </div>";
}
?>
