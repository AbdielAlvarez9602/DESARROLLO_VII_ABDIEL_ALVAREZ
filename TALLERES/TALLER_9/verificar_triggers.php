<?php
require_once "config_pdo.php";

echo "<h1>Prueba de Triggers (Automatización)</h1><hr>";

function verificarCambiosPrecio($pdo, $producto_id, $nuevo_precio) {
    try {
        $stmt = $pdo->prepare("UPDATE productos SET precio = ? WHERE id = ?");
        $stmt->execute([$nuevo_precio, $producto_id]);
        
        $stmt = $pdo->prepare("SELECT * FROM historial_precios WHERE producto_id = ? ORDER BY fecha_cambio DESC LIMIT 1");
        $stmt->execute([$producto_id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>1. Auditoría de Precios (Trigger Update):</h3>";
        if($log) {
            echo "Producto ID: $producto_id <br>";
            echo "Precio Anterior: $<strong>{$log['precio_anterior']}</strong> -> Nuevo: $<strong>{$log['precio_nuevo']}</strong><br>";
        }
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function verificarMovimientoInventario($pdo, $producto_id, $nueva_cantidad) {
    try {
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $stock_actual = $stmt->fetchColumn();

        $stmt = $pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->execute([$nueva_cantidad, $producto_id]);
        
        $stmt = $pdo->prepare("SELECT * FROM movimientos_inventario WHERE producto_id = ? ORDER BY fecha_movimiento DESC LIMIT 1");
        $stmt->execute([$producto_id]);
        $mov = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>2. Auditoría de Inventario:</h3>";
        if($mov) {
            echo "Movimiento: <strong>{$mov['tipo_movimiento']}</strong> (Dif: {$mov['cantidad']})<br>";
            echo "Stock: {$mov['stock_anterior']} -> {$mov['stock_nuevo']}<br>";
        }
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function testMembresiaAutomatica($pdo) {
    echo "<h3>3. Membresía Automática (Tarea A):</h3>";
    try {
        $cliente_id = 4; 
        
        $pdo->query("UPDATE clientes SET nivel_membresia = 'básico' WHERE id = $cliente_id");
        echo "Nivel inicial: Básico<br>";
        
        $pdo->query("INSERT INTO ventas (cliente_id, total, estado) VALUES ($cliente_id, 6000.00, 'completada')");
        
        $stmt = $pdo->query("SELECT nombre, nivel_membresia FROM clientes WHERE id = $cliente_id");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Insertando venta de $6000...<br>";
        echo "Nuevo Nivel: <strong>{$res['nivel_membresia']}</strong> (Debería ser VIP)<br>";
        
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function testEstadisticasCategoria($pdo) {
    echo "<h3>4. Estadísticas Categoría en Tiempo Real (Tarea B):</h3>";
    try {
        $prod_id = 2; 
        $subtotal = 2000.00;
        
        echo "Registrando venta de producto ID $prod_id por $$subtotal...<br>";
        
        $pdo->query("INSERT INTO ventas (cliente_id, total) VALUES (1, $subtotal)");
        $venta_id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, 2, 1000, ?)");
        $stmt->execute([$venta_id, $prod_id, $subtotal]);
        
        $stmt = $pdo->query("SELECT * FROM estadisticas_categorias");
        echo "<table border='1'><tr><th>Cat ID</th><th>Total Acumulado</th></tr>";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo "<tr><td>{$row['categoria_id']}</td><td>${$row['total_ventas']}</td></tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function testAlertaStock($pdo) {
    echo "<h3>5. Alerta de Stock Crítico (Tarea C):</h3>";
    try {
        $prod_id = 3; 
        $pdo->query("UPDATE productos SET stock = 20 WHERE id = $prod_id");
        
        echo "Bajando stock de 20 a 3...<br>";
        $pdo->query("UPDATE productos SET stock = 3 WHERE id = $prod_id");
        
        $stmt = $pdo->query("SELECT * FROM alertas_stock ORDER BY id DESC LIMIT 1");
        $alerta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($alerta) {
            echo "<div style='color:red; border:1px solid red; padding:5px;'>";
            echo " Notificación: {$alerta['mensaje']} <br>Fecha: {$alerta['fecha_alerta']}";
            echo "</div>";
        }
        
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

function testHistorialCliente($pdo) {
    echo "<h3>6. Historial Estado Cliente (Tarea D):</h3>";
    try {
        $cliente_id = 1;
        
        echo "Cambiando estado de cliente 1 a 'inactivo'...<br>";
        $pdo->query("UPDATE clientes SET estado = 'inactivo' WHERE id = $cliente_id");
        
        $stmt = $pdo->query("SELECT * FROM log_estado_clientes ORDER BY id DESC LIMIT 1");
        $log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($log) {
            echo "Cambio detectado: <strong>{$log['estado_anterior']}</strong> -> <strong>{$log['estado_nuevo']}</strong><br>";
            echo "Fecha: {$log['fecha_cambio']}";
        }
        
        $pdo->query("UPDATE clientes SET estado = 'activo' WHERE id = $cliente_id");
        
    } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
}

verificarCambiosPrecio($pdo, 1, 1050.00);
verificarMovimientoInventario($pdo, 1, 25);
testMembresiaAutomatica($pdo);
testEstadisticasCategoria($pdo);
testAlertaStock($pdo);
testHistorialCliente($pdo);

$pdo = null;
?>