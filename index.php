<?php
require_once 'conexion.php';
session_start();
//
include "configLanguage.php";
//
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeepoMusic</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/swiper-bundle.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

//include_once 'frontPage.php';
echo '
<body>
<header class="masthead bg-primary text-white text-center">
        <div>';
if (isset($_REQUEST['loggedOut'])) {
    ?>
    <div class='form-outline form-dark mb-5' style='z-index: 999'>
        <span class='alert alert-warning'><?= $lang['sesionCerrada'] ?></span>
    </div>
    <?php
}
echo '</div>
        <div class="container d-flex align-items-center flex-column">
            <img class="masthead-avatar mb-5 rotating" src="./assets/img/fotos/pmVinilo.png" alt="pm-vinilo" />';
            ?><h1 class="masthead-heading text-uppercase mb-0"><?= $lang['enterateDeLoQueSuena'] ?></h1><?php
      echo '<div class="divider-custom divider-light">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-guitar"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <!-- el boton mandará a acceder.php  -->
            <p class="masthead-subheading font-weight-light mb-0">
                <!-- esto será un enlace a acceder.php -->';

            if (isset($_SESSION['userId'])) {

                $consultaNicknamePibe = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsuLogged");
                $consultaNicknamePibe->bindParam(":idUsuLogged", $_SESSION['userId']);
                $consultaNicknamePibe->execute();

                $datosUsuLogged = $consultaNicknamePibe->fetch(PDO::FETCH_ASSOC);

                ?>
<h5 class="masthead-subheading"><?= $lang['nosAlegramosDeVerte'] ?>&nbsp;<a class="text-secondary enlaces" href="perfil.php?id=<?= $datosUsuLogged['id'] ?>"><i><?= $datosUsuLogged['nickName'] ?></i></a> !</h5>
<?php

}
else {
    ?>
                    <a class="explorar-frontpage2 d-flex justify-content-center align-items-center m-auto" href="acceder.php" style="text-decoration: none">
                        <?= $lang['registrateYa'] ?>
                    </a>
    <?php
}

echo '</p>
        </div>
    </header>';

//si el usuario ha iniciado sesion, mostrar una seccion de portfolio (fondo blanco) en el que aparezcan las novedades de la gente a la que sigues
//novedades de la gente que sigues
if (isset($_SESSION['userId'])) {
?>
<section class="page-section portfolio" id="portfolio">
    <div class="container">
        <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0"><?= $lang['novedadesArtistasFav'] ?></h2>
        <div class="divider-custom">
            <div class="divider-custom-line"></div>
            <div class="divider-custom-icon"><i class="fas fa-user-check"></i></div>
            <div class="divider-custom-line"></div>
        </div>
    </div>
    <?php
    //consulta de las ultimas canciones de los usuarios que el usuario que ha iniciado sesion sigue
    //sacar de usuario_followers 10 canciones por idUsu DESC
    $consultaUltimasSubidasQueSigue = $database->prepare("SELECT * FROM usuario_followers WHERE idUsuSeguidor = :idLogged ORDER BY id DESC");
    $consultaUltimasSubidasQueSigue->bindParam(":idLogged", $_SESSION['userId']);
    $consultaUltimasSubidasQueSigue->execute();

    //si el usuario ha dado algun like
    if ($consultaUltimasSubidasQueSigue->rowCount() > 0) {
        //tenemos los id de cancion que el usuario le ha dado like, sacar datos de la cancion a partir de ahi
        $datosUsusSiguiendo = $consultaUltimasSubidasQueSigue->fetchAll(PDO::FETCH_ASSOC);

        echo '
        <div class="swiper2 mySwiper2 m-auto" style="max-width: 1700px; padding-top:35px; overflow: hidden; height: 30rem;">
            <div class="swiper-wrapper">
        ';
        foreach ($datosUsusSiguiendo as $cadaSeguido) {

            //consulta de la cancion y del artista para las cards del swiper
            $consultaArtista = $database->prepare("SELECT * FROM usuarios WHERE id = :idCadaUsu");
            $consultaArtista->bindParam(":idCadaUsu", $cadaSeguido['idUsuSeguido']);
            $consultaArtista->execute();
            $cadaArtista = $consultaArtista->fetch(PDO::FETCH_ASSOC);

            //consulta de las ultimas canciones del idUsuSeguido
            $consultaCancion = $database->prepare("SELECT * FROM canciones WHERE idUsu = :idUsuSeguido ORDER BY id DESC LIMIT 1");
            $consultaCancion->bindParam(":idUsuSeguido", $cadaSeguido['idUsuSeguido']);
            $consultaCancion->execute();

            if ($consultaCancion->rowCount() > 0) {
                $cadaCancion = $consultaCancion->fetch(PDO::FETCH_ASSOC);

                echo '<div class="swiper-slide">';
                ?>
                <div class="card carta p-2 cartaCancionFrontPage lista-elementos text-uppercase" style="width: 18rem;">
                    <a href="singleCancion.php?id=<?= $cadaCancion['id'] ?>">
                        <img src="<?= $cadaCancion['fotoPortada'] ?>" class="card-img-top" style="height: 250px;" alt="<?= $cadaCancion['titulo'] ?>">
                    </a>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">
                            <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $cadaCancion['id'] ?>">
                                <?php
                                if (strlen($cadaCancion['titulo']) > 30) {
                                    $cadaCancion['titulo'] = substr($cadaCancion['titulo'], 0, 30) . '...';
                                    echo $cadaCancion['titulo'];
                                }
                                else {
                                    echo $cadaCancion['titulo'];
                                }
                                ?>
                            </a>
                        </h5>
                        <p class="card-text text-secondary">
                            <?= $lang['por'] ?>
                            <a class="enlaces text-primary" href="perfil.php?id=<?= $cadaArtista['id'] ?>">
                                <?= $cadaArtista['nickName'] ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php
                echo '</div>';
            }
        }
        echo '
              </div>
            <!--<div class="swiper-pagination"></div>-->
        </div>
        ';

    }
    //si el usuario no ha dado ningun like
    else {
        ?>
            <div class='p-3 d-flex flex-column justify-content-center align-items-center'>
              <h4><?= $lang['aunNoSiguesArtista'] ?></h4>
              <span></span>
              <h5><?= $lang['comienzaAExplorar1'] ?>&nbsp;<a href='explorar.php' class='btn-outline-warning' style='background: none;'><?= $lang['comienzaAExplorar2'] ?></a>&nbsp;<?= $lang['comienzaAExplorar3'] ?></h5>
            </div>
        <?php
    }


    ?>
</section>

<?php
}
echo '<section class="page-section bg-secondary text-white mb-0" id="about">
        <div class="container" style="max-width: 1400px;">';
