<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '14f9693f5efeaa';
        $mail->Password = 'b9383ccf58cd9b';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola: " . $this->nombre . " </strong> Creaste tu cuenta en UpTask, solo falta confirmarla en el siguiente enlace:</p>";
        $contenido .= "<a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirma tu cuenta aquí</a>";
        $contenido .= "<p>Si no creaste este cuenta, ignora este mensaje</p>";
        $contenido .= '<html>';

        $mail->Body = $contenido; 

        //Enviar el email
        $mail->send();

    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '14f9693f5efeaa';
        $mail->Password = 'b9383ccf58cd9b';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu Contraseña';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola: " . $this->nombre . " </strong> Olvidaste tu contraseña en UpTask, cambiala en el siguiente enlace:</p>";
        $contenido .= "<a href='http://localhost:3000/reestablecer?token=" . $this->token . "'>Cambia tu contraseña aquí</a>";
        $contenido .= "<p>Si no fuiste tú, ignora este mensaje</p>";
        $contenido .= '<html>';

        $mail->Body = $contenido; 

        //Enviar el email
        $mail->send();
    }

}