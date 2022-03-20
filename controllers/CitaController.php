<?php
namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index(Router $router) {
        $script_moment = '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';

        $script_momentEs = '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es-mx.min.js" integrity="sha512-Qy4cmZ6v7vnVEc0cn/BIj9q15eB98do4hMvu8xtc/H+v+YYpdpDrB35flHS3NPLbZUpe1npSYY/u+Gi3UB61vw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';

        $script_sweetalert = '<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        $script_app = '<script src="/public/build/js/app.js"></script>';

        $scripts = $script_moment
                .$script_momentEs
                .$script_sweetalert 
                .$script_app;

        $router->render("cita/index", [
            "nombre" => ucwords($_SESSION["nombre"]),
            "id" => $_SESSION["id"],
            "script" => $scripts //este script se carga en el layout.php
        ]);
    }
}