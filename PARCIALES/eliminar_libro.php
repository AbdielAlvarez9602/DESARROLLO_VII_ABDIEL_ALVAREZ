<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Verificar si el ID del libro a eliminar se ha recibido por POST
if (isset($_POST['book_id'])) {
    $bookId = $_POST['book_id'];

    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'biblioteca');
    
    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Preparar y ejecutar la consulta SQL para eliminar el libro
    $stmt = $conn->prepare("DELETE FROM libros_guardados WHERE google_books_id = ? AND user_id = ?");
    $stmt->bind_param("si", $bookId, $_SESSION['user']['id']); // Aseguramos que solo el usuario actual pueda eliminar sus propios libros

    if ($stmt->execute()) {
        // Si la eliminación es exitosa, redirigir al usuario de vuelta
        header('Location: profile.php');
        exit();
    } else {
        // Si ocurre un error, mostrar mensaje de error
        echo "<p>Error al eliminar el libro: " . $stmt->error . "</p>";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    echo "<p>No se proporcionó el ID del libro.</p>";
}
?>
