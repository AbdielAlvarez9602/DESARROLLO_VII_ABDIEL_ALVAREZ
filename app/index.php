<?php
require_once 'clases.php';

$gestorBlog = new GestorBlog();
$gestorBlog->cargarEntradas();

// Determina la acción a realizar (agregar, editar, eliminar, mover)
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

switch ($accion) {
    case 'add':
        $tipo = $_POST['tipo'];
        $id = rand(1000, 9999); // Genera un ID aleatorio (en un entorno real usarías una lógica más sólida)
        $fecha_creacion = date('Y-m-d');

        // Dependiendo del tipo de entrada, crea una nueva entrada y agrégala al blog
        if ($tipo == 1) {
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $entrada = new EntradaUnaColumna($id, $fecha_creacion, $tipo, $titulo, $descripcion);
        } elseif ($tipo == 2) {
            $titulo1 = $_POST['titulo1'];
            $descripcion1 = $_POST['descripcion1'];
            $titulo2 = $_POST['titulo2'];
            $descripcion2 = $_POST['descripcion2'];
            $entrada = new EntradaDosColumnas($id, $fecha_creacion, $tipo, $titulo1, $descripcion1, $titulo2, $descripcion2);
        } elseif ($tipo == 3) {
            $titulo1 = $_POST['titulo1'];
            $descripcion1 = $_POST['descripcion1'];
            $titulo2 = $_POST['titulo2'];
            $descripcion2 = $_POST['descripcion2'];
            $titulo3 = $_POST['titulo3'];
            $descripcion3 = $_POST['descripcion3'];
            $entrada = new EntradaTresColumnas($id, $fecha_creacion, $tipo, $titulo1, $descripcion1, $titulo2, $descripcion2, $titulo3, $descripcion3);
        } else {
            die('Tipo de entrada inválido');
        }

        // Agrega la entrada y guarda en el archivo JSON
        $gestorBlog->agregarEntrada($entrada);
        echo "Entrada agregada exitosamente!";
        break;

    case 'edit':
        $entrada = $gestorBlog->obtenerEntrada($id);

        // Si la entrada existe, permite editar los detalles
        if ($entrada) {
            $tipo = $entrada->tipo;
            if ($tipo == 1) {
                $entrada->titulo = $_POST['titulo'];
                $entrada->descripcion = $_POST['descripcion'];
            } elseif ($tipo == 2) {
                $entrada->titulo1 = $_POST['titulo1'];
                $entrada->descripcion1 = $_POST['descripcion1'];
                $entrada->titulo2 = $_POST['titulo2'];
                $entrada->descripcion2 = $_POST['descripcion2'];
            } elseif ($tipo == 3) {
                $entrada->titulo1 = $_POST['titulo1'];
                $entrada->descripcion1 = $_POST['descripcion1'];
                $entrada->titulo2 = $_POST['titulo2'];
                $entrada->descripcion2 = $_POST['descripcion2'];
                $entrada->titulo3 = $_POST['titulo3'];
                $entrada->descripcion3 = $_POST['descripcion3'];
            }

            // Actualiza la entrada editada
            $gestorBlog->editarEntrada($entrada);
            echo "Entrada editada exitosamente!";
        } else {
            die('Entrada no encontrada');
        }
        break;

    case 'delete':
        // Elimina una entrada específica por su ID
        $gestorBlog->eliminarEntrada($id);
        echo "Entrada eliminada exitosamente!";
        break;

    case 'move_up':
        // Mueve la entrada hacia arriba
        $gestorBlog->moverEntrada($id, 'up');
        echo "Entrada movida hacia arriba!";
        break;

    case 'move_down':
        // Mueve la entrada hacia abajo
        $gestorBlog->moverEntrada($id, 'down');
        echo "Entrada movida hacia abajo!";
        break;

    default:
        // Si no hay acción especificada, muestra todas las entradas del blog
        $entradas = $gestorBlog->obtenerTodasLasEntradas();
        if (!empty($entradas)) {
            foreach ($entradas as $entrada) {
                echo $entrada->obtenerDetallesEspecificos() . "<br>";
            }
        } else {
            echo "No hay entradas disponibles.";
        }
        break;
}
?>

<!-- Formulario para agregar una nueva entrada -->
<h2>Agregar Nueva Entrada</h2>
<form method="POST" action="index.php?accion=add">
    <label for="tipo">Tipo de Entrada:</label>
    <select name="tipo" id="tipo" required>
        <option value="1">Una Columna</option>
        <option value="2">Dos Columnas</option>
        <option value="3">Tres Columnas</option>
    </select><br>

    <!-- Entrada de una columna -->
    <div id="columna1" style="display:none;">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo"><br>
        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion"></textarea><br>
    </div>

    <!-- Entrada de dos columnas -->
    <div id="columna2" style="display:none;">
        <label for="titulo1">Título Columna 1:</label>
        <input type="text" name="titulo1" id="titulo1"><br>
        <label for="descripcion1">Descripción Columna 1:</label>
        <textarea name="descripcion1" id="descripcion1"></textarea><br>

        <label for="titulo2">Título Columna 2:</label>
        <input type="text" name="titulo2" id="titulo2"><br>
        <label for="descripcion2">Descripción Columna 2:</label>
        <textarea name="descripcion2" id="descripcion2"></textarea><br>
    </div>

    <!-- Entrada de tres columnas -->
    <div id="columna3" style="display:none;">
        <label for="titulo1">Título Columna 1:</label>
        <input type="text" name="titulo1" id="titulo1"><br>
        <label for="descripcion1">Descripción Columna 1:</label>
        <textarea name="descripcion1" id="descripcion1"></textarea><br>

        <label for="titulo2">Título Columna 2:</label>
        <input type="text" name="titulo2" id="titulo2"><br>
        <label for="descripcion2">Descripción Columna 2:</label>
        <textarea name="descripcion2" id="descripcion2"></textarea><br>

        <label for="titulo3">Título Columna 3:</label>
        <input type="text" name="titulo3" id="titulo3"><br>
        <label for="descripcion3">Descripción Columna 3:</label>
        <textarea name="descripcion3" id="descripcion3"></textarea><br>
    </div>

    <button type="submit">Agregar Entrada</button>
</form>

<script>
    // Muestra y oculta los campos de acuerdo al tipo seleccionado
    document.getElementById('tipo').addEventListener('change', function() {
        document.getElementById('columna1').style.display = 'none';
        document.getElementById('columna2').style.display = 'none';
        document.getElementById('columna3').style.display = 'none';

        if (this.value == '1') {
            document.getElementById('columna1').style.display = 'block';
        } else if (this.value == '2') {
            document.getElementById('columna2').style.display = 'block';
        } else if (this.value == '3') {
            document.getElementById('columna3').style.display = 'block';
        }
    });
</script>
