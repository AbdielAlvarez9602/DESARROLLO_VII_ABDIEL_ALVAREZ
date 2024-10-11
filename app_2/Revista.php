<?php
class Revista extends RecursoBiblioteca {
    private $numeroEdicion;

    public function __construct($id, $titulo, $autor, $estado, $numeroEdicion) {
        parent::__construct($id, $titulo, $autor, $estado);
        $this->numeroEdicion = $numeroEdicion;
    }

    public function obtenerDetallesPrestamo(): string {
        return $this->obtenerDetallesGenerales() . ", Número de Edición: {$this->numeroEdicion}";
    }
}
