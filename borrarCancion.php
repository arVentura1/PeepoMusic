<?php
require_once 'conexion.php';
session_start();
include "configLanguage.php";

$datosUsuario = "";
//si el usuario ha iniciado sesion
if (isset($_SESSION['userId'])) {

    $idUsu = $_SESSION['userId'];

    $database = openConection();
    try {
        $queryUsu = "SELECT * FROM usuarios WHERE id = :idusu";
        $consultaUsu = $database->prepare($queryUsu);
        $consultaUsu->bindParam(":idusu", $idUsu);
        $consultaUsu->execute();

        $datosUsuario = $consultaUsu->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}
else {
    header("Location:403.php");
}

$datosCancion = "";
//esto es el id de la cancion
if (isset($_REQUEST['id'])) {
    $idCancion = $_REQUEST['id'];
    $database = openConection();
    try {
        $queryCancion = "SELECT * FROM canciones WHERE id = :idUrlCancion";
        $consultaCancion = $database->prepare($queryCancion);
        $consultaCancion->bindParam(":idUrlCancion", $idCancion);
        $consultaCancion->execute();

        if ($consultaCancion->rowCount() > 0) {
            $datosCancion = $consultaCancion->fetch(PDO::FETCH_ASSOC);
        }
        else {
            header("Location:404.php");
        }

    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}
else if (isset($_REQUEST['accionBorrarCancion'])) {
    $idCancion = $_REQUEST['accionBorrarCancion'];
}
else {

    //consulta datos cancion con $idCancion_url

    $consultaDatosCancionConIdUrl = $database->prepare("SELECT * FROM canciones WHERE id = :idUrlCancion");
    $consultaDatosCancionConIdUrl->bindParam(":idUrlCancion", $idCancion_url);
    $consultaDatosCancionConIdUrl->execute();

    if ($consultaDatosCancionConIdUrl->rowCount() > 0) {
        $datosCancion = $consultaDatosCancionConIdUrl->fetch(PDO::FETCH_ASSOC);
    }
    else {
        header("Location:404.php");
    }

    //echo "error 404";
//    header("Location:404.php");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar "<?= $datosCancion['titulo'] ?>" de <?= $datosUsuario['nickName'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

if (isset($_REQUEST['accionBorrarCancion'])) {
    //accion de borrar
    $idCancionFormulario = htmlspecialchars($_REQUEST['accionBorrarCancion']);

    $deleteCancion = $database->prepare("DELETE FROM canciones WHERE id = :idCancion");
    $deleteCancion->bindParam(":idCancion",$idCancionFormulario);
    $deleteCancion->execute();

    header("Location:listaCanciones.php?id=$_SESSION[userId]&deletedSong=t");
}
else {
    //pregunta para borrar
?>
        <section class="page-section bg-primary text-white mb-0" style="height:75vh;" id="about">
    <div class="container">
        <h1 class="text-center text-uppercase text-white"><?= $lang['adminAskDelete'] ?>&nbsp;<i>"<?= $datosCancion['titulo'] ?>"</i>&nbsp;?</h1>
<?php

     echo "<form action='$_SERVER[PHP_SELF]' method='post' class='p-4 d-flex flex-row justify-content-center gap-3' style='background:inherit;'>";
 ?>
                <button type='submit' value='<?= $datosCancion['id'] ?>' name='accionBorrarCancion' class='p-3 align-items-center borrarCuenta btn btn-danger fw-bolder fs-4 text-wrap' style='width: 350px; height: 100px;'>
                    <i class='fas fa-exclamation-triangle'></i>
                    <?= $lang['adminYesDelete'] ?>
                </button>
    <a href='listaCanciones.php?id=<?= $datosUsuario['id'] ?>' type='button' class='p-3 d-flex gap-3 justify-content-center align-items-center borrarCuenta btn btn-info fw-bolder fs-4 text-wrap' style='width: 350px;  height: 100px;'>
        <i class="fas fa-backward"></i>
        <?= $lang['adminNoDelete'] ?>
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


include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/canciones.js"></script>
<script src="./assets/scripts/perfil.js"></script>

</html>