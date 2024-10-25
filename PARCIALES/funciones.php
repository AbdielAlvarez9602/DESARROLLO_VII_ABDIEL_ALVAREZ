<?php
function validar_fecha_futura($fecha) {
    $fecha_actual = date('Y-m-d');
    return $fecha > $fecha_actual;
}