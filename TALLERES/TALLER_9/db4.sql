USE taller9_db;

-- Tabla Productos
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_precio ON productos(precio);
CREATE INDEX idx_productos_stock ON productos(stock);
CREATE INDEX idx_productos_nombre_precio ON productos(nombre, precio);

-- Tabla Ventas
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_cliente_fecha ON ventas(cliente_id, fecha_venta);
CREATE INDEX idx_ventas_estado ON ventas(estado);

-- Tabla Detalles
CREATE INDEX idx_detalles_producto ON detalles_venta(producto_id);
CREATE INDEX idx_detalles_compuesto ON detalles_venta(venta_id, producto_id);

ALTER TABLE productos ADD FULLTEXT INDEX ft_idx_productos_nombre (nombre, descripcion);

CREATE INDEX idx_clientes_email ON clientes(email);

CREATE INDEX idx_clientes_membresia ON clientes(nivel_membresia, id);

CREATE INDEX idx_ventas_total ON ventas(total);