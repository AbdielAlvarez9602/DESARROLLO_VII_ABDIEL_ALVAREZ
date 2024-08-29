<?php
require 'includes/funciones.php';
include 'includes/header.php';

$libros = obtenerLibros();
?>

<main>
    <?php
    foreach ($libros as $libro) {
        echo mostrarDetallesLibro($libro);
    }
    ?>
</main>

<?php include 'includes/footer.php'; ?>
