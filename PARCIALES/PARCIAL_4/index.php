<?php
session_start();

// ---------------- CONFIG ----------------
$dbHost = '127.0.0.1';
$dbName = 'mini_biblio';
$dbUser = 'root';
$dbPass = 'root'; // <-- Contraseña corregida
$googleApiKey = ''; // Si usas API key para busquedas, ponla aquí.

// Google OAuth Credentials
define('GOOGLE_CLIENT_ID', '696786352660-ojv4eh0ttjf42uf9vf6ncuhgful48epk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/PARCIALES/PARCIAL_4/oauth_callback.php');
// ----------------------------------------

function pdo() {
    global $dbHost, $dbName, $dbUser, $dbPass;
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

function is_logged() {
    return !empty($_SESSION['user']);
}

// Construir URL de Google OAuth
function google_oauth_url() {
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => 'email profile',
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'select_account'
    ];
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

$searchResults = [];
$searchTerm = $_GET['q'] ?? '';

if (!empty($searchTerm)) {
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($searchTerm) . "&maxResults=10";
    if (!empty($googleApiKey)) {
        $url .= "&key=" . $googleApiKey;
    }
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    $searchResults = $data['items'] ?? [];
}

// Lógica para OBTENER LIBROS GUARDADOS (READ)
$savedBooks = [];
if (is_logged()) {
    try {
        $pdo = pdo();
        $stmt = $pdo->prepare("SELECT * FROM libros_guardados WHERE user_id = :u ORDER BY fecha_guardado DESC");
        $stmt->execute([':u' => $_SESSION['user']['id']]);
        $savedBooks = $stmt->fetchAll();
    } catch (PDOException $e) {
        // En un entorno de producción, esto debería ser un error más amigable.
        echo "Error de base de datos al cargar libros: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mini Biblioteca Personal</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 1200px; margin: auto; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .book-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .book-img { max-width: 100px; margin-right: 15px; float: left; }
        .book-card-content { overflow: hidden; }
        .button { background-color: #5cb85c; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; }
        .button.red { background-color: #d9534f; }
        .button:hover { opacity: 0.8; }
        .user-info { font-weight: bold; }
        textarea { resize: vertical; padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header card">
        <h1>📚 Mini Biblioteca Personal</h1>
        <div>
            <?php if (is_logged()): ?>
                <span class="user-info">Bienvenido, <?= htmlspecialchars($_SESSION['user']['nombre']) ?></span>
                <a href="logout.php" class="button red">Cerrar Sesión</a>
            <?php else: ?>
                <a href="<?= google_oauth_url() ?>" class="button">Iniciar Sesión con Google</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h3>Buscar Libros (Google Books API)</h3>
        <form method="get" action="index.php">
            <input type="text" name="q" placeholder="Introduce el título o autor..." value="<?= htmlspecialchars($searchTerm) ?>" style="width: 80%; padding: 10px;">
            <button class="button">Buscar</button>
        </form>
    </div>

    <?php if (!empty($searchResults)): ?>
    <div class="card">
        <h3>Resultados de Búsqueda</h3>
        <div class="book-grid">
            <?php foreach ($searchResults as $book):
                $id = $book["id"];
                $title = $book["volumeInfo"]["title"] ?? "Sin título";
                $authors = isset($book["volumeInfo"]["authors"]) ? implode(", ", $book["volumeInfo"]["authors"]) : "Autor desconocido";
                $thumb = $book["volumeInfo"]["imageLinks"]["thumbnail"] ?? "";
            ?>
            <div class="card" style="padding:10px">
                <?php if ($thumb): ?>
                <img class="book-img" src="<?= $thumb ?>">
                <?php endif; ?>

                <h4><?= htmlspecialchars($title) ?></h4>
                <p><?= htmlspecialchars($authors) ?></p>

                <?php if (is_logged()): ?>
                <form method="post" action="save.php">
                    <input type="hidden" name="google_books_id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="titulo" value="<?= htmlspecialchars($title) ?>">
                    <input type="hidden" name="autor" value="<?= htmlspecialchars($authors) ?>">
                    <input type="hidden" name="imagen" value="<?= htmlspecialchars($thumb) ?>">
                    <textarea name="reseña_personal" placeholder="Tu reseña..." style="width:100%;height:60px;"></textarea>
                    <button class="button" style="margin-top:5px;">Guardar</button>
                </form>
                <?php else: ?>
                    <p>Inicia sesión para guardar libros.</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (is_logged()): ?>
    <div class="card">
        <h3>📖 Mi Biblioteca (<?= count($savedBooks) ?> Libros Guardados)</h3>
        <?php if (empty($savedBooks)): ?>
            <p>Aún no tienes libros guardados. ¡Busca y añade algunos!</p>
        <?php else: ?>
            <div class="book-grid">
                <?php foreach ($savedBooks as $book): ?>
                <div class="card" style="padding:10px;">
                    <?php if ($book['imagen_portada']): ?>
                    <img class="book-img" src="<?= htmlspecialchars($book['imagen_portada']) ?>">
                    <?php endif; ?>

                    <h4><?= htmlspecialchars($book['titulo']) ?></h4>
                    <p>Autor: <?= htmlspecialchars($book['autor'] ?? 'N/A') ?></p>
                    
                    <form method="post" action="save.php">
                        <input type="hidden" name="action" value="update_review">
                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                        <textarea name="reseña_personal" placeholder="Tu reseña..." style="width:100%;height:60px;margin-bottom:5px;"><?= htmlspecialchars($book['reseña_personal'] ?? '') ?></textarea>
                        <button class="button" style="background-color:#337ab7;">Actualizar Reseña</button>
                    </form>

                    <form method="post" action="save.php" style="margin-top: 10px;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este libro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                        <button class="button red">Eliminar Libro</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>