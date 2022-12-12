<?php
/*##########Script Information#########
  # Purpose: Send mail Using PHPMailer#
  #          & Gmail SMTP Server 	  #
  # Created: 24-11-2019 			  #
  #	Author : Hafiz Haider			  #
  # Version: 1.0					  #
  # Website: www.BroExperts.com 	  #
  #####################################*/

//Include required PHPMailer files
require 'includes/PHPMailer.php';
require 'includes/SMTP.php';
require 'includes/Exception.php';
//Define name spaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Create instance of PHPMailer
$mail = new PHPMailer();
//Set mailer to use smtp
$mail->isSMTP();
//Define smtp host
$mail->Host = "smtp.gmail.com";
//Enable smtp authentication
$mail->SMTPAuth = true;
//Set smtp encryption type (ssl/tls)
$mail->SMTPSecure = "tls";
//Port to connect smtp
$mail->Port = "587";
//Set gmail username
$mail->Username = "andresruizventura@gmail.com";
//Set gmail password
$mail->Password = "avbpigtpvuedpjhn";
//Email subject
$mail->Subject = 'Restablecer clave de acceso - PeepoMusic';
//Set sender email
$mail->setFrom('andresruizventura@gmail.com', 'PeepoMusic');
//Enable HTML
$mail->isHTML(true);
//Attachment
$mail->addAttachment('assets/img/fotos/pmLogo.png');
//Email body
$mail->Body = '
                <p>
                    Has solicitado restablecer tu clave de acceso de PeepoMusic
                </p>   
                <h1>Tu nueva contraseña es: '.$randomPass.'</h1> 
                <p>
                    No te olvides de cambiar tu contraseña (Mi Perfil > Editar Perfil > Nueva clave)
                </p>
                <p>
                    <a class="enlaces" href="http://www.iestrassierra.net/alumnado/curso2122/DAW/daw2122a16/peepomusic/acceder.php">Inicia sesión aquí</a>
                </p>'
;
//Add recipient
$mail->addAddress($correoUsu, $nombreUsu);
//Finally send email
//if (
    $mail->send();
//) {
//    echo "Email Sent..!";
//}
//else{
//    echo "Message could not be sent. Mailer Error: "{$mail->ErrorInfo};
//}
//Closing smtp connection
$mail->smtpClose();
