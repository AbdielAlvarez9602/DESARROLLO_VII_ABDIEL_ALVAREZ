<?php
require_once "config_pdo.php";

echo "<h1>Gestión con Procedimientos Almacenados (PDO)</h1>";
echo "<hr>";

function registrarVenta($pdo, $cliente_id, $producto_id, $cantidad) {
    echo "<h3>1. Registrando Venta (Ejemplo)...</h3>";
    try {
        $stmt = $pdo->prepare("CALL sp_registrar_venta(:cliente, :prod, :cant, @venta_id)");
        $stmt->bindParam(':cliente', $cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(':prod', $producto_id, PDO::PARAM_INT);
        $stmt->bindParam(':cant', $cantidad, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor(); 
        $row = $pdo->query("SELECT @venta_id as venta_id")->fetch(PDO::FETCH_ASSOC);
        echo "<div style='color:green;'> Venta registrada con éxito. ID de venta: <strong>{$row['venta_id']}</strong></div>";
        
    } catch (PDOException $e) {
        echo "<div style='color:red;'>Error: " . $e->getMessage() . "</div>";
    }
}

function obtenerEstadisticasCliente($pdo, $cliente_id) {
    echo "<h3>2. Estadísticas del Cliente (Ejemplo):</h3>";
    try {
        $stmt = $pdo->prepare("CALL sp_estadisticas_cliente(:id)");
        $stmt->bindParam(':id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $est = $stmt->fetch(PDO::FETCH_ASSOC);
        if($est) {
            echo "<ul>";
            echo "<li><strong>Cliente:</strong> {$est['nombre']} ({$est['nivel_membresia']})</li>";
            echo "<li><strong>Gastado Total:</strong> $" . number_format($est['total_gastado'], 2) . "</li>";
            echo "</ul>";
        }
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function procesarDevolucion($pdo, $venta_id, $producto_id, $cantidad) {
    echo "<h3>3. Procesar Devolución (Tarea A):</h3>";
    try {
        $stmt = $pdo->prepare("CALL sp_procesar_devolucion(:vid, :pid, :cant)");
        $stmt->bindParam(':vid', $venta_id, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $producto_id, PDO::PARAM_INT);
        $stmt->bindParam(':cant', $cantidad, PDO::PARAM_INT);
        $stmt->execute();
        
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<div style='background-color:#e6fffa; padding:10px; border:1px solid green;'>";
        echo ($res['mensaje'] ?? 'Devolución exitosa');
        echo "</div>";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo "<div style='color:red;'>Error en devolución: " . $e->getMessage() . "</div>";
    }
}

function verificarDescuentos($pdo, $cliente_id) {
    echo "<h3>4. Verificar Descuento (Tarea B):</h3>";
    try {
        $stmt = $pdo->prepare("CALL sp_calcular_descuento_cliente(:cid, @d, @n)");
        $stmt->bindParam(':cid', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        $res = $pdo->query("SELECT @d as pct, @n as nivel")->fetch(PDO::FETCH_ASSOC);
        
        echo "Nivel actualizado: <strong>{$res['nivel']}</strong><br>";
        echo "Descuento ganado: <strong>{$res['pct']}%</strong>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function reporteReposicion($pdo) {
    echo "<h3>5. Reporte de Reposición (Tarea C):</h3>";
    try {
        $umbral = 10;
        $stmt = $pdo->prepare("CALL sp_reporte_reposicion_stock(:umbral)");
        $stmt->bindParam(':umbral', $umbral, PDO::PARAM_INT);
        $stmt->execute();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Producto</th><th>Stock</th><th>Sugerencia</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['nombre']}</td>";
            echo "<td>{$row['stock']}</td>";
            echo "<td>Reponer {$row['cantidad_sugerida_reposicion']} unid.</td>";
            echo "</tr>";
        }
        echo "</table>";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function calcularComisiones($pdo) {
    echo "<h3>6. Cálculo de Comisiones (Tarea D):</h3>";
    try {
        $mes = date('m');
        $anio = date('Y');
        $stmt = $pdo->prepare("CALL sp_calcular_comisiones_mes(:m, :a)");
        $stmt->bindParam(':m', $mes, PDO::PARAM_INT);
        $stmt->bindParam(':a', $anio, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total Ventas Mes: $" . number_format($row['monto_total_ventas'], 2) . "<br>";
        echo "Comisión (5%): <strong>$" . number_format($row['comision_calculada'], 2) . "</strong>";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


registrarVenta($pdo, 3, 2, 1);
obtenerEstadisticasCliente($pdo, 3);
procesarDevolucion($pdo, 1, 1, 1);
verificarDescuentos($pdo, 3);
reporteReposicion($pdo);
calcularComisiones($pdo);

$pdo = null;
?>