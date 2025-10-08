<?php
function sanitizarNombre($nombre) {
    return strip_tags(trim($nombre));
}

function sanitizarEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function sanitizarFechaNac($fechaNac) {
    return filter_var(trim($fechaNac), FILTER_SANITIZE_STRING);
}

function sanitizarSitioWeb($sitioWeb) {
    return filter_var(trim($sitioWeb), FILTER_SANITIZE_URL);
}

function sanitizarGenero($genero) {
    return strip_tags(trim($genero));
}

function sanitizarIntereses($intereses) {
    return array_map(function($interes) {
        return strip_tags(trim($interes));
    }, $intereses);
}

function sanitizarComentarios($comentarios) {
    return htmlspecialchars(trim($comentarios), ENT_QUOTES, 'UTF-8');
}
?>