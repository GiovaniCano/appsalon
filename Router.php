<?php
namespace MVC;

class Router {    
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get(string $url, array $fn):void {
        $this->getRoutes[$url] = $fn;
    }

    public function post(string $url, array $fn):void {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas(): void {
        /* Proteger Rutas */
        $rutasAdmin = [
            "/admin",
            "/servicios",
            "/servicios/crear",
            "/servicios/actualizar",
            "/servicios/eliminar"
        ];
        $rutasProtegidas = [ ...$rutasAdmin,
            "/cita"
        ];

        session_start();
        $auth = $_SESSION['login'] ?? false;
        $admin = $_SESSION["admin"] ?? false;

        $currentUrl = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if(in_array($currentUrl, $rutasProtegidas)) {
            if(!$auth) exit(header("location: /"));

            if(in_array($currentUrl, $rutasAdmin)) {
                if(!$admin) exit(header("location: /cita"));
            }
        }

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }

    public function render($view, $datos = [])
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer

        include_once __DIR__ . '/views/layout.php';
    }
}
