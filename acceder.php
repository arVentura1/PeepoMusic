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
    <title><?= $lang['accederTitulo'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

//si le hemos dado a iniciar sesion
if (isset($_REQUEST['loginUser'])) {
    $usuario = htmlspecialchars($_REQUEST['usuario']);
    $contra = htmlspecialchars($_REQUEST['contra']);
    $contra_crypt = md5($contra);

    //abrimos la conexion
    $database = openConection();
    try {
        //query para inicio de sesion
        $queryLogin = "SELECT * FROM usuarios WHERE nickName = :nombre AND clave = :contrasena";
        $consultaLogin = $database->prepare($queryLogin);
        $consultaLogin->bindParam(':nombre', $usuario);
        $consultaLogin->bindParam(':contrasena', $contra_crypt);
        $consultaLogin->execute();

        if ($consultaLogin->rowCount() > 0) {
            $datosUsuario = $consultaLogin->fetch(PDO::FETCH_ASSOC);
            $_SESSION['userId'] = $datosUsuario['id'];
            header("Location:index.php");
            //header("Location:index.php?logSucc=t");
            //header("Location:acceder.php?logSucc=t");
        }
        else {
            unset($_REQUEST['usuario']);
            unset($_REQUEST['contra']);
            header("Location:acceder.php?logErr=t");
        }
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}
//si le hemos dado a registrarnos
else if (isset($_REQUEST['registerUser'])) {

    $nickname = htmlspecialchars($_REQUEST['nickname']);
    $email = htmlspecialchars($_REQUEST['email']);
    $telefono = htmlspecialchars($_REQUEST['telefono']);
    $password = htmlspecialchars($_REQUEST['password']);
    $password_crypt = md5($password);
    $passwordRepe = htmlspecialchars($_REQUEST['password-repeat']);

    $database = openConection();

    try {
        $registerError = false;
        //
        if (strcmp($password, $passwordRepe) !== 0) {
            //no son iguales, error
            $registerError = true;
        }
        else {
            $password_crypt = md5($password);
        }
        //
        $usuarioExiste = false;
        $queryCheckNickname = "SELECT * FROM usuarios WHERE nickName LIKE :nickNameInput";
        $consultaCheckNickname = $database->prepare($queryCheckNickname);
        $consultaCheckNickname->bindParam(":nickNameInput", $nickname);
        $consultaCheckNickname->execute();

        if($consultaCheckNickname->rowCount() > 0){
            $datos = $consultaCheckNickname->fetchAll(PDO::FETCH_ASSOC);
            foreach ($datos as $dato){
                if($dato['nickName'] == $nickname){
                    $registerError = true;
                    $usuarioExiste = true;
                }
            }
        }

        // registro hecho correctamente
        if ($registerError == false) {
            //hacer el insert
            $queryInsert = "INSERT INTO `usuarios` VALUES (NULL,:nickName,:password,2,:mail,:telefono,'','assets/img/fotos/defaultPfp.png',CURRENT_DATE,0,0)";
            $consultaCrearUsuario = $database->prepare($queryInsert);
            $consultaCrearUsuario->bindParam(":nickName", $nickname);
            $consultaCrearUsuario->bindParam(":password", $password_crypt);
            $consultaCrearUsuario->bindParam(":mail", $email);
            $consultaCrearUsuario->bindParam(":telefono", $telefono);

            if ($consultaCrearUsuario->execute()){
                //creacion del usuario con exito
                header("Location:acceder.php?regSucc=t");

                //enviar correo de registro
                $correoUsu = $email;
                $nickNameUsu = $nickname;
                //echo "correoUsu: ".$email;
                //echo "<br>nickNameUsu: ".$nickNameUsu;

                include 'enviarCorreoRegister.php';
            }

        }
        // ha habido un error al registrarse
        else {
            header("Location:acceder.php?regErr=t");
        }

    }
    catch (PDOException $exception) {
        echo $exception->getMessage();
    }

}
//si no, formulario de registro
else {

    if (isset($_REQUEST['regErr'])) {
        ?>
        <div class='form-outline form-dark text-center mt-4 p-3'>
            <span class='alert alert-danger'><?= $lang['accederRegErr'] ?></span>
        </div>
        <?php
    }
    if (isset($_REQUEST['regSucc'])) {
        ?>
        <div class='form-outline form-dark text-center mt-4 p-3'>
            <span class='alert alert-success'><?= $lang['accederRegSucc'] ?></span>
        </div>
        <?php
    }
    if (isset($_REQUEST['logSucc'])) {
        ?>
        <div class='form-outline form-dark text-center mt-4 p-3'>
            <span class='alert alert-success'><?= $lang['accederLogSucc'] ?></span>
        </div>
        <?php
    }
    if (isset($_REQUEST['logErr'])) {
        ?>
        <div class='form-outline form-dark text-center mt-4 p-3'>
            <span class='alert alert-danger'><?= $lang['accederLogErr'] ?></span>
        </div>
        <?php
    }
echo '
<div class="contenedor-acceder">
    <div class="inner-contenedor-acceder">
        <div class="login-page">
            <div class="form">';
         echo "<form class='register-form' action='$_SERVER[PHP_SELF]' method='post' enctype='multipart/form-data'>";
?>
                    <h3 class='text-center text-uppercase text-dark mb-4'><?= $lang['accederRegistrate'] ?></h3>
                    <input type='text' name='nickname' placeholder='<?= $lang['accederPlaceholderNickname'] ?>' value='<?= isset($_REQUEST["nickname"]) ? $_REQUEST["nickname"] : "" ?>'  required/>
                    <input type='email' name='email' placeholder='Email' value='<?= isset($_REQUEST["email"]) ? $_REQUEST["email"] : "" ?>'  required/>
                    <input type='tel' pattern='[0-9]{9}' name='telefono' placeholder='<?= $lang['accederPlaceholderTelefono'] ?>' value='<?= isset($_REQUEST["telefono"]) ? $_REQUEST["telefono"] : "" ?>'  required/>
                    <input type="password" maxlength='60' minlength='8' id="contraRegister" name="password" placeholder="<?= $lang['accederPlaceholderContrasena'] ?>" required/>
                    <input type="password" minlength="8" name="password-repeat" placeholder="<?= $lang['accederPlaceholderRepeatContra'] ?>" required/>
                    <button type="submit" name="registerUser"><?= $lang['accederRegistrate'] ?></button>
                    <div class="ojo ojoRegister ojoNormal"></div>
                    <p class="message"><?= $lang['alreadyRegistered'] ?>&nbsp;<a href="#"><?= $lang['iniciaSesion'] ?></a></p>
                </form>
<?php
          echo "<form class='login-form' action='$_SERVER[PHP_SELF]' method='post' enctype='multipart/form-data'>";
?>
                    <h3 class='text-center text-uppercase text-dark mb-4'><?= $lang['iniciaSesion'] ?></h3>
                    <input type="text" name="usuario" placeholder="<?= $lang['accederPlaceholderNickname'] ?>" required/>
                    <input type="password" id="contraLogin" name="contra" placeholder="<?= $lang['accederPlaceholderContrasena'] ?>" required/>
                    <button type="submit" name="loginUser"><?= $lang['iniciaSesion'] ?></button>
                    <div class="ojo ojoLogin ojoNormal"></div>               
                    <p class="message"><?= $lang['olvidasteContra'] ?>&nbsp;<a href="restablecer.php"><?= $lang['restablecer'] ?></a></p>
                    <p class="message"><?= $lang['noEstasRegistrado'] ?>&nbsp;<a href="#"><?= $lang['creaUnaCuenta'] ?></a></p>
                </form>
            </div>
        </div>
    </div>
</div>

    <section class="page-section bg-primary text-white mb-0">
        <div class="container">
            <h1 class="text-center text-uppercase text-white"><?= $lang['entraYdisfruta'] ?></h1>
        </div>
    </section>
<?php
}

include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/perfil.js"></script>


</html>