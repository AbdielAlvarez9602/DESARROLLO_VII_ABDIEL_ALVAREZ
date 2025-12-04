<?php
require_once "config_mysqli.php";
require_once "funciones_log.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    mysqli_begin_transaction($conn);

    $sql = "INSERT INTO usuarios (nombre, email) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    $nombre = "Nuevo Usuario Transaccion";
    $email = "transaccion_" . rand(1,1000) . "@example.com";
    
    mysqli_stmt_bind_param($stmt, "ss", $nombre, $email);
    mysqli_stmt_execute($stmt);
    
    $usuario_id = mysqli_insert_id($conn);

    $sql = "INSERT INTO publicaciones (usuario_id, titulo, contenido) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    $titulo = "Nueva Publicación Transacción";
    $contenido = "Contenido de prueba en bloque";
    
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $titulo, $contenido);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);
    echo "Transacción completada con éxito. Usuario ID: $usuario_id";

} catch (Exception $e) {
    mysqli_rollback($conn);
    registrarError("Fallo en transacción MySQLi: " . $e->getMessage());
    echo "Error en la transacción. Revise el log de errores.";
}

mysqli_close($conn);
?>