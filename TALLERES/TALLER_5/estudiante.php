<?php
// Estudiante.php

/**
 * Clase Estudiante
 * Representa a un estudiante con sus datos personales y calificaciones.
 */
class Estudiante {
    private int $id;
    private string $nombre;
    private int $edad;
    private string $carrera;
    private array $materias; // Arreglo asociativo: ['materia' => calificacion]

    // ... (El resto de los atributos y el constructor permanecen igual) ...
    public function __construct(int $id, string $nombre, int $edad, string $carrera) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->carrera = $carrera;
        $this->materias = [];
    }

    // Métodos de la clase Estudiante

    public function agregarMateria(string $materia, float $calificacion): void {
        if ($calificacion >= 0 && $calificacion <= 100) {
            $this->materias[$materia] = $calificacion;
        } else {
            // Nota: En un sistema real, esto debería lanzar una excepción o registrar un error.
            echo "Advertencia: Calificación fuera de rango para {$this->nombre} en {$materia}.\n";
        }
    }

    /**
     * Calcula y retorna el promedio de calificaciones del estudiante.
     */
    public function obtenerPromedio(): float {
        if (empty($this->materias)) {
            return 0.0;
        }
        $suma = array_sum($this->materias);
        return $suma / count($this->materias);
    }

    public function obtenerDetalles(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'edad' => $this->edad,
            'carrera' => $this->carrera,
            'promedio' => $this->obtenerPromedio(),
            'materias' => $this->materias,
            'flags' => $this->determinarFlags()
        ];
    }

    private function determinarFlags(): array {
        $flags = [];
        $promedio = $this->obtenerPromedio();
        $materiasReprobadas = array_filter($this->materias, fn($c) => $c < 70);

        if ($promedio >= 90) {
            $flags[] = "Honor Roll";
        } elseif ($promedio < 75) {
            $flags[] = "En Revisión Académica";
        }

        if (count($materiasReprobadas) >= 2) {
            $flags[] = "En Riesgo Académico (" . count($materiasReprobadas) . " reprobadas)";
        }

        return $flags;
    }

    public function __toString(): string {
        $detalles = $this->obtenerDetalles();
        $flagsStr = !empty($detalles['flags']) ? ' | Flags: ' . implode(', ', $detalles['flags']) : '';
        return "ID: {$this->id} | Nombre: {$this->nombre} | Edad: {$this->edad} | Carrera: {$this->carrera} | Promedio: " . number_format($detalles['promedio'], 2) . $flagsStr;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getCarrera(): string {
        return $this->carrera;
    }

    public function getMaterias(): array {
        return $this->materias;
    }
}