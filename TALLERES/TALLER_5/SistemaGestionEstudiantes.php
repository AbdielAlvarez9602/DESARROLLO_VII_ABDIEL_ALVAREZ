<?php
// SistemaGestionEstudiantes.php
// Requiere la clase Estudiante para funcionar
require_once 'Estudiante.php'; 

/**
 * Clase SistemaGestionEstudiantes
 * Gestiona una colección de objetos Estudiante.
 */
class SistemaGestionEstudiantes {
    private array $estudiantes; // Contiene objetos Estudiante
    private array $graduados;  // Contiene objetos Estudiante graduados

    public function __construct() {
        $this->estudiantes = [];
        $this->graduados = [];
    }

    // ... (Todos los métodos de la clase SistemaGestionEstudiantes) ...
    
    public function agregarEstudiante(Estudiante $estudiante): void {
        $this->estudiantes[$estudiante->getId()] = $estudiante;
    }

    public function obtenerEstudiante(int $id): ?Estudiante {
        return $this->estudiantes[$id] ?? null;
    }

    public function listarEstudiantes(): array {
        return $this->estudiantes;
    }

    public function calcularPromedioGeneral(): float {
        if (empty($this->estudiantes)) {
            return 0.0;
        }

        $promedios = array_map(fn(Estudiante $e) => $e->obtenerPromedio(), $this->estudiantes);
        $sumaPromedios = array_reduce($promedios, fn($carry, $promedio) => $carry + $promedio, 0);

        return $sumaPromedios / count($this->estudiantes);
    }

    public function obtenerEstudiantesPorCarrera(string $carrera): array {
        $carreraLower = strtolower($carrera);
        // Uso de array_filter
        return array_filter($this->estudiantes, function(Estudiante $e) use ($carreraLower) {
            return strtolower($e->getCarrera()) === $carreraLower;
        });
    }

    public function obtenerMejorEstudiante(): ?Estudiante {
        if (empty($this->estudiantes)) {
            return null;
        }
        // Uso de array_reduce
        return array_reduce($this->estudiantes, function(?Estudiante $mejor, Estudiante $actual) {
            if ($mejor === null || $actual->obtenerPromedio() > $mejor->obtenerPromedio()) {
                return $actual;
            }
            return $mejor;
        });
    }

    public function generarReporteRendimiento(): array {
        $reporte = [];
        
        foreach ($this->estudiantes as $estudiante) {
            foreach ($estudiante->getMaterias() as $materia => $calificacion) {
                if (!isset($reporte[$materia])) {
                    $reporte[$materia] = ['sumatoria' => 0, 'conteo' => 0, 'max' => -1, 'min' => 101];
                }
                
                $reporte[$materia]['sumatoria'] += $calificacion;
                $reporte[$materia]['conteo']++;
                $reporte[$materia]['max'] = max($reporte[$materia]['max'], $calificacion);
                $reporte[$materia]['min'] = min($reporte[$materia]['min'], $calificacion);
            }
        }
        
        // Uso de array_map para procesar el promedio final
        $reporteFinal = array_map(function($datos) {
            $promedio = $datos['conteo'] > 0 ? $datos['sumatoria'] / $datos['conteo'] : 0;
            return [
                'promedio' => number_format($promedio, 2),
                'calificacion_max' => $datos['max'],
                'calificacion_min' => $datos['min']
            ];
        }, $reporte);
        
        return $reporteFinal;
    }

    public function graduarEstudiante(int $id): bool {
        if (isset($this->estudiantes[$id])) {
            $estudiante = $this->estudiantes[$id];
            $this->graduados[$id] = $estudiante;
            unset($this->estudiantes[$id]);
            return true;
        }
        return false;
    }

    public function generarRanking(): array {
        $ranking = $this->estudiantes;
        
        // Uso de usort
        usort($ranking, function(Estudiante $a, Estudiante $b) {
            return $b->obtenerPromedio() <=> $a->obtenerPromedio(); 
        });
        
        return $ranking;
    }

    public function buscarEstudiantes(string $termino): array {
        $terminoLower = strtolower($termino);
        
        // Uso de array_filter
        return array_filter($this->estudiantes, function(Estudiante $e) use ($terminoLower) {
            $nombreLower = strtolower($e->getNombre());
            $carreraLower = strtolower($e->getCarrera());
            
            return strpos($nombreLower, $terminoLower) !== false || 
                   strpos($carreraLower, $terminoLower) !== false;
        });
    }

    public function generarEstadisticasPorCarrera(): array {
        $estadisticas = [];
        $estudiantesPorCarrera = [];

        // Agrupar estudiantes por carrera
        foreach ($this->estudiantes as $e) {
            $carrera = $e->getCarrera();
            $estudiantesPorCarrera[$carrera][] = $e;
        }

        // Calcular estadísticas para cada carrera
        foreach ($estudiantesPorCarrera as $carrera => $lista) {
            $numEstudiantes = count($lista);
            
            // Cálculo del promedio (uso de array_reduce)
            $sumaPromedios = array_reduce($lista, fn(float $sum, Estudiante $e) => $sum + $e->obtenerPromedio(), 0.0);
            $promedioCarrera = $sumaPromedios / $numEstudiantes;

            // Encontrar al mejor estudiante (uso de array_reduce)
            $mejorEstudiante = array_reduce($lista, function(?Estudiante $mejor, Estudiante $actual) {
                if ($mejor === null || $actual->obtenerPromedio() > $mejor->obtenerPromedio()) {
                    return $actual;
                }
                return $mejor;
            });
            
            $estadisticas[$carrera] = [
                'num_estudiantes' => $numEstudiantes,
                'promedio_general' => number_format($promedioCarrera, 2),
                'mejor_estudiante' => $mejorEstudiante ? $mejorEstudiante->getNombre() : 'N/A'
            ];
        }

        return $estadisticas;
    }
}