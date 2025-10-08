<?php 
$datos_previos = isset($_GET['datos_previos']) ? json_decode(base64_decode($_GET['datos_previos']), true) : []; 
$errores = isset($_GET['errores']) ? json_decode(base64_decode($_GET['errores']), true) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro Avanzado</title>
</head>
<body>
    <h2>Formulario de Registro Avanzado</h2>
    
    <?php if (!empty($errores)): ?>
        <h3 style="color: red;">Errores de Validación:</h3>
        <ul style="color: red;">
            <?php foreach ($errores as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="procesar.php" method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($datos_previos['nombre'] ?? ''); ?>"><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($datos_previos['email'] ?? ''); ?>"><br><br>

        <label for="fecha_nac">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nac" name="fecha_nac" required value="<?php echo htmlspecialchars($datos_previos['fecha_nac'] ?? ''); ?>"><br><br>

        <label for="sitio_web">Sitio Web:</label>
        <input type="url" id="sitio_web" name="sitio_web" value="<?php echo htmlspecialchars($datos_previos['sitio_web'] ?? ''); ?>"><br><br>

        <label for="genero">Género:</label>
        <select id="genero" name="genero">
            <option value="masculino" <?php echo (($datos_previos['genero'] ?? '') === 'masculino') ? 'selected' : ''; ?>>Masculino</option>
            <option value="femenino" <?php echo (($datos_previos['genero'] ?? '') === 'femenino') ? 'selected' : ''; ?>>Femenino</option>
            <option value="otro" <?php echo (($datos_previos['genero'] ?? '') === 'otro') ? 'selected' : ''; ?>>Otro</option>
        </select><br><br>

        <label>Intereses:</label><br>
        <input type="checkbox" id="deportes" name="intereses[]" value="deportes" <?php echo (in_array('deportes', $datos_previos['intereses'] ?? [])) ? 'checked' : ''; ?>>
        <label for="deportes">Deportes</label><br>
        <input type="checkbox" id="musica" name="intereses[]" value="musica" <?php echo (in_array('musica', $datos_previos['intereses'] ?? [])) ? 'checked' : ''; ?>>
        <label for="musica">Música</label><br>
        <input type="checkbox" id="lectura" name="intereses[]" value="lectura" <?php echo (in_array('lectura', $datos_previos['intereses'] ?? [])) ? 'checked' : ''; ?>>
        <label for="lectura">Lectura</label><br><br>

        <label for="comentarios">Comentarios:</label><br>
        <textarea id="comentarios" name="comentarios" rows="4" cols="50"><?php echo htmlspecialchars($datos_previos['comentarios'] ?? ''); ?></textarea><br><br>

        <label for="foto_perfil">Foto de Perfil:</label>
        <input type="file" id="foto_perfil" name="foto_perfil"><br><br>

        <input type="submit" value="Enviar">
        <input type="reset" value="Limpiar">
    </form>
    <br><a href='registros.php'>Ver todos los registros</a>
</body>
</html>        