<?php
require_once 'validaciones.php';
require_once 'sanitizacion.php';

// Directorios y Archivos
$directorioSubida = 'uploads/'; 
$archivoRegistros = 'registros.json';

// Lógica para crear directorios si no existen
if (!is_dir($directorioSubida)) {
    mkdir($directorioSubida, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = [];
    $datos = [];

    // Mapeo para manejar la llamada dinámica
    $camposMapeo = [
        'nombre' => 'Nombre',
        'email' => 'Email',
        'fecha_nac' => 'FechaNac', // Campo nuevo
        'sitio_web' => 'SitioWeb', 
        'genero' => 'Genero',
        'intereses' => 'Intereses',
        'comentarios' => 'Comentarios'
    ];
    
    // Almacenar datos limpios para repoblar el formulario en caso de error
    $datos_previos = [];

    // Procesar y validar cada campo
    foreach ($camposMapeo as $campoHTML => $campoFuncion) {
        $valor = $_POST[$campoHTML] ?? ($campoHTML === 'intereses' ? [] : ''); 
        
        $funcionSanitizar = "sanitizar" . $campoFuncion;
        $valorSanitizado = call_user_func($funcionSanitizar, $valor);
        
        $datos_previos[$campoHTML] = $valor; // Almacena el valor original para repoblar
        $datos[$campoHTML] = $valorSanitizado; // Almacena el valor sanitizado para el registro

        $funcionValidar = "validar" . $campoFuncion;
        if (!call_user_func($funcionValidar, $valorSanitizado)) {
            $errores[] = "El campo $campoHTML no es válido.";
        }
    }
    
    // Lógica para calcular la edad (Requisito 3)
    if (empty($errores)) {
        try {
            $fechaNacimiento = new DateTime($datos['fecha_nac']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;
            $datos['edad'] = $edad;
        } catch (Exception $e) {
            $errores[] = "Error al calcular la edad.";
        }
    }


    // Procesar la foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
        if (!validarFotoPerfil($_FILES['foto_perfil'])) {
            $errores[] = "La foto de perfil no es válida.";
        } else {
            // Generar nombre de archivo único (Requisito 4)
            $nombreOriginal = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_FILENAME);
            $extension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $nombreUnico = $nombreOriginal . '_' . time() . '.' . $extension;
            $rutaDestino = $directorioSubida . $nombreUnico;
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaDestino)) {
                $datos['foto_perfil'] = $rutaDestino;
            } else {
                $errores[] = "Hubo un error al subir la foto de perfil.";
            }
        }
    }

    // Comprobación de errores y Persistencia de Datos
    if (!empty($errores)) {
        // Persistencia (Requisito 5): Redireccionar con datos y errores codificados
        $datos_str = base64_encode(json_encode($datos_previos));
        $errores_str = base64_encode(json_encode($errores));
        header("Location: formulario.php?datos_previos={$datos_str}&errores={$errores_str}");
        exit();
    } else {
        // Guardar registro en JSON (Requisito 6)
        $registros = file_exists($archivoRegistros) ? json_decode(file_get_contents($archivoRegistros), true) : [];
        $registros[] = $datos;
        file_put_contents($archivoRegistros, json_encode($registros, JSON_PRETTY_PRINT));
        
        // Mostrar resultados
        echo "<h2>Datos Recibidos:</h2>";
        echo "<table border='1'>";
        foreach ($datos as $campo => $valor) {
            echo "<tr>";
            echo "<th>" . ucfirst(str_replace('_', ' ', $campo)) . "</th>";
            if ($campo === 'intereses') {
                echo "<td>" . implode(", ", $valor) . "</td>";
            } elseif ($campo === 'foto_perfil') {
                echo "<td><img src='$valor' width='100'></td>";
            } else {
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<br><a href='formulario.php'>Volver al formulario</a>";
        echo "<br><a href='registros.php'>Ver todos los registros</a>";
    }

} else {
    echo "Acceso no permitido.";
}
?>