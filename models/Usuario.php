<?php declare(strict_types=1);
namespace Model;

class Usuario extends ActiveRecord {
    /* Base de datos*/
    protected static $tabla = "usuarios";
    protected static $columnasDB = [
        // "id",
        "nombre",
        "apellido",
        "email",
        "password",
        "telefono",
        "admin",
        "confirmado",
        "token"        
    ];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    function __construct(array $args = []) {
        $this->id           = $args["id"] ?? null;
        $this->nombre       = trim($args["nombre"] ?? '');
        $this->apellido     = trim($args["apellido"] ?? '');
        $this->email        = trim($args["email"] ?? '');
        $this->password     = trim($args["password"] ?? '');
        $this->telefono     = trim($args["telefono"] ?? '');
        $this->admin        = $args["admin"] ?? "0";
        $this->confirmado   = $args["confirmado"] ?? "0";
        $this->token        = $args["token"] ?? "";
    }

    /* Mensajes de validacion para la creación de una cuenta */
    public function validarNuevaCuenta():array {
        if(!$this->nombre) self::$alertas["error"][] = "El Nombre es Obligatorio";
        if(!$this->apellido) self::$alertas["error"][] = "El Apellido es Obligatorio";
        if(!$this->email) self::$alertas["error"][] = "El E-mail es Obligatorio";

        if(!$this->password) {
            self::$alertas["error"][] = "La Contraseña es Obligatoria";
        } else  {
            if(strlen($this->password) < 6) self::$alertas["error"][] = "La Contraseña debe Contener al Menos 6 Caracteres";
        }

        return self::$alertas;
    }

    public function validarLogin() {
        if(!$this->email) self::$alertas["error"][] = "El E-mail es Obligatorio";
        if(!$this->password) self::$alertas["error"][] = "La Contraseña es Obligatoria";        

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) self::$alertas["error"][] = "El E-mail es Obligatorio";
        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas["error"][] = "La Contraseña es Obligatoria";
        } else  {
            if(strlen($this->password) < 6) self::$alertas["error"][] = "La Contraseña debe Contener al Menos 6 Caracteres";
        }
        return self::$alertas;
    }

    public function existeUsuario() {
        $query = "SELECT * FROM ".SELF::$tabla." WHERE email = '".$this->email."' LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado->num_rows) self::$alertas["error"][] = "Este Correo ya Está Regisrado";

        return $resultado;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarPasswordYVerificado($postPassword) {
        $resultado = password_verify($postPassword, $this->password);
        if(!$resultado || !$this->confirmado) {
            self::$alertas["error"][] = "Contraseña incorrecta o tu cuenta no ha sido confirmada";
            return false;
        } else {
            return true;
        }
    }
}
?>