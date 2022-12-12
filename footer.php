<a href="#" id="ancla-top" class="back-to-top">
    <i class="fas fa-arrow-alt-circle-up fa-3x" style="border-radius: 50px; border: 1px solid #2c3e50; background: whitesmoke;"></i>
</a>
<!-- -->
<footer class="footer text-center">
    <div class="container">
        <div class="row">
            <!-- footer direccion -->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4>
                    <a href="acercaDe.php" class="page-section-heading enlaces text-uppercase mb-4 text-white"><?= $lang['about'] ?></a>
                </h4>
                <p class="lead mb-0">
                    Avenida del aeropuerto
                    <br />
                    14004, Córdoba
                </p>
                <p class="mt-1">
                    <a href="<?= basename($_SERVER['PHP_SELF'], ".php") ?>.php?lang=es" class="enlaces" style="text-decoration: none;">
                        <?php //$lang['lang_es']; ?>
                        <img src="./assets/img/fotos/es.png" width="40" height="30" alt="<?= $lang['lang_es']; ?>">
                    </a>
                    |
                    <a href="<?= basename($_SERVER['PHP_SELF'], ".php") ?>.php?lang=en" class="enlaces" style="text-decoration: none;">
                        <?php //$lang['lang_en']; ?>
                        <img src="./assets/img/fotos/en.png" width="40" height="30" alt="<?= $lang['lang_en']; ?>">
                    </a>
                </p>
            </div>
            <!--footer rrss -->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="text-uppercase mb-4"><?= $lang['socialMedia'] ?></h4>
                <!-- canal de youtube poner el enlace del video demostrativo de la plataforma -->
                <a class="btn btn-outline-light btn-social mx-1" href="#"><i class="fab fa-youtube"></i></a>
                <!-- enlace del codigo del proyecto en github -->
                <a class="btn btn-outline-light btn-social mx-1" href="#"><i class="fab fa-github"></i></a>
                <a class="btn btn-outline-light btn-social mx-1" href="https://www.linkedin.com/in/andresruizventura"><i class="fab fa-fw fa-linkedin-in"></i></a>
                <a class="btn btn-outline-light btn-social mx-1" href="acercaDe.php"><i class="fas fa-info"></i></a>
            </div>
            <!-- footer sobre nosotros -->
            <div class="col-lg-4">
                <h4 class="text-uppercase mb-4">PeepoMusic</h4>
                <p class="lead mb-0">
                    <?= $lang['copyright'] ?>
                </p>
            </div>
        </div>                      
    </div>
</footer>