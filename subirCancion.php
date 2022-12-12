<?php


require 'includes/PHPMailer.php';
require 'includes/SMTP.php';
require 'includes/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require_once 'conexion.php';
session_start();
include "configLanguage.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['subirCancion'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

//si el usuario ha iniciado sesion
if (isset($_SESSION['userId'])) {

    $database = openConection();

    if (isset($_REQUEST['publicarCancion'])) {

        /*
         *  para publicar una canción debo tener:
         *
         *  > id de la cancion (automatico)
         *  > id del usuario (asignarle la variable de sesion)
         *  > ubicacion es el fichero en sí (fichero required)
         *  > titulo de la cancion (formulario)
         *  > fechaSubida (current_date())
         *  > fotoPortada (otro fichero pero not required)
         */
        $tieneCaracteresInvalidos = false;
        $cancion = "";
        if (isset($_FILES['ficheroAudio'])) {
            $directory = "assets/sounds/";
            $sound = basename($_FILES['ficheroAudio']['name']);
            $cancionRoot = $directory.$sound;

            $tamanoCancion = $_FILES['ficheroAudio']['size'];

            //caracteres ilegales para los archivos
            $caracteresIlegales = ["#","%","&","{","}","<",">","*","?","¿","$","!","'",":","@","+","`","|","="];
            //si el archivo de audio contiene uno de los caracteres ilegales
            if(preg_match('(#|&|%|=|@)', $sound) === 1) {
                $tieneCaracteresInvalidos = true;
                //header("Location: subirCancion.php?errChar=t");
            }

            /*
            echo "<br>tamaño en bytes de la cancion: ".$tamanoCancion;
            //tamaño cancion
            if ($tamanoCancion > 41000000) {
                echo "<br>XD";
                //header("Location:subirCancion.php?id=$_SESSION[userId]&errSize=t");
            }
            */

            if (move_uploaded_file($_FILES['ficheroAudio']['tmp_name'], $cancionRoot)) {
                $cancion = $cancionRoot;
            }
            else {
                //ha habido algun error subiendo el archivo
            }

        }

        $fotoPortada = "";
        //si hemos metido una foto portada
        if (isset($_FILES['ficheroPortada'])) {
            $accept = ["jpeg", "jpg", "png", "webp"];
            $extension = strtolower(pathinfo($_FILES["ficheroPortada"]["name"], PATHINFO_EXTENSION));

            $directory = "assets/img/cancionesFotos/";
            $img = basename($_FILES['ficheroPortada']['name']);
            $imgRoot = $directory.$img;

            if (in_array($extension, $accept)) {
                if (move_uploaded_file($_FILES['ficheroPortada']['tmp_name'], $imgRoot)) {
                    //se ha subido correctamente
                    $fotoPortada = $imgRoot;
                }
                else {
                    //no se ha subido correctamente
                    //echo "no se ha subido correctamente";
                    $fotoPortada = "assets/img/cancionesFotos/defaultSong.png";
                }
            }
            else {
                //si no está en esos formatos mensaje error
                //echo "no esta en esos formatos";
                $fotoPortada = "assets/img/cancionesFotos/defaultSong.png";
            }
        }
        else {
            //si no hemos introducido foto de portada, asignar la placeholder;
            $fotoPortada = "assets/img/cancionesFotos/defaultSong.png";
        }

        $tituloCancion = htmlspecialchars($_REQUEST['titulo']);

        if ($tieneCaracteresInvalidos) {
            header("Location: subirCancion.php?errChar=t");
        }
        else {
            $queryNuevaCancion = "INSERT INTO `canciones` VALUES(NULL,:ubicacion,:titulo,:idUsu,:fotoPortada,CURRENT_DATE,0)";
            $consultaNuevaCancion = $database->prepare($queryNuevaCancion);
            $consultaNuevaCancion->bindParam(":ubicacion", $cancion);
            $consultaNuevaCancion->bindParam(":titulo", $tituloCancion);
            $consultaNuevaCancion->bindParam(":idUsu", $_SESSION['userId']);
            $consultaNuevaCancion->bindParam(":fotoPortada", $fotoPortada);

            if ($consultaNuevaCancion->execute()) {
                //la cancion se ha subido correctamente

                // que nos mande al singleCancion que hemos subido
                $consultaRedirect = $database->prepare("SELECT id FROM canciones WHERE idUsu = :idLogged ORDER BY id DESC");
                $consultaRedirect->bindParam(":idLogged", $_SESSION['userId']);
                $consultaRedirect->execute();
                $idDeLaCancion = $consultaRedirect->fetch(PDO::FETCH_ASSOC);

                $consultaNombreArtista = $database->prepare("SELECT nickName FROM usuarios WHERE id = :idLogeado");
                $consultaNombreArtista->bindParam(":idLogeado", $_SESSION['userId']);
                $consultaNombreArtista->execute();

                $nombreArtista = $consultaNombreArtista->fetch(PDO::FETCH_ASSOC);
                //idCancion para el correo se utilizará idDeLaCancion

                //select * from usuario_followers where idUsuSeguido = idLogeado
                $consultaSeguidores = $database->prepare("SELECT * FROM usuario_followers WHERE idUsuSeguido = :idLoggg");
                $consultaSeguidores->bindParam(":idLoggg", $_SESSION['userId']);
                $consultaSeguidores->execute();

                //si tiene seguidores
                if ($consultaSeguidores->rowCount() > 0) {
                    $listaSeguidores = $consultaSeguidores->fetchAll(PDO::FETCH_ASSOC);

                    //mandar el correo a cada usuario que sigue al que ha subido la cancion
                    foreach ($listaSeguidores as $s) {
                        //echo "<br>id Seguidor del usuario logeado: ".$s['idUsuSeguidor'];

                        //sacar datos de cada seguidor
                        $consultaDatosSeguidor = $database->prepare("SELECT * FROM usuarios WHERE id = :idDelSeguidor");
                        $consultaDatosSeguidor->bindParam(":idDelSeguidor", $s['idUsuSeguidor']);
                        $consultaDatosSeguidor->execute();

                        $datosDelSeguidor = $consultaDatosSeguidor->fetch(PDO::FETCH_ASSOC);
                        //echo "<br><br>id seguidor: ".$datosDelSeguidor['id'];
                        //echo "<br>nombre seguidor: ".$datosDelSeguidor['nickName'];

                        //a cada seguidor mandarle el correo
                        //$nombreSeguidor = $datosDelSeguidor['nickName'];
                        //$correoSeguidor = $datosDelSeguidor['mail'];

                        $mail = new PHPMailer();
                        $mail->isSMTP();
                        $mail->Host = "smtp.gmail.com";
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = "tls";
                        $mail->Port = "587";
                        $mail->Username = "andresruizventura@gmail.com";
                        $mail->Password = "avbpigtpvuedpjhn";
                        $mail->Subject = 'El artista '.$nombreArtista['nickName'].' ha subido una cancion nueva';
                        $mail->setFrom('andresruizventura@gmail.com', 'PeepoMusic');
                        $mail->isHTML(true);
                        $mail->Body = '
                                    <h1>El artista <b>'.$nombreArtista['nickName'].'</b> que sigues ha subido una nueva cancion llamada <i>"'.$tituloCancion.'"</i></h1>
                                    <a href="https://www.iestrassierra.net/alumnado/curso2122/DAW/daw2122a16/peepomusic/singleCancion.php?id='.$idDeLaCancion['id'].'">
                                        <img src="https://www.iestrassierra.net/alumnado/curso2122/DAW/daw2122a16/peepomusic/'.$fotoPortada.'" alt="foto-portada" style="width: 150px; height: 150px;">                                    
                                    </a>
                                    <p>
                                        ¡Entra
                                        <a class="enlaces" href="https://www.iestrassierra.net/alumnado/curso2122/DAW/daw2122a16/peepomusic/singleCancion.php?id='.$idDeLaCancion['id'].'">aquí</a>
                                        para escucharla!
                                    </p>
                                    ';
                        $mail->addAddress($datosDelSeguidor['mail'], $datosDelSeguidor['nickName']);
                        $mail->send();
                        $mail->smtpClose();
                    }
                }
                //redirección a la canción en cuestión
                header("Location:singleCancion.php?id=$idDeLaCancion[id]");
            }
        }

    }
    //mostrar formulario de subida de canción
    else {
?>
        <section class="h-100 w-100 gradient-custom-2">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col col-lg-9 col-xl-7">



<?php
if (isset($_REQUEST['uploaded'])) {
    ?>
    <div class='form-outline form-dark text-center p-0 mb-0'>
        <span class='alert alert-success'>
            <i class='fas fa-cloud-upload-alt'></i>
            <?= $lang['subidaCorrectamente'] ?>
            <i class='far fa-check-circle'></i>
        </span>
    </div>
    <?php
}
if (isset($_REQUEST['errChar'])) {
    ?>
    <div class='form-outline form-dark text-center p-0 mb-5'>
        <span class='alert alert-danger'>
            <i class='far fa-times-circle'></i>
            <?= $lang['errChar'] ?>
        </span>
    </div>
    <?php
}

echo "<form action='$_SERVER[PHP_SELF]' method='post' enctype='multipart/form-data' class='card sombra-cards form-editar-perfil p-3'>"; ?>
                            <h2 class='page-section-heading pb-3 text-uppercase text-dark' style="padding-left: 46px; padding-top:20px;"><?= $lang['subirCancion'] ?></h2>

                            <div class='m-5 mt-0 mb-0'>
                                <label class='form-label' for='tituloCancion'><?= $lang['tituloDeLaCancion'] ?>*</label>
                                <input maxlength='60' type='text' name='titulo' class='form-control' id='titulo' placeholder='<?= $lang['tituloDeLaCancion'] ?>' required>
                            </div>
                            <div class='m-5 mb-0'>
                                <label class='form-label' for='Archivo de audio'><?= $lang['archivoDeAudio'] ?>*</label>
                                <button class='file-upload-btn' type='button' onclick='$(".file-upload-input").trigger("click")'><?= $lang['anadirArchivo'] ?></button>
                                <div class='image-upload-wrap'>
                                    <input class='file-upload-input'  type='file' onchange='readURL(this);' class='form-control' name='ficheroAudio' id='ficheroAudio' accept='audio/*' required/>
                                    <div class='drag-text' style="padding-left: 25px; padding-right: 25px;">
                                        <h3><i class="fas fa-cloud-upload-alt"></i>&nbsp;<?= $lang['arrastraAudio'] ?></h3>
                                    </div>
                                </div>
                                <div class='file-upload-content fileUploadContent p-3 pb-0'>
                                    <h3 class="image-title"></h3>
                                </div>
                            </div>

                            <div class='m-5 mb-0'>
                                <label class='form-label' for='Portada'><?= $lang['arrastraPortada'] ?></label>
                                <button class='file-upload-btn2' type='button' onclick='$(".file-upload-input2").trigger("click")'><?= $lang['anadirPortada'] ?></button>
                                <div class='image-upload-wrap2'>
                                    <input class='file-upload-input2' type='file' onchange='readURL2(this);' class='form-control' name='ficheroPortada' id='ficheroPortada' accept='image/jpeg,image/jpg,image/png,image/webp' />
                                    <div class='drag-text2'>
                                        <h3><i class="fas fa-file-image"></i>&nbsp;<?= $lang['arrastraPortada'] ?></h3>
                                    </div>
                                </div>

                                <div class='file-upload-content2 fileUploadContent2 p-3 pb-0'>
                                    <h3><?= $lang['previewPortada'] ?></h3>
                                    <img class='file-upload-image2' src='#' alt='your image' />
                                </div>
                            </div>

                            <div class="form-check d-flex justify-content-center gap-3 mt-4 mb-2" style="font-weight: 200; font-style: italic;">
                                <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                                <label class="form-check-label" for="invalidCheck">
                                    <?= $lang['checkboxConfirmo'] ?>
                                </label>
                                <div class="invalid-feedback">
                                    <?= $lang['debesAceptarLosTerminos'] ?>
                                </div>
                            </div>

                            <div class='d-flex justify-content-center align-items-center p-3'>
                                <!--<button type='submit' name='publicarCancion' class='botonsito2 btn btn-outline-secondary' style='width: 350px;'>
                                    <i class='far fa-check-circle'></i>
                                    Publicar
                                </button>-->
                                <button type='submit' name='publicarCancion' class='editar-okey carta2' style='width: 350px; outline: none; border: none;'>
                                    <i class='far fa-check-circle'></i>
                                    <?= $lang['publicar'] ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php
    }

}
//si el usuario NO ha iniciado sesiónº
else {
    //no se puede acceder al contenido
    header("Location:403.php");
}


include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/canciones.js"></script>
<script src="./assets/scripts/perfil.js"></script>

</html>