?>
            <h2 class="page-section-heading text-center text-uppercase text-white"><?= $lang['loMasNuevoEnPeepoMusic'] ?></h2>
            <div class="divider-custom divider-light">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>
<?php
$database = openConection();

//ultimas 8 canciones subidas
$query = "SELECT * FROM canciones ORDER BY id DESC LIMIT 25";
$consulta = $database->prepare($query);
$consulta->execute();

if ($consulta->rowCount() > 0) {

$canciones = $consulta->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">';
foreach ($canciones as $cancion) {

$queryUsuario = "SELECT * FROM usuarios WHERE id = :idu";
$consultaUsuario = $database->prepare($queryUsuario);

$consultaUsuario->bindParam(":idu",$cancion['idUsu']);
$consultaUsuario->execute();

$datosUsuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);
?>      <div class="swiper-slide">
    <div class="card carta p-2 cartaCancionFrontPage lista-elementos text-uppercase" style="width: 18rem;">
        <a href="singleCancion.php?id=<?= $cancion['id'] ?>">
            <img src="<?= $cancion['fotoPortada'] ?>" class="card-img-top" style="height: 250px;" alt="<?= $cancion['titulo'] ?>">
        </a>
        <div class="card-body d-flex flex-column justify-content-between">
            <h5 class="card-title">
                <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $cancion['id'] ?>">
                    <?php
                    if (strlen($cancion["titulo"]) > 30) {
                        $cancion["titulo"] = substr($cancion["titulo"], 0, 30) . '...';
                        echo $cancion["titulo"];
                    }
                    else {
                        echo $cancion["titulo"];
                    }
                    ?>
                </a>
            </h5>
            <p class="card-text text-secondary">
                <?= $lang['por'] ?>
                <a class="enlaces text-primary" href="perfil.php?id=<?= $cancion['idUsu'] ?>">
                    <?php
                    if (strlen($datosUsuario['nickName']) > 20) {
                        $datosUsuario['nickName'] = substr($datosUsuario['nickName'], 0, 20) . '...';
                        echo $datosUsuario['nickName'];
                    }
                    else {
                        echo $datosUsuario['nickName'];
                    }
                    ?>
                </a>
            </p>
        </div>
    </div>
</div>
<?php
}
echo '
      </div>
      <!--<div class="swiper-pagination"></div>-->
    </div>
</div>';

}
?>
        <div class="text-center mt-4">
                <a class="explorar-frontpage carta2 d-flex gap-4 justify-content-center align-items-center m-auto" href="explorar.php" style="text-decoration: none">
                    <i class="far fa-check-circle"></i>
                    <?= $lang['exploraLasUltimasNovedades'] ?>
                </a>
            </div>
        </div>
    </section>
<?php

//probar swiper vertical de canciones

