<?php
   function capitalizar_palabras($texto) {
    $palabras = explode(" ", $texto);
    $resultado = [];
    foreach ($palabras as $palabra) {
        if (strlen($palabra) > 0) {
            $primera = strtoupper(substr($palabra, 0, 1));
            $resto = strtolower(substr($palabra, 1));
            $resultado[] = $primera . $resto;
        } else {
            $resultado[] = "";
        }
    }
    return implode(" ", $resultado);
}

?>