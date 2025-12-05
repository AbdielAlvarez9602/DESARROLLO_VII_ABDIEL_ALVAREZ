-- Crear la base de datos
CREATE DATABASE taller9_db;
USE taller9_db;

-- Tabla de Categorías
CREATE TABLE categorias (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    categoria_padre_id INT,
    activa BOOLEAN DEFAULT true,
    FOREIGN KEY (categoria_padre_id) REFERENCES categorias(id)
);

-- Tabla de Productos
CREATE TABLE productos (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    categoria_id INT,
    stock INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de Clientes
CREATE TABLE clientes (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nivel_membresia ENUM('básico', 'premium', 'vip') DEFAULT 'básico',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Ventas
CREATE TABLE ventas (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Tabla de Detalles de Venta
CREATE TABLE detalles_venta (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    venta_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Insertar datos de ejemplo
INSERT INTO categorias (nombre, descripcion, categoria_padre_id) VALUES
('Electrónica', 'Productos electrónicos', NULL),
('Computadoras', 'Equipos de cómputo', 1),
('Smartphones', 'Teléfonos inteligentes', 1),
('Accesorios', 'Accesorios electrónicos', 1),
('Ropa', 'Artículos de vestir', NULL),
('Calzado', 'Todo tipo de calzado', 5);

INSERT INTO productos (nombre, descripcion, precio, categoria_id, stock) VALUES
('Laptop HP', 'Laptop HP 15 pulgadas', 899.99, 2, 10),
('iPhone 13', 'Apple iPhone 13 128GB', 999.99, 3, 15),
('Mouse Gaming', 'Mouse gaming RGB', 49.99, 4, 30),
('Tenis Nike', 'Tenis deportivos', 89.99, 6, 20),
('Monitor 24"', 'Monitor LED 24 pulgadas', 199.99, 2, 8),
('Audífonos BT', 'Audífonos Bluetooth', 79.99, 4, 25);

INSERT INTO clientes (nombre, email, nivel_membresia) VALUES
('Juan Pérez', 'juan@example.com', 'básico'),
('María García', 'maria@example.com', 'premium'),
('Carlos López', 'carlos@example.com', 'vip'),
('Ana Martínez', 'ana@example.com', 'básico');

INSERT INTO ventas (cliente_id, total, estado) VALUES
(1, 999.99, 'completada'),
(2, 1299.98, 'completada'),
(3, 2499.97, 'completada'),
(2, 129.98, 'pendiente');

INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 1, 899.99, 899.99),
(1, 3, 2, 49.99, 99.98),
(2, 2, 1, 999.99, 999.99),
(2, 6, 1, 79.99, 79.99),
(3, 2, 2, 999.99, 1999.98),
(3, 5, 1, 199.99, 199.99),
(4, 3, 2, 49.99, 99.98),
(4, 6, 1, 29.99, 29.99);