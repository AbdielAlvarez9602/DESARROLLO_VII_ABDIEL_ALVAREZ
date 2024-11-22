<?php
$host = 'localhost'; // Dirección del servidor de base de datos
$db = 'biblioteca'; // Nombre de la base de datos
$user = 'root'; // Usuario de la base de datos
$pass = ''; // Contraseña de la base de datos

try {
    // Conexión con PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>