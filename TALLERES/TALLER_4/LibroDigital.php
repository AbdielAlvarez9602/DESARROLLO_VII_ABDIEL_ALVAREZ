<?php
require_once 'Libro.php';
require_once 'Prestable.php';

class LibroDigital extends Libro implements Prestable {
    private $formatoArchivo;
    private $tamanoMB;

    public function __construct($titulo, $autor, $anioPublicacion, $formatoArchivo, $tamanoMB) {
        parent::__construct($titulo, $autor, $anioPublicacion);
        $this->formatoArchivo = $formatoArchivo;
        $this->tamanoMB = $tamanoMB;
    }

    public function obtenerInformacion() {
        return parent::obtenerInformacion() . ", Formato: {$this->formatoArchivo}, Tamaño: {$this->tamanoMB}MB";
    }
}