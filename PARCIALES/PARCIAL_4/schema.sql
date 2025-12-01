-- Script SQL para crear la base de datos y tablas
CREATE DATABASE IF NOT EXISTS `mini_biblio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mini_biblio`;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `google_id` VARCHAR(255) NOT NULL,
  `fecha_registro` DATETIME NOT NULL,
  UNIQUE KEY `uk_google_id` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla libros_guardados
CREATE TABLE IF NOT EXISTS `libros_guardados` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `google_books_id` VARCHAR(255) NOT NULL,
  `titulo` VARCHAR(512) NOT NULL,
  `autor` VARCHAR(512) DEFAULT NULL,
  `imagen_portada` VARCHAR(1024) DEFAULT NULL,
  `reseña_personal` TEXT DEFAULT NULL,
  `fecha_guardado` DATETIME NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  INDEX (`user_id`),
  INDEX (`google_books_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
