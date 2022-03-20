<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        /* que no inicie sesion si ya tiene una sesion */
        if($_SESSION["login"] ?? false) {
            if($_SESSION["admin"]) {
                exit(header("location: /admin"));
            } else {
                exit(header("location: /cita"));
            }
        }/*  */

        $alertas = [];
        $auth = new Usuario;

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                /* comprobar que exista el usuario */
                $usuario = Usuario::where("email", $auth->email);
                if($usuario) {
                    /* Verificar password */
                    if($usuario->comprobarPasswordYVerificado($auth->password)) {
                        /* Autenticar al usuario */
                        $_SESSION = [
                            "id" => $usuario->id,
                            "nombre" => $usuario->nombre . " " . $usuario->apellido,
                            "email" => $usuario->email,
                            "login" => true
                        ];

                        /* Redireccionamiento */
                        if($usuario->admin === "1") {
                            $_SESSION["admin"] = true;
                            header("location: /admin");
                        } else {
                            header("location: /cita");
                        }
                    }
                } else {
                    Usuario::setAlerta("error", "Usuario no Encontrado");
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/login", [
            "alertas" => $alertas,
            "auth" => $auth
        ]);
    }

    public static function logout() {
        $_SESSION = [];
        header("location: /");
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);
                if($usuario && $usuario->confirmado === "1") {
                    /* Generar un token */
                    $usuario->crearToken();
                    $usuario->guardar();

                    /* Enviar el email */
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta("exito", "Revisa tu email");
                } else {
                    Usuario::setAlerta("error", "El Usuario no Existe o no Está Confirmado");
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/olvide-password", [
            "alertas" => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;
        $token = s($_GET["token"]);

        /* buscar usuario por su token */
        $usuario = Usuario::where("token", $token);

        if(empty($usuario)) {
            Usuario::setAlerta("error", "Token No Valido");
            $error = true;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            /* leer el nuevo password y guardarlo */
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();

                if($resultado) {
                    header("location: /");
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/recuperar-password", [
            "alertas" => $alertas,
            "error" => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario; //values iniciales vacios

        $alertas = []; // alertas vacias

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                /* Verificar que el usuario no esté registrado */
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    /* hashear el password */
                    $usuario->hashPassword();

                    /* generar un token unico */
                    $usuario->crearToken();

                    /* Enviar email */
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    /* Crear el usuario */
                    $resultado = $usuario->guardar();

                    if($resultado) header("location: /mensaje");
                }
            }
        }
        $router->render("auth/crear-cuenta", [
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render("auth/mensaje");
    }

    public static function confirmar(Router $router) {
        $alertas = [];

        $token = s($_GET["token"]);

        $usuario = Usuario::where("token", $token);

        if(empty($usuario)) {
            /* mensaje de error */
            Usuario::setAlerta("error", "Token no Válido");
        } else {
            /* modificar a usuario confirmado */
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta("exito", "Cuenta Comprobada Correctamente");
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/confirmar-cuenta", [
            "alertas" => $alertas
        ]);
    }
}