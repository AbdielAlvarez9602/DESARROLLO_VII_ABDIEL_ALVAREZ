<?php

require_once 'Empresa.php';
require_once 'Gerente.php';
require_once 'Desarrollador.php';

$miEmpresa = new Empresa();

$gerente1 = new Gerente("Ana García", "G-001", 60000, "Ventas");
$desarrollador1 = new Desarrollador("Luis Pérez", "D-001", 45000, "PHP", "Senior");
$desarrollador2 = new Desarrollador("Marta López", "D-002", 35000, "Python", "Junior");
$empleadoBase = new Empleado("Pedro Ruiz", "E-001", 30000); // Un empleado que no es evaluable

$miEmpresa->agregarEmpleado($gerente1);
$miEmpresa->agregarEmpleado($desarrollador1);
$miEmpresa->agregarEmpleado($desarrollador2);
$miEmpresa->agregarEmpleado($empleadoBase);

echo "--- Listado Inicial de Empleados ---\n";
$miEmpresa->listarEmpleados();
echo "\n";

$nominaTotal = $miEmpresa->calcularNominaTotal();
echo "Nómina total de la empresa: $$nominaTotal\n\n";

echo "--- Realizando Evaluaciones de Desempeño ---\n";
$miEmpresa->realizarEvaluaciones();
echo "\n";

$gerente1->asignarBono(5000);
echo "\n";
?>