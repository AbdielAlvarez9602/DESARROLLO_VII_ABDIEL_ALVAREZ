<?php
require_once "config_mysqli.php";

echo "<h1>Gestión con Procedimientos Almacenados (MySQLi)</h1>";
echo "<hr>";

function registrarVenta($conn, $cliente_id, $producto_id, $cantidad) {
    echo "<h3>1. Registrando Venta (Ejemplo)...</h3>";
    $query = "CALL sp_registrar_venta(?, ?, ?, @venta_id)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $cliente_id, $producto_id, $cantidad);
    
    try {
        if (mysqli_stmt_execute($stmt)) {
            while (mysqli_next_result($conn)) {;} 
            
            $result = mysqli_query($conn, "SELECT @venta_id as venta_id");
            $row = mysqli_fetch_assoc($result);
            echo "<div style='color:green;'>✅ Venta registrada con éxito. ID de venta: <strong>{$row['venta_id']}</strong></div>";
        }
    } catch (Exception $e) {
        echo "<div style='color:red;'>❌ Error al registrar la venta: " . $e->getMessage() . "</div>";
    }
    mysqli_stmt_close($stmt);
}

function obtenerEstadisticasCliente($conn, $cliente_id) {
    echo "<h3>2. Estadísticas del Cliente (Ejemplo):</h3>";
    $query = "CALL sp_estadisticas_cliente(?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $cliente_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $estadisticas = mysqli_fetch_assoc($result);
        
        if ($estadisticas) {
            echo "<ul>";
            echo "<li><strong>Nombre:</strong> {$estadisticas['nombre']}</li>";
            echo "<li><strong>Membresía:</strong> {$estadisticas['nivel_membresia']}</li>";
            echo "<li><strong>Total compras:</strong> {$estadisticas['total_compras']}</li>";
            echo "<li><strong>Total gastado:</strong> $" . number_format($estadisticas['total_gastado'], 2) . "</li>";
            echo "</ul>";
        } else {
            echo "Cliente no encontrado.";
        }
        mysqli_stmt_close($stmt);
        while (mysqli_next_result($conn)) {;}
    }
}

function procesarDevolucion($conn, $venta_id, $producto_id, $cantidad) {
    echo "<h3>3. Procesar Devolución (Tarea A):</h3>";
    $query = "CALL sp_procesar_devolucion(?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $venta_id, $producto_id, $cantidad);

    try {
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            echo "<div style='background-color:#e6fffa; padding:10px; border:1px solid green;'>";
            echo "🔄 " . ($row['mensaje'] ?? 'Proceso completado');
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color:red;'>Error: " . $e->getMessage() . "</div>";
    }
    mysqli_stmt_close($stmt);
    while (mysqli_next_result($conn)) {;}
}

function verificarDescuentos($conn, $cliente_id) {
    echo "<h3>4. Verificar Descuento y Nivel (Tarea B):</h3>";
    
    $query = "CALL sp_calcular_descuento_cliente(?, @descuento, @nivel)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $cliente_id);
    
    if (mysqli_stmt_execute($stmt)) {
        while (mysqli_next_result($conn)) {;} 
        
        $result = mysqli_query($conn, "SELECT @descuento as pct, @nivel as lvl");
        $row = mysqli_fetch_assoc($result);
        
        echo "Cliente ID $cliente_id:<br>";
        echo "Nuevo Nivel: <strong>{$row['lvl']}</strong><br>";
        echo "Descuento aplicable: <strong>{$row['pct']}%</strong>";
    }
    mysqli_stmt_close($stmt);
}

function reporteReposicion($conn) {
    echo "<h3>5. Reporte de Reposición de Stock (Tarea C):</h3>";
    $umbral = 10; 
    $query = "CALL sp_reporte_reposicion_stock(?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $umbral);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Producto</th><th>Stock Actual</th><th>Sugerido Reponer</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['nombre']}</td>";
            echo "<td style='color:red'>{$row['stock']}</td>";
            echo "<td>+{$row['cantidad_sugerida_reposicion']} unid.</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    mysqli_stmt_close($stmt);
    while (mysqli_next_result($conn)) {;}
}

function calcularComisiones($conn) {
    echo "<h3>6. Cálculo de Comisiones del Mes (Tarea D):</h3>";
    $mes = date('m'); 
    $anio = date('Y'); 
    
    $query = "CALL sp_calcular_comisiones_mes(?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $mes, $anio);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        echo "Mes: $mes/$anio<br>";
        echo "Ventas totales: $" . number_format($row['monto_total_ventas'], 2) . "<br>";
        echo "Comisión a pagar (5%): <strong>$" . number_format($row['comision_calculada'], 2) . "</strong>";
    }
    mysqli_stmt_close($stmt);
}

registrarVenta($conn, 2, 3, 2); 

obtenerEstadisticasCliente($conn, 2);

procesarDevolucion($conn, 1, 1, 1);

verificarDescuentos($conn, 2);

reporteReposicion($conn);

calcularComisiones($conn);

mysqli_close($conn);
?>