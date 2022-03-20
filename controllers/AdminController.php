<?php
namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index(Router $router) {
        $fecha = $_GET["fecha"] ?? Date("Y-m-d");;
        $fechaArray = explode("-", $fecha);
        if(!checkdate($fechaArray[1], $fechaArray[2], $fechaArray[0])) {
            exit(header("location: /admin"));
        }

        /* Consultar la base de datos */        
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasServicios ";
        $consulta .= " ON citasServicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasServicios.servicioId ";
        $consulta .= " WHERE fecha =  '${fecha}' ";

        $citas = AdminCita::SQL($consulta);

        $router->render("admin/index", [
            "nombre" => ucwords($_SESSION["nombre"]),
            "citas" => $citas,
            "fecha" => $fecha,
            "script" => '<script src="/public/build/js/buscador.js"></script>'
        ]);
    }
}