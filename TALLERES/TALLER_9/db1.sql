USE taller9_db;

-- 1. CREACIÓN DE ÍNDICES (Optimización de rendimiento)
-- Es vital indexar las claves foráneas para que las vistas sean rápidas
CREATE INDEX idx_producto_categoria ON productos(categoria_id);
CREATE INDEX idx_venta_cliente ON ventas(cliente_id);
CREATE INDEX idx_detalle_venta ON detalles_venta(venta_id);
CREATE INDEX idx_detalle_producto ON detalles_venta(producto_id);

-- 2. VISTAS DEL EJEMPLO (Proporcionadas en el taller)
CREATE OR REPLACE VIEW vista_resumen_categorias AS
SELECT c.nombre AS categoria, COUNT(p.id) AS total_productos, SUM(p.stock) AS total_stock,
    ROUND(AVG(p.precio), 2) AS precio_promedio, MIN(p.precio) AS precio_minimo, MAX(p.precio) AS precio_maximo
FROM categorias c LEFT JOIN productos p ON c.id = p.categoria_id GROUP BY c.id, c.nombre;

CREATE OR REPLACE VIEW vista_productos_populares AS
SELECT p.id, p.nombre AS producto, p.precio, cat.nombre AS categoria,
    SUM(dv.cantidad) AS total_vendido, SUM(dv.subtotal) AS ingresos_totales, COUNT(DISTINCT v.cliente_id) AS compradores_unicos
FROM productos p JOIN categorias cat ON p.categoria_id = cat.id
LEFT JOIN detalles_venta dv ON p.id = dv.producto_id LEFT JOIN ventas v ON dv.venta_id = v.id
GROUP BY p.id, p.nombre, p.precio, cat.nombre ORDER BY total_vendido DESC;

-- 3. VISTAS DE LA TAREA (Nuevas implementaciones)

-- Tarea A: Productos con bajo stock (< 5) con info de ventas
CREATE OR REPLACE VIEW vista_productos_bajo_stock AS
SELECT p.nombre, p.stock, c.nombre as categoria, COALESCE(SUM(dv.cantidad), 0) as total_vendido_historico
FROM productos p
JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN detalles_venta dv ON p.id = dv.producto_id
WHERE p.stock < 5
GROUP BY p.id, p.nombre, p.stock, c.nombre;

-- Tarea B: Historial completo de clientes (productos y montos)
CREATE OR REPLACE VIEW vista_historial_completo_clientes AS
SELECT c.nombre as cliente, p.nombre as producto, v.fecha_venta, dv.cantidad, dv.subtotal
FROM clientes c
JOIN ventas v ON c.id = v.cliente_id
JOIN detalles_venta dv ON v.id = dv.venta_id
JOIN productos p ON dv.producto_id = p.id
ORDER BY c.nombre, v.fecha_venta DESC;

-- Tarea C: Métricas de rendimiento por categoría
CREATE OR REPLACE VIEW vista_metricas_categorias_avanzada AS
SELECT c.nombre as categoria,
    COUNT(DISTINCT p.id) as cantidad_productos,
    COALESCE(SUM(dv.cantidad), 0) as items_vendidos_totales,
    COALESCE(SUM(dv.subtotal), 0) as ingresos_totales
FROM categorias c
LEFT JOIN productos p ON c.id = p.categoria_id
LEFT JOIN detalles_venta dv ON p.id = dv.producto_id
GROUP BY c.id, c.nombre;

-- Tarea D: Tendencias de ventas por mes
CREATE OR REPLACE VIEW vista_tendencias_mensuales AS
SELECT DATE_FORMAT(fecha_venta, '%Y-%m') as mes,
    COUNT(id) as numero_ventas,
    SUM(total) as total_ingresos
FROM ventas
WHERE estado = 'completada'
GROUP BY mes
ORDER BY mes DESC;