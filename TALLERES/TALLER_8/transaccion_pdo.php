<?php
require_once "config_pdo.php";
require_once "funciones_log.php";

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO usuarios (nombre, email) VALUES (:nombre, :email)";
    $stmt = $pdo->prepare($sql);
    
    $email = "transaccion_pdo_" . rand(1,1000) . "@example.com";
    
    $stmt->execute([':nombre' => 'Nuevo Usuario PDO', ':email' => $email]);
    $usuario_id = $pdo->lastInsertId();

    $sql = "INSERT INTO publicaciones (usuario_id, titulo, contenido) VALUES (:usuario_id, :titulo, :contenido)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':titulo' => 'Nueva Publicación PDO',
        ':contenido' => 'Contenido de prueba PDO'
    ]);

    $pdo->commit();
    echo "Transacción completada con éxito. Usuario ID: $usuario_id";

} catch (Exception $e) {
    $pdo->rollBack();
    registrarError("Fallo en transacción PDO: " . $e->getMessage());
    echo "Error en la transacción. Revise el log de errores.";
}
?>