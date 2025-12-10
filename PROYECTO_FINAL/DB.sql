DROP DATABASE IF EXISTS veterinaria_alvarez;
CREATE DATABASE veterinaria_alvarez;
USE veterinaria_alvarez;

-- Tabla Mascotas con CÉDULA y CORREO
CREATE TABLE mascotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_mascota VARCHAR(50) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    nombre_propietario VARCHAR(50) NOT NULL,
    cedula VARCHAR(20) NOT NULL,
    correo VARCHAR(100),
    telefono VARCHAR(20)
);

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT,
    fecha DATETIME,
    motivo TEXT,
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id)
);

CREATE TABLE historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT,
    fecha DATE DEFAULT (CURRENT_DATE()),
    tipo ENUM('Consulta', 'Vacuna') NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id)
);

CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT,
    servicio VARCHAR(100),
    monto DECIMAL(10,2),
    fecha DATE DEFAULT (CURRENT_DATE()),
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id)
);