//consulta de las 5 canciones con más likes
$consultaMasLikes = $database->prepare("SELECT * FROM canciones WHERE fechaSubida >= DATE(NOW() - INTERVAL 7 DAY) ORDER BY numeroLikesCancion DESC  LIMIT 1");
$consultaMasLikes->execute();

$cancionesMasLikeadas = $consultaMasLikes->fetchAll(PDO::FETCH_ASSOC);
?>
<!--  -->
<section class="page-section bg-primary text-white mb-0">
    <div class="alert mb-0">
        <h2 class="page-section-heading text-center text-uppercase text-white mb-0"><?= $lang['laCancionDelMomento'] ?></h2>
        <div class="divider-custom mb-0">
            <div class="divider-custom-line bg-white"></div>
            <div class="divider-custom-icon" style="height: 50px;">
                <!--<i class="fas fa-music"></i>-->
                <img class="masthead-avatar mb-5 rotating" width="50" height="50" src="./assets/img/fotos/pmVinilo.png" alt="pm-vinilo" />
            </div>
            <div class="divider-custom-line bg-white"></div>
        </div>
    </div>

    <div class="container">
    <div class="swiper mySwiper3">
        <div class="swiper-wrapper">
            <?php
            foreach ($cancionesMasLikeadas as $cml) {
                echo "<div class='swiper-slide d-flex justify-content-around' style='width:100%;'>";
                ?>

            <div class="main">
                <!-- parte izquierda -->
                <div class="left">
                    <!-- imagen de la cancion (añadirle lo de que gire la imagen -->
                    <img class="fotoCancion rounded-circle" style="width:220px;height:220px;" src="<?= $cml['fotoPortada'] ?>" alt="<?= $cml['titulo'] ?>">
                    <div class="volume">
                        <!--<i id="volume_icon" class="botonSonido fas fa-volume-mute ocultar-icono" style="color: whitesmoke;"></i>-->
                        <i id="volume_icon" class="botonSonido fas fa-volume-up displayear-icono" style="color: whitesmoke;"></i>
                        <input id="volume-slider" class="form-range" type="range" min="0" max="100" value="100">
                    </div>
                </div>
                <!-- parte derecha -->
                <div class="right">
                    <div clas="d-flex flex-column datosCancion">
                        <a href="singleCancion.php?id=<?= $cml['id'] ?>" class="enlaces">
                            <h2 class="text-center text-uppercase text-white mb-0" id="titulo" style="word-break: break-all;">
                                <?= $cml['titulo'] ?>
                            </h2>
                        </a>
                        <?php
                            //consulta del artista que posee la cancion
                            $consultaDelAutor = $database->prepare("SELECT * FROM usuarios WHERE id = :idUsu");
                            $consultaDelAutor->bindParam(":idUsu", $cml['idUsu']);
                            $consultaDelAutor->execute();
                            $datosAutor = $consultaDelAutor->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <h2 class="text-center text-uppercase text-secondary mb-0" id="artista">
                            <?= $lang['por'] ?>
                            <a href="perfil.php?id=<?= $datosAutor["id"] ?>" class="enlaces text-primary"><?= $datosAutor["nickName"] ?></a>
                        </h2>
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

                    <audio id="player" src="<?= $cml['ubicacion'] ?>"></audio>
                    <!-- -->
                </div>
            </div>
                <?php
                echo "</div>";
            }
            ?>
        </div>
    </div>
</div>
</section>
<!-- -->
<section class="page-section portfolio" id="portfolio">
        <div class="container">
            <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0"><?= $lang['preguntasFrecuentesTitulo'] ?></h2>
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-music"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <div class="articulos row justify-content-center">
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span><?= $lang['articulo1Titulo'] ?></span>
                            <i class="fas fa-angle-down fa-2x flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        <?= $lang['articulo1Texto'] ?>
                    </span>
                </article>
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span><?= $lang['articulo2Titulo'] ?></span>
                            <i class="fas fa-angle-down fa-2x flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        <?= $lang['articulo2Texto'] ?>
                    </span>
                </article>
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span><?= $lang['articulo3Titulo'] ?></span>
                            <i class="fas fa-angle-down fa-2x flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        <?= $lang['articulo3Texto'] ?>
                    </span>
                </article>
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span><?= $lang['articulo4Titulo'] ?></span>
                            <i class="fas fa-angle-down fa-2x flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        <?= $lang['articulo4Texto'] ?>
                    </span>
                </article>
            </div>
        </div>
    </section>
</body>
<?php
include_once 'footer.php';
?>
    <script src="./assets/scripts/jquery-3.6.1.min.js"></script>
    <script src="./assets/scripts/bootstrap.bundle.min.js"></script>
    <script src="./assets/scripts/swiper-bundle.min.js"></script>
    <script src="./assets/fontawesome/js/all.min.js"></script>
    <script src="./assets/scripts/myScript.js"></script>
    <script src="./assets/scripts/canciones.js"></script>

</html>