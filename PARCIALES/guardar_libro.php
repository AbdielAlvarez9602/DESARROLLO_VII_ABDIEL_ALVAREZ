<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Incluir archivo de conexión a la base de datos
require_once 'db.php'; // Asegúrate de que este archivo contenga los datos correctos de conexión

// Obtener el ID del usuario desde la sesión
$userId = $_SESSION['user']['id']; // Suponiendo que el ID de usuario está almacenado en $_SESSION['user']['id']

// Verificar si el ID del libro se pasa por la URL
if (isset($_GET['id'])) {
    $bookId = $_GET['id'];
    
    // Llamada a la API para obtener los detalles del libro (con la API de Google Books)
    $url = "https://www.googleapis.com/books/v1/volumes/$bookId";
    $response = file_get_contents($url);
    $bookData = json_decode($response, true);

    // Verificar si la respuesta contiene los detalles del libro
    if (isset($bookData['volumeInfo'])) {
        $titulo = $bookData['volumeInfo']['title'];
        $autores = isset($bookData['volumeInfo']['authors']) ? implode(', ', $bookData['volumeInfo']['authors']) : 'Desconocido';

        // Conectar a la base de datos y preparar la consulta para insertar el libro
        $conn = new mysqli('localhost', 'root', '', 'biblioteca');
        
        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Preparar y ejecutar la consulta SQL
        $stmt = $conn->prepare("INSERT INTO libros_guardados (user_id, google_books_id, titulo, autor) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $bookId, $titulo, $autores);

        if ($stmt->execute()) {
            // Si la inserción es exitosa, mostrar mensaje de éxito
          //  echo "<p>¡El libro '$titulo' ha sido guardado exitosamente!</p>";
        } else {
            // Si ocurre un error en la inserción, mostrar mensaje de error
            echo "<p>Error al guardar el libro: " . $stmt->error . "</p>";
        }

        // Cerrar la conexión
        $stmt->close();

        // Mostrar los datos de los libros guardados
        $result = $conn->query("SELECT * FROM libros_guardados WHERE user_id = $userId");

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Mostrar los datos en una tabla
            echo "<table class='book-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autores</th>
                            <th>ID del Libro en Google Books</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['titulo'] . "</td>
                        <td>" . $row['autor'] . "</td>
                        <td>" . $row['google_books_id'] . "</td>
                        <td>
                            <!-- Botón de eliminar -->
                            <form action='eliminar_libro.php' method='post' onsubmit='return confirm(\"¿Estás seguro de que quieres eliminar este libro?\");'>
                                <input type='hidden' name='book_id' value='" . $row['google_books_id'] . "'>
                                <button type='submit'>Eliminar</button>
                            </form>
                        </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron libros guardados.</p>";
        }

        // Cerrar la conexión
        $conn->close();
    } else {
        echo "<p>No se pudo encontrar el libro.</p>";
    }
} else {
    echo "<p>ID del libro no proporcionado.</p>";
}
?>

<!-- Botón Volver -->
<a href="profile.php">
    <button type="button">Volver al perfil</button>
</a>

<!-- Enlace al archivo CSS externo -->
<link rel="stylesheet" type="text/css" href="stiles.css">
