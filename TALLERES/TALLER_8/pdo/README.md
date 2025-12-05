# Sistema de Gestión de Biblioteca - Taller 8

Sistema de gestión para biblioteca implementado en PHP con dos drivers: PDO y MySQLi.

## Configuración

1.  Crear base de datos `biblioteca_db` ejecutando el script SQL.
2.  Configurar credenciales en `pdo/config.php` y `mysqli/config.php` (por defecto: root/vacio).
3.  Ejecutar en servidor local (ej: Laragon/XAMPP).

## Estructura

-   **pdo/**: Implementación Orientada a Objetos.
-   **mysqli/**: Implementación Procedural.
-   **Archivos**: `libros.php` (CRUD), `usuarios.php` (CRUD), `prestamos.php` (Transacciones).

## Comparación

-   **PDO**: Sintaxis más limpia, parámetros nombrados (`:id`), excepciones nativas, multi-driver.
-   **MySQLi**: Sintaxis procedural, binding posicional (`?`), específico para MySQL.