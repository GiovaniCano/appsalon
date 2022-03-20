<?php
namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

use function PHPSTORM_META\type;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar() {
        /* Almacena la cita y devuelve el id */
        $cita = new Cita($_POST);
        $resultadoCita = $cita->guardar(); //retorna bool $resulatdo e int $id en un array asociativo

        /* Almacena la cita con servicios */
        $idServicios = null;
        if(gettype($_POST["servicios"]) === "string") {
            $idServicios = explode(",", $_POST["servicios"]); //con fetch
        } elseif(gettype($_POST["servicios"]) === "array") {
            $idServicios = $_POST["servicios"]; //con jquery
        }

        $resultadosCitaServicio = [];
        foreach ($idServicios as $idServicio) {
            $args = [
                "citaId" => $resultadoCita["id"],
                "servicioId" => $idServicio
            ];

            $citaServicio = new CitaServicio($args);
            $resultadosCitaServicio[] = $citaServicio->guardar();
        }

        /* retornamos una respuesta */
        $respuesta = [
            // "resultado" => $resultadosCitaServicio
            "resultado" => $resultadoCita
            // "resultadoCita" => $resultadoCita,
            // "resultadoCitaId" => $resultadoCita["id"]
        ];
        echo json_encode($respuesta);
    }

    public static function eliminar() {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? 0;
            $cita = Cita::find($id);
            $cita->eliminar();
            header("location: " . $_SERVER["HTTP_REFERER"]);
        }
    }
}