<?php
require_once "config_pdo.php";

class ConsultasOptimizadas {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function buscarProductos($categoria_id, $precio_min, $precio_max) {
        $sql = "SELECT p.id, p.nombre, p.precio, p.stock
                FROM productos p
                USE INDEX (idx_productos_categoria, idx_productos_precio)
                WHERE p.categoria_id = :cat
                AND p.precio BETWEEN :min AND :max
                AND p.stock > 0";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cat' => $categoria_id, ':min' => $precio_min, ':max' => $precio_max]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarProductosTexto($termino) {
        $sql = "SELECT p.*, 
                MATCH(p.nombre, p.descripcion) AGAINST(:termino IN NATURAL LANGUAGE MODE) as relevancia
                FROM productos p
                WHERE MATCH(p.nombre, p.descripcion) AGAINST(:termino IN NATURAL LANGUAGE MODE)
                ORDER BY relevancia DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':termino' => $termino]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTopClientes($limite = 5) {
        $sql = "SELECT c.nombre, c.email, COUNT(v.id) as compras, SUM(v.total) as total_gastado
                FROM clientes c
                JOIN ventas v ON c.id = v.cliente_id
                GROUP BY c.id
                ORDER BY total_gastado DESC
                LIMIT :limite";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function productosSinVentas() {
        $sql = "SELECT p.nombre, p.stock, c.nombre as categoria
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE NOT EXISTS (
                    SELECT 1 FROM detalles_venta dv WHERE dv.producto_id = p.id
                )";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function historialVentasCliente($cliente_id) {
        $sql = "SELECT v.fecha_venta, v.total, v.estado
                FROM ventas v
                USE INDEX (idx_ventas_cliente_fecha)
                WHERE v.cliente_id = :cid
                ORDER BY v.fecha_venta DESC
                LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cid' => $cliente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$opt = new ConsultasOptimizadas($pdo);

echo "<h1>Consultas Optimizadas</h1>";

echo "<h3>1. Búsqueda Fulltext (Ejemplo):</h3>";
$res = $opt->buscarProductosTexto("laptop");
echo "<pre>" . print_r($res, true) . "</pre>";

echo "<h3>2. Top Clientes (Tarea):</h3>";
$res = $opt->obtenerTopClientes(3);
echo "<pre>" . print_r($res, true) . "</pre>";

echo "<h3>3. Productos Sin Rotación (Tarea):</h3>";
$res = $opt->productosSinVentas();
if(empty($res)) echo "Todos los productos tienen ventas.";
else echo "<pre>" . print_r($res, true) . "</pre>";

$pdo = null;
?>