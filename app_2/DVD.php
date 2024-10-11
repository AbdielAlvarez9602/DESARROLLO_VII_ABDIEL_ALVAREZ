<?php
class DVD extends RecursoBiblioteca {
    private $duracion;

    public function __construct($id, $titulo, $autor, $estado, $duracion) {
        parent::__construct($id, $titulo, $autor, $estado);
        $this->duracion = $duracion;
    }

    public function obtenerDetallesPrestamo(): string {
        return $this->obtenerDetallesGenerales() . ", Duración: {$this->duracion} minutos";
    }
}
?>
