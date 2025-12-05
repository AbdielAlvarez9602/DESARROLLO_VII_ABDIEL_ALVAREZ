USE taller9_db;

-- 1. TABLAS DE AUDITORÍA (DEL EJEMPLO)
CREATE TABLE IF NOT EXISTS log_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    accion ENUM('INSERT', 'UPDATE', 'DELETE'),
    campo_modificado VARCHAR(50),
    valor_anterior VARCHAR(255),
    valor_nuevo VARCHAR(255),
    usuario VARCHAR(50),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS log_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    accion ENUM('INSERT', 'UPDATE', 'DELETE'),
    estado_anterior VARCHAR(20),
    estado_nuevo VARCHAR(20),
    usuario VARCHAR(50),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS historial_precios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    precio_anterior DECIMAL(10,2),
    precio_nuevo DECIMAL(10,2),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    tipo_movimiento ENUM('ENTRADA', 'SALIDA'),
    cantidad INT,
    motivo VARCHAR(100),
    stock_anterior INT,
    stock_nuevo INT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABLAS NECESARIAS PARA LA TAREA
CREATE TABLE IF NOT EXISTS estadisticas_categorias (
    categoria_id INT PRIMARY KEY,
    total_ventas DECIMAL(12,2) DEFAULT 0.00,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS alertas_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    mensaje VARCHAR(255),
    fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS log_estado_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    estado_anterior VARCHAR(20),
    estado_nuevo VARCHAR(20),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar columna 'estado' a clientes si no existe
ALTER TABLE clientes ADD COLUMN estado ENUM('activo', 'inactivo') DEFAULT 'activo';

-- 3. TRIGGERS DEL EJEMPLO

DROP TRIGGER IF EXISTS tr_productos_update;
DROP TRIGGER IF EXISTS tr_validar_stock;
DROP TRIGGER IF EXISTS tr_ventas_update;
DROP TRIGGER IF EXISTS tr_nuevos_productos;

DELIMITER //

CREATE TRIGGER tr_productos_update
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    IF OLD.precio != NEW.precio THEN
        INSERT INTO log_productos (producto_id, accion, campo_modificado, valor_anterior, valor_nuevo, usuario)
        VALUES (NEW.id, 'UPDATE', 'precio', OLD.precio, NEW.precio, CURRENT_USER());
        
        INSERT INTO historial_precios (producto_id, precio_anterior, precio_nuevo, usuario)
        VALUES (NEW.id, OLD.precio, NEW.precio, CURRENT_USER());
    END IF;
    
    IF OLD.stock != NEW.stock THEN
        INSERT INTO log_productos (producto_id, accion, campo_modificado, valor_anterior, valor_nuevo, usuario)
        VALUES (NEW.id, 'UPDATE', 'stock', OLD.stock, NEW.stock, CURRENT_USER());
        
        INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, stock_anterior, stock_nuevo)
        VALUES (NEW.id, CASE WHEN NEW.stock > OLD.stock THEN 'ENTRADA' ELSE 'SALIDA' END, ABS(NEW.stock - OLD.stock), 'Actualización de stock', OLD.stock, NEW.stock);
    END IF;
END //

CREATE TRIGGER tr_validar_stock
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El stock no puede ser negativo';
    END IF;
END //

CREATE TRIGGER tr_ventas_update
AFTER UPDATE ON ventas
FOR EACH ROW
BEGIN
    INSERT INTO log_ventas (venta_id, accion, estado_anterior, estado_nuevo, usuario)
    VALUES (NEW.id, 'UPDATE', OLD.estado, NEW.estado, CURRENT_USER());
    
    IF NEW.estado = 'cancelada' AND OLD.estado != 'cancelada' THEN
        UPDATE productos p
        JOIN detalles_venta dv ON p.id = dv.producto_id
        SET p.stock = p.stock + dv.cantidad
        WHERE dv.venta_id = NEW.id;
    END IF;
END //

CREATE TRIGGER tr_nuevos_productos
AFTER INSERT ON productos
FOR EACH ROW
BEGIN
    INSERT INTO log_productos (producto_id, accion, campo_modificado, valor_nuevo, usuario)
    VALUES (NEW.id, 'INSERT', 'nuevo_producto', NEW.nombre, CURRENT_USER());
    
    IF NEW.stock > 0 THEN
        INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, stock_anterior, stock_nuevo)
        VALUES (NEW.id, 'ENTRADA', NEW.stock, 'Stock inicial', 0, NEW.stock);
    END IF;
END //

-- 4. TRIGGERS DE LA TAREA

-- Tarea A: Actualizar nivel de membresía basado en historial
DROP TRIGGER IF EXISTS tr_actualizar_membresia //
CREATE TRIGGER tr_actualizar_membresia
AFTER INSERT ON ventas
FOR EACH ROW
BEGIN
    DECLARE total_gastado DECIMAL(10,2);
    
    SELECT SUM(total) INTO total_gastado FROM ventas WHERE cliente_id = NEW.cliente_id;
    
    IF total_gastado > 5000 THEN
        UPDATE clientes SET nivel_membresia = 'vip' WHERE id = NEW.cliente_id;
    ELSEIF total_gastado > 2000 THEN
        UPDATE clientes SET nivel_membresia = 'premium' WHERE id = NEW.cliente_id;
    END IF;
END //

-- Tarea B: Estadísticas automáticas por categoría
DROP TRIGGER IF EXISTS tr_actualizar_stats_categoria //
CREATE TRIGGER tr_actualizar_stats_categoria
AFTER INSERT ON detalles_venta
FOR EACH ROW
BEGIN
    DECLARE cat_id INT;
    SELECT categoria_id INTO cat_id FROM productos WHERE id = NEW.producto_id;
    
    INSERT INTO estadisticas_categorias (categoria_id, total_ventas)
    VALUES (cat_id, NEW.subtotal)
    ON DUPLICATE KEY UPDATE total_ventas = total_ventas + NEW.subtotal;
END //

-- Tarea C: Alerta de Stock Crítico
DROP TRIGGER IF EXISTS tr_alerta_stock_critico //
CREATE TRIGGER tr_alerta_stock_critico
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.stock <= 5 AND OLD.stock > 5 THEN
        INSERT INTO alertas_stock (producto_id, mensaje)
        VALUES (NEW.id, CONCAT('ALERTA: El producto ', NEW.nombre, ' ha llegado a stock crítico (', NEW.stock, ')'));
    END IF;
END //

-- Tarea D: Historial de estado de clientes
DROP TRIGGER IF EXISTS tr_auditar_estado_cliente //
CREATE TRIGGER tr_auditar_estado_cliente
AFTER UPDATE ON clientes
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO log_estado_clientes (cliente_id, estado_anterior, estado_nuevo)
        VALUES (NEW.id, OLD.estado, NEW.estado);
    END IF;
END //

DELIMITER ;