<?php
interface Detalle {
    public function obtenerDetallesEspecificos(): string;
}

abstract class Entrada implements Detalle {
    public $id;
    public $fecha_creacion;
    public $tipo;

    public function __construct($id, $fecha_creacion, $tipo) {
        $this->id = $id;
        $this->fecha_creacion = $fecha_creacion;
        $this->tipo = $tipo;
    }
}

class EntradaUnaColumna extends Entrada {
    public $titulo;
    public $descripcion;

    public function __construct($id, $fecha_creacion, $tipo, $titulo, $descripcion) {
        parent::__construct($id, $fecha_creacion, $tipo);
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
    }

    public function obtenerDetallesEspecificos(): string {
        return "Entrada de una columna: " . $this->titulo;
    }
}

class EntradaDosColumnas extends Entrada {
    public $titulo1;
    public $descripcion1;
    public $titulo2;
    public $descripcion2;

    public function __construct($id, $fecha_creacion, $tipo, $titulo1, $descripcion1, $titulo2, $descripcion2) {
        parent::__construct($id, $fecha_creacion, $tipo);
        $this->titulo1 = $titulo1;
        $this->descripcion1 = $descripcion1;
        $this->titulo2 = $titulo2;
        $this->descripcion2 = $descripcion2;
    }

    public function obtenerDetallesEspecificos(): string {
        return "Entrada de dos columnas: " . $this->titulo1 . " | " . $this->titulo2;
    }
}

class EntradaTresColumnas extends Entrada {
    public $titulo1;
    public $descripcion1;
    public $titulo2;
    public $descripcion2;
    public $titulo3;
    public $descripcion3;

    public function __construct($id, $fecha_creacion, $tipo, $titulo1, $descripcion1, $titulo2, $descripcion2, $titulo3, $descripcion3) {
        parent::__construct($id, $fecha_creacion, $tipo);
        $this->titulo1 = $titulo1;
        $this->descripcion1 = $descripcion1;
        $this->titulo2 = $titulo2;
        $this->descripcion2 = $descripcion2;
        $this->titulo3 = $titulo3;
        $this->descripcion3 = $descripcion3;
    }

    public function obtenerDetallesEspecificos(): string {
        return "Entrada de tres columnas: " . $this->titulo1 . " | " . $this->titulo2 . " | " . $this->titulo3;
    }
}

class GestorBlog {
    private $entradas = [];

    public function cargarEntradas() {
        if (file_exists('blog.json')) {
            $json = file_get_contents('blog.json');
            $data = json_decode($json, true);
            foreach ($data as $entradaData) {
                switch ($entradaData['tipo']) {
                    case 1:
                        $this->entradas[] = new EntradaUnaColumna($entradaData['id'], $entradaData['fecha_creacion'], $entradaData['tipo'], $entradaData['titulo'], $entradaData['descripcion']);
                        break;
                    case 2:
                        $this->entradas[] = new EntradaDosColumnas($entradaData['id'], $entradaData['fecha_creacion'], $entradaData['tipo'], $entradaData['titulo1'], $entradaData['descripcion1'], $entradaData['titulo2'], $entradaData['descripcion2']);
                        break;
                    case 3:
                        $this->entradas[] = new EntradaTresColumnas($entradaData['id'], $entradaData['fecha_creacion'], $entradaData['tipo'], $entradaData['titulo1'], $entradaData['descripcion1'], $entradaData['titulo2'], $entradaData['descripcion2'], $entradaData['titulo3'], $entradaData['descripcion3']);
                        break;
                }
            }
        }
    }

    public function guardarEntradas() {
        $data = array_map(function($entrada) {
            return get_object_vars($entrada);
        }, $this->entradas);
        file_put_contents('blog.json', json_encode($data, JSON_PRETTY_PRINT));
    }

    public function agregarEntrada(Entrada $entrada) {
        $this->entradas[] = $entrada;
        $this->guardarEntradas();
    }

    public function editarEntrada(Entrada $entrada) {
        foreach ($this->entradas as &$e) {
            if ($e->id == $entrada->id) {
                $e = $entrada;
                break;
            }
        }
        $this->guardarEntradas();
    }

    public function eliminarEntrada($id) {
        $this->entradas = array_filter($this->entradas, function($e) use ($id) {
            return $e->id != $id;
        });
        $this->guardarEntradas();
    }

    public function obtenerEntrada($id) {
        foreach ($this->entradas as $entrada) {
            if ($entrada->id == $id) {
                return $entrada;
            }
        }
        return null;
    }

    public function moverEntrada($id, $direccion) {
        $index = null;
        foreach ($this->entradas as $key => $entrada) {
            if ($entrada->id == $id) {
                $index = $key;
                break;
            }
        }

        if ($index !== null) {
            if ($direccion == 'up' && $index > 0) {
                $temp = $this->entradas[$index - 1];
                $this->entradas[$index - 1] = $this->entradas[$index];
                $this->entradas[$index] = $temp;
            } elseif ($direccion == 'down' && $index < count($this->entradas) - 1) {
                $temp = $this->entradas[$index + 1];
                $this->entradas[$index + 1] = $this->entradas[$index];
                $this->entradas[$index] = $temp;
            }
            $this->guardarEntradas();
        }
    }

    public function obtenerTodasLasEntradas() {
        return $this->entradas;
    }
}
?>