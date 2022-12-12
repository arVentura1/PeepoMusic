<?php
require_once 'conexion.php';
session_start();
include 'configLanguage.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['editar'] ?> <?= $lang['headerPerfil'] ?></title>
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

    $idUsuario = $_SESSION['userId'];
    //sacar los datos del usuario
    $database = openConection();
    try {
        $queryPerfil = "SELECT * FROM usuarios WHERE id = :idusu";
        $consultaPerfil = $database->prepare($queryPerfil);
        $consultaPerfil->bindParam(":idusu", $idUsuario);
        $consultaPerfil->execute();

        if ($consultaPerfil->rowCount() > 0) {
            $datosUsuario = $consultaPerfil->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }

    //guardar cambios boton del formulario de guardar cambios
    if (isset($_REQUEST['guardarCambios'])) {

        //variables donde guardaremos los datos del formulario
        $fotoUsu = "";
        //si hemos metido una foto
        if (isset($_FILES['fichero'])) {
            $accept = ["jpeg", "jpg", "png", "webp"];
            $extension = strtolower(pathinfo($_FILES["fichero"]["name"], PATHINFO_EXTENSION));

            $directory = "assets/img/usersFotos/";
            $img = basename($_FILES['fichero']['name']);
            $imgRoot = $directory.$img;

            if (in_array($extension, $accept)) {
                //subir la foto
                if (move_uploaded_file($_FILES['fichero']['tmp_name'], $imgRoot)) {
                    //se ha subido correctamente
                    $fotoUsu = $imgRoot;
                }
                //si no se ha subido correctamente, asignar la placeholder de nuevo
                else {
                    $consultaFoto = $database->prepare("SELECT * FROM usuarios WHERE id = :userId");
                    $consultaFoto->bindParam(":userId",$_SESSION['userId']);
                    $consultaFoto->execute();

                    $usuario = $consultaFoto->fetch(PDO::FETCH_ASSOC);
                    $fotoUsu = $usuario['fotoPfp'];
                }
            }
            //si no está en esos formatos
            else {
                $consultaFoto = $database->prepare("SELECT * FROM usuarios WHERE id = :userId");
                $consultaFoto->bindParam(":userId",$_SESSION['userId']);
                $consultaFoto->execute();

                $usuario = $consultaFoto->fetch(PDO::FETCH_ASSOC);
                $fotoUsu = $usuario['fotoPfp'];
            }
        }

        $nickName = htmlspecialchars($_REQUEST['nickName']);
        $claveActual = htmlspecialchars($_REQUEST['claveActual']);
        echo "clave actual en md5: ".$claveActual;
        $nuevaClave = "";
        $repetirNuevaClave = "";
        $nuevaClave_crypt = "";
        $clave_usar = "";
        $telefono = htmlspecialchars($_REQUEST['phoneNum']);
        $mail = htmlspecialchars($_REQUEST['mail']);

        $biografia = "";
        if (isset($_REQUEST['biografia'])) {
            $biografia = htmlspecialchars($_REQUEST['biografia']);
        }

        //si se han rellenado los dos campos para la nueva contraseña
        if (($_REQUEST['nuevaClave'] !== "") && ($_REQUEST['repetirNuevaClave'] !== "")) {

            $nuevaClave = htmlspecialchars($_REQUEST['nuevaClave']);
            $repetirNuevaClave = htmlspecialchars($_REQUEST['repetirNuevaClave']);

            $nuevaPass = false;
            //nuevas contraseñas coinciden, asignar
            if ($nuevaClave === $repetirNuevaClave) {
                $nuevaClave_crypt = md5($nuevaClave);
                $clave_usar = $nuevaClave_crypt;
                //echo "<br>clave_usar SI COINCIDEN las nuevas contraseñas: ".$nuevaClave_crypt;
                $nuevaPass = true;
            }
            //las contraseñas nuevas no coinciden, usar la actual pues
            else {
                $clave_usar = $claveActual;
                //echo "<br>clave_usar SI NO COINCIDEN las nuevas contraseñas: ".$clave_usar;
            }
        }
        //no se han introducido contraseñas nuevas
        else {
            $clave_usar = $claveActual;
            //echo "<br>NO ESCRIBISTE NUEVAS CONTRASEÑAS: ".$clave_usar;
        }

        echo "<br>clave_usar: ".$clave_usar;
        //para editar el usuario debemos coger todos los datos del usuario
        //y aqui hay que hacer el update de la base de datos ( y lo de subir el archivo )
        $queryUpdate = "UPDATE usuarios SET nickName=:nickName,clave=:clave_usar,mail=:mail,phoneNum=:telefono,biografia=:biografia,fotoPfp=:fotoUsu WHERE id=:idUsuario";
        $consultaUpdate = $database->prepare($queryUpdate);
        $consultaUpdate->bindParam(":nickName", $nickName);
        $consultaUpdate->bindParam(":clave_usar", $clave_usar);
        $consultaUpdate->bindParam(":mail", $mail);
        $consultaUpdate->bindParam(":telefono", $telefono);
        $consultaUpdate->bindParam(":biografia", $biografia);
        $consultaUpdate->bindParam(":fotoUsu", $fotoUsu);
        $consultaUpdate->bindParam(":idUsuario", $_SESSION['userId']);

        if ($consultaUpdate->execute()){
            $usuId = $_SESSION['userId'];
            header("Location:perfil.php?id=$usuId&updated=t");

            if ($nuevaPass) {
                $nombreUsu = $nickName;
                //$nuevaContra = $nuevaClave;
                $correoUsu = $mail;
                include 'enviarCorreoNuevaClave.php';
            }

        }

    }
    else if (isset($_REQUEST['accionBorrarCuenta'])) {

        $queryIsAdmin = "SELECT * FROM usuarios WHERE id = :idUsuario";
        $consultaIsAdmin = $database->prepare($queryIsAdmin);
        $consultaIsAdmin->bindParam(":idUsuario",$_SESSION['userId']);
        $consultaIsAdmin->execute();
        $usu = $consultaIsAdmin->fetch(PDO::FETCH_ASSOC);

        //es admin
        if ($usu['rol'] == 1) {
            header("Location:perfil.php?id=$usu[id]&errf=t");
        }
        //no es admin
        else {
            //confirmacion
            $deleteUsuario = $database->prepare("DELETE FROM usuarios WHERE id = :idusu");
            $deleteUsuario->bindParam(":idusu",$_SESSION['userId']);
            $deleteUsuario->execute();

            //session_destroy();
            unset($_SESSION["userId"]);
            header("Location:index.php");
        }
    }
    //borrar usuario
    else if (isset($_REQUEST['borrarCuenta'])){
        ?>
        <section class="page-section bg-primary text-white mb-0" style="height:75vh;" id="about">
    <div class="container">
        <h4 class="page-section-heading text-center text-uppercase text-white"><?= $lang['preguntaBorrarPerfil'] ?></h4>
        <?php
        //////////////////////////////////////////////////////////////////////////////////////////////////////////
            // voy por aqui
        //////////////////////////////////////////////////////////////////////////////////////////////////////////
        echo "
            <form action='$_SERVER[PHP_SELF]' method='post' class='p-4 d-flex flex-row justify-content-center gap-2' style='background:inherit;'>";
?>
                <button type='submit' name='accionBorrarCuenta' class='p-3 align-items-center borrarCuenta btn btn-danger fw-bolder fs-4 text-wrap' style='width: 350px; height: 100px;'>
                    <i class='fas fa-exclamation-triangle'></i>
                    <?= $lang['siQuieroBorrarCuenta'] ?>
                </button>

                <a href='perfil.php?id=<?= $datosUsuario['id'] ?>' type='button' class='p-3 d-flex justify-content-center align-items-center borrarCuenta btn btn-info fw-bolder fs-4 text-wrap' style='width: 350px;  height: 100px;'>
                    <i class="fas fa-backward"></i>
                    <?= $lang['noQuieroBorrarCuenta'] ?>
                </a>
<?php
      echo '</form>
        <div class="error-img">
            <img src="./assets/img/fotos/pepeSad.png" alt="peepo-error" width="360" height="300">
        </div>
    </div>
</section>
        ';
    }
    //si no le hemos dado a nada, sacar formulario con los datos del usuario rellenos
    else {
?>
<section class="h-100 gradient-custom-2">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7">
<?php
            if (isset($_REQUEST['errFoto'])) {
                ?>
                <div class='form-outline form-dark text-center p-0 mb-5'>
                    <span class='alert alert-warning'>
                        <i class='far fa-file-image'></i>
                        <?= $lang['formatoImagenIncorrecto'] ?>
                    </span>
                </div>
                <?php
            }
            echo "<form action='$_SERVER[PHP_SELF]' method='post' enctype='multipart/form-data' class='card form-editar-perfil'>";
                    ?>
                    <h2 class='page-section-heading m-3 text-uppercase text-dark'><?= $lang['editarMiPerfil'] ?></h2>
                    <div class='m-3 d-flex flex-column justify-content-center'>
                        <label for='formFile' class='form-label'><?= $lang['fotoDePerfil'] ?></label>

                        <img src="<?= $datosUsuario['fotoPfp'] ?>" alt="foto-usuario" class="img-fluid img-thumbnail mb-2 d-flex flex-column justify-content-center" style="width: 150px; z-index: 1">
<!--<input type='file' accept='image/jpeg,image/jpg,image/png,image/webp' name='fichero' class='form-control' id='fichero'>-->
                    <button class='file-upload-btn' type='button' onclick='$(".file-upload-input").trigger("click")'><?= $lang['anadirImagen'] ?></button>
                    <div class='image-upload-wrap'>
                        <input class='file-upload-input' type='file' onchange='readURL(this);' class='form-control' name='fichero' id='fichero' accept='image/jpeg,image/jpg,image/png,image/webp' />
                        <div class='drag-text'>
                            <!--<i class="fas fa-cloud-upload-alt"></i>-->
                            <h3><?= $lang['arrastraUnaImagenAqui'] ?></h3>
                        </div>
                    </div>

                    <div class='file-upload-content fileUploadContent p-3 pb-0'>
                        <h3>Preview:</h3>
                        <img class='file-upload-image' src='#' alt='your image' />
                    </div>
                    <!-- -->

                </div>
                    <div class='m-3'>
                        <label class='form-label' for='username'><?= $lang['nombreDeUsuario'] ?></label>
                        <input maxlength='30' type='text' name='nickName' class='form-control' id='nickname' placeholder='<?= $lang['nombreDeUsuario'] ?>' value='<?= $datosUsuario["nickName"] ?>' required>
                    </div>
                    <!--<div class='m-3 mb-0'>
                        <label class='form-label' for='claveActual'>Contraseña actual</label>
                        <input readonly type='password' maxlength='150' id='claveActual' name='claveActual' class='form-control bg-gray-dark' id='clave' placeholder='".$datosUsuario["clave"]."' value='".$datosUsuario["clave"]."' required>
                        <div class='ojo ojoEditarPerfil ojoNormal'></div>
                    </div>-->
                    <div class='m-3 mb-0'>
                        <input type='hidden' name='claveActual' id='claveActual' value='<?= $datosUsuario["clave"] ?>'>
                        <label class='form-label' for='nuevaClaveCampo'><?= $lang['nuevaContrasena'] ?></label>
                        <input maxlength='150' type='password' id='contraEditarPerfil' name='nuevaClave' class='form-control' id='nuevaClave' placeholder='<?= $lang['nuevaContrasena'] ?>'>
                        <div class='ojo ojoEditarPerfil ojoNormal'></div>
                    </div>
                    <div class='m-3'>
                        <label class='form-label' for='repitaNuevaClaveCampo'><?= $lang['repitaNuevaContrasena'] ?></label>
                        <input maxlength='150' type='password' id='contraEditarPerfil' name='repetirNuevaClave' class='form-control' id='repitaNuevaClave' placeholder='<?= $lang['repitaNuevaContrasena'] ?>'>
                    </div>
                    <div class='m-3'>
                        <label class='form-label' for='email'>Email</label>
                        <input maxlength='60' type='email' name='mail' class='form-control' id='mail' placeholder='<?= $lang['correoPlaceholder'] ?>' value='<?= $datosUsuario["mail"] ?>' required>
                    </div>
                    <div class='m-3'>
                        <label class='form-label' for='telefono'><?= $lang['accederPlaceholderTelefono'] ?></label>
                        <input maxlength='30' type='tel' pattern='[0-9]{9}' name='phoneNum' class='form-control' id='telefono' placeholder='957000000' value='<?= $datosUsuario["phoneNum"] ?>' required>
                        <small><i><?= $lang['formato9numeros'] ?></i></small>
                    </div>
                    <div class='m-3'>
                        <label class='form-label' for='biografia'><?= $lang['biografia'] ?></label>
                        <textarea maxlength='350' style='resize:none;' name='biografia' rows='5' cols='5' class='form-control' id='biografia' placeholder='<?= $lang['escribeSobreTi'] ?>'><?= $datosUsuario["biografia"] ?></textarea>
                        <div class='d-flex flex-row gap-1 justify-content-end'>
                            <i>
                                <span id='rchars'>350</span>
                                <span><?= $lang['restantes'] ?></span>
                            </i>
                        </div>
                    </div>
                    
                    <div class='d-flex justify-content-between align-items-center m-3'>
                        <button type='submit' name='guardarCambios' class='editar-okey carta3' style='width: 350px; outline: none; border: none;'>
                            <i class='far fa-check-circle'></i>
                            <?= $lang['guardarCambios'] ?>
                        </button>
                        <!--<a href='perfil.php?id=<?= $datosUsuario['id'] ?>' type='button' class='btn btn-outline-warning' style='width: 350px;'>
                            <i class="fas fa-backward"></i>
                            Volver atrás
                        </a>-->
                        <a onclick="history.back()" class="editar-back carta3" style="cursor: pointer; text-decoration: none; display: flex; justify-content: center; align-items: center">
                            <i class="fas fa-backward"></i>&nbsp;
                            <?= $lang['volverAtras'] ?>
                        </a>
                </div>
                    <div class='d-flex justify-content-center align-items-center m-3'>
                        <button type='submit' name='borrarCuenta' class='editar-borrar carta3' style='width: 250px; outline: none; border: none;'>
                            <i class='fas fa-user-slash'></i>
                            <?= $lang['borrarCuenta'] ?>
                        </button>
                    </div>     
                </form>
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
<script src="./assets/scripts/perfil.js"></script>


</html>