<?php
function contar_palabras_repetidas($texto) {
  
    $texto_lower = strtolower($texto);

    $texto_trim = trim($texto_lower);

    $palabras = explode(" ", $texto_trim);
    $conteo_palabras = [];

   
    foreach ($palabras as $palabra) {
       
        if (!empty($palabra)) {
           
            if (isset($conteo_palabras[$palabra])) {
                $conteo_palabras[$palabra]++;
            } else {
                $conteo_palabras[$palabra] = 1;
            }
        }
    }
    return $conteo_palabras;
}
?>