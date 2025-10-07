<?php
// Archivo: clases.php

class Producto {
    public $id;
    public $nombre;
    public $descripcion;
    public $estado;
    public $stock;
    public $fechaIngreso;
    public $categoria;

    public function __construct($datos) {
        foreach ($datos as $clave => $valor) {
            if (property_exists($this, $clave) || in_array($clave, ['garantiaMeses', 'fechaVencimiento', 'talla'])) {
                $this->$clave = $valor;
            }
        }
    }
}

class GestorInventario {
    private $items = [];
    private $rutaArchivo = 'productos.json';

    public function __construct() {
        $this->cargarDesdeArchivo();
    }

    public function obtenerTodos() {
        return $this->items;
    }

    private function cargarDesdeArchivo() {
        if (!file_exists($this->rutaArchivo)) {
            return;
        }
        
        $jsonContenido = file_get_contents($this->rutaArchivo);
        $arrayDatos = json_decode($jsonContenido, true);
        
        if ($arrayDatos === null) {
            return;
        }
        
        $this->items = []; // Limpiar antes de cargar
        foreach ($arrayDatos as $datos) {
            $this->items[] = new Producto($datos);
        }
    }

    private function persistirEnArchivo() {
        $arrayParaGuardar = array_map(function($item) {
            return get_object_vars($item);
        }, $this->items);
        
        file_put_contents(
            $this->rutaArchivo, 
            json_encode($arrayParaGuardar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function obtenerMaximoId() {
        if (empty($this->items)) {
            return 0;
        }
        
        $ids = array_map(function($item) {
            return $item->id;
        }, $this->items);
        
        return max($ids);
    }

    public function agregar($nuevoProducto) {
        $nuevoProducto->id = $this->obtenerMaximoId() + 1;
        $nuevoProducto->fechaIngreso = date('Y-m-d');
        $this->items[] = $nuevoProducto;
        $this->persistirEnArchivo();
    }

    public function eliminar($idProducto) {
        $encontrado = false;
        foreach ($this->items as $indice => $item) {
            if ($item->id == $idProducto) {
                array_splice($this->items, $indice, 1);
                $encontrado = true;
                break;
            }
        }
        if ($encontrado) {
            $this->persistirEnArchivo();
        }
        return $encontrado;
    }

    public function actualizar($productoActualizado) {
        $encontrado = false;
        foreach ($this->items as $indice => $item) {
            if ($item->id == $productoActualizado->id) {
                // Mantenemos la fecha de ingreso original
                $productoActualizado->fechaIngreso = $item->fechaIngreso;
                $this->items[$indice] = $productoActualizado;
                $encontrado = true;
                break;
            }
        }
        if ($encontrado) {
            $this->persistirEnArchivo();
        }
        return $encontrado;
    }

    public function cambiarEstado($idProducto, $estadoNuevo) {
        $encontrado = false;
        foreach ($this->items as $item) {
            if ($item->id == $idProducto) {
                $item->estado = $estadoNuevo;
                $encontrado = true;
                break;
            }
        }
        if ($encontrado) {
            $this->persistirEnArchivo();
        }
        return $encontrado;
    }

    public function filtrarPorEstado($estadoBuscado) {
        if (empty($estadoBuscado)) {
            return $this->obtenerTodos();
        }
        return array_filter($this->items, function($item) use ($estadoBuscado) {
            return $item->estado === $estadoBuscado;
        });
    }

    public function obtenerPorId($idBuscado) {
        foreach ($this->items as $item) {
            if ($item->id == $idBuscado) {
                return $item;
            }
        }
        return null;
    }

    
}