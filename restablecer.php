<?php
require_once 'conexion.php';
session_start();
include "configLanguage.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['restablecerContrasena'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

if (isset($_REQUEST['restablecerBoton'])) {
    /*
        para seccion de "he olvidado mi contraseña"

        >. al darle a enviar, que genere una contraseña aleatoria (hash php, investigar), updatear la contraseña del usuario cuyo correo sea el introducido por la contraseña generada aleatoriamente
        >. mandar el correo con la contraseña aleatoria generada (en el correo dar instrucciones de: ir a mi perfil, editar perfil y cambiar la contraseña)
     */
    $string = "abcdefghijklmopqrstuvwxyz1234567";
    $randomPass = str_shuffle($string);
    $randomPass_crypt = md5($randomPass);

    $correoFormulario = htmlspecialchars($_REQUEST['email']);

    $database = openConection();
    $consulta = $database->prepare("SELECT * FROM usuarios WHERE mail = :mailForm");
    $consulta->bindParam(":mailForm", $correoFormulario);
    $consulta->execute();

    //el correo sí está registrado
    if ($consulta->rowCount() > 0) {

        $datos = $consulta->fetch(PDO::FETCH_ASSOC);

        //la contraseña actual del usuario es $datos['clave'];
        //ahora tendria que updatear la contraseña del mail formulario
        $queryUpdate = "UPDATE usuarios SET clave = :randomPass_crypt WHERE mail = :mailFormulario";
        $consultaUpdate = $database->prepare($queryUpdate);
        $consultaUpdate->bindParam(":randomPass_crypt", $randomPass_crypt);
        $consultaUpdate->bindParam(":mailFormulario", $correoFormulario);

        if ($consultaUpdate->execute()) {

            $correoUsu = $datos['mail'];
            $nombreUsu = $datos['nickName'];
            $fotoUsu = $datos['fotoPfp'];
            //echo "correoUsu: ".$correoUsu;
            //echo "<br>nombreUsu: ".$nombreUsu;
            include 'enviarCorreoRestablecer.php';

            header("Location:restablecer.php?mailSent=t");
        }

    }
    //el correo no está registrado
    else {
        header("Location:restablecer.php?invalidMail=t");
    }

}
else {
?>
    <section class="page-section bg-primary text-white mb-0">
        <div class="container">
            <h1 class="text-center text-uppercase text-white"><?= $lang['restableceTuContrasena'] ?></h1>
        </div>
    </section>
    <?php

    if (isset($_REQUEST['invalidMail'])) {
        ?>
            <div class='form-outline form-dark text-center mt-4 mb-0 p-3'>
                <span class='alert alert-danger'>
                    <i class='fas fa-exclamation-triangle'></i>
                    <?= $lang['invalidMail'] ?>
                </span>
            </div>
        <?php
    }
    if (isset($_REQUEST['mailSent'])) {
        ?>
            <div class='form-outline form-dark text-center mt-4 mb-0 p-3'>
                <span class='alert alert-info'>
                    <i class='fas fa-exclamation-triangle'></i>
                    <?= $lang['mailSent'] ?>
                </span>
            </div>
        <?php
    }

    ?>
<div class="contenedor-acceder">
    <div class="inner-contenedor-acceder">
        <div class="login-page">
            <div class="form">
<?php
    echo "<form class='pb-0' action='$_SERVER[PHP_SELF]' method='post' enctype='multipart/form-data'>
            <h3 class='text-center text-uppercase text-dark mb-4'></h3>
            <input type='email' name='email' placeholder='Email' required>";
            ?>
            <button type='submit' name='restablecerBoton'><?= $lang['restablecer'] ?></button>
            <?php echo "
            <p class='message text-secondary pt-3 pb-0'>";
                ?>
                <i><?= $lang['captionMail'] ?></i>
                <?php echo "
            </p>
            <p class='message'>
                <a href='acceder.php'>";
                ?>
                <?= $lang['headerAcceder'] ?>
                <?php echo "        
                </a>
            </p>
          </form>";
?>
            </div>
        </div>
    </div>
</div>
<?php
}
include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>

</html>