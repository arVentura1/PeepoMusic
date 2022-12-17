<?php

require 'includes/PHPMailer.php';
require 'includes/SMTP.php';
require 'includes/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer();

$mail->isSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->SMTPSecure = "tls";
$mail->Port = "587";
$mail->Username = "andresruizventura@gmail.com";
$mail->Password = "avbpigtpvuedpjhn";
$mail->Subject = 'Te damos la bienvenida a PeepoMusic';
$mail->setFrom('andresruizventura@gmail.com', 'PeepoMusic');
$mail->isHTML(true);
$mail->addAttachment('assets/img/fotos/pmLogo.png');
$mail->Body = '
                <h1>¡Gracias por registrarte en <b>PeepoMusic</b>!</h1>
                <p>
                    Puedes <a class="enlaces" href="http://www.iestrassierra.net/alumnado/curso2122/DAW/daw2122a16/peepomusic/acceder.php">iniciar sesión aquí</a>
                </p>    
                <p>
                    ¡Esperamos con ganas escuchar tus canciones!
                </p>'
              ;
$mail->addAddress($correoUsu, $nickNameUsu);
$mail->send();

$mail->smtpClose();
