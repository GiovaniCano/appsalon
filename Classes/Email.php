<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

class Email {
    public $nombre;
    public $email;
    public $token;

    public function __construct($nombre, $email, $token) {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '7655cc7c118432';
        $phpmailer->Password = 'af7085d52be1c7';

        $phpmailer->setFrom("cuentas@appsalon.com", "AppSalon");
        $phpmailer->addAddress($this->email, $this->nombre);
        $phpmailer->Subject = "Confirma tu cuenta";

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $dominio = "http://localhost";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola ".ucfirst($this->nombre)."</strong>, has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace.</p>";
        $contenido .= "<p>Presiona aquí: <a href='{$dominio}/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta.</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
        $contenido .= "</html>";
        $phpmailer->Body = $contenido;

        $sent = $phpmailer->send();

        // echo $sent ? "sent" : "no sent";
    }

    public function enviarInstrucciones() {
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '7655cc7c118432';
        $phpmailer->Password = 'af7085d52be1c7';

        $phpmailer->setFrom("cuentas@appsalon.com", "AppSalon");
        $phpmailer->addAddress($this->email, $this->nombre);
        $phpmailer->Subject = "Restablece tu Contraseña";

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $dominio = "http://localhost";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola ".ucfirst($this->nombre)."</strong>, has solicitado restablecer tu contraseña. </p>";
        $contenido .= "<p>Presiona aquí: <a href='{$dominio}/recuperar?token=" . $this->token . "'>Restablecer Contraseña.</a></p>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje.</p>";
        $contenido .= "</html>";
        $phpmailer->Body = $contenido;

        $sent = $phpmailer->send();

        // echo $sent ? "sent" : "no sent";
    }
}