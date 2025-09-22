<?php

require_once 'Empleado.php';

class Gerente extends Empleado implements Evaluable {
    private $departamento;

    public function __construct($nombre, $idEmpleado, $salarioBase, $departamento) {
        parent::__construct($nombre, $idEmpleado, $salarioBase);
        $this->departamento = $departamento;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    public function asignarBono($monto) {
        echo "Bono de $$monto asignado al gerente {$this->getNombre()} del departamento {$this->getDepartamento()}.\n";
    }

    public function evaluarDesempenio() {
        return "El gerente {$this->getNombre()} ha sido evaluado como 'Excepcional' basándose en el desempeño de su departamento.";
    }
}