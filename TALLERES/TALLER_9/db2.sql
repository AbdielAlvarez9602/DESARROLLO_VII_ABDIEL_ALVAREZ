USE taller9_db;

DROP PROCEDURE IF EXISTS sp_registrar_venta;
DROP PROCEDURE IF EXISTS sp_estadisticas_cliente;
DROP PROCEDURE IF EXISTS sp_actualizar_precios_categoria;
DROP PROCEDURE IF EXISTS sp_reporte_ventas;
DROP PROCEDURE IF EXISTS sp_procesar_devolucion;
DROP PROCEDURE IF EXISTS sp_calcular_descuento_cliente;
DROP PROCEDURE IF EXISTS sp_reporte_reposicion_stock;
DROP PROCEDURE IF EXISTS sp_calcular_comisiones_mes;

DELIMITER //

CREATE PROCEDURE sp_registrar_venta(
    IN p_cliente_id INT,
    IN p_producto_id INT,
    IN p_cantidad INT,
    OUT p_venta_id INT
)
BEGIN
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_subtotal DECIMAL(10,2);
    DECLARE v_stock INT;
    
    SELECT stock, precio INTO v_stock, v_precio FROM productos WHERE id = p_producto_id;
    
    IF v_stock >= p_cantidad THEN
        START TRANSACTION;
        SET v_subtotal = v_precio * p_cantidad;
        INSERT INTO ventas (cliente_id, total, estado) VALUES (p_cliente_id, v_subtotal, 'completada');
        SET p_venta_id = LAST_INSERT_ID();
        INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal)
        VALUES (p_venta_id, p_producto_id, p_cantidad, v_precio, v_subtotal);
        UPDATE productos SET stock = stock - p_cantidad WHERE id = p_producto_id;
        COMMIT;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente';
    END IF;
END //

CREATE PROCEDURE sp_estadisticas_cliente(IN p_cliente_id INT)
BEGIN
    SELECT c.nombre, c.nivel_membresia, COUNT(v.id) as total_compras,
        COALESCE(SUM(v.total), 0) as total_gastado, COALESCE(AVG(v.total), 0) as promedio_compra,
        (SELECT GROUP_CONCAT(DISTINCT p.nombre) FROM ventas v2 JOIN detalles_venta dv ON v2.id = dv.venta_id 
         JOIN productos p ON dv.producto_id = p.id WHERE v2.cliente_id = p_cliente_id LIMIT 3) as ultimos_productos
    FROM clientes c LEFT JOIN ventas v ON c.id = v.cliente_id
    WHERE c.id = p_cliente_id GROUP BY c.id;
END //

CREATE PROCEDURE sp_procesar_devolucion(
    IN p_venta_id INT,
    IN p_producto_id INT,
    IN p_cantidad INT
)
BEGIN
    DECLARE v_cantidad_actual INT;

    SELECT cantidad INTO v_cantidad_actual 
    FROM detalles_venta WHERE venta_id = p_venta_id AND producto_id = p_producto_id;
    
    IF v_cantidad_actual IS NOT NULL AND v_cantidad_actual >= p_cantidad THEN
        START TRANSACTION;
        
        UPDATE productos SET stock = stock + p_cantidad WHERE id = p_producto_id;
        
        IF v_cantidad_actual = p_cantidad THEN
             UPDATE ventas SET estado = 'cancelada' WHERE id = p_venta_id;
        END IF;
        
        COMMIT;
        SELECT 'Devolución procesada correctamente' as mensaje;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede devolver: cantidad inválida o producto no existe en la venta';
    END IF;
END //

CREATE PROCEDURE sp_calcular_descuento_cliente(
    IN p_cliente_id INT,
    OUT p_descuento_porcentaje DECIMAL(5,2),
    OUT p_nuevo_nivel VARCHAR(20)
)
BEGIN
    DECLARE v_total_gastado DECIMAL(10,2);
    
    SELECT COALESCE(SUM(total), 0) INTO v_total_gastado 
    FROM ventas WHERE cliente_id = p_cliente_id AND estado = 'completada';
    
    IF v_total_gastado > 3000 THEN
        SET p_descuento_porcentaje = 15.00;
        SET p_nuevo_nivel = 'vip';
    ELSEIF v_total_gastado > 1000 THEN
        SET p_descuento_porcentaje = 10.00;
        SET p_nuevo_nivel = 'premium';
    ELSE
        SET p_descuento_porcentaje = 0.00;
        SET p_nuevo_nivel = 'básico';
    END IF;
    
    UPDATE clientes SET nivel_membresia = p_nuevo_nivel WHERE id = p_cliente_id;
END //

CREATE PROCEDURE sp_reporte_reposicion_stock(
    IN p_umbral INT
)
BEGIN
    SELECT nombre, stock, 
           p_umbral as umbral_minimo,
           (20 - stock) as cantidad_sugerida_reposicion,
           precio
    FROM productos 
    WHERE stock < p_umbral;
END //

CREATE PROCEDURE sp_calcular_comisiones_mes(
    IN p_mes INT,
    IN p_anio INT
)
BEGIN
    SELECT 
        COUNT(id) as total_ventas_mes,
        SUM(total) as monto_total_ventas,
        (SUM(total) * 0.05) as comision_calculada
    FROM ventas
    WHERE MONTH(fecha_venta) = p_mes AND YEAR(fecha_venta) = p_anio AND estado = 'completada';
END //

DELIMITER ;