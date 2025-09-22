<?php

require_once 'Empleado.php';
require_once 'Evaluable.php';

class Empresa {
    private $empleados = [];

    public function agregarEmpleado(Empleado $empleado) {
        $this->empleados[] = $empleado;
    }

    public function listarEmpleados() {
        echo "=== Listado de Empleados ===\n";
        foreach ($this->empleados as $empleado) {
            echo "Nombre: {$empleado->getNombre()}, ID: {$empleado->getIdEmpleado()}, Salario Base: {$empleado->getSalarioBase()}\n";
            if ($empleado instanceof Gerente) {
                echo "Tipo: Gerente, Departamento: {$empleado->getDepartamento()}\n";
            } elseif ($empleado instanceof Desarrollador) {
                echo "Tipo: Desarrollador, Lenguaje: {$empleado->getLenguajePrincipal()}, Nivel: {$empleado->getNivelExperiencia()}\n";
            }
            echo "--------------------------\n";
        }
    }

    public function calcularNominaTotal() {
        $nominaTotal = 0;
        foreach ($this->empleados as $empleado) {
            $nominaTotal += $empleado->getSalarioBase();
        }
        return $nominaTotal;
    }

    public function realizarEvaluaciones() {
        echo "=== Evaluaciones de Desempeño ===\n";
        foreach ($this->empleados as $empleado) {
            if ($empleado instanceof Evaluable) {
                echo $empleado->evaluarDesempenio() . "\n";
            } else {
                echo "El empleado {$empleado->getNombre()} no es evaluable.\n";
            }
        }
    }
}