<?php
session_start();
// ... (Configuración de conexión a BD) ...

// SEGURIDAD: Si no está logueado, matamos el proceso. Nadie puede guardar libros sin usuario.
if (!isset($_SESSION['user'])) {
    die("Debes iniciar sesión.");
}

$pdo = pdo();

// --- 1. OPERACIÓN ELIMINAR (DELETE) ---
if (isset($_POST["action"]) && $_POST["action"] === "delete") {
    // Preparamos la consulta. IMPORTANTE: 'AND user_id = :u' asegura que solo puedas borrar TUS propios libros.
    $pdo->prepare("DELETE FROM libros_guardados WHERE id = :id AND user_id = :u")
        ->execute([
            ':id' => $_POST["id"],
            ':u' => $_SESSION["user"]["id"] // Usamos el ID de la sesión
        ]);
    header("Location: index.php"); // Volvemos al inicio
    exit;
}

// --- 2. OPERACIÓN ACTUALIZAR RESEÑA (UPDATE) ---
if (isset($_POST["action"]) && $_POST["action"] === "update_review") {
    // Actualizamos solo el campo 'reseña_personal'
    $pdo->prepare("UPDATE libros_guardados SET reseña_personal = :r WHERE id = :id AND user_id = :u")
        ->execute([
            ':r' => $_POST["reseña_personal"],
            ':id' => $_POST["id"],
            ':u' => $_SESSION["user"]["id"]
        ]);
    header("Location: index.php");
    exit;
}

// --- 3. OPERACIÓN GUARDAR NUEVO LIBRO (CREATE/INSERT) ---

// Paso A: Evitar duplicados.
// Verificamos si este usuario ya guardó este libro antes (buscando por el ID de Google).
$stmt = $pdo->prepare("SELECT id FROM libros_guardados WHERE google_books_id = :gid AND user_id = :uid");
$stmt->execute([
    ':gid' => $_POST['google_books_id'],
    ':uid' => $_SESSION['user']['id']
]);
if ($stmt->fetch()) {
    // Si la consulta encuentra algo, redirigimos sin guardar.
    header("Location: index.php?status=duplicate");
    exit;
}

// Paso B: Insertar.
$pdo->prepare("
    INSERT INTO libros_guardados
    (user_id, google_books_id, titulo, autor, imagen_portada, reseña_personal, fecha_guardado)
    VALUES
    (:uid, :gid, :t, :a, :i, :r, NOW())
")->execute([
    ':uid' => $_SESSION["user"]["id"], // El dueño del libro es el usuario en sesión
    ':gid' => $_POST["google_books_id"],
    ':t' => $_POST["titulo"],
    ':a' => $_POST["autor"],
    ':i' => $_POST["imagen"],
    ':r' => $_POST["reseña_personal"]
]);

header("Location: index.php");
exit;
?>
