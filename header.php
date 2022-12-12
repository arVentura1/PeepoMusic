<?php
$activePage = basename($_SERVER['PHP_SELF'], ".php");
// parametro id de la url
if (isset($_REQUEST['id'])) {
    $id_url = $_REQUEST['id'];
}

echo '
<!--<div class="progress-container">
    <div class="progress-bar" id="p-bar"></div>
</div>-->
<header>
        <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                      <img class="logo" src="./assets/img/fotos/pmLogo.png" alt="logo">
                      PeepoMusic
                </a>
                <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menú
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto barra-header">
';
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            ?>
            <!--<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'index') ? 'active':''; ?>" href="index.php">Inicio</a></li>-->
            <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'explorar') ? 'active':''; ?>" href="explorar.php"><?= $lang['headerExplorar'] ?></a></li>
            <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'subirCancion') ? 'active':''; ?>" href="subirCancion.php"><?= $lang['headerSubirCancion'] ?></a></li>
            <!--<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'crearPlaylist') ? 'active':''; ?>" href="crearPlaylist.php">Crear Playlist</a></li>-->

            <?php
            if (isset($id_url)) {
                if ($id_url == $userId) {
                    ?>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'perfil') ? 'active':''; ?>" href="perfil.php?id=<?=$userId?>"><?= $lang['headerPerfil'] ?></a></li>
                    <?php
                }
                else {
                    ?>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="perfil.php?id=<?=$userId?>"><?= $lang['headerPerfil'] ?></a></li>
                    <?php
                }
            }
            else {
                ?>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'perfil') ? 'active':''; ?>" href="perfil.php?id=<?=$userId?>"><?= $lang['headerPerfil'] ?></a></li>
                <?php
            }

            try {
                $database = openConection();
                $query1 = "SELECT * FROM usuarios WHERE id = :userid";
                $consulta1 = $database->prepare($query1);
                $consulta1->bindParam(":userid", $userId);
                $consulta1->execute();

                if ($consulta1->rowCount() > 0) {

                    $usuario = $consulta1->fetch(PDO::FETCH_ASSOC);

                    if ($usuario['rol'] == 1) {
                        ?>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'administracion') ? 'active':''; ?>" href="administracion.php"><?= $lang['headerAdmin'] ?></a></li>
                        <?php
                    }

                    echo '<a class="btn btn-outline-light botonsitoLogout" href="logout.php">&nbsp;<i class="fas fa-sign-out-alt fa-2x" style="position: relative; top: 6px;"></i>&nbsp;<b>'.$usuario['nickName'].'</b>&nbsp;&nbsp;';
                    echo "<img class='rounded-circle' style='object-fit:cover;' width='50' height='50' src='".$usuario["fotoPfp"]."' alt='".$usuario['nickName']."'>";
                    echo "</a>";
                } else {
                    echo "no image";
                }

            } catch (PDOException $exception) {
                echo $exception->getMessage();
            }
        }
        else {
        ?>
            <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'explorar') ? 'active':''; ?>" href="explorar.php"><?= $lang['headerExplorar'] ?></a></li>
            <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'subirCancion') ? 'active':''; ?>" href="subirCancion.php"><?= $lang['headerSubirCancion'] ?></a></li>
            <!--<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'crearPlaylist') ? 'active':''; ?>" href="crearPlaylist.php">Crear Playlist</a></li>-->
            <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded <?= ($activePage == 'acceder') ? 'active':''; ?>" href="acceder.php"><?= $lang['headerAcceder'] ?></a></li>
            <?php
        }
?>
<?php
echo '
                    </ul>
                </div>
            </div>
        </nav>
</header>
';