<?php
class Libro extends RecursoBiblioteca {
    private $isbn;

    public function __construct($id, $titulo, $autor, $estado, $isbn) {
        parent::__construct($id, $titulo, $autor, $estado);
        $this->isbn = $isbn;
    }

    public function obtenerDetallesPrestamo(): string {
        return $this->obtenerDetallesGenerales() . ", ISBN: {$this->isbn}";
    }
}
