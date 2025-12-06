USE taller9_db;

CREATE TABLE IF NOT EXISTS log_transacciones_fallidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    error_mensaje TEXT,
    punto_fallo VARCHAR(100),
    fecha_falla TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE clientes ADD COLUMN puntos_lealtad INT DEFAULT 1000;