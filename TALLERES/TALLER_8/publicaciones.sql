-- Crear tabla de publicaciones
CREATE TABLE publicaciones (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    titulo VARCHAR(100) NOT NULL,
    contenido TEXT,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Insertar usuarios de ejemplo
INSERT INTO usuarios (nombre, email) VALUES
('Ana García', 'ana@example.com'),
('Carlos Rodríguez', 'carlos@example.com'),
('Elena Martínez', 'elena@example.com'),
('David López', 'david@example.com');

-- Insertar publicaciones de ejemplo
INSERT INTO publicaciones (usuario_id, titulo, contenido) VALUES
(4, 'Mi primera publicación de Ana', 'Contenido de la primera publicación de Ana'),
(4, 'Reflexiones del día', 'Ana comparte sus pensamientos'),

(5, 'Tecnología moderna', 'Carlos habla sobre avances tecnológicos'),

(6, 'Receta del día', 'Elena comparte una receta deliciosa'),
(6, 'Viaje a la montaña', 'Experiencias de Elena en su última excursión'),
(6, 'Reseña de libro', 'Elena analiza su libro favorito'),

(7, 'Ejercicios para principiantes', 'David comparte rutinas de ejercicio'),
(2, 'Contenido de Camen', 'Publicación adicional para Camen'),
(3, 'Contenido de Luis', 'Publicación adicional para Luis');
        