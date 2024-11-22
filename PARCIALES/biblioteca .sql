
CREATE DATABASE biblioteca_personal;

-- Usar la base de datos creada
USE biblioteca;

-- Tabla de usuarios (para almacenar los datos de los usuarios autenticados con Google)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,          -- ID único del usuario
    email VARCHAR(255) NOT NULL,                -- Email del usuario
    name VARCHAR(255) NOT NULL,                 -- Nombre completo del usuario
    google_id VARCHAR(255) NOT NULL,            -- ID de Google para la autenticación
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP -- Fecha de registro del usuario
);


CREATE TABLE IF NOT EXISTS libros_guardados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,           -- ID del usuario que guarda el libro
    libro_id VARCHAR(255) NOT NULL,  -- ID del libro desde la API de Google Books
    titulo VARCHAR(255) NOT NULL,    -- Título del libro
    autores TEXT,                    -- Autores del libro
    portada VARCHAR(255),            -- URL de la imagen de la portada
    fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de guardado
    FOREIGN KEY (user_id) REFERENCES usuarios(id) -- Relación con la tabla usuarios (si tienes una tabla de usuarios)
);
DROP TABLE libros_guardados;


