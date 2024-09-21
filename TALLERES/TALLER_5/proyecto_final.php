<?php

class Estudiante {
    private $id;
    private $nombre;
    private $edad;
    private $carrera;
    private $materias; // Arreglo de materias y calificaciones

    public function __construct($id, $nombre, $edad, $carrera) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->carrera = $carrera;
        $this->materias = [];
    }

    public function agregarMateria($materia, $calificacion) {
        $this->materias[$materia] = $calificacion;
    }

    public function obtenerPromedio() {
        return empty($this->materias) ? 0 : array_sum($this->materias) / count($this->materias);
    }

    public function obtenerDetalles() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'edad' => $this->edad,
            'carrera' => $this->carrera,
            'materias' => $this->materias,
            'promedio' => $this->obtenerPromedio()
        ];
    }

    public function __toString() {
        return "{$this->nombre} (ID: {$this->id}) - Carrera: {$this->carrera}, Promedio: " . number_format($this->obtenerPromedio(), 2);
    }
}

class SistemaGestionEstudiantes {
    private $estudiantes = [];
    private $graduados = [];

    public function agregarEstudiante(Estudiante $estudiante) {
        $this->estudiantes[$estudiante->obtenerDetalles()['id']] = $estudiante;
    }

    public function obtenerEstudiante($id) {
        return $this->estudiantes[$id] ?? null;
    }

    public function listarEstudiantes() {
        return $this->estudiantes;
    }

    public function calcularPromedioGeneral() {
        if (empty($this->estudiantes)) return 0;
        $sumaPromedios = array_reduce($this->estudiantes, function($carry, $estudiante) {
            return $carry + $estudiante->obtenerPromedio();
        }, 0);
        return $sumaPromedios / count($this->estudiantes);
    }

    public function obtenerEstudiantesPorCarrera($carrera) {
        return array_filter($this->estudiantes, function($estudiante) use ($carrera) {
            return stripos($estudiante->obtenerDetalles()['carrera'], $carrera) !== false;
        });
    }

    public function obtenerMejorEstudiante() {
        return array_reduce($this->estudiantes, function($mejor, $estudiante) {
            return ($mejor === null || $estudiante->obtenerPromedio() > $mejor->obtenerPromedio()) ? $estudiante : $mejor;
        });
    }

    public function generarReporteRendimiento() {
        $reportes = [];
        foreach ($this->estudiantes as $estudiante) {
            foreach ($estudiante->obtenerDetalles()['materias'] as $materia => $calificacion) {
                if (!isset($reportes[$materia])) {
                    $reportes[$materia] = ['total' => 0, 'cantidad' => 0, 'calificaciones' => []];
                }
                $reportes[$materia]['total'] += $calificacion;
                $reportes[$materia]['cantidad']++;
                $reportes[$materia]['calificaciones'][] = $calificacion;
            }
        }

        foreach ($reportes as $materia => &$info) {
            $info['promedio'] = $info['total'] / $info['cantidad'];
            $info['max'] = max($info['calificaciones']);
            $info['min'] = min($info['calificaciones']);
        }
        return $reportes;
    }

    public function graduarEstudiante($id) {
        if (isset($this->estudiantes[$id])) {
            $this->graduados[] = $this->estudiantes[$id];
            unset($this->estudiantes[$id]);
        }
    }

    public function generarRanking() {
        $ranking = $this->estudiantes;
        usort($ranking, function($a, $b) {
            return $b->obtenerPromedio() <=> $a->obtenerPromedio();
        });
        return $ranking;
    }

    public function buscarEstudiantes($termino) {
        return array_filter($this->estudiantes, function($estudiante) use ($termino) {
            return stripos($estudiante->obtenerDetalles()['nombre'], $termino) !== false || 
                   stripos($estudiante->obtenerDetalles()['carrera'], $termino) !== false;
        });
    }

    public function generarEstadisticasPorCarrera() {
        $estadisticas = [];
        foreach ($this->estudiantes as $estudiante) {
            $carrera = $estudiante->obtenerDetalles()['carrera'];
            if (!isset($estadisticas[$carrera])) {
                $estadisticas[$carrera] = ['cantidad' => 0, 'sumaPromedios' => 0, 'mejorEstudiante' => null];
            }
            $estadisticas[$carrera]['cantidad']++;
            $estadisticas[$carrera]['sumaPromedios'] += $estudiante->obtenerPromedio();
            $mejor = $estadisticas[$carrera]['mejorEstudiante'];
            if ($mejor === null || $estudiante->obtenerPromedio() > $mejor->obtenerPromedio()) {
                $estadisticas[$carrera]['mejorEstudiante'] = $estudiante;
            }
        }

        foreach ($estadisticas as &$info) {
            $info['promedioGeneral'] = $info['sumaPromedios'] / $info['cantidad'];
        }
        return $estadisticas;
    }
}

// Sección de prueba
$sistema = new SistemaGestionEstudiantes();

$estudiantesData = [
    [1, "Ana López", 20, "Ingeniería"],
    [2, "Carlos Gómez", 22, "Derecho"],
    [3, "María Rodríguez", 21, "Ingeniería"],
    [4, "Luis Pérez", 23, "Medicina"],
    [5, "Laura Martínez", 19, "Ingeniería"],
    [6, "Pedro Fernández", 24, "Derecho"],
    [7, "Sofía Torres", 20, "Medicina"],
    [8, "Javier Díaz", 22, "Ingeniería"],
    [9, "Claudia Jiménez", 21, "Derecho"],
    [10, "Fernando Castro", 25, "Medicina"]
];

foreach ($estudiantesData as $data) {
    $estudiante = new Estudiante(...$data);
    $estudiante->agregarMateria("Matemáticas", rand(60, 100));
    $estudiante->agregarMateria("Física", rand(60, 100));
    $estudiante->agregarMateria("Química", rand(60, 100));
    $sistema->agregarEstudiante($estudiante);
}

// Demostración de funcionalidades
echo "Estudiantes:\n";
foreach ($sistema->listarEstudiantes() as $estudiante) {
    echo $estudiante . "\n";
}

echo "\nPromedio general: " . number_format($sistema->calcularPromedioGeneral(), 2) . "\n";

echo "\nMejor estudiante: " . $sistema->obtenerMejorEstudiante() . "\n";

echo "\nReporte de rendimiento:\n";
$reporte = $sistema->generarReporteRendimiento();
foreach ($reporte as $materia => $info) {
    echo "$materia - Promedio: " . number_format($info['promedio'], 2) . ", Max: {$info['max']}, Min: {$info['min']}\n";
}

// Graduar un estudiante
$sistema->graduarEstudiante(1);
echo "\nEstudiantes después de graduar a Ana:\n";
foreach ($sistema->listarEstudiantes() as $estudiante) {
    echo $estudiante . "\n";
}

// Estadísticas por carrera
echo "\nEstadísticas por carrera:\n";
$estadisticas = $sistema->generarEstadisticasPorCarrera();
foreach ($estadisticas as $carrera => $info) {
    echo "$carrera - Estudiantes: {$info['cantidad']}, Promedio General: " . number_format($info['promedioGeneral'], 2) . ", Mejor Estudiante: {$info['mejorEstudiante']}\n";
}

// Busqueda de estudiantes
$busqueda = "Ingeniería";
echo "\nEstudiantes en la carrera de '$busqueda':\n";
foreach ($sistema->obtenerEstudiantesPorCarrera($busqueda) as $estudiante) {
    echo $estudiante . "\n";
}
?>