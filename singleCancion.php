<?php
require_once 'conexion.php';
session_start();
include "configLanguage.php";

//deberia guardar tambien en una variable el parámetro de búsqueda cuando haga "explorar"
$datosCancion = "";
$datosArtista = "";
//info de la cancion a la que hemos entrado
if (isset($_REQUEST['id'])) {
    $idCancion = $_REQUEST['id'];

    $database = openConection();
    //comprobar si el id de la cancion existe
    $queryCancion = "SELECT * FROM canciones WHERE id = :idCancion";
    $consultaCancion = $database->prepare($queryCancion);
    $consultaCancion->bindParam(":idCancion",$idCancion);
    $consultaCancion->execute();

    if ($consultaCancion->rowCount() > 0) {
        $datosCancion = $consultaCancion->fetch(PDO::FETCH_ASSOC);

        //a partir del id de usuario de la cancion sacamos los datos del artista
        $idArtista = $datosCancion['idUsu'];
        $consultaArtista = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsuario");
        $consultaArtista->bindParam(":idUsuario",$idArtista);
        $consultaArtista->execute();
        $datosArtista = $consultaArtista->fetch(PDO::FETCH_ASSOC);
    }
    else {
        //la cancion no existe
        header("Location:404.php");
    }
}
else {
    header("Location:404.php");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $datosCancion['titulo'] ?> - <?= $datosArtista['nickName'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

$database = openConection();


if (isset($_REQUEST['quitarLike'])) {

    $idLoggedUsu = $_SESSION['userId'];
    $idSong = $datosCancion['id'];

    /*
        en la tabla canciones, la columna numeroLikesCancion restarle 1
        en la tabla usuario_likes borrarle la entrada que tenga los datos de idLoggedUsu, idSong
     */
    $consultaParaRestar = $database->prepare("SELECT * FROM canciones WHERE id = :idCancion");
    $consultaParaRestar->bindParam(":idCancion", $idSong);
    $consultaParaRestar->execute();
    $cancionARestar = $consultaParaRestar->fetch(PDO::FETCH_ASSOC);
    $numeroLikesCancion = $cancionARestar['numeroLikesCancion'] - 1;

    $consultaQuitarLike = $database->prepare("DELETE FROM usuario_likes WHERE idUsu = :idUsuLike AND idCancion = :idCancionLike");
    $consultaQuitarLike->bindParam(":idUsuLike", $idLoggedUsu);
    $consultaQuitarLike->bindParam(":idCancionLike", $idSong);
    $consultaQuitarLike->execute();

    $consultaActualizarLikes = $database->prepare("UPDATE canciones SET numeroLikesCancion = :numeroLikesCancion WHERE id = :idCancion");
    $consultaActualizarLikes->bindParam(":numeroLikesCancion", $numeroLikesCancion);
    $consultaActualizarLikes->bindParam(":idCancion", $idSong);

    if ($consultaActualizarLikes->execute()){
        header("Location:singleCancion.php?id=$idSong");
    }

}
//dar like a cancion
if (isset($_REQUEST['darLike'])) {

    $idLoggedUsu = $_SESSION['userId'];
    $idSong = $datosCancion['id'];

    /*
        en la tabla canciones, la columna numeroLikesCancion sumarle 1
        en la tabla usuario_likes insertarle la entrada que tenga los datos de idLoggedUsu, idSong
     */
    $consultaParaSumar = $database->prepare("SELECT * FROM canciones WHERE id = :idCancion");
    $consultaParaSumar->bindParam(":idCancion", $idSong);
    $consultaParaSumar->execute();
    $cancionASumar =  $consultaParaSumar->fetch(PDO::FETCH_ASSOC);
    $numeroLikesCancion_add = $cancionASumar['numeroLikesCancion'] + 1;

    $consultaPonerLike = $database->prepare("INSERT INTO `usuario_likes` VALUES (NULL, :idUsu, :idCancion)");
    $consultaPonerLike->bindParam(":idUsu", $idLoggedUsu);
    $consultaPonerLike->bindParam(":idCancion", $idSong);
    $consultaPonerLike->execute();

    $consultaActualizarLikes = $database->prepare("UPDATE canciones SET numeroLikesCancion = :numeroLikesCancion WHERE id = :idCancion");
    $consultaActualizarLikes->bindParam(":numeroLikesCancion", $numeroLikesCancion_add);
    $consultaActualizarLikes->bindParam(":idCancion", $idSong);

    if ($consultaActualizarLikes->execute()){
        header("Location:singleCancion.php?id=$idSong");
    }

}

?>
<section class="reproductor gradient-custom-2">
    <div class="container py-5 h-100">
        <div class="d-flex flex-column justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7">
                <!--
                    like section
                -->
                <div class="d-flex justify-content-end" style="">
                    <?php
                        if (isset($_SESSION['userId'])) {
                            //tienes $datosCancion y $datosArtista
                            $idUsuarioLogeado = $_SESSION['userId'];
                            //comprobar si el usuario logeado tiene likeada la cancion (tabla usuario_likes)
                            $queryLikeado = "SELECT * FROM usuario_likes WHERE idUsu = :idUsuarioLogeado AND idCancion = :idCancion";
                            $consultaLikeado = $database->prepare($queryLikeado);
                            $consultaLikeado->bindParam(":idUsuarioLogeado", $idUsuarioLogeado);
                            $consultaLikeado->bindParam(":idCancion", $datosCancion['id']);
                            $consultaLikeado->execute();

                            if ($consultaLikeado->rowCount() > 0) {
                                $cancionLikeada = $consultaLikeado->fetch(PDO::FETCH_ASSOC);

                                ?>
                                <form action='singleCancion.php?id=<?= $datosCancion['id'] ?>' method='post'>
                                <?php
                                    if ($datosCancion['idUsu'] == $_SESSION['userId']) {
                                        echo "<button type='submit' name='quitarLike' class='botonsito2 btn btn-outline-primary likeButton-belongs'>";
                                    }
                                    else {
                                        echo "<button type='submit' name='quitarLike' class='botonsito2 btn btn-outline-primary likeButton-notBelongs'>";
                                    }
                                ?>
                                    <!--<button type='submit' name='quitarLike' class='botonsito2 btn btn-outline-primary likeButton-belongs'>-->
                                        <div class='likeIcon' id='con-like'></div>
                                    </button>
                                </form>
                                <?php
                            }
                            else {
                                ?>
                                <form action='singleCancion.php?id=<?= $datosCancion['id'] ?>' method='post'>
                                    <button type='submit' name='darLike' class='botonsito2 btn btn-outline-primary likeButton-notBelongs'>
                                        <div class='likeIcon' id='sin-like'></div>
                                    </button>
                                </form>
                                <?php
                            }


                            //si el id del usuario que ha iniciado sesión es el mismo que el owner de la cancion (cancion['idUsu']),
                            // que aparezca el boton de editar y te mande al editar cancion
                            if ($_SESSION['userId'] == $datosCancion['idUsu']) {
                                ?>
                                    <form action='editarCancion.php?id=<?= $datosCancion['id'] ?>' method='post' enctype='multipart/form-data'>
                                        <button type='submit' name='editarCancion' class="botonsito btn btn-outline-primary editLaCancion2" style="position:relative; top: 60px; right:10px;">
                                            <i class='fas fa-edit fa-2x'></i>
                                        </button>
                                    </form>
                                <?php
                            }


                        }
                    ?>
                </div>
                <div class="main">
                    <!-- parte izquierda -->
                    <div class="left">
                        <!-- imagen de la cancion (añadirle lo de que gire la imagen -->
                        <img class="fotoCancion rounded-circle" style="width:220px;height:220px;" src="<?= $datosCancion['fotoPortada'] ?>" alt="<?= $datosCancion['titulo'] ?>">
                        <div class="volume">
                            <!--<i id="volume_icon" class="botonSonido fas fa-volume-mute ocultar-icono" style="color: whitesmoke;"></i>-->
                            <i id="volume_icon" class="botonSonido fas fa-volume-up displayear-icono" style="color: whitesmoke;"></i>
                            <input id="volume-slider" class="form-range" type="range" min="0" max="100" value="100">
                        </div>
                    </div>
                    <!-- parte derecha -->
                    <div class="right">
                        <div clas="d-flex flex-column datosCancion">
                            <h2 class="page-section-heading text-center text-uppercase text-white mb-0" id="titulo" style="word-break: break-all;">
                                <?php
                                //if (strlen($datosCancion['titulo']) > 30) {
                                //    $datosCancion['titulo'] = substr($datosCancion['titulo'], 0, 30) . '...';
                                //    echo $datosCancion['titulo'];
                                //}
                                //else {
                                //    echo $datosCancion['titulo'];
                                //}
                                ?>
                                <?= $datosCancion['titulo'] ?>
                            </h2>
                            <h5 class="page-section-heading text-center text-uppercase text-secondary mb-0" id="artista">
                                <?= $lang['por'] ?>
                                <a href="perfil.php?id=<?= $datosArtista["id"] ?>" class="enlaces text-primary"><?= $datosArtista["nickName"] ?></a>
                            </h5>
                        </div>

                        <div class="funcionalidadesCancion">
                            <div class="middle">
                                <!--  onclick="document.getElementById('player').play()" -->
                                <div id="play" class="accionCancion">
                                    <i class="iconoRepro fa fa-play fa-2x displayear-icono carta" onclick="document.getElementById('player').play()" style="color: whitesmoke;"></i>
                                    <i class="iconoRepro fa fa-pause fa-2x ocultar-icono carta" onclick="document.getElementById('player').pause()" style="color: whitesmoke;"></i>
                                </div>
                            </div>

                            <div class="duration">
                                <div class="progreso-cancion">00:00</div>
                                <input type="range" class="form-range" min="0" max="100" value="0" id="duration_slider">
                                <div class="duracion-cancion">00:00</div>
                            </div>
                        </div>

                        <audio id="player" src="<?= $datosCancion['ubicacion'] ?>"></audio>
                        <!-- -->
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center text-center card mt-5 sombra-cards">
                <div class="p-3">
                    <h4 class="text-secondary"><?= $lang['comparteLaCancion'] ?>&nbsp;<i class="fas fa-icons" style="color: #1abc9c;"></i></h4>
                    <div class="d-flex flex-row">
                        <input readonly id="compartirEnlace" name="share-link" type="text" value="http://www.iestrassierra.net/alumnado/curso2122/DAW/daw2122a16/peepomusic/singleCancion.php?id=<?= $datosCancion['id'] ?>" style="height: 50px; border-radius:20px 0px 0px 20px;border: 1px solid #2c3e50; border-right: none;">
                        <button class="botonsito2 carta3 boton-copiar" onclick="myCopy()" style="border-radius:0px 20px 20px 0px; border: 1px solid #2c3e50; border-left: none; width: 75px; height: 50px; outline: none;">
                            <i class="fas fa-external-link-alt fa-2x"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center flex-column text-center mt-5 mb-0" style="width: 10rem; margin: 0 auto;">
                <a style="cursor: pointer;text-decoration: none" onclick="history.back()" class="volver-atras-boton"><?= $lang['volverAtras'] ?></a>
            </div>
        </div>
    </div>
</section>
<?php
include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/perfil.js"></script>
<script src="./assets/scripts/canciones.js"></script>
<script src="./assets/scripts/myScript.js"></script>

</html>