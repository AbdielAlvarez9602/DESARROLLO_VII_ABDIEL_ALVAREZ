<?php
$archivoJson = 'registros.json';

if (file_exists($archivoJson)) {
    $registros = json_decode(file_get_contents($archivoJson), true);
    echo "<h2>Resumen de Registros</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nombre</th><th>Email</th><th>Edad</th><th>Género</th><th>Intereses</th><th>Comentarios</th><th>Foto de Perfil</th></tr>";
    foreach ($registros as $registro) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($registro['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($registro['email']) . "</td>";
        echo "<td>" . htmlspecialchars($registro['edad']) . "</td>";
        echo "<td>" . htmlspecialchars($registro['genero']) . "</td>";
        echo "<td>" . htmlspecialchars(implode(", ", $registro['intereses'])) . "</td>";
        echo "<td>" . htmlspecialchars($registro['comentarios']) . "</td>";
        if (isset($registro['foto_perfil'])) {
            echo "<td><img src='" . htmlspecialchars($registro['foto_perfil']) . "' width='100'></td>";
        } else {
            echo "<td>No se subió foto</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No hay registros.";
}
?>
