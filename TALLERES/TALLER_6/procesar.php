<?php
require_once 'validaciones.php';
require_once 'sanitizacion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = [];
    $datos = [];

    // Procesar y validar cada campo
    $campos = ['nombre', 'email', 'fecha_nacimiento', 'sitio_web', 'genero', 'intereses', 'comentarios'];
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            $valor = $_POST[$campo];
            $valorSanitizado = call_user_func("sanitizar" . ucfirst($campo), $valor);
            $datos[$campo] = $valorSanitizado;

            if (!call_user_func("validar" . ucfirst($campo), $valorSanitizado)) {
                $errores[] = "El campo $campo no es válido.";
            }
        }
    }

    // Calcular la edad basada en la fecha de nacimiento
    if (isset($datos['fecha_nacimiento']) && validarFecha_nacimiento($datos['fecha_nacimiento'])) {
        $fechaIngresada = strtotime($datos['fecha_nacimiento']);
        $edad = date('Y') - date('Y', $fechaIngresada);
        $datos['edad'] = $edad;
    } else {
        $errores[] = "La fecha de nacimiento no es válida o no cumple los requisitos de edad.";
    }

    // Procesar la foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
        if (!validarFotoPerfil($_FILES['foto_perfil'])) {
            $errores[] = "La foto de perfil no es válida.";
        } else {
            // Generar un nombre único para la foto
            $nombreUnico = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
            $rutaDestino = 'uploads/' . $nombreUnico;
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaDestino)) {
                $datos['foto_perfil'] = $rutaDestino;
            } else {
                $errores[] = "Hubo un error al subir la foto de perfil.";
            }
        }
    }

    // Mostrar resultados o errores
    if (empty($errores)) {
        echo "<h2>Datos Recibidos:</h2>";
        echo "<table border='1'>";
        foreach ($datos as $campo => $valor) {
            echo "<tr>";
            echo "<th>" . ucfirst($campo) . "</th>";
            if ($campo === 'intereses') {
                echo "<td>" . implode(", ", $valor) . "</td>";
            } elseif ($campo === 'foto_perfil') {
                echo "<td><img src='$valor' width='100'></td>";
            } else {
                echo "<td>$valor</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<h2>Errores:</h2>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }

    echo "<br><a href='formulario.html'>Volver al formulario</a>";
} else {
    echo "Acceso no permitido.";
}
?>