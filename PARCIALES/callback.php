<?php
session_start();
include('db.php'); // Incluir el archivo de conexión a la base de datos

// Configuración de credenciales de Google
$googleClientId = '505423101388-iifot8hbh2ldbst0b1n79i2vl4v8oiv7.apps.googleusercontent.com';
$googleClientSecret = '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';
$googleRedirectUri = 'http://localhost/biblioteca/callback.php';

// Verificar si el código está presente
if (isset($_GET['code'])) {
    // Obtener el código de la URL
    $code = $_GET['code'];

    // Obtener el token de acceso usando el código
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code' => $code,
        'client_id' => $googleClientId,
        'client_secret' => $googleClientSecret,
        'redirect_uri' => $googleRedirectUri,
        'grant_type' => 'authorization_code'
    ];

    // Realizar la solicitud POST
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    // Decodificar la respuesta JSON
    $responseData = json_decode($response, true);

    if (isset($responseData['access_token'])) {
        // Usar el token de acceso para obtener los datos del usuario
        $accessToken = $responseData['access_token'];
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $userInfo = curl_exec($ch);
        curl_close($ch);

        // Decodificar la respuesta JSON
        $userData = json_decode($userInfo, true);
// Suponiendo que ya tienes acceso a la base de datos a través de $pdo
$sql = "SELECT * FROM usuarios WHERE google_id = :google_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['google_id' => $userData['id']]);
$existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingUser) {
    // Si el usuario ya existe, actualizar los datos si es necesario
    $_SESSION['user'] = $existingUser;
} else {
    // Si el usuario no existe, insertar los datos en la base de datos
    $sql = "INSERT INTO usuarios (email, nombre, google_id) VALUES (:email, :nombre, :google_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'email' => $userData['email'],
        'nombre' => $userData['name'],
        'google_id' => $userData['id']
    ]);

    // Obtener los datos del nuevo usuario
    $userId = $pdo->lastInsertId();
    $_SESSION['user'] = [
        'id' => $userId,
        'email' => $userData['email'],
        'nombre' => $userData['name'],
        'google_id' => $userData['id'],
        'fecha_registro' => date("Y-m-d H:i:s"), // Si no tienes la fecha del registro de Google, puedes hacerlo manualmente al insertarlo
        'picture' => $userData['picture'] ?? 'ruta/a/imagen/por-defecto.jpg'
    ];
}
        // Redirigir a la página principal
        header('Location: profile.php');
        exit();
    } else {
        // Error al obtener el token de acceso
        echo 'Error al obtener el token de acceso.';
    }
} else {
    echo 'No se recibió el código de autenticación.';
}
?>