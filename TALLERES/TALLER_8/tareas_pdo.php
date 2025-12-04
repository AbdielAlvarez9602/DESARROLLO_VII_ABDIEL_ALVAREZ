<?php
require_once "config_pdo.php";

echo "<h2>Tareas Avanzadas - PDO</h2>";

try {

    $sql = "SELECT p.titulo, u.nombre AS autor, p.fecha_publicacion 
            FROM publicaciones p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            ORDER BY p.fecha_publicacion DESC 
            LIMIT 5";
    $stmt = $pdo->query($sql);

    echo "<h3>1. Últimas 5 publicaciones:</h3>";
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<strong>" . $row['titulo'] . "</strong> por " . $row['autor'] . " (" . $row['fecha_publicacion'] . ")<br>";
        }
    } else {
        echo "No hay publicaciones.";
    }

    echo "<hr>";

    $sql = "SELECT u.nombre 
            FROM usuarios u 
            LEFT JOIN publicaciones p ON u.id = p.usuario_id 
            WHERE p.id IS NULL";
    $stmt = $pdo->query($sql);

    echo "<h3>2. Usuarios sin publicaciones:</h3>";
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['nombre'] . "<br>";
        }
    } else {
        echo "Todos los usuarios han publicado algo.";
    }

    echo "<hr>";

    $sql = "SELECT (SELECT COUNT(*) FROM publicaciones) / (SELECT COUNT(*) FROM usuarios) AS promedio";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h3>3. Promedio de publicaciones por usuario:</h3>";
    echo "Promedio: " . round($row['promedio'], 2) . " publicaciones por usuario.";

    echo "<hr>";

    $sql = "SELECT u.nombre, p.titulo, p.fecha_publicacion 
            FROM publicaciones p
            INNER JOIN (
                SELECT usuario_id, MAX(fecha_publicacion) as max_fecha
                FROM publicaciones
                GROUP BY usuario_id
            ) ultimas ON p.usuario_id = ultimas.usuario_id AND p.fecha_publicacion = ultimas.max_fecha
            INNER JOIN usuarios u ON p.usuario_id = u.id";
    $stmt = $pdo->query($sql);

    echo "<h3>4. Publicación más reciente de cada usuario:</h3>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Usuario: " . $row['nombre'] . " | Último Post: " . $row['titulo'] . " (" . $row['fecha_publicacion'] . ")<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$pdo = null;
?>