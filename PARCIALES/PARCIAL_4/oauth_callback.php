<?php
session_start(); // 1. Iniciamos la sesión para poder guardar datos del usuario (como su nombre o ID) y recordarlos en otras páginas.

// ---------------- CONFIGURACIÓN ----------------
$dbHost = '127.0.0.1';
$dbName = 'mini_biblio';
$dbUser = 'root';
$dbPass = 'root'; 

// Credenciales de Google
define('GOOGLE_CLIENT_ID', '696786352660-ojv4eh0ttjf42uf9vf6ncuhgful48epk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/PARCIALES/PARCIAL_4/oauth_callback.php');
// ----------------------------------------

// Función para conectar a la Base de Datos (BD)
function pdo() {
    global $dbHost, $dbName, $dbUser, $dbPass;
    return new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Si hay error, avísame.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
        ]
    );
}

// Si Google no nos mandó un código en la URL (?code=...), algo salió mal.
if (!isset($_GET['code'])) {
    echo "Error: Google no devolvió ningún código.";
    exit;
}

$code = $_GET['code']; // Este es el "pase temporal" que nos dio Google.

// PASO 1: Canjear el "pase temporal" ($code) por un "Token de Acceso" real.
// Hacemos una petición POST a Google detrás de escena.
$tokenData = json_decode(
    file_get_contents("https://oauth2.googleapis.com/token", false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'code' => $code,
                'client_id' => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
                'grant_type' => 'authorization_code'
            ])
        ]
    ])),
true); // El 'true' convierte la respuesta JSON en un Array de PHP.

// Si no nos dieron el token, paramos todo.
if (empty($tokenData['access_token'])) {
    echo "Error obteniendo access token:";
    print_r($tokenData);
    exit;
}

// PASO 2: Usar el Token para preguntar "¿Quién es este usuario?"
// Hacemos una petición GET a Google para obtener el email, nombre y ID de Google.
$userdata = json_decode(
    file_get_contents("https://www.googleapis.com/oauth2/v3/userinfo?access_token=" . $tokenData['access_token']),
true);

// Conectamos a nuestra BD local
$pdo = pdo();

// Verificamos si este usuario ya existe en nuestra tabla 'usuarios' usando su ID de Google ('sub').
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE google_id = :gid");
$stmt->execute(['gid' => $userdata['sub']]); 
$exists = $stmt->fetch();

if ($exists) {
    // CASO A: El usuario YA EXISTE.
    $userId = $exists['id'];
    // Actualizamos su nombre o email por si los cambió en Google.
    $pdo->prepare("UPDATE usuarios SET email = :e, nombre = :n WHERE id = :id")
        ->execute([
            'e' => $userdata['email'],
            'n' => $userdata['name'],
            'id' => $userId
        ]);
} else {
    // CASO B: El usuario es NUEVO.
    // Lo insertamos en la base de datos.
    $pdo->prepare(
        "INSERT INTO usuarios (email, nombre, google_id, fecha_registro)
        VALUES (:e, :n, :g, NOW())"
    )->execute([
        'e' => $userdata['email'],
        'n' => $userdata['name'],
        'g' => $userdata['sub']
    ]);

    // Obtenemos el ID que la BD le acaba de asignar
    $userId = $pdo->lastInsertId();
}

// PASO FINAL: Guardar al usuario en la SESIÓN.
$_SESSION['user'] = [
    'id' => $userId,
    'email' => $userdata['email'],
    'nombre' => $userdata['name']
];

// Lo mandamos a la página principal.
header("Location: index.php");
exit;
?>
