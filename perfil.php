<?php
require_once 'conexion.php';
session_start();
include "configLanguage.php";

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

// parametro id de la url
if (isset($_REQUEST['id'])) {
    $id_url = $_REQUEST['id'];
    $database = openConection();
    try {
        $queryPerfil = "SELECT * FROM usuarios WHERE id = :idurl";
        $consultaPerfil = $database->prepare($queryPerfil);
        $consultaPerfil->bindParam(":idurl", $id_url);
        $consultaPerfil->execute();

        if ($consultaPerfil->rowCount() > 0) {
            $datosUsuario = $consultaPerfil->fetch(PDO::FETCH_ASSOC);
        }
        else {
            header("Location:404.php");
        }

    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
} else {
    header("Location:404.php");
}

// subir contador del perfil de la sesion +1
if (isset($_REQUEST['empezarASeguir'])) {

    $idUsuUrl = htmlspecialchars($_REQUEST['idUsuUrl']);

    $consultaSeguir = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsuUrl");
    $consultaSeguir->bindParam(":idUsuUrl", $idUsuUrl);
    $consultaSeguir->execute();

    $datos = $consultaSeguir->fetch(PDO::FETCH_ASSOC);
    $numeroDeSeguidores = $datos['numeroSeguidores'] + 1;

    $queryInsertarFollower = "INSERT INTO `usuario_followers` VALUES (NULL,:idUsuSeguidor,:idUsuSeguido)";
    $consultaInsertarFollower = $database->prepare($queryInsertarFollower);
    $consultaInsertarFollower->bindParam(":idUsuSeguidor",$_SESSION['userId']);
    $consultaInsertarFollower->bindParam(":idUsuSeguido",$idUsuUrl);
    $consultaInsertarFollower->execute();

    $queryEmpezarASeguir = "UPDATE usuarios SET numeroSeguidores = :numSeg WHERE id = :id_usu_url";
    $consultaEmpezarASeguir = $database->prepare($queryEmpezarASeguir);
    $consultaEmpezarASeguir->bindParam(":numSeg", $numeroDeSeguidores);
    $consultaEmpezarASeguir->bindParam(":id_usu_url", $idUsuUrl);

    if ($consultaEmpezarASeguir->execute()){
        header("Location:perfil.php?id=$idUsuUrl");
    }

}
// bajar contador del perfil de la sesion -1
if (isset($_REQUEST['dejarDeSeguir'])) {

    $idUsuUrl = htmlspecialchars($_REQUEST['idUsuUrl']);

    $consultaUsuario = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsuUrl");
    $consultaUsuario->bindParam(":idUsuUrl", $idUsuUrl);
    $consultaUsuario->execute();

    $datos = $consultaUsuario->fetch(PDO::FETCH_ASSOC);
    $numeroDeSeguidores = $datos['numeroSeguidores'] - 1;

    $queryQuitarFollower = "DELETE FROM usuario_followers WHERE idUsuSeguidor = :idSeguidor AND idUsuSeguido = :idSeguido";
    $quitarFollower = $database->prepare($queryQuitarFollower);
    $quitarFollower->bindParam(":idSeguidor",$_SESSION['userId']);
    $quitarFollower->bindParam(":idSeguido", $id_url);
    $quitarFollower->execute();

    $queryDejarDeSeguir = "UPDATE usuarios SET numeroSeguidores = :numSeg WHERE id = :id_usu_url";
    $consultaDejarDeSeguir = $database->prepare($queryDejarDeSeguir);
    $consultaDejarDeSeguir->bindParam(":numSeg", $numeroDeSeguidores);
    $consultaDejarDeSeguir->bindParam(":id_usu_url", $idUsuUrl);

    if ($consultaDejarDeSeguir->execute()){
        header("Location:perfil.php?id=$idUsuUrl");
    }

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['headerPerfil'] ?> - <?= $datosUsuario['nickName'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/swiper-bundle.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

if (isset($_REQUEST['errf'])) {
    ?>
    <div class='form-outline form-dark text-center mt-4 mb-0 p-3'>
        <span class='alert alert-info'>
            <?= $lang['errf'] ?>
        </span>
    </div>
    <?php
}
if (isset($_REQUEST['updated'])) {
    ?>
    <div class='form-outline form-dark text-center mt-4 mb-0 p-3'>
        <span class='alert alert-success'>
            <i class='far fa-check-circle'></i>
            <?= $lang['perfilUpdated'] ?>
        </span>
    </div>
    <?php
}
?>
<section class="h-100 gradient-custom-2">
    <div class="container py-5 h-100">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="columna">
                <div class="card sombra-cards" style="width: 75rem; margin: 0 auto;">
                    <!-- hacer que el banner al editar perfil puedas seleccionar un color -->
                    <div class="banner-perfil rounded-top text-white d-flex flex-row">
                        <div class="ms-4 mt-5 d-flex flex-column" style="">
                            <img src="<?= $datosUsuario["fotoPfp"] ?>" alt="imagen-usuario" class="img-fluid img-thumbnail mt-4 mb-2" style="object-position: center; object-fit: cover; height: 180px; z-index: 1; width: 180px;">

<?php

    $mismoUsuario = false;
    if (isset($_REQUEST['id']) && isset($_SESSION['userId'])) {
        $id_url = $_REQUEST['id'];
        $userId = $_SESSION['userId'];

        if ($id_url == $userId) {
            $mismoUsuario = true;
        }
    }

    $numeroDeCanciones = 0;
    //sacar numero de canciones del usuario
    $queryNumeroCanciones = "SELECT COUNT(id) AS nCanciones FROM canciones WHERE idUsu = :idusu";
    $consultaNCanciones = $database->prepare($queryNumeroCanciones);
    $consultaNCanciones->bindParam(":idusu", $id_url);
    $consultaNCanciones->execute();

    if ($consultaNCanciones->rowCount() > 0) {
        $datosCanciones = $consultaNCanciones->fetch(PDO::FETCH_ASSOC);
        $numeroDeCanciones = $datosCanciones['nCanciones'];
    }

                        if ($mismoUsuario == true) {
?>
                            <a href="editarPerfil.php?id=<?= $datosUsuario['id'] ?>" type="button" class="btn border-secondary botonsito" data-mdb-ripple-color="dark" style="z-index: 1; width: 180px;">
                                <i class="fas fa-wrench"></i>
                                <?= $lang['editarMiPerfil'] ?>
<?php
                      echo "</a>";
                        }
?>

                        </div>
                        <div class="ms-3" style="width: 46rem;display: flex; flex-direction: column; justify-content: flex-end;">
                            <h5><?= $datosUsuario['nickName'] ?></h5>
                            <p><?= $datosUsuario['mail'] ?></p>
                        </div>
                        <div class="" style="position: relative;top: 162px; height: 25px;">
                            <p><?= $lang['miembroDesde'] ?>&nbsp;<i><b><?= $datosUsuario['fechaRegistro'] ?></b></i></p>
                        </div>
                    </div>
                    <div class="p-4 text-black" style="background-color: #f8f9fa;">
                        <div class="d-flex justify-content-end text-center py-1 gap-2">
                            <div class="">
                                <!-- numero de canciones -->
                                <p class="mb-1 h5"><?= $numeroDeCanciones ?></p>
                                <p class="small text-muted mb-0"><?= $lang['canciones'] ?></p>
                            </div>
                            <div class="px-2">
                                <!-- numero de seguidores -->
                                <p class="mb-1 h5"><?= $datosUsuario['numeroSeguidores'] ?></p>
                                <p class="small text-muted mb-0"><?= $lang['seguidores'] ?></p>
                            </div>
                            <div class="">
                                <?php
                                if (isset($_SESSION['userId'])) {
                                    /*
                                     icono seguir / no seguir
                                     cosas a tener en cuenta:

                                     si el perfil es el mismo del que ha iniciado sesion, poner boton en disabled
                                  */
                                    if ($id_url != $_SESSION['userId']) {

                                        // comprobar en usuario_followers si el id del usuario que hemos accedido por url contiene al id del usuario que ha iniciado sesion,
                                        $queryFollower = "SELECT * FROM usuario_followers WHERE idUsuSeguidor = :idUsuSeguidor AND idUsuSeguido = :idUsuSeguido";
                                        $consultaFollower = $database->prepare($queryFollower);
                                        $consultaFollower->bindParam(":idUsuSeguidor", $_SESSION['userId']);
                                        $consultaFollower->bindParam(":idUsuSeguido", $id_url);
                                        $consultaFollower->execute();
                                        //$datosConsulta = $consultaFollower->fetchAll(PDO::FETCH_ASSOC);
                                        //var_dump($datosConsulta);
                                        /*
                                            si el perfil es diferente al mismo que ha iniciado sesion,
                                                - comprobar en usuario_followers si el id del usuario que hemos accedido por url contiene al id del usuario que ha iniciado sesion,
                                                    si no lo contiene, permitir seguirle
                                                    si lo contiene, no permitir seguirle
                                         */
                                        //si el perfil no es el mismo que el que ha iniciado sesion, que aparezca el boton
                                        if ($consultaFollower->rowCount() > 0) {
                                            echo "<form action='perfil.php?id=$id_url' method='post'>";
                                            echo "<button type='submit' value='$id_url' name='dejarDeSeguir' class='botonsito2 btn btn-outline-primary'>
                                                    <div class='followIcon' id='seguir-tachado'></div>
                                                    <input type='hidden' name='idUsuUrl' value='$id_url'>
                                                  </button>";
                                            echo "</form>";
                                        }
                                        else {
                                            echo "<form action='perfil.php?id=$id_url' method='post'>";
                                            echo "<button type='submit' value='$id_url' name='empezarASeguir' class='botonsito2 btn btn-outline-primary'>
                                                    <div class='followIcon' id='seguir-plus'></div>
                                                    <input type='hidden' name='idUsuUrl' value='$id_url'>
                                                  </button>";
                                            echo "</form>";
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black">
                        <div class="mb-5">
                            <p class="lead fw-normal mb-1"><?= $lang['sobreMi'] ?></p>
                            <div class="p-4" style="background-color: #f8f9fa;">
                                <p class="font-italic mb-1">
                                    <!-- descripción de la persona hecha con textarea al editar perfil -->
                                    <?= $datosUsuario['biografia'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <a href="listaCanciones.php?id=<?= $datosUsuario['id'] ?>" class="enlaces">
                                <p class="lead fw-normal mb-0"><?= $lang['canciones'] ?> (<?= $numeroDeCanciones ?>)</p>
                            </a>
                        </div>
                        <?php
                        $database = openConection();

                        //últimas 3 canciones del usuario
                        $query = "SELECT * FROM canciones WHERE idUsu = :idUsuario ORDER BY id DESC LIMIT 3";
                        $consulta = $database->prepare($query);
                        $consulta->bindParam(":idUsuario", $datosUsuario['id']);
                        $consulta->execute();

                        if ($consulta->rowCount() > 0) {

                            $cancionesUsuario = $consulta->fetchAll(PDO::FETCH_ASSOC);

                            echo '<div class="row g-2 d-flex flex-row gap-4 justify-content-start">';
                            foreach ($cancionesUsuario as $cu) {
                        ?>
                        <!--
                            hacer una query con 4 de las últimas canciones subidas por el usuario e imprimir el nombre con la foto de la cancion
                        -->
                                <div class="card carta p-2 cartaCancionFrontPage lista-elementos text-uppercase" style="width: 18rem;">
                                    <a href="singleCancion.php?id=<?= $cu['id'] ?>">
                                        <img src="<?= $cu['fotoPortada'] ?>" class="card-img-top" style="height: 250px;" alt="<?= $cu['titulo'] ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title">
                                            <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $cu['id'] ?>">
                                                <?= $cu['titulo'] ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-secondary">
                                            <?= $lang['por'] ?>
                                            <a class="enlaces text-primary" href="perfil.php?id=<?= $cu['idUsu'] ?>">
                                                <?= $datosUsuario['nickName'] ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>

                        <?php
                            }
                            echo '</div>';
                        }
                        else {

                            if (isset($_SESSION['userId'])) {
                                $identificadorUsuario = $_SESSION['userId'];

                                if ($id_url == $identificadorUsuario) {
                                    ?>
                                        <h4><?= $lang['vaya'] ?>&nbsp;<?= $lang['aunNoTienesCanciones'] ?></h4>
                                          <span></span>
                                        <h5><?= $lang['subeUna'] ?>&nbsp;<a href='subirCancion.php' class='btn-outline-warning' style='background: none;'><?= $lang['aqui'] ?></a></h5>
                                    <?php
                                }
                                else {
                                    ?>
                                    <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoTieneCanciones'] ?></h4>
                                    <span></span>
                                    <?php
                                }
                            }
                            else {
                                ?>
                                <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoTieneCanciones'] ?></h4>
                                <span></span>
                                <?php
                            }
                        }
                        ?>
                        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
                            <a href="listaSeguidores.php?id=<?= $datosUsuario['id'] ?>" class="enlaces">
                                <?php
                                //todos los seguidores del usuario
                                $consultaNSeg = $database->prepare("SELECT * FROM usuario_followers WHERE idUsuSeguido = :idUsuario");
                                $consultaNSeg->bindParam(":idUsuario", $id_url);
                                $consultaNSeg->execute();
                                $nSeg = $consultaNSeg->rowCount();
                                ?>
                                <p class="lead fw-normal mb-0"><?= $lang['seguidores'] ?> (<?= $nSeg; ?>)</p>
                            </a>
                        </div>
                        <?php
                        $database = openConection();

                        //últimas 2 canciones del usuario
                        $querySeguidores = "SELECT * FROM usuario_followers WHERE idUsuSeguido = :idUsuario ORDER BY id DESC LIMIT 3";
                        $consultaSeguidores = $database->prepare($querySeguidores);
                        $consultaSeguidores->bindParam(":idUsuario", $id_url);
                        $consultaSeguidores->execute();

                        if ($consultaSeguidores->rowCount() > 0) {

                            $seguidores = $consultaSeguidores->fetchAll(PDO::FETCH_ASSOC);

                            echo '<div class="row g-2 d-flex flex-row gap-4 justify-content-start">';
                            foreach ($seguidores as $seguidor) {

                                //de cada seguidor sacar los datos en la card, foto y nickname
                                $consultaDatosSeguidor = $database->prepare("SELECT * FROM usuarios WHERE id = :idSeguidor");
                                $consultaDatosSeguidor->bindParam("idSeguidor", $seguidor['idUsuSeguidor']);
                                $consultaDatosSeguidor->execute();

                                $datosSeguidor = $consultaDatosSeguidor->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <!--
                                    hacer una query con 4 de las últimas canciones subidas por el usuario e imprimir el nombre con la foto de la cancion
                                -->
                                <div class="card carta p-2 cartaCancionFrontPage lista-elementos text-uppercase" style="width: 18rem;">
                                    <a href="perfil.php?id=<?= $datosSeguidor['id'] ?>">
                                        <img src="<?= $datosSeguidor['fotoPfp'] ?>" class="card-img-top" style="height: 250px;" alt="<?= $datosSeguidor['nickName'] ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title">
                                            <a class="enlaces text-secondary" href="perfil.php?id=<?= $datosSeguidor['id'] ?>">
                                                <?= $datosSeguidor['nickName'] ?>
                                            </a>
                                        </h5>
                                    </div>
                                </div>

                                <?php
                            }
                            echo '</div>';
                        }
                        else {

                            if (isset($_SESSION['userId'])) {
                                $identificadorUsuario = $_SESSION['userId'];

                                if ($id_url == $identificadorUsuario) {
                                    ?>
                                        <h5><?= $lang['subeUnaCancion'] ?>&nbsp;<a href='subirCancion.php' class='btn-outline-warning' style='background: none;'><?= $lang['aqui'] ?></a> <?= $lang['paraDarteAConocer'] ?></h5>
                                    <?php
                                }
                                else {
                                    ?>
                                    <h4><?= $lang['aunNoTieneCanciones'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoTieneSeguidores'] ?></h4>
                                    <span></span>
                                    <?php
                                }
                            }
                            else {
                                ?>
                                <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoTieneSeguidores'] ?></h4>
                                <span></span>
                                <?php
                            }
                        }
                        ?>
                        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
                            <a href="listaSiguiendo.php?id=<?= $datosUsuario['id'] ?>" class="enlaces">
                                <?php
                                $querySig = "SELECT * FROM usuario_followers WHERE idUsuSeguidor = :idUsuSeguidor";
                                $consultaNSig = $database->prepare($querySig);
                                $consultaNSig->bindParam(":idUsuSeguidor", $id_url);
                                $consultaNSig->execute();
                                $nSig = $consultaNSig->rowCount();
                                ?>
                                <p class="lead fw-normal mb-0"><?= $lang['siguiendo'] ?> (<?= $nSig; ?>)</p>
                            </a>
                        </div>
                        <?php
                        $database = openConection();

                        $querySiguiendo = "SELECT * FROM usuario_followers WHERE idUsuSeguidor = :idUsuSeguidor ORDER BY id DESC LIMIT 3";
                        $consultaSiguiendo = $database->prepare($querySiguiendo);
                        $consultaSiguiendo->bindParam(":idUsuSeguidor", $id_url);
                        $consultaSiguiendo->execute();

                        if ($consultaSiguiendo->rowCount() > 0) {
                            //echo "el usuario sigue a otros usuarios";
                            $listaSiguiendo = $consultaSiguiendo->fetchAll(PDO::FETCH_ASSOC);
                            echo '<div class="row g-2 d-flex flex-row gap-4 justify-content-start">';
                            foreach ($listaSiguiendo as $usuSiguiendo) {

                            //de cada seguidor sacar los datos en la card, foto y nickname
                                $consultaDatosSiguiendo = $database->prepare("SELECT * FROM usuarios WHERE id = :idSeguido");
                                $consultaDatosSiguiendo->bindParam("idSeguido", $usuSiguiendo['idUsuSeguido']);
                                $consultaDatosSiguiendo->execute();

                                $datosSeguido = $consultaDatosSiguiendo->fetch(PDO::FETCH_ASSOC);
                        ?>
                                <!--
                                    hacer una query con 4 de los ultimos usuarios seguidos por el usuario e imprimir el nombre con la foto del usuario
                                -->
                                <div class="card carta p-2 cartaCancionFrontPage lista-elementos text-uppercase" style="width: 18rem;">
                                    <a href="perfil.php?id=<?= $datosSeguido['id'] ?>">
                                        <img src="<?= $datosSeguido['fotoPfp'] ?>" class="card-img-top" style="height: 250px;" alt="<?= $datosSeguido['nickName'] ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title">
                                            <a class="enlaces text-secondary" href="perfil.php?id=<?= $datosSeguido['id'] ?>">
                                                <?= $datosSeguido['nickName'] ?>
                                            </a>
                                        </h5>
                                    </div>
                                </div>
                        <?php
                            }
                            echo '</div>';
                        }
                        else {
                            if (isset($_SESSION['userId'])) {
                                $identificadorUsuario = $_SESSION['userId'];

                                if ($id_url == $identificadorUsuario) {
                                    ?>
                                        <h4><?= $lang['vaya'] ?>&nbsp;<?= $lang['aunNoSiguesANadie'] ?></h4>
                                            <span></span>
                                        <h5><?= $lang['comienzaA'] ?>&nbsp;<?= $lang['descubrirArtistas'] ?>&nbsp;<a href='explorar.php' class='btn-outline-warning' style='background: none;'><?= $lang['aqui'] ?></a>!</h5>
                                    <?php
                                }
                                else {
                                    ?>
                                        <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoSigueANadie'] ?></h4>
                                        <span></span>
                                    <?php
                                }
                            }
                            else {
                                ?>
                                <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoSigueANadie'] ?></h4>
                                <span></span>
                                <?php
                            }
                        }
                        ?>

                        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
                            <a href="listaLikes.php?id=<?= $datosUsuario['id'] ?>" class="enlaces">
                                <?php
                                //canciones likeadas por el usuario
                                $consultaNLikeadas = $database->prepare("SELECT * FROM usuario_likes WHERE idUsu = :idUsuario");
                                $consultaNLikeadas->bindParam(":idUsuario", $id_url);
                                $consultaNLikeadas->execute();
                                $nLikeadas = $consultaNLikeadas->rowCount();

                                if (isset($_SESSION['userId'])) {
                                    if ($id_url == $_SESSION['userId']) {
                                        ?>
                                        <p class="lead fw-normal mb-0"><?= $lang['cancionesQueMeGustan'] ?>&nbsp;<i class='fas fa-heart'></i> (<?= $nLikeadas; ?>)</p>
                                        <?php
                                    }
                                    else {
                                        ?>
                                        <p class="lead fw-normal mb-0"><?= $lang['cancionesQueLeGustan'] ?>&nbsp;<i class='fas fa-heart'></i> (<?= $nLikeadas; ?>)</p>
                                        <?php
                                    }
                                }
                                else {
                                    ?>
                                    <p class="lead fw-normal mb-0"><?= $lang['cancionesQueLeGustan'] ?>&nbsp;<i class='fas fa-heart'></i> (<?= $nLikeadas; ?>)</p>
                                    <?php
                                }
                                ?>
                            </a>
                        </div>
                        <?php
                        $database = openConection();

                        //canciones likeadas por el usuario
                        $queryCancionesLikeadas = "SELECT * FROM usuario_likes WHERE idUsu = :idUsuario ORDER BY id DESC LIMIT 3";
                        $consultaCancionesLikeadas = $database->prepare($queryCancionesLikeadas);
                        $consultaCancionesLikeadas->bindParam(":idUsuario", $id_url);
                        $consultaCancionesLikeadas->execute();

                        if ($consultaCancionesLikeadas->rowCount() > 0) {

                            $listaCancionesLikeadas = $consultaCancionesLikeadas->fetchAll(PDO::FETCH_ASSOC);

                            echo '<div class="row g-2 d-flex flex-row gap-4 justify-content-start">';
                            foreach ($listaCancionesLikeadas as $cancionLikeada) {

                                //tenemos el id de las canciones en $cancionLikeada['idCancion'];
                                $consultaCancion = $database->prepare("SELECT * FROM canciones WHERE id = :idCancion");
                                $consultaCancion->bindParam(":idCancion", $cancionLikeada['idCancion']);
                                $consultaCancion->execute();

                                //de cada cancion sacar los datos en la card, foto, titulo, nickname autor, fecha subida
                                $datosCancionLikeada = $consultaCancion->fetch(PDO::FETCH_ASSOC);

                                //datosCancionLikeada['idUsu'];
                                $consultaUsuarioCancion = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsu");
                                $consultaUsuarioCancion->bindParam(":idUsu", $datosCancionLikeada['idUsu']);
                                $consultaUsuarioCancion->execute();
                                $datosUsuarioCancion = $consultaUsuarioCancion->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <!--
                                    hacer una query con 4 de las últimas canciones subidas por el usuario e imprimir el nombre con la foto de la cancion
                                -->
                                <div class="card carta p-2 cartaCancionFrontPage lista-elementos text-uppercase" style="width: 18rem;">
                                    <a href="singleCancion.php?id=<?= $datosCancionLikeada['id'] ?>">
                                        <img src="<?= $datosCancionLikeada['fotoPortada'] ?>" class="card-img-top" style="height: 250px;" alt="<?= $datosUsuarioCancion['nickName'] ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title">
                                            <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $datosCancionLikeada['id'] ?>">
                                                <?= $datosCancionLikeada['titulo'] ?>
                                            </a>
                                        </h5>
                                        <!-- nombre del artista + enlace a su perfil -->
                                        <p class="card-text text-secondary">
                                            <?= $lang['por'] ?>
                                            <a class="enlaces text-primary" href="perfil.php?id=<?= $datosUsuarioCancion['id'] ?>">
                                                <?= $datosUsuarioCancion['nickName'] ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>

                                <?php
                            }
                            echo '</div>';
                        }
                        else {
                            if (isset($_SESSION['userId'])) {
                                $identificadorUsuario = $_SESSION['userId'];

                                if ($id_url == $identificadorUsuario) {
                                    ?>
                                        <h4><?= $lang['aunNoLeHasDado'] ?>&nbsp;<i class='fas fa-heart'></i>&nbsp;<?= $lang['aNingunaCancion'] ?></h4>
                                            <span></span>
                                        <h5><?= $lang['comienzaA'] ?>&nbsp;<a href='explorar.php' class='btn-outline-warning' style='background: none;'><?= $lang['headerExplorar'] ?></a>&nbsp;<?= $lang['cancionesEnPM'] ?></h5>
                                    <?php
                                }
                                else {
                                    ?>
                                    <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoLeHasDado'] ?>&nbsp;<i class='fas fa-heart'></i>&nbsp;<?= $lang['aNingunaCancion'] ?></h4>
                                    <span></span>
                                    <?php
                                }
                            }
                            else {
                                ?>
                                <h4><?= $lang['vaya'] ?>&nbsp;<i><?= $datosUsuario['nickName'] ?></i>&nbsp;<?= $lang['aunNoLeHasDado'] ?>&nbsp;<i class='fas fa-heart'></i>&nbsp;<?= $lang['aNingunaCancion'] ?></h4>
                                <span></span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php


include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/swiper-bundle.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/perfil.js"></script>

</html>