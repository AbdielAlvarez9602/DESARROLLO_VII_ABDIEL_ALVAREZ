<?php
// Archivo principal para inicializar y probar el sistema

// Incluimos la definición de la clase SistemaGestionEstudiantes, que a su vez incluye Estudiante.php
require_once 'SistemaGestionEstudiantes.php'; 

echo "================================================\n";
echo "  PROYECTO FINAL: SISTEMA DE GESTIÓN DE ESTUDIANTES\n";
echo "================================================\n\n";

// 1. Instanciar el Sistema
$sistema = new SistemaGestionEstudiantes();

// 2. Crear al menos 10 estudiantes y añadir materias y calificaciones
echo "Creando y añadiendo estudiantes (10+)...\n";

$e1 = new Estudiante(1, "Ana Fernández", 20, "Ingeniería");
$e1->agregarMateria("Cálculo", 95.0);
$e1->agregarMateria("Programación", 98.5);
$e1->agregarMateria("Física", 92.0);
$sistema->agregarEstudiante($e1);

$e2 = new Estudiante(2, "Carlos Gómez", 22, "Arquitectura");
$e2->agregarMateria("Dibujo", 85.0);
$e2->agregarMateria("Estructuras", 78.0);
$e2->agregarMateria("Historia", 65.0); // Reprobada
$sistema->agregarEstudiante($e2);

$e3 = new Estudiante(3, "María López", 19, "Ingeniería");
$e3->agregarMateria("Cálculo", 70.0);
$e3->agregarMateria("Programación", 68.0); // Reprobada
$e3->agregarMateria("Química", 75.0);
$sistema->agregarEstudiante($e3);

$e4 = new Estudiante(4, "David Ríos", 21, "Derecho");
$e4->agregarMateria("Constitucional", 90.0);
$e4->agregarMateria("Civil", 88.0);
$sistema->agregarEstudiante($e4);

$e5 = new Estudiante(5, "Elena Cruz", 20, "Arquitectura");
$e5->agregarMateria("Dibujo", 92.0);
$e5->agregarMateria("Estructuras", 90.0);
$e5->agregarMateria("Historia", 85.0);
$sistema->agregarEstudiante($e5);

$e6 = new Estudiante(6, "Fernando Muro", 23, "Medicina");
$e6->agregarMateria("Anatomía", 80.0);
$e6->agregarMateria("Fisiología", 85.0);
$sistema->agregarEstudiante($e6);

$e7 = new Estudiante(7, "Gabriela Sol", 18, "Ingeniería");
$e7->agregarMateria("Cálculo", 60.0); // Reprobada
$e7->agregarMateria("Física", 65.0);  // Reprobada
$sistema->agregarEstudiante($e7);

$e8 = new Estudiante(8, "Héctor Paz", 25, "Derecho");
$e8->agregarMateria("Constitucional", 85.0);
$e8->agregarMateria("Penal", 95.0);
$sistema->agregarEstudiante($e8);

$e9 = new Estudiante(9, "Irene Vila", 20, "Medicina");
$e9->agregarMateria("Anatomía", 95.0);
$e9->agregarMateria("Fisiología", 98.0);
$sistema->agregarEstudiante($e9);

$e10 = new Estudiante(10, "Juan Soto", 21, "Ingeniería");
$e10->agregarMateria("Programación", 90.0);
$e10->agregarMateria("Física", 85.0);
$sistema->agregarEstudiante($e10);

$e11 = new Estudiante(11, "Laura Pérez", 19, "Ingeniería");
$e11->agregarMateria("Programación", 95.0);
$e11->agregarMateria("Química", 90.0);
$sistema->agregarEstudiante($e11);

echo "Total de estudiantes activos: " . count($sistema->listarEstudiantes()) . "\n\n";

// 3. Demostración de Funcionalidades

echo "------------------------------------------------\n";
echo "DEMOSTRACIÓN DE FUNCIONALIDADES INDIVIDUALES\n";
echo "------------------------------------------------\n";

// 3.1 Obtener y mostrar un estudiante por ID
$e3Info = $sistema->obtenerEstudiante(3);
echo "Detalles del Estudiante ID 3 (María López):\n";
echo $e3Info . "\n";
// print_r($e3Info->obtenerDetalles()); // Descomentar para ver el detalle completo

// 3.2 Buscar estudiantes (Búsqueda parcial e insensible)
echo "\nBuscando estudiantes por término 'inGe'\n";
$busquedaIng = $sistema->buscarEstudiantes("inGe");
foreach ($busquedaIng as $e) {
    echo $e . "\n";
}

echo "\n------------------------------------------------\n";
echo "DEMOSTRACIÓN DE FUNCIONALIDADES DE REPORTE\n";
echo "------------------------------------------------\n";

// 3.3 Calcular Promedio General
echo "Promedio General de todos los estudiantes: " . number_format($sistema->calcularPromedioGeneral(), 2) . "\n\n";

// 3.4 Ranking por Promedio
echo "Ranking de Estudiantes (Mejor Promedio Primero):\n";
$ranking = $sistema->generarRanking();
$pos = 1;
foreach ($ranking as $e) {
    echo "{$pos}. {$e}\n";
    $pos++;
}

// 3.5 Reporte de Rendimiento por Materia
echo "\nReporte de Rendimiento por Materia:\n";
print_r($sistema->generarReporteRendimiento());

// 3.6 Estadísticas por Carrera
echo "\nEstadísticas Detalladas por Carrera:\n";
print_r($sistema->generarEstadisticasPorCarrera());

// 3.7 Graduar Estudiante
echo "\n------------------------------------------------\n";
echo "DEMOSTRACIÓN DE GRADUACIÓN\n";
echo "------------------------------------------------\n";
$idGraduar = 4;
$nombreGraduar = $sistema->obtenerEstudiante($idGraduar)->getNombre();

echo "Intentando graduar a {$nombreGraduar} (ID: {$idGraduar})... ";
if ($sistema->graduarEstudiante($idGraduar)) {
    echo "¡Graduado con éxito!\n";
} else {
    echo "Error al graduar.\n";
}

echo "Total de estudiantes activos restantes: " . count($sistema->listarEstudiantes()) . "\n";