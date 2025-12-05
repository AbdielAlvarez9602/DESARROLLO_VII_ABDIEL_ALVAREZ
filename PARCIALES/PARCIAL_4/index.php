<?php
session_start(); // Iniciamos sesión para saber si el usuario está logueado.

// ... (Configuración de DB y Google igual que el archivo anterior) ...

// Función para saber si alguien está logueado
function is_logged() {
    return !empty($_SESSION['user']); // Devuelve verdadero si existe la variable de sesión 'user'.
}

// Esta función genera el enlace largo y complicado de Google para iniciar sesión
function google_oauth_url() {
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => 'email profile', // Qué datos queremos pedirle
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'select_account' // Obliga a preguntar qué cuenta usar
    ];
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

// --- LÓGICA DE BÚSQUEDA (API EXTERNA) ---
$searchResults = [];
$searchTerm = $_GET['q'] ?? ''; // Si escribieron algo en el buscador, lo tomamos.

if (!empty($searchTerm)) {
    // Llamamos a la API pública de Google Books
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($searchTerm) . "&maxResults=10";
    
    $response = file_get_contents($url); // Hacemos la petición
    $data = json_decode($response, true); // Convertimos JSON a Array
    $searchResults = $data['items'] ?? []; // Guardamos los libros encontrados
}

// --- LÓGICA DE LIBROS GUARDADOS (BD LOCAL) ---
$savedBooks = [];
if (is_logged()) {
    try {
        $pdo = pdo();
        // Le pedimos a la base de datos SOLO los libros de ESTE usuario ($_SESSION['user']['id'])
        $stmt = $pdo->prepare("SELECT * FROM libros_guardados WHERE user_id = :u ORDER BY fecha_guardado DESC");
        $stmt->execute([':u' => $_SESSION['user']['id']]);
        $savedBooks = $stmt->fetchAll(); // Guardamos la lista en una variable
    } catch (PDOException $e) {
        echo "Error de base de datos al cargar libros: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
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
            <input type="text" name="q" placeholder="Introduce el título o autor..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button class="button">Buscar</button>
        </form>
    </div>

    <?php if (!empty($searchResults)): ?>
    <div class="card">
        <h3>Resultados de Búsqueda</h3>
        <div class="book-grid">
            <?php foreach ($searchResults as $book): 
                // Extraemos datos limpios del JSON de Google
                $id = $book["id"];
                $title = $book["volumeInfo"]["title"] ?? "Sin título";
            ?>
            <div class="card" style="padding:10px">
                <h4><?= htmlspecialchars($title) ?></h4>
                
                <?php if (is_logged()): ?>
                <form method="post" action="save.php">
                    <input type="hidden" name="google_books_id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="titulo" value="<?= htmlspecialchars($title) ?>">
                    <textarea name="reseña_personal" placeholder="Tu reseña..."></textarea>
                    <button class="button">Guardar</button>
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
        <div class="card">
                    <h4><?= htmlspecialchars($book['titulo']) ?></h4>
                    
                    <form method="post" action="save.php">
                        <input type="hidden" name="action" value="update_review">
                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                        <textarea name="reseña_personal"><?= htmlspecialchars($book['reseña_personal'] ?? '') ?></textarea>
                        <button class="button">Actualizar Reseña</button>
                    </form>

                    <form method="post" action="save.php" onsubmit="return confirm('¿Seguro?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                        <button class="button red">Eliminar Libro</button>
                    </form>
                </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
