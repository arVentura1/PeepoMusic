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
    <title><?= $lang['adminTitulo'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/sweetalert2.min.css">
    <link rel="stylesheet" href="./assets/styles/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

//el usuario administrador podrá borrar usuarios, canciones y playlists

//si se ha iniciado sesion,
if (isset($_SESSION['userId'])) {

    $usuarioLogeado = $_SESSION['userId'];

    //comprobar si el usuario que ha iniciado sesion es administrador
    $database = openConection();

    $consultaAdmin = $database->prepare("SELECT * FROM usuarios WHERE id = :idLogged");
    $consultaAdmin->bindParam(":idLogged", $usuarioLogeado);
    $consultaAdmin->execute();
    $usuario = $consultaAdmin->fetch(PDO::FETCH_ASSOC);

    //es admin
    if ($usuario['rol'] == 1) {

        if (isset($_REQUEST['deletedSong'])) {
            ?>
                <div class='d-flex justify-content-center align-items-center form-outline form-dark mt-4' style='z-index: 999'>
                    <span class='alert alert-info'><?= $lang['adminDeletedSong'] ?></span>
                </div>
            <?php
        }
        if (isset($_REQUEST['deletedUsu'])) {
            ?>
                <div class='d-flex justify-content-center align-items-center form-outline form-dark mt-4' style='z-index: 999'>
                    <span class='alert alert-info'><?= $lang['adminDeletedUser'] ?></span>
                </div>
            <?php
        }
        if (isset($_REQUEST['rolUpdated'])) {
            ?>
                <div class='d-flex justify-content-center align-items-center form-outline form-dark mt-4' style='z-index: 999'>
                    <span class='alert alert-secondary'><?= $lang['adminUpdatedRole'] ?></span>
                </div>
            <?php
        }
        //
        //
        if (isset($_REQUEST['hacerAdmin'])) {
            $idUsu = htmlspecialchars($_REQUEST['hacerAdmin']);

            //para dar el rol de admin hacer un update cuyo rol sea idUsu
            $consultaDarAdmin = $database->prepare("UPDATE usuarios SET rol = 1 WHERE id = :idUsu");
            $consultaDarAdmin->bindParam(":idUsu", $idUsu);

            if ($consultaDarAdmin->execute()) {
                //se ha actualizado el rol
                header("Location:administracion.php?rolUpdated=t");
            }

        }
        else if (isset($_REQUEST['quitarAdmin'])) {
            $idUsu = htmlspecialchars($_REQUEST['quitarAdmin']);

            //para quitar admin hacer un update del rol cuyo id sea idUsu
            $consultaQuitarAdmin = $database->prepare("UPDATE usuarios SET rol = 2 WHERE id = :idUsu");
            $consultaQuitarAdmin->bindParam(":idUsu", $idUsu);

            if ($consultaQuitarAdmin->execute()) {
                //se ha actualizado el rol
                header("Location:administracion.php?rolUpdated=t");
            }

        }
        else if (isset($_REQUEST['accionDeBorrarUsu'])) {

            $idUsuarioFormulario = htmlspecialchars($_REQUEST['accionDeBorrarUsu']);
            $deleteUsuario = $database->prepare("DELETE FROM usuarios WHERE id = :idUsu");
            $deleteUsuario->bindParam(":idUsu",$idUsuarioFormulario);
            $deleteUsuario->execute();
            header("Location:administracion.php?deletedUsu=t");

        }
        else if (isset($_REQUEST['adminBorrarUsu'])) {

            $idUsuParaBorrar = htmlspecialchars($_REQUEST['adminBorrarUsu']);
            //consulta para datos
            $consultaParaDatos2 = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsuParaBorrar");
            $consultaParaDatos2->bindParam(":idUsuParaBorrar", $idUsuParaBorrar);
            $consultaParaDatos2->execute();
            $datosUsuParaBorrar = $consultaParaDatos2->fetch(PDO::FETCH_ASSOC);
            ?>

            <section class="page-section bg-primary text-white mb-0" style="height:75vh;" id="about">
                <div class="container">
                    <h1 class="text-center text-uppercase text-white"><?= $lang['adminAskDelete'] ?>&nbsp;<i>"<?= $datosUsuParaBorrar['nickName'] ?>"</i>&nbsp;?</h1>
                    <?php

                    echo "<form action='$_SERVER[PHP_SELF]' method='post' class='p-4 d-flex gap-3 flex-row justify-content-center' style='background:inherit;'>";
                    ?>
                    <button type='submit' value='<?= $datosUsuParaBorrar['id'] ?>' name='accionDeBorrarUsu' class='p-3 align-items-center borrarCuenta btn btn-danger fw-bolder fs-4 text-wrap' style='width: 350px; height: 100px;'>
                        <i class='fas fa-exclamation-triangle'></i>
                        <?= $lang['adminYesDelete'] ?>
                    </button>
                    <a href="administracion.php" type='button' class='p-3 d-flex gap-3 justify-content-center align-items-center borrarCuenta btn btn-info fw-bolder fs-4 text-wrap' style='width: 350px;  height: 100px;'>
                        <i class="fas fa-backward"></i>
                        <?= $lang['adminNoDelete'] ?>
                    </a>
                    <?php
                    echo '</form>
                            <div class="error-img">
                                <img src="./assets/img/fotos/pepeSad.png" alt="peepo-error" width="360" height="300">
                            </div>
                        </div>
                    </section>';

        }
        else if (isset($_REQUEST['accionDeBorrarSong'])) {

            $idCancionFormulario = htmlspecialchars($_REQUEST['accionDeBorrarSong']);
            $deleteCancion = $database->prepare("DELETE FROM canciones WHERE id = :idCancion");
            $deleteCancion->bindParam(":idCancion",$idCancionFormulario);
            $deleteCancion->execute();
            header("Location:administracion.php?deletedSong=t");

        }
        else if (isset($_REQUEST['adminBorrarCancion'])) {

            $idCancionParaBorrar = htmlspecialchars($_REQUEST['adminBorrarCancion']);
            //consulta para datos
            $consultaParaDatos = $database->prepare("SELECT * FROM canciones WHERE id = :idCancionParaBorrar");
            $consultaParaDatos->bindParam(":idCancionParaBorrar", $idCancionParaBorrar);
            $consultaParaDatos->execute();
            $datosCancionParaBorrar = $consultaParaDatos->fetch(PDO::FETCH_ASSOC);
            ?>

            <section class="page-section bg-primary text-white mb-0" style="height:75vh;" id="about">
                <div class="container">
                    <h1 class="text-center text-uppercase text-white"><?= $lang['adminAskDelete'] ?>&nbsp;<i>"<?= $datosCancionParaBorrar['titulo'] ?>"</i>&nbsp;?</h1>
                    <?php

        echo "<form action='$_SERVER[PHP_SELF]' method='post' class='p-4 d-flex gap-3 flex-row justify-content-center' style='background:inherit;'>";
                    ?>
                    <button type='submit' value='<?= $datosCancionParaBorrar['id'] ?>' name='accionDeBorrarSong' class='p-3 align-items-center borrarCuenta btn btn-danger fw-bolder fs-4 text-wrap' style='width: 350px; height: 100px;'>
                        <i class='fas fa-exclamation-triangle'></i>
                        <?= $lang['adminYesDelete'] ?>
                    </button>
                    <a href="administracion.php" type='button' class='p-3 d-flex gap-3 justify-content-center align-items-center borrarCuenta btn btn-info fw-bolder fs-4 text-wrap' style='width: 350px;  height: 100px;'>
                        <i class="fas fa-backward"></i>
                        <?= $lang['adminNoDelete'] ?>
                    </a>
                    <?php
        echo '</form>
                <div class="error-img">
                    <img src="./assets/img/fotos/pepeSad.png" alt="peepo-error" width="360" height="300">
                </div>
            </div>
        </section>';
        }
        else {

        //si es admin qué debería de hacer???
?>

        <div class="alert mb-0">
            <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0"><?= $lang['zonaDeGestion'] ?></h2>
            <div class="divider-custom mb-0">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon" style="height: 50px;">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="divider-custom-line"></div>
            </div>
        </div>
        <section class="container">
            <div class="col-12">
                <div class="row">
                    <div class="pregunta-gestionar d-flex justify-content-between align-items-center text-white bg-secondary" style="cursor:pointer; border-radius: 20px; padding: 20px 60px;">
                        <h2><?= $lang['gestionarUsuarios'] ?></h2>
                        <i class="fas fa-angle-down fa-2x flechita"></i>
                    </div>
                    <!-- datatable usuarios probar -->
                    <div class="col card sombra-cards p-4 gestion-administracion ocultar-icono">
                        <?php
                        $consultaUsuarios = $database->prepare("SELECT * FROM usuarios");
                        $consultaUsuarios->execute();

                        if ($consultaUsuarios->rowCount() > 0) {
                            $listadoUsuarios = $consultaUsuarios->fetchAll(PDO::FETCH_ASSOC);
                            echo "
                                <table class='pt-3 table table-hover' id='listaAdminUsus'>
                                    <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>";
                                    ?>
                                        <th><?= $lang['imagen'] ?></th>
                                        <th><?= $lang['nickName'] ?></th>
                                        <th><?= $lang['rol'] ?></th>
                                        <th><?= $lang['numeroTelefono'] ?></th>
                                        <th><?= $lang['fechaRegistro'] ?></th>
                                        <th><?= $lang['numeroCanciones'] ?></th>
                                        <!--<th>Editar</th>-->
                                        <th><?= $lang['borrar'] ?></th>
                                    <?php echo "
                                    </thead>
                                    <tbody>  
                                    ";
                                    foreach ($listadoUsuarios as $usu) {
                                        ?>
                                        <tr class="bg-light list-group-item-primary">
                                            <!-- imagen -->
                                            <td>
                                                <a class="enlaces text-secondary" href="perfil.php?id=<?= $usu["id"] ?>">
                                                    <img class="explorar-foto card-img" style="width:100px;height:100px;" src="<?= $usu["fotoPfp"] ?>" alt="<?= $usu["nickName"] ?>">
                                                </a>
                                            </td>
                                            <!-- nickname -->
                                            <td>
                                                <a href='perfil.php?id=<?= $usu["id"] ?>' class="enlaces text-secondary" style="font-size:18px; text-transform: uppercase">
                                                    <?= $usu["nickName"] ?>
                                                </a>
                                            </td>
                                            <!-- Rol -->
                                            <td>
                                                    <?php
                                                    if ($usu['rol'] == 1) {

                                                        if ($usu['id'] == $_SESSION['userId']) {
                                                            ?>
                                                            <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                                                <button disabled type='submit' name='quitarAdmin' value="<?= $usu['id']; ?>" class="botonsito3" style="background: none; outline: none; border: none;">
                                                                    <?= $lang['tipoAdmin'] ?>
                                                                </button>
                                                            </form>
                                                            <?php
                                                        }
                                                        else {
                                                            //si el $usu['id'] es 1 (andres, estará disabled siempre, porque yo siempre voy a ser administrador)
                                                            if ($usu['id'] == 1) {
                                                                ?>
                                                                <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                                                    <button disabled type='submit' name='quitarAdmin' value="<?= $usu['id']; ?>" class="botonsito3" style="background: none; outline: none; border: none;">
                                                                        <?= $lang['tipoAdmin'] ?>
                                                                    </button>
                                                                </form>
                                                                <?php
                                                            }
                                                            else {
                                                                ?>
                                                                <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                                                    <button type='submit' name='quitarAdmin' value="<?= $usu['id']; ?>" class="botonsito3" style="background: none; outline: none; border: none;">
                                                                        <?= $lang['tipoAdmin'] ?>
                                                                    </button>
                                                                </form>
                                                                <?php
                                                            }
                                                        }

                                                        //echo "Administrador";
                                                    }
                                                    else {
                                                        ?>
                                                            <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                                                <button type='submit' name='hacerAdmin' value="<?= $usu['id']; ?>" class="botonsito3" style="background: none; outline: none; border: none;">
                                                                    <?= $lang['tipoUsu'] ?>
                                                                </button>
                                                            </form>
                                                        <?php
                                                        //echo "Artista";
                                                    }
                                                    ?>
                                            </td>
                                            <!-- Nº Telefono  -->
                                            <td>
                                                <p>
                                                    <?= $usu['phoneNum'] ?>
                                                </p>
                                            </td>
                                            <!-- Fecha de Registro -->
                                            <td>
                                                <p>
                                                    <?= $usu['fechaRegistro'] ?>
                                                </p>
                                            </td>
                                            <!-- Nº de Canciones -->
                                            <?php
                                                $queryNumeroCanciones = "SELECT COUNT(id) AS nCanciones FROM canciones WHERE idUsu = :idusu";
                                                $consultaNCanciones = $database->prepare($queryNumeroCanciones);
                                                $consultaNCanciones->bindParam(":idusu", $usu['id']);
                                                $consultaNCanciones->execute();

                                                $numeroDeCanciones = 0;
                                                if ($consultaNCanciones->rowCount() > 0) {
                                                    $datosCanciones = $consultaNCanciones->fetch(PDO::FETCH_ASSOC);
                                                    $numeroDeCanciones = $datosCanciones['nCanciones'];
                                                }
                                            ?>
                                            <td><?= $numeroDeCanciones ?></td>
                                            <!-- editar
                                            <td>
                                                <form action='editarPerfil.php?id=<?= $usu['id'] ?>' method='post' enctype='multipart/form-data'>
                                                    <button type='submit' name='editarUsuario' class="botonsito3" style="background: none; outline: none; border: none;">
                                                        <i class='fas fa-edit fa-2x'></i>
                                                    </button>
                                                </form>
                                            </td>-->
                                            <!-- borrar -->
                                            <td>
                                            <?php
                                            if ($usu['rol'] == 1) {
                                                ?>
                                                <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                                    <button disabled type='submit' name='borrarCancion' class="" style="background: none; outline: none; border: none;">
                                                        <i class='fas fa-trash-alt fa-2x' style="color: lightgray"></i>
                                                    </button>
                                                </form>
                                                <?php
                                            }
                                            else {
                                                ?>
                                                <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                                    <button type='submit' name='adminBorrarUsu' value="<?= $usu['id']; ?>" class="botonsito3" style="background: none; outline: none; border: none;">
                                                        <i class='fas fa-trash-alt fa-2x'></i>
                                                    </button>
                                                </form>
                                                <?php
                                            }
                                            ?>

                                            </td>
                                        </tr>
                                        <?php
                                    }
                              echo "</tbody>
                                </table>";
                        }
                        ?>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="pregunta-gestionar d-flex justify-content-between align-items-center text-white bg-secondary" style="cursor: pointer;border-radius: 20px; padding: 20px 60px;">
                        <h2><?= $lang['gestionarCanciones'] ?></h2>
                        <i class="fas fa-angle-down fa-2x flechita"></i>
                    </div>
                    <!-- datatable canciones probar -->
                    <div class="col card sombra-cards p-4 gestion-administracion ocultar-icono">
                        <?php
                        $consultaCanciones = $database->prepare("SELECT * FROM canciones");
                        $consultaCanciones->execute();

                        if ($consultaCanciones->rowCount() > 0) {
                            $listadoCanciones = $consultaCanciones->fetchAll(PDO::FETCH_ASSOC);
                            echo "
                                <table class='pt-3 table table-hover' id='listaAdminSongs'>
                                    <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>";
                                    ?>
                                        <th><?= $lang['imagen'] ?></th>
                                        <th><?= $lang['titulo'] ?></th>
                                        <th><?= $lang['artista'] ?></th>
                                        <th><?= $lang['numeroLikes'] ?></th>
                                        <th><?= $lang['fechaSubida'] ?></th>
                                        <!--<th>Editar</th>-->
                                        <th><?= $lang['borrar'] ?></th>
                                    <?php echo "
                                    </thead>
                                    <tbody>  
                                    ";
                            foreach ($listadoCanciones as $canc) {
                                ?>
                                <tr class="bg-light list-group-item-primary">
                                    <!-- imagen -->
                                    <td>
                                        <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $canc["id"] ?>">
                                            <img class="explorar-foto card-img" style="width:100px;height:100px;" src="<?= $canc["fotoPortada"] ?>" alt="<?= $canc["titulo"] ?>">
                                        </a>
                                    </td>
                                    <!-- titulo -->
                                    <td>
                                        <a href='singleCancion.php?id=<?= $canc["id"] ?>' class="enlaces text-secondary" style="font-size:18px; text-transform: uppercase">
                                            <?= $canc["titulo"] ?>
                                        </a>
                                    </td>
                                    <!-- artista -->
                                    <?php
                                    $consultaArtista = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsu");
                                    $consultaArtista->bindParam(":idUsu", $canc['idUsu']);
                                    $consultaArtista->execute();
                                    $datosArtista = $consultaArtista->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <td>
                                        <a href='perfil.php?id=<?= $datosArtista["id"] ?>' class="enlaces text-secondary" style="font-size:18px; text-transform: uppercase">
                                            <?= $datosArtista['nickName'] ?>
                                        </a>
                                    </td>
                                    <!-- nº de likes  -->
                                    <td>
                                        <p>
                                            <?= $canc['numeroLikesCancion'] ?>
                                        </p>
                                    </td>
                                    <!-- Fecha de subida -->
                                    <td>
                                        <p>
                                            <?= $canc['fechaSubida'] ?>
                                        </p>
                                    </td>
                                    <!-- editar
                                    <td>
                                        <form action='editarPerfil.php?id=<?= $usu['id'] ?>' method='post' enctype='multipart/form-data'>
                                            <button type='submit' name='editarUsuario' class="botonsito3" style="background: none; outline: none; border: none;">
                                                <i class='fas fa-edit fa-2x'></i>
                                            </button>
                                        </form>
                                    </td>-->
                                    <!-- borrar -->
                                    <td>
                                        <form action='administracion.php' method='post' enctype='multipart/form-data'>
                                            <button type='submit' name='adminBorrarCancion' value="<?= $canc['id'] ?>" class="botonsito3" style="background: none; outline: none; border: none;">
                                                <i class='fas fa-trash-alt fa-2x'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            echo "</tbody>
                                </table>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>

            <section class="page-section portfolio" id="portfolio">
                <div class="container">
                    <h3 class="text-center text-uppercase text-secondary mb-0"><?= $lang['manualHeading'] ?></h3>
                    <div class="divider-custom">
                        <div class="divider-custom-line"></div>
                        <div class="divider-custom-icon"><i class="fas fa-wrench"></i></div>
                        <div class="divider-custom-line"></div>
                    </div>
                    <p class="message text-center text-secondary m-auto mb-5" style="max-width: 1000px;">
                        <i>
                            <?= $lang['manualDescripcion'] ?>
                        </i>
                    </p>
                    <div class="articulos row justify-content-center">
                        <article class="pregunta">
                            <div class="pregunta-titulo">
                                <div class="inner-pt">
                                    <span><?= $lang['manualPregunta1'] ?></span>
                                    <i class="fas fa-angle-down fa-2x flechita"></i>
                                </div>
                            </div>
                            <span class="inner-pt-text">
                               <?= $lang['manualRespuesta1'] ?>
                            </span>
                        </article>
                        <article class="pregunta">
                            <div class="pregunta-titulo">
                                <div class="inner-pt">
                                    <span><?= $lang['manualPregunta2'] ?></span>
                                    <i class="fas fa-angle-down fa-2x flechita"></i>
                                </div>
                            </div>
                            <span class="inner-pt-text">
                                <?= $lang['manualRespuesta2'] ?>
                            </span>
                        </article>
                        <article class="pregunta">
                            <div class="pregunta-titulo">
                                <div class="inner-pt">
                                    <span><?= $lang['manualPregunta3'] ?></span>
                                    <i class="fas fa-angle-down fa-2x flechita"></i>
                                </div>
                            </div>
                            <span class="inner-pt-text">
                                <?= $lang['manualRespuesta3'] ?>
                            </span>
                        </article>
                    </div>
                </div>
            </section>

        <!--<div class="p-5 pt-0 d-flex justify-content-center align-items-center">
            <a style="text-decoration: none; cursor: pointer;" onclick="history.back()" class="volver-atras-boton">Volver atrás</a>
        </div>-->
<?php

        }
    }
    //no es admin
    else {
        header("Location:403admin.php");
    }


}
else {
    header("Location:403.php");
}

?>


<?php
include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/jquery.dataTables.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/scripts/admin.js"></script>
<script src="./assets/scripts/admin2.js"></script>
<script src="./assets/scripts/sweetalert2.all.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/gestion.js"></script>
<script src="./assets/scripts/myScript.js"></script>

</html>