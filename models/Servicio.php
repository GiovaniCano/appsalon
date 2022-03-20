<?php
namespace Model;

class Servicio extends ActiveRecord{
    /* base de datos */
    protected static $tabla = "servicios";
    protected static $columnasDB = [
        "id",
        "nombre",
        "precio"
    ];

    public $id;
    public $nombre;
    public $precio;

    public function __construct(array $args = []) {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->precio = $args["precio"] ?? "";
    }

    public function validar() {
        $this->precio = filter_var($this->precio, FILTER_VALIDATE_FLOAT);
        if(!$this->precio) self::$alertas["error"][] = "El Precio del Servicio es Obligatorio";

        if(!$this->nombre) self::$alertas["error"][] = "El Nombre del Servicio es Obligatorio";

        return self::$alertas;
    }
}