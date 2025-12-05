<?php
require_once "config_pdo.php";

// --- FUNCIONES DEL EJEMPLO ---

function mostrarResumenCategorias($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM vista_resumen_categorias");
        echo "<h3>Resumen por Categorías (Ejemplo):</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Categoría</th><th>Total Productos</th><th>Stock Total</th><th>Precio Promedio</th></tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['categoria']}</td>";
            echo "<td>{$row['total_productos']}</td>";
            echo "<td>{$row['total_stock']}</td>";
            echo "<td>$" . number_format($row['precio_promedio'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function mostrarProductosPopulares($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM vista_productos_populares LIMIT 5");
        echo "<h3>Top 5 Productos Más Vendidos (Ejemplo):</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Producto</th><th>Categoría</th><th>Total Vendido</th><th>Ingresos</th></tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['producto']}</td>";
            echo "<td>{$row['categoria']}</td>";
            echo "<td>{$row['total_vendido']}</td>";
            echo "<td>$" . number_format($row['ingresos_totales'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

// --- FUNCIONES DE LA TAREA (NUEVAS) ---

function mostrarBajoStock($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM vista_productos_bajo_stock");
        echo "<h3>⚠️ Alerta de Bajo Stock (< 5 unidades):</h3>";
        
        // Usamos fetchAll para contar antes de imprimir
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultados) > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%; border-color: red;'>";
            echo "<tr style='background-color: #ffe6e6;'><th>Producto</th><th>Stock Actual</th><th>Categoría</th><th>Histórico Ventas</th></tr>";
            foreach ($resultados as $row) {
                echo "<tr>";
                echo "<td><b>{$row['nombre']}</b></td>";
                echo "<td style='color:red; font-weight:bold;'>{$row['stock']}</td>";
                echo "<td>{$row['categoria']}</td>";
                echo "<td>{$row['total_vendido_historico']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Todo el inventario está saludable.</p>";
        }
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function mostrarHistorialClientes($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM vista_historial_completo_clientes LIMIT 10");
        echo "<h3>Historial de Compras de Clientes (Últimos 10):</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #e6f7ff;'><th>Cliente</th><th>Producto</th><th>Fecha</th><th>Subtotal</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['cliente']}</td>";
            echo "<td>{$row['producto']}</td>";
            echo "<td>{$row['fecha_venta']}</td>";
            echo "<td>$" . number_format($row['subtotal'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function mostrarMetricasCategorias($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM vista_metricas_categorias_avanzada");
        echo "<h3>Métricas de Rendimiento por Categoría:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #e6fffa;'><th>Categoría</th><th>Cant. Productos</th><th>Items Vendidos</th><th>Ingresos Totales</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['categoria']}</td>";
            echo "<td>{$row['cantidad_productos']}</td>";
            echo "<td>{$row['items_vendidos_totales']}</td>";
            echo "<td>$" . number_format($row['ingresos_totales'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function mostrarTendenciasMensuales($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM vista_tendencias_mensuales");
        echo "<h3>Tendencias de Ventas Mensuales:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #fffbe6;'><th>Mes (Año-Mes)</th><th># Transacciones</th><th>Total Ingresos</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['mes']}</td>";
            echo "<td>{$row['numero_ventas']}</td>";
            echo "<td>$" . number_format($row['total_ingresos'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

// EJECUCIÓN
echo "<h1>Panel de Control con Vistas (PDO)</h1>";
mostrarResumenCategorias($pdo);
mostrarProductosPopulares($pdo);
echo "<hr>";
mostrarBajoStock($pdo);
mostrarHistorialClientes($pdo);
mostrarMetricasCategorias($pdo);
mostrarTendenciasMensuales($pdo);

$pdo = null;
?>