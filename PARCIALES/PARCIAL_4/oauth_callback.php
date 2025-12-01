<?php
session_start();

// ---------------- CONFIG ----------------
$dbHost = '127.0.0.1';
$dbName = 'mini_biblio';
$dbUser = 'root';
$dbPass = 'root'; // <-- Usando la contraseña 'root' según tu última indicación

// Google OAuth Credentials
define('GOOGLE_CLIENT_ID', '696786352660-ojv4eh0ttjf42uf9vf6ncuhgful48epk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/PARCIALES/PARCIAL_4/oauth_callback.php');
// ----------------------------------------

function pdo() {
    global $dbHost, $dbName, $dbUser, $dbPass;
    return new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}

if (!isset($_GET['code'])) {
    echo "Error: Google no devolvió ningún código.";
    exit;
}

$code = $_GET['code'];

// 1. Obtener el Token
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
true);

if (empty($tokenData['access_token'])) {
    echo "Error obteniendo access token:";
    print_r($tokenData);
    exit;
}

// 2. Obtener datos del usuario (AHORA SE ACCEDE CORRECTAMENTE COMO ARRAY)
$userdata = json_decode(
    file_get_contents("https://www.googleapis.com/oauth2/v3/userinfo?access_token=" . $tokenData['access_token']),
true);

$pdo = pdo();

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE google_id = :gid");
// CAMBIO 1: de $userdata->sub a $userdata['sub']
$stmt->execute(['gid' => $userdata['sub']]); 
$exists = $stmt->fetch();

if ($exists) {
    $userId = $exists['id'];
    // Actualizar datos por si cambiaron en Google
    $pdo->prepare("UPDATE usuarios SET email = :e, nombre = :n WHERE id = :id")
        ->execute([
            // CAMBIO 2 y 3: de $userdata->email a $userdata['email'] y de $userdata->name a $userdata['name']
            'e' => $userdata['email'],
            'n' => $userdata['name'],
            'id' => $userId
        ]);
} else {
    // Insertar nuevo usuario
    $pdo->prepare(
        "INSERT INTO usuarios (email, nombre, google_id, fecha_registro)
        VALUES (:e, :n, :g, NOW())"
    )->execute([
        // CAMBIO 4, 5 y 6: a sintaxis de array
        'e' => $userdata['email'],
        'n' => $userdata['name'],
        'g' => $userdata['sub']
    ]);

    $userId = $pdo->lastInsertId();
}

// Guardar en sesión (CAMBIO 7 y 8: a sintaxis de array)
$_SESSION['user'] = [
    'id' => $userId,
    'email' => $userdata['email'],
    'nombre' => $userdata['name']
];

header("Location: index.php");
exit;
?>