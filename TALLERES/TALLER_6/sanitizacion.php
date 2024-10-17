<?php
function sanitizarNombre($nombre) {
    return filter_var(trim($nombre), FILTER_SANITIZE_SPECIAL_CHARS);
}

function sanitizarEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function sanitizarEdad($edad) {
    return filter_var($edad, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizarSitio_web($sitioWeb) {  // Corregido el nombre
    return filter_var(trim($sitioWeb), FILTER_SANITIZE_URL);
}

function sanitizarGenero($genero) {
    return filter_var(trim($genero), FILTER_SANITIZE_SPECIAL_CHARS);
}

function sanitizarIntereses($intereses) {
    return array_map(function($interes) {
        return filter_var(trim($interes), FILTER_SANITIZE_SPECIAL_CHARS);
    }, $intereses);
}

function sanitizarComentarios($comentarios) {
    return htmlspecialchars(trim($comentarios), ENT_QUOTES, 'UTF-8');
}

function sanitizarFecha_nacimiento($fecha) {
    return filter_var($fecha, FILTER_SANITIZE_SPECIAL_CHARS);
}
?>