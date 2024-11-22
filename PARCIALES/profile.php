<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
//mostrar la fecha y hora real de la compu
date_default_timezone_set('America/Mexico_City');
// Obtener los datos del usuario
$userData = $_SESSION['user'];

// Verificar si la fecha de registro existe en la sesión
if (!isset($_SESSION['registro_fecha'])) {
    // Obtener la fecha y hora actuales del servidor en formato 12 horas (AM/PM)
    $hora_registro = date('l, F j, Y g:i A'); // Ejemplo: "Tuesday, October 15, 2024 3:30 PM"
    $_SESSION['registro_fecha'] = $hora_registro; // Guardar la fecha de registro en la sesión
}

// Lógica para la búsqueda de libros
$data = null;
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $url = "https://www.googleapis.com/books/v1/volumes?q=$query";

    $response = file_get_contents($url);
    $data = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Usuario y Buscar Libros</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Perfil del Usuario y Buscar Libros</h1>
    </header>

    <div class="container">
        <!-- Información del usuario autenticado -->
        <div class="user-profile">
            <div style="display: flex; align-items: center;">
                <img src="<?php echo isset($userData['picture']) ? $userData['picture'] : 'ruta/a/imagen/por-defecto.jpg'; ?>" alt="Foto de perfil">
                <div>
                    <h2>Bienvenido, <?php echo htmlspecialchars(isset($userData['nombre']) ? $userData['nombre'] : 'Usuario desconocido'); ?></h2>
                    <p>Correo electrónico: <?php echo htmlspecialchars(isset($userData['email']) ? $userData['email'] : 'Correo no disponible'); ?></p>
                    <p>Fecha de registro: <?php echo $_SESSION['registro_fecha']; ?></p> <!-- Mostrar fecha de registro en formato 12 horas -->
                    <a href="mislibros.php" class="btn-ver-libros">Mis libros guardados</a>
                    <a href="logout.php" class="logout-btn">Cerrar sesión</a>
                </div>
            </div>
        </div>

        <div class="search-section">
            <h2>Buscar Libros</h2>
            <!-- Formulario con el botón de limpiar -->
            <form method="GET" action="">
               <input type="text" name="query" placeholder="Buscar libros..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                <button type="submit">Buscar</button>
                <!-- Botón de limpiar -->
                <button type="button" onclick="window.location.href = window.location.pathname;">Limpiar</button>
            </form>

            <!-- Mostrar resultados de la búsqueda -->
            <?php if (isset($data['items'])): ?>
                <ul class="books-list">
                    <?php foreach ($data['items'] as $item): ?>
                        <li>
                           <img src="<?php echo isset($item['volumeInfo']['imageLinks']['thumbnail']) ? $item['volumeInfo']['imageLinks']['thumbnail'] : 'ruta/a/imagen/por-defecto.jpg'; ?>" alt="Portada">
                             <div class="book-info">
                                <h3><?php echo htmlspecialchars($item['volumeInfo']['title']); ?></h3>
                                 <p>Autor: <?php echo htmlspecialchars(implode(', ', $item['volumeInfo']['authors'])); ?></p>
                    <a href="guardar_libro.php?id=<?php echo $item['id']; ?>">Guardar</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>