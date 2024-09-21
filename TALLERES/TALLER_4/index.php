<?php
require_once 'Empleado.php';
require_once 'Gerente.php';
require_once 'Desarrollador.php';
require_once 'Empresa.php';

$empresa = new Empresa();

$gerente = new Gerente("Carlos Pérez", 1, 5000, "Recursos Humanos");
$desarrollador = new Desarrollador("Ana Martínez", 2, 4000, "PHP", "Senior");

$empresa->agregarEmpleado($gerente);
$empresa->agregarEmpleado($desarrollador);

echo "Listado de empleados:\n";
$empresa->listarEmpleados();

echo "Nómina total: " . $empresa->calcularNominaTotal() . "\n";

echo "Evaluaciones de desempeño:\n";
$empresa->evaluarDesempenio();
?>