<?php
require_once "config_pdo.php";

class Paginator {
    protected $pdo;
    protected $table;
    protected $perPage;
    protected $currentPage;
    protected $conditions = [];
    protected $params = [];
    protected $orderBy = '';
    protected $joins = [];
    protected $fields = ['*'];

    public function __construct(PDO $pdo, $table, $perPage = 10) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->perPage = $perPage;
        $this->currentPage = 1;
    }

    public function select($fields) {
        $this->fields = is_array($fields) ? $fields : func_get_args();
        return $this;
    }

    public function where($condition, $params = []) {
        $this->conditions[] = $condition;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function join($join) {
        $this->joins[] = $join;
        return $this;
    }

    public function orderBy($orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function setPage($page) {
        $this->currentPage = max(1, (int)$page);
        return $this;
    }

    public function getTotalRecords() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(" AND ", $this->conditions);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchColumn();
    }

    public function getResults($export = false) {
        $offset = ($this->currentPage - 1) * $this->perPage;
        
        $sql = "SELECT " . implode(", ", $this->fields) . " FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }
        
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(" AND ", $this->conditions);
        }
        
        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }
        
        if (!$export) {
            $sql .= " LIMIT {$this->perPage} OFFSET {$offset}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPageInfo() {
        $totalRecords = $this->getTotalRecords();
        $totalPages = ceil($totalRecords / $this->perPage);

        return [
            'current_page' => $this->currentPage,
            'per_page' => $this->perPage,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages,
            'has_previous' => $this->currentPage > 1,
            'has_next' => $this->currentPage < $totalPages,
            'previous_page' => $this->currentPage - 1,
            'next_page' => $this->currentPage + 1,
            'first_page' => 1,
            'last_page' => $totalPages,
        ];
    }
}

class CacheSystem {
    private $cacheDir = 'cache/';
    private $expiry = 60; 

