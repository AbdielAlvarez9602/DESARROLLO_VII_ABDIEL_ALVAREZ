<?php
require_once "config_pdo.php";

function analizarConsulta($pdo, $sql, $params = []) {
    try {
        echo "<div style='background:#f4f4f4; border:1px solid #ddd; padding:10px; margin-bottom:20px;'>";
        echo "<strong>Consulta SQL:</strong> <pre>" . htmlspecialchars($sql) . "</pre>";

        $stmt = $pdo->prepare("EXPLAIN " . $sql);
        $stmt->execute($params);
        $explain = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' cellpadding='5' style='border-collapse:collapse; font-size:12px;'>";
        echo "<tr style='background:#e0e0e0'><th>id</th><th>select_type</th><th>table</th><th>type</th><th>possible_keys</th><th>key</th><th>rows</th><th>Extra</th></tr>";
        foreach ($explain as $row) {
            echo "<tr>";
            echo "<td>" . ($row['id'] ?? '') . "</td>";
            echo "<td>" . ($row['select_type'] ?? '') . "</td>";
            echo "<td>" . ($row['table'] ?? '') . "</td>";
            echo "<td>" . ($row['type'] ?? '') . "</td>";
            echo "<td>" . ($row['possible_keys'] ?? '') . "</td>";
            echo "<td><strong>" . ($row['key'] ?? 'NULL') . "</strong></td>"; 
            echo "<td>" . ($row['rows'] ?? '') . "</td>";
            echo "<td>" . ($row['Extra'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        $inicio = microtime(true);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $fin = microtime(true);
        
        $tiempo = number_format($fin - $inicio, 6);
        $filas = $stmt->rowCount();
        
        echo "<p>⏱ Tiempo: <strong>{$tiempo} seg</strong> | Filas: <strong>{$filas}</strong></p>";
        echo "</div>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

echo "<h1>Análisis de Rendimiento (EXPLAIN)</h1>";

analizarConsulta($pdo, "SELECT * FROM productos WHERE categoria_id = 1");

analizarConsulta($pdo, "SELECT * FROM productos WHERE precio BETWEEN 100 AND 900");

analizarConsulta($pdo, "
    SELECT v.id, c.nombre 
    FROM ventas v 
    JOIN clientes c ON v.cliente_id = c.id 
    WHERE v.fecha_venta >= '2023-01-01'
");

analizarConsulta($pdo, "
    SELECT * FROM productos 
    WHERE MATCH(nombre, descripcion) AGAINST('laptop' IN NATURAL LANGUAGE MODE)
");

$pdo = null;
?>