<?php
require_once "config_pdo.php";

try {
    $pdo->query("SELECT 1 FROM log_transacciones_fallidas LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS log_transacciones_fallidas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT,
        error_mensaje TEXT,
        punto_fallo VARCHAR(100),
        fecha_falla TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

try {
    $pdo->query("SELECT puntos_lealtad FROM clientes LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE clientes ADD COLUMN puntos_lealtad INT DEFAULT 1000");
}

class TransactionManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function setIsolationLevel($level) {
        $this->pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL " . $level);
        echo "Nivel de aislamiento establecido a: <strong>$level</strong><br>";
    }
    
    public function demonstrateDirtyRead() {
        echo "<h3>1. Demostración de Lectura Sucia (Dirty Read):</h3>";
        try {
            $this->setIsolationLevel('READ UNCOMMITTED');
            echo "<em>Esta demostración necesita un script concurrente para ver el efecto.</em><br>";
            $this->pdo->rollBack();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function demonstrateRepeatableRead() {
        echo "<h3>2. Demostración de Lectura Repetible:</h3>";
        try {
            $this->setIsolationLevel('REPEATABLE READ');
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->query("SELECT precio FROM productos WHERE id = 1");
            $precio1 = $stmt->fetchColumn();
            
            $stmt = $this->pdo->query("SELECT precio FROM productos WHERE id = 1");
            $precio2 = $stmt->fetchColumn();
            
            echo "Primera lectura: $precio1 | Segunda lectura: $precio2<br>";
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
}

class ComplexTransactionManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function procesarPedidoConSavepoints($cliente_id, $items) {
        echo "<h3>3. Procesamiento de Pedido con Savepoints (Tarea A):</h3>";
        
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("INSERT INTO ventas (cliente_id, total, estado) VALUES (?, 0, 'pendiente')");
            $stmt->execute([$cliente_id]);
            $venta_id = $this->pdo->lastInsertId();
            echo "Venta $venta_id creada.<br>";
            
            $this->pdo->exec("SAVEPOINT pedido_creado");
            
            $total_venta = 0;
            
            foreach ($items as $item) {
                $stmt = $this->pdo->prepare("SELECT stock, precio FROM productos WHERE id = ? FOR UPDATE");
                $stmt->execute([$item['producto_id']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($producto['stock'] < $item['cantidad']) {
                    $this->pdo->exec("ROLLBACK TO SAVEPOINT pedido_creado");
                    throw new Exception("Stock insuficiente para producto {$item['producto_id']}. Transacción parcial revertida.");
                }
                
                $subtotal = $producto['precio'] * $item['cantidad'];
                $total_venta += $subtotal;
            }
            
            $this->pdo->exec("SAVEPOINT stock_reservado");
            echo "Stock reservado.<br>";

            if ($cliente_id == 99) { 
                throw new Exception("Error en el procesamiento del pago.");
            }
            
            $stmt = $this->pdo->prepare("UPDATE ventas SET total = ?, estado = 'completada' WHERE id = ?");
            $stmt->execute([$total_venta, $venta_id]);
            
            $this->pdo->commit();
            echo "Venta $venta_id procesada exitosamente con COMMIT.<br>";
            
        } catch (Exception $e) {
            if ($this->isSavepointException($e)) {
                $this->pdo->exec("ROLLBACK TO SAVEPOINT pedido_creado"); 
            }
            $this->pdo->rollBack();
            $this->logTransaccionFallida($cliente_id, $e->getMessage(), 'procesarPedidoConSavepoints');
            echo "<div style='color:red;'> Transacción fallida. Todo revertido. Error: {$e->getMessage()}</div>";
        }
    }

    private function isSavepointException(Exception $e) {
        return strpos($e->getMessage(), 'Stock insuficiente') !== false;
    }

    public function logTransaccionFallida($cliente_id, $mensaje, $punto) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO log_transacciones_fallidas (cliente_id, error_mensaje, punto_fallo) VALUES (?, ?, ?)");
            $stmt->execute([$cliente_id, $mensaje, $punto]);
        } catch (Exception $e) {
        }
    }
}

class DeadlockManager {
    private $pdo;
    private $maxRetries = 3;
    private $retryDelay = 1; 
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function executeWithDeadlockRetry(callable $operation, $cliente_id, $punto_falla) {
        $attempts = 0;
        
        while ($attempts < $this->maxRetries) {
            try {
                $this->pdo->beginTransaction();
                $result = $operation($this->pdo);
                $this->pdo->commit();
                return $result;
                
            } catch (PDOException $e) {
                $this->pdo->rollBack();
                
                if ($e->errorInfo[1] === 1213 && $attempts < $this->maxRetries - 1) {
                    $attempts++;
                    echo "Deadlock detectado, reintentando (intento $attempts)...<br>";
                    sleep($this->retryDelay);
                    continue;
                }
                
                $this->logTransaccionFallida($cliente_id, $e->getMessage(), $punto_falla);
                throw $e;
            } catch (Exception $e) {
                 $this->pdo->rollBack();
                 $this->logTransaccionFallida($cliente_id, $e->getMessage(), $punto_falla);
                 throw $e;
            }
        }
    }
    
    public function logTransaccionFallida($cliente_id, $mensaje, $punto) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO log_transacciones_fallidas (cliente_id, error_mensaje, punto_fallo) VALUES (?, ?, ?)");
            $stmt->execute([$cliente_id, $mensaje, $punto]);
        } catch (Exception $e) {
        }
    }

    public function transferirStockSegura($origen_id, $destino_id, $cantidad) {
        echo "<h3>4. Transferencia de Stock (Concurrencia - Tarea B):</h3>";
        return $this->executeWithDeadlockRetry(function($pdo) use ($origen_id, $destino_id, $cantidad) {
            $ids = [$origen_id, $destino_id];
            sort($ids);
            
            foreach ($ids as $id) {
                $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ? FOR UPDATE");
                $stmt->execute([$id]);
            }
            
            $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
            $stmt->execute([$origen_id]);
            $stock_origen = $stmt->fetchColumn();
            
            if ($stock_origen < $cantidad) {
                throw new Exception("Stock insuficiente en origen.");
            }
            
            $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?")->execute([$cantidad, $origen_id]);
            $pdo->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?")->execute([$cantidad, $destino_id]);
            
            echo "Transferencia de $cantidad de Producto $origen_id a $destino_id exitosa.<br>";
            return true;
        }, 0, 'transferirStockSegura'); 
    }

    public function canjearPuntosYComprar($cliente_id, $monto_a_pagar, $puntos_requeridos) {
        echo "<h3>5. Canje de Puntos y Compra (Transacción Distribuida - Tarea C):</h3>";
        return $this->executeWithDeadlockRetry(function($pdo) use ($cliente_id, $monto_a_pagar, $puntos_requeridos) {
            
            $stmt = $pdo->prepare("SELECT puntos_lealtad FROM clientes WHERE id = ? FOR UPDATE");
            $stmt->execute([$cliente_id]);
            $puntos_actuales = $stmt->fetchColumn();
            
            if ($puntos_actuales < $puntos_requeridos) {
                throw new Exception("Puntos de lealtad insuficientes para el canje.");
            }
            
            $pdo->prepare("UPDATE clientes SET puntos_lealtad = puntos_lealtad - ? WHERE id = ?")->execute([$puntos_requeridos, $cliente_id]);
            
            $stmt = $pdo->prepare("INSERT INTO ventas (cliente_id, total, estado) VALUES (?, ?, 'completada')");
            $stmt->execute([$cliente_id, $monto_a_pagar]);
            
            echo "Canje exitoso. Se descontaron $puntos_requeridos puntos y se registró la venta de $$monto_a_pagar.<br>";
            return true;
        }, $cliente_id, 'canjearPuntosYComprar');
    }
}

$tm = new TransactionManager($pdo);
$tm->demonstrateDirtyRead();
$tm->demonstrateRepeatableRead();
echo "<hr>";

$items_ok = [
    ['producto_id' => 1, 'cantidad' => 1],
    ['producto_id' => 2, 'cantidad' => 1]
];
$ctm = new ComplexTransactionManager($pdo);
$ctm->procesarPedidoConSavepoints(1, $items_ok);

$items_fail = [
    ['producto_id' => 1, 'cantidad' => 1000] 
];
$ctm->procesarPedidoConSavepoints(4, $items_fail); 
echo "<hr>";

$dm = new DeadlockManager($pdo);
$dm->transferirStockSegura(1, 2, 1);
$dm->canjearPuntosYComprar(1, 50.00, 500);

$pdo = null;
?>