    public function __construct() {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function get($key) {
        $file = $this->cacheDir . md5($key) . '.json';
        if (file_exists($file) && (time() - filemtime($file) < $this->expiry)) {
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }

    public function set($key, $data) {
        $file = $this->cacheDir . md5($key) . '.json';
        file_put_contents($file, json_encode($data));
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$action = isset($_GET['action']) ? $_GET['action'] : '';

$validLimits = [5, 10, 25, 50, 100];
if (!in_array($limit, $validLimits)) $limit = 10;

$paginator = new Paginator($pdo, 'productos', $limit);
$paginator->select('productos.id', 'productos.nombre', 'productos.precio', 'productos.stock', 'categorias.nombre as categoria')
          ->join('LEFT JOIN categorias ON productos.categoria_id = categorias.id')
          ->where('productos.stock >= ?', [0])
          ->orderBy('productos.id ASC')
          ->setPage($page);

if ($action === 'export') {
    $filename = "productos_" . date('Ymd') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Nombre', 'Precio', 'Stock', 'Categoría']);
    
    $allResults = $paginator->getResults(true); 
    
    foreach ($allResults as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

$cache = new CacheSystem();
$cacheKey = "products_page_{$page}_limit_{$limit}";

$cachedData = $cache->get($cacheKey);

if ($cachedData) {
    $results = $cachedData['results'];
    $pageInfo = $cachedData['info'];
    $fromCache = true;
} else {
    $results = $paginator->getResults();
    $pageInfo = $paginator->getPageInfo();
    $cache->set($cacheKey, ['results' => $results, 'info' => $pageInfo]);
    $fromCache = false;
}

if ($action === 'ajax_scroll') {
    header('Content-Type: application/json');
    echo json_encode([
        'results' => $results,
        'info' => $pageInfo
    ]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paginación Avanzada</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .controls { margin-bottom: 20px; padding: 15px; background: #f4f4f4; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: white; }
        .badge { background: #007bff; color: white; padding: 3px 8px; border-radius: 3px; font-size: 0.8em; }
        .cache-notice { color: green; font-weight: bold; font-size: 0.9em; }
        .pagination { list-style: none; display: flex; gap: 5px; padding: 0; }
        .pagination a, .pagination span { padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; color: #333; }
        .pagination .active { background-color: #007bff; color: white; border-color: #007bff; }
        #loading { display: none; text-align: center; padding: 20px; font-style: italic; color: #666; }
        .btn { padding: 8px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; }
        .toggle-mode { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="toggle-mode">
        <label><input type="checkbox" id="infiniteScrollToggle"> Activar Scroll Infinito</label>
        <?php if($fromCache): ?>
            <span class="cache-notice"> (Datos cargados desde caché)</span>
        <?php endif; ?>
    </div>

    <div class="controls">
        <form method="GET" id="limitForm">
            <label for="limit">Mostrar:</label>
            <select name="limit" id="limit" onchange="document.getElementById('limitForm').submit()">
                <?php foreach($validLimits as $l): ?>
                    <option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
            resultados por página.
        </form>

        <a href="?action=export&limit=<?= $limit ?>&page=<?= $page ?>" class="btn">Descargar CSV</a>
    </div>

    <table id="productTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
            </tr>
        </thead>
        <tbody id="productBody">
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td>$<?= number_format($row['precio'], 2) ?></td>
                <td><?= $row['stock'] ?></td>
                <td><span class="badge"><?= htmlspecialchars($row['categoria']) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="loading">Cargando más productos...</div>

    <div id="paginationControls">
        <ul class="pagination">
            <?php if ($pageInfo['has_previous']): ?>
                <li><a href="?page=1&limit=<?= $limit ?>">First</a></li>
                <li><a href="?page=<?= $pageInfo['previous_page'] ?>&limit=<?= $limit ?>">Prev</a></li>
            <?php endif; ?>

            <li class="active"><?= $pageInfo['current_page'] ?></li>

            <?php if ($pageInfo['has_next']): ?>
                <li><a href="?page=<?= $pageInfo['next_page'] ?>&limit=<?= $limit ?>">Next</a></li>
                <li><a href="?page=<?= $pageInfo['last_page'] ?>&limit=<?= $limit ?>">Last</a></li>
            <?php endif; ?>
        </ul>
        <p>Página <?= $pageInfo['current_page'] ?> de <?= $pageInfo['total_pages'] ?> (Total: <?= $pageInfo['total_records'] ?>)</p>
    </div>

    <script>
        let currentPage = <?= $page ?>;
        let totalPages = <?= $pageInfo['total_pages'] ?>;
        let limit = <?= $limit ?>;
        let isLoading = false;
        const infiniteScrollCheckbox = document.getElementById('infiniteScrollToggle');
        const paginationControls = document.getElementById('paginationControls');
        const loadingDiv = document.getElementById('loading');

        function toggleMode() {
            if (infiniteScrollCheckbox.checked) {
                paginationControls.style.display = 'none';
                window.addEventListener('scroll', handleScroll);
            } else {
                paginationControls.style.display = 'block';
                window.removeEventListener('scroll', handleScroll);
            }
        }

        infiniteScrollCheckbox.addEventListener('change', toggleMode);

        function handleScroll() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                loadMoreData();
            }
        }

        function loadMoreData() {
            if (isLoading || currentPage >= totalPages) return;

            isLoading = true;
            loadingDiv.style.display = 'block';
            currentPage++;

            fetch(`?action=ajax_scroll&page=${currentPage}&limit=${limit}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('productBody');
                    data.results.forEach(item => {
                        const row = `
                            <tr>
                                <td>${item.id}</td>
                                <td>${item.nombre}</td>
                                <td>$${parseFloat(item.precio).toFixed(2)}</td>
                                <td>${item.stock}</td>
                                <td><span class="badge">${item.categoria || 'Sin Cat'}</span></td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                    isLoading = false;
                    loadingDiv.style.display = 'none';
                })
                .catch(err => {
                    console.error('Error:', err);
                    isLoading = false;
                    loadingDiv.style.display = 'none';
                });
        }
    </script>
</body>
</html>