<?php
namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index(Router $router) {
        $servicios = Servicio::all();

        $router->render("/servicios/index", [
            "nombre" => ucwords($_SESSION["nombre"]),
            "servicios" => $servicios
        ]);
    }

    public static function crear(Router $router) {
        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(!$alertas) {
                $servicio->guardar();
                exit(header("location: /servicios"));
            }
        }

        $router->render("/servicios/crear", [
            "nombre" => ucwords($_SESSION["nombre"]),
            "servicio" => $servicio,
            "alertas" => $alertas
        ]);
    }

    public static function actualizar(Router $router) {
        $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
        if(!$id) exit(header("location: /servicios"));

        $servicio = Servicio::find($id);
        if(!$servicio) exit(header("location: /servicios"));

        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(!$alertas) {
                $servicio->guardar();
                exit(header("location: /servicios"));
            }
        }

        $router->render("/servicios/actualizar", [
            "nombre" => ucwords($_SESSION["nombre"]),
            "servicio" => $servicio,
            "alertas" => $alertas
        ]);
    }

    public static function eliminar(Router $router) {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
            if(!$id) exit(header("location: /servicios"));

            $servicio = Servicio::find($id);
            if(!$servicio) exit(header("location: /servicios"));
            
            $servicio->eliminar();
            header("location: /servicios");
        }
    }
}