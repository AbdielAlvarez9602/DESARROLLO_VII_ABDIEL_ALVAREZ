<?php
require_once "config_mysqli.php";

echo "<h2>Tareas Avanzadas - MySQLi</h2>";

$sql = "SELECT p.titulo, u.nombre AS autor, p.fecha_publicacion 
        FROM publicaciones p 
        INNER JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_publicacion DESC 
        LIMIT 5";
$result = mysqli_query($conn, $sql);

echo "<h3>1. Últimas 5 publicaciones:</h3>";
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<strong>" . $row['titulo'] . "</strong> por " . $row['autor'] . " (" . $row['fecha_publicacion'] . ")<br>";
        }
    } else {
        echo "No hay publicaciones.";
    }
    mysqli_free_result($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

echo "<hr>";

$sql = "SELECT u.nombre 
        FROM usuarios u 
        LEFT JOIN publicaciones p ON u.id = p.usuario_id 
        WHERE p.id IS NULL";
$result = mysqli_query($conn, $sql);

echo "<h3>2. Usuarios sin publicaciones:</h3>";
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo $row['nombre'] . "<br>";
        }
    } else {
        echo "Todos los usuarios han publicado algo.";
    }
    mysqli_free_result($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

echo "<hr>";

$sql = "SELECT (SELECT COUNT(*) FROM publicaciones) / (SELECT COUNT(*) FROM usuarios) AS promedio";
$result = mysqli_query($conn, $sql);

echo "<h3>3. Promedio de publicaciones por usuario:</h3>";
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "Promedio: " . round($row['promedio'], 2) . " publicaciones por usuario.";
    mysqli_free_result($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

echo "<hr>";

$sql = "SELECT u.nombre, p.titulo, p.fecha_publicacion 
        FROM publicaciones p
        INNER JOIN (
            SELECT usuario_id, MAX(fecha_publicacion) as max_fecha
            FROM publicaciones
            GROUP BY usuario_id
        ) ultimas ON p.usuario_id = ultimas.usuario_id AND p.fecha_publicacion = ultimas.max_fecha
        INNER JOIN usuarios u ON p.usuario_id = u.id";
$result = mysqli_query($conn, $sql);

echo "<h3>4. Publicación más reciente de cada usuario:</h3>";
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Usuario: " . $row['nombre'] . " | Último Post: " . $row['titulo'] . " (" . $row['fecha_publicacion'] . ")<br>";
    }
    mysqli_free_result($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>