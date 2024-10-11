<?php
// Incluimos la interfaz Prestable
require_once 'Prestable.php';

abstract class RecursoBiblioteca implements Prestable {
    protected $id;
    protected $titulo;
    protected $estado;

    public function __construct($id, $titulo, $estado) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->estado = $estado;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($nuevoEstado) {
        $this->estado = $nuevoEstado;
    }

    // Método abstracto para obtener los detalles de préstamo, implementado en las clases hijas
    abstract public function obtenerDetallesPrestamo(): string;
}

/**
 * Clase Libro
 * Hereda de RecursoBiblioteca y agrega el atributo ISBN.
 */
class Libro extends RecursoBiblioteca {
    private $isbn;

    public function __construct($id, $titulo, $estado, $isbn) {
        parent::__construct($id, $titulo, $estado);
        $this->isbn = $isbn;
    }

    public function obtenerDetallesPrestamo(): string {
        return "Libro (ISBN: {$this->isbn}) - Título: {$this->titulo}";
    }
}

/**
 * Clase Revista
 * Hereda de RecursoBiblioteca y agrega el atributo número de edición.
 */
class Revista extends RecursoBiblioteca {
    private $numeroEdicion;

    public function __construct($id, $titulo, $estado, $numeroEdicion) {
        parent::__construct($id, $titulo, $estado);
        $this->numeroEdicion = $numeroEdicion;
    }

    public function obtenerDetallesPrestamo(): string {
        return "Revista (Edición No. {$this->numeroEdicion}) - Título: {$this->titulo}";
    }
}

/**
 * Clase DVD
 * Hereda de RecursoBiblioteca y agrega el atributo duración.
 */
class DVD extends RecursoBiblioteca {
    private $duracion;

    public function __construct($id, $titulo, $estado, $duracion) {
        parent::__construct($id, $titulo, $estado);
        $this->duracion = $duracion;
    }

    public function obtenerDetallesGenerales(): string {
        return "DVD (Duración: {$this->duracion} minutos) - Título: {$this->titulo}";
    }
}

/**
 * Clase GestorBiblioteca
 * Gestiona los recursos de la biblioteca.
 */
class GestorBiblioteca {
    private $recursos;

    public function __construct() {
        $this->recursos = [];
    }

    public function agregarRecursos(RecursoBiblioteca $recurso) {
        $this->recursos[$recurso->getId()] = $recurso;
    }

    public function eliminarRecurso($id) {
        unset($this->recursos[$id]);
    }

    public function actualizarRecurso(RecursoBiblioteca $recurso) {
        if (isset($this->recursos[$recurso->getId()])) {
            $this->recursos[$recurso->getId()] = $recurso;
        }
    }

    public function actualizarEstadoRecurso($id, $nuevoEstado) {
        if (isset($this->recursos[$id])) {
            $this->recursos[$id]->setEstado($nuevoEstado);
        }
    }

    public function buscarRecursosPorEstado($estado) {
        return array_filter($this->recursos, function ($recurso) use ($estado) {
            return $recurso->getEstado() === $estado;
        });
    }

    public function listarRecursos($filtroEstado = '', $campoOrden = 'id', $direccionOrden = 'ASC') {
        $recursosFiltrados = $this->recursos;

        if ($filtroEstado) {
            $recursosFiltrados = $this->buscarRecursosPorEstado($filtroEstado);
        }

        usort($recursosFiltrados, function ($a, $b) use ($campoOrden, $direccionOrden) {
            if ($campoOrden === 'id') {
                $comparison = $a->getId() <=> $b->getId();
            } elseif ($campoOrden === 'titulo') {
                $comparison = strcmp($a->getTitulo(), $b->getTitulo());
            }

            return $direccionOrden === 'ASC' ? $comparison : -$comparison;
        });

        return $recursosFiltrados;
    }
}
?>
