<?php
require_once "config_pdo.php";

class QueryBuilder {
    private $pdo;
    private $table;
    private $conditions = [];
    private $parameters = [];
    private $orderBy = [];
    private $limit = null;
    private $offset = null;
    private $joins = [];
    private $groupBy = [];
    private $having = [];
    private $fields = ['*'];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function select($fields) {
        $this->fields = is_array($fields) ? $fields : func_get_args();
        return $this;
    }

    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = ':' . str_replace('.', '_', $column) . count($this->parameters);
        $this->conditions[] = "$column $operator $placeholder";
        $this->parameters[$placeholder] = $value;

        return $this;
    }

    public function whereIn($column, array $values) {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = ':' . str_replace('.', '_', $column) . $i;
            $placeholders[] = $placeholder;
            $this->parameters[$placeholder] = $value;
        }

        $this->conditions[] = "$column IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }

    public function join($table, $first, $operator, $second, $type = 'INNER') {
        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'conditions' => "$first $operator $second"
        ];
        return $this;
    }

    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = "$column $direction";
        return $this;
    }

    public function groupBy($columns) {
        $this->groupBy = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function having($condition, $value) {
        $placeholder = ':having' . count($this->parameters);
        $this->having[] = "$condition $placeholder";
        $this->parameters[$placeholder] = $value;
        return $this;
    }

    public function limit($limit, $offset = null) {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function buildQuery() {
        $sql = "SELECT " . implode(', ', $this->fields) . " FROM " . $this->table;

        foreach ($this->joins as $join) {
            $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['conditions']}";
        }

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        }

        if (!empty($this->having)) {
            $sql .= " HAVING " . implode(' AND ', $this->having);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
            if ($this->offset !== null) {
                $sql .= " OFFSET " . $this->offset;
            }
        }

        return $sql;
    }

    public function execute() {
        $sql = $this->buildQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->parameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class InsertBuilder {
    private $pdo;
    private $table;
    private $data = [];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function into($table) {
        $this->table = $table;
        return $this;
    }

    public function values(array $data) {
        $this->data = $data;
        return $this;
    }

    public function execute() {
        $columns = array_keys($this->data);
        $placeholders = array_map(function($col) {
            return ':' . $col;
        }, $columns);

        $sql = "INSERT INTO {$this->table} (" . 
               implode(', ', $columns) . 
               ") VALUES (" . 
               implode(', ', $placeholders) . 
               ")";

        $stmt = $this->pdo->prepare($sql);
        
        $params = array_combine($placeholders, array_values($this->data));
        return $stmt->execute($params);
    }
}

class UpdateBuilder {
    private $pdo;
    private $table;
    private $data = [];
    private $conditions = [];
    private $parameters = [];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function set(array $data) {
        $this->data = $data;
        return $this;
    }

    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = ':where_' . str_replace('.', '_', $column);
        $this->conditions[] = "$column $operator $placeholder";
        $this->parameters[$placeholder] = $value;

        return $this;
    }

    public function execute() {
        $setParts = [];
        foreach ($this->data as $column => $value) {
            $placeholder = ':set_' . $column;
            $setParts[] = "$column = $placeholder";
            $this->parameters[$placeholder] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);

        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->parameters);
    }
}

function filtrarProductos($pdo, $filtros) {
    $qb = new QueryBuilder($pdo);
    $qb->table('productos p')
       ->select('p.id', 'p.nombre', 'p.precio', 'p.stock', 'c.nombre as categoria')
       ->join('categorias c', 'p.categoria_id', '=', 'c.id');

    if (!empty($filtros['precio_min'])) {
        $qb->where('p.precio', '>=', $filtros['precio_min']);
    }

    if (!empty($filtros['precio_max'])) {
        $qb->where('p.precio', '<=', $filtros['precio_max']);
    }

    if (!empty($filtros['categoria_id'])) {
        $qb->where('p.categoria_id', '=', $filtros['categoria_id']);
    }

    if (isset($filtros['disponible']) && $filtros['disponible'] === true) {
        $qb->where('p.stock', '>', 0);
    }

    return $qb->execute();
}

function generarReporteDinamico($pdo, $tabla, $campos, $filtros) {
    $qb = new QueryBuilder($pdo);
    $qb->table($tabla)->select($campos);

    foreach ($filtros as $columna => $valor) {
        if (is_array($valor)) {
            $qb->whereIn($columna, $valor);
        } else {
            $qb->where($columna, '=', $valor);
        }
    }

    return $qb->execute();
}

function buscarVentas($pdo, $criterios) {
    $qb = new QueryBuilder($pdo);
    $qb->table('ventas v')
       ->select('v.id', 'v.fecha_venta', 'v.total', 'c.nombre as cliente', 'c.email')
       ->join('clientes c', 'v.cliente_id', '=', 'c.id');

    if (!empty($criterios['fecha_inicio'])) {
        $qb->where('v.fecha_venta', '>=', $criterios['fecha_inicio']);
    }

    if (!empty($criterios['fecha_fin'])) {
        $qb->where('v.fecha_venta', '<=', $criterios['fecha_fin']);
    }

    if (!empty($criterios['cliente_id'])) {
        $qb->where('v.cliente_id', '=', $criterios['cliente_id']);
    }

    if (!empty($criterios['monto_min'])) {
        $qb->where('v.total', '>=', $criterios['monto_min']);
    }

    if (!empty($criterios['producto_id'])) {
        $qb->join('detalles_venta dv', 'v.id', '=', 'dv.venta_id');
        $qb->where('dv.producto_id', '=', $criterios['producto_id']);
    }

    return $qb->execute();
}

function actualizacionMasivaProductos($pdo, $cambios, $criterios) {
    $ub = new UpdateBuilder($pdo);
    $ub->table('productos')->set($cambios);

    if (!empty($criterios['categoria_id'])) {
        $ub->where('categoria_id', '=', $criterios['categoria_id']);
    }

    if (!empty($criterios['precio_menor_que'])) {
        $ub->where('precio', '<', $criterios['precio_menor_que']);
    }

    if (!empty($criterios['stock_cero'])) {
        $ub->where('stock', '=', 0);
    }

    return $ub->execute();
}

echo "<h3>1. Sistema de Filtrado de Productos</h3>";
$filtrosProductos = [
    'precio_min' => 100,
    'disponible' => true
];
$productos = filtrarProductos($pdo, $filtrosProductos);
echo "<pre>" . print_r($productos, true) . "</pre>";

echo "<h3>2. Generador de Reportes Dinámico</h3>";
$camposReporte = ['nombre', 'email', 'nivel_membresia'];
$filtrosReporte = ['nivel_membresia' => ['premium', 'vip']];
$reporte = generarReporteDinamico($pdo, 'clientes', $camposReporte, $filtrosReporte);
echo "<pre>" . print_r($reporte, true) . "</pre>";

echo "<h3>3. Búsqueda de Ventas</h3>";
$criteriosVentas = [
    'monto_min' => 500,
    'fecha_inicio' => '2023-01-01'
];
$ventas = buscarVentas($pdo, $criteriosVentas);
echo "<pre>" . print_r($ventas, true) . "</pre>";

echo "<h3>4. Actualización Masiva</h3>";
$cambios = ['precio' => 19.99]; 
$criteriosUpdate = ['categoria_id' => 1, 'stock_cero' => true]; 
$resultadoUpdate = actualizacionMasivaProductos($pdo, $cambios, $criteriosUpdate);

if ($resultadoUpdate) {
    echo "Actualización masiva completada con éxito.";
} else {
    echo "Error en la actualización masiva.";
}

$pdo = null;
?>