<?php
$archivoRegistros = 'registros.json';
$registros = file_exists($archivoRegistros) ? json_decode(file_get_contents($archivoRegistros), true) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Registros</title>
    <style>
        body { font-family: sans-serif; }
        .registro { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        .registro h3 { margin-top: 0; }
    </style>
</head>
<body>
    <h2>Resumen de Registros Procesados</h2>
    
    <?php if (empty($registros)): ?>
        <p>No hay registros guardados aún.</p>
    <?php else: ?>
        <?php foreach ($registros as $index => $registro): ?>
            <div class="registro">
                <h3>Registro #<?php echo $index + 1; ?></h3>
                <ul>
                    <?php foreach ($registro as $campo => $valor): ?>
                        <li>
                            <strong><?php echo ucfirst(str_replace('_', ' ', $campo)); ?>:</strong> 
                            <?php 
                            if ($campo === 'intereses' && is_array($valor)) {
                                echo htmlspecialchars(implode(', ', $valor));
                            } elseif ($campo === 'foto_perfil' && $valor) {
                                echo "<img src='" . htmlspecialchars($valor) . "' width='50' style='vertical-align: middle;'>";
                            } else {
                                echo htmlspecialchars(is_array($valor) ? json_encode($valor) : $valor);
                            }
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><a href='formulario.php'>Volver al formulario de registro</a>
</body>
</html>