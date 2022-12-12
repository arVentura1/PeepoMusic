<?php

echo '
<body>
<header class="masthead bg-primary text-white text-center">
        <div>';
if (isset($_REQUEST['loggedOut'])) {
    echo "
    <div class='form-outline form-dark mb-5' style='z-index: 999'>
        <span class='alert alert-warning'>Sesión cerrada correctamente</span>
    </div>
    ";
}
echo '</div>
        <div class="container d-flex align-items-center flex-column">
            <img class="masthead-avatar mb-5 rotating" src="./assets/img/fotos/pmVinilo.png" alt="pm-vinilo" />
            <h1 class="masthead-heading text-uppercase mb-0">Entérate de lo que está sonando</h1>
            <div class="divider-custom divider-light">
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
                <h5 class="masthead-subheading">¡ Nos alegramos de verte de nuevo, <a class="text-secondary enlaces" href="perfil.php?id=<?= $datosUsuLogged['id'] ?>"><i><?= $datosUsuLogged['nickName'] ?></i></a> !</h5>
                <?php

            }
            else {
                echo '
                    <a class="btn btn-xl btn-outline-light" href="acceder.php">
                        ¡ Regístrate ya !
                    </a>
                ';
            }

      echo '</p>
        </div>
    </header>
    <section class="page-section bg-secondary text-white mb-0" id="about">
        <div class="container">
            <!-- 
                About Section Heading
                
                AQUÍ PUEDO PONER UN SLIDER CON SLIDER.JS, CON VARIAS FOTOS DE UN CONCIERTO
                
                ESTUDIAR QUÉ PONER AQUÍ
            -->
            <h2 class="page-section-heading text-center text-uppercase text-white">Últimas novedades</h2>
            <div class="divider-custom divider-light">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>';

    $database = openConection();

    //ultimas 8 canciones subidas
    $query = "SELECT * FROM canciones ORDER BY id DESC LIMIT 8";
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
                            <?= $cancion['titulo'] ?>
                        </a>
                    </h5>
                    <p class="card-text text-secondary">
                        Por:
                        <a class="enlaces text-primary" href="perfil.php?id=<?= $cancion['idUsu'] ?>">
                            <?= $datosUsuario['nickName'] ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
<?php
        }
        echo '
      </div>
      <div class="swiper-pagination"></div>
    </div>
</div>';

    }
      echo '<div class="text-center mt-4">
                <a class="btn btn-xl btn-outline-light" href="explorar.php">
                    <i class="far fa-check-circle"></i>
                    Explora las últimas novedades
                </a>
            </div>
        </div>
    </section>
    <section class="page-section portfolio" id="portfolio">
        <div class="container">
            <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">Preguntas Frecuentes</h2>
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-music"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <div class="articulos row justify-content-center">
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span>¿Qué es PeepoMusic?</span>
                            <i class="fas fa-angle-down flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        <i>"PeepoMusic"</i> es un proyecto que surge de mi pasión por la música, por escucharla y practicarla; surge de la necesidad de una plataforma libre de grandes nombres en la que todos los artistas puedan <b>compartir</b> sus composiciones, para lograr posicionarse y que su arte pueda llegar al máximo número de personas posible.
                    </span>
                </article>
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span>¿Cuál es nuestro propósito?</span>
                            <i class="fas fa-angle-down flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        El propósito fundamental de “PeepoMusic” es <b>brindar apoyo a artistas independientes</b> o <b>potenciales músicos</b> que quieran darse a conocer. <br><br>
                        Permitir a los fanáticos <b>experimentar</b> la música como tenían la intención los artistas. <br><br>
                        Queremos ofrecer la posibilidad de <i>participar</i> en experiencias únicas de la vida real, tales como conciertos con artistas famosos o emergentes y otras interacciones directas entre los fans y sus artistas favoritos. La <i>curación humana</i> es una parte importante de lo que hace PeepoMusic un proyecto cercano y real al artista y usuario.
                    </span>
                </article>
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span>¿Necesito pagar para utilizar la plataforma?</span>
                            <i class="fas fa-angle-down flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        ¡Absolutamente no! Actualmente en nuestros planes no se encuentra implementar servicios de pago o suscripciones premium. <br><br> <b>¡A disfrutar de la música!</b>
                    </span>
                </article>
                <article class="pregunta">
                    <div class="pregunta-titulo">
                        <div class="inner-pt">
                            <span>¿Qué contenidos puedo subir?</span>
                            <i class="fas fa-angle-down flechita"></i>
                        </div>
                    </div>
                    <span class="inner-pt-text">
                        Cualquier canción, tus conciertos, programas de podcast o episodios individuales
                        <br>
                        Nota: No puedes subir canciones con derechos de autor activos, si lo haces nos desentendemos legalmente.
                    </span>
                </article>
            </div>
        </div>
    </section>
</body>
';