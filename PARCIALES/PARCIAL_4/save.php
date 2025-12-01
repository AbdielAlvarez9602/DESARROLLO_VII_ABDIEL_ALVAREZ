<?php
session_start();

$dbHost = '127.0.0.1';
$dbName = 'mini_biblio';
$dbUser = 'root';
$dbPass = 'root'; // <-- Contraseña corregida

function pdo() {
    global $dbHost, $dbName, $dbUser, $dbPass;
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    return $pdo;
}

if (!isset($_SESSION['user'])) {
    die("Debes iniciar sesión.");
}

$pdo = pdo();

// --- OPERACIÓN ELIMINAR (DELETE) ---
if (isset($_POST["action"]) && $_POST["action"] === "delete") {
    // Usamos sentencias preparadas para seguridad (SQL Injection)
    $pdo->prepare("DELETE FROM libros_guardados WHERE id = :id AND user_id = :u")
        ->execute([
            ':id' => $_POST["id"],
            ':u' => $_SESSION["user"]["id"]
        ]);
    header("Location: index.php");
    exit;
}

// --- OPERACIÓN ACTUALIZAR RESEÑA (UPDATE) ---
if (isset($_POST["action"]) && $_POST["action"] === "update_review") {
    // Usamos sentencias preparadas para seguridad (SQL Injection)
    $pdo->prepare("UPDATE libros_guardados SET reseña_personal = :r WHERE id = :id AND user_id = :u")
        ->execute([
            ':r' => $_POST["reseña_personal"],
            ':id' => $_POST["id"],
            ':u' => $_SESSION["user"]["id"]
        ]);
    header("Location: index.php");
    exit;
}

// --- OPERACIÓN GUARDAR LIBRO (CREATE) ---

// 1. Validar que el libro no esté ya guardado
$stmt = $pdo->prepare("SELECT id FROM libros_guardados WHERE google_books_id = :gid AND user_id = :uid");
$stmt->execute([
    ':gid' => $_POST['google_books_id'],
    ':uid' => $_SESSION['user']['id']
]);
if ($stmt->fetch()) {
    // Si ya existe, redirigir y evitar duplicados
    header("Location: index.php?status=duplicate");
    exit;
}

// 2. Insertar nuevo libro
// Usamos sentencias preparadas para seguridad (SQL Injection)
$pdo->prepare("
    INSERT INTO libros_guardados
    (user_id, google_books_id, titulo, autor, imagen_portada, reseña_personal, fecha_guardado)
    VALUES
    (:uid, :gid, :t, :a, :i, :r, NOW())
")->execute([
    ':uid' => $_SESSION["user"]["id"],
    ':gid' => $_POST["google_books_id"],
    ':t' => $_POST["titulo"],
    ':a' => $_POST["autor"],
    ':i' => $_POST["imagen"],
    ':r' => $_POST["reseña_personal"]
]);

header("Location: index.php");
exit;