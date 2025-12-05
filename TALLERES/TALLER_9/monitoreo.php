<?php
require_once "config_pdo.php";

class MonitorRendimiento {
    private $pdo;
    private $archivo_log = 'slow_queries.log';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarConsultaCritica($sql, $umbral_segundos = 0.001) {
        $inicio = microtime(true);
        
        $stmt = $this->pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $fin = microtime(true);
        $duracion = $fin - $inicio;


        if ($duracion > $umbral_segundos) {
            $fecha = date('Y-m-d H:i:s');
            $log = "[$fecha] DURACIÓN: " . number_format($duracion, 5) . "s | SQL: $sql" . PHP_EOL;
            file_put_contents($this->archivo_log, $log, FILE_APPEND);
            echo "<div style='color:red'> Consulta lenta detectada y registrada en log.</div>";
        }

        return $resultados;
    }
    public function mostrarEstadisticasIndices($tabla) {
        $stmt = $this->pdo->prepare("SHOW INDEX FROM " . $tabla);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tamanoTablas() {
        $sql = "SELECT table_name AS tabla, 
                round(((data_length + index_length) / 1024 / 1024), 2) AS tamano_mb 
                FROM information_schema.TABLES 
                WHERE table_schema = :db";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':db' => DB_NAME]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$monitor = new MonitorRendimiento($pdo);

echo "<h1>Panel de Monitoreo y Rendimiento</h1>";

$tablas = ['productos', 'ventas'];
foreach ($tablas as $tabla) {
    echo "<h3>Índices en tabla: $tabla</h3>";
    $indices = $monitor->mostrarEstadisticasIndices($tabla);
    
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
    echo "<tr style='background:#ddd'><th>Key_name</th><th>Column_name</th><th>Index_type</th><th>Cardinality</th></tr>";
    foreach ($indices as $idx) {
        echo "<tr>";
        echo "<td>{$idx['Key_name']}</td>";
        echo "<td>{$idx['Column_name']}</td>";
        echo "<td>{$idx['Index_type']}</td>";
        echo "<td>{$idx['Cardinality']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>Tamaño de Tablas en Disco</h3>";
$tamanos = $monitor->tamanoTablas();
echo "<ul>";
foreach($tamanos as $t) {
    echo "<li><strong>{$t['tabla']}</strong>: {$t['tamano_mb']} MB</li>";
}
echo "</ul>";

echo "<h3>Prueba de Logger de Consultas Lentas</h3>";
echo "<p>Ejecutando consulta pesada...</p>";

$sql_prueba = "SELECT SQL_NO_CACHE * FROM ventas v 
               JOIN detalles_venta dv ON v.id = dv.venta_id 
               JOIN productos p ON dv.producto_id = p.id";

$monitor->registrarConsultaCritica($sql_prueba, 0.0001);

echo "<p><em>Verifica el archivo <strong>slow_queries.log</strong> en tu carpeta.</em></p>";

$pdo = null;
?>