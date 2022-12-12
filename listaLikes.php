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

//
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['likesDe'] ?>&nbsp;<?= $datosUsuario['nickName'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';


?>
<section class="h-100 gradient-custom-2 mt-5 mb-5">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7 bg-light p-4 sombra-cards" style="border: 2px solid #0d5a4b; border-radius: 10px;">
                <h2><?= $lang['likesDe'] ?>&nbsp;<?= $datosUsuario['nickName'] ?></h2>
                <h5 class="card-tittle"></h5>
                <div class="list-group">
                    <?php
                    //sacar los likes dados por este usuario

                    $queryLikes = "SELECT * FROM usuario_likes WHERE idUsu = :idUsu ORDER BY id DESC";
                    $consultaLikes = $database->prepare($queryLikes);
                    $consultaLikes->bindParam(":idUsu", $datosUsuario['id']);
                    $consultaLikes->execute();

                    if ($consultaLikes->rowCount() > 0) {
                        //echo "el usuario tiene likes";
                        $listaLikes = $consultaLikes->fetchAll(PDO::FETCH_ASSOC);

                        echo "
                            <table class='pt-3 table table-hover' id='listaLikes'>
                                <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>";
                            ?>
                                    <th><?= $lang['imagen'] ?></th>
                                    <th><?= $lang['titulo'] ?></th>
                                    <th><?= $lang['artista'] ?></th>
                                    <th><?= $lang['subida'] ?></th>
                                </thead>
                                <tbody>
                            <?php
                        foreach ($listaLikes as $cancionLikeada) {

                            //ahora, de cada cancion likeada tenemos el id, por lo tanto tenemos que hacer otra consulta de la tabla canciones para sacar sus datos
                            $queryCancion = "SELECT * FROM canciones WHERE id = :idCancion";
                            $consultaCancion = $database->prepare($queryCancion);
                            $consultaCancion->bindParam(":idCancion", $cancionLikeada['idCancion']);
                            $consultaCancion->execute();

                            $datosCancion = $consultaCancion->fetch(PDO::FETCH_ASSOC);

                            //sacar datos del usuario que ha hecho la cancion
                            $queryArtista = "SELECT * FROM usuarios WHERE id = :idUsu";
                            $consultaArtista = $database->prepare($queryArtista);
                            $consultaArtista->bindParam(":idUsu", $datosCancion['idUsu']);
                            $consultaArtista->execute();

                            $datosArtista = $consultaArtista->fetch(PDO::FETCH_ASSOC);

                            ?>
                            <tr class="bg-light list-group-item-primary">
                                <!--<div class='lista-elementos list-group-item list-group-item-action d-flex gap-3 justify-content-between' aria-current='true'>-->
                                <td>
                                    <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $datosCancion["id"] ?>">
                                        <img class="explorar-foto card-img" style="width:100px;height:100px;" src="<?= $datosCancion["fotoPortada"] ?>" alt="<?= $datosCancion["titulo"] ?>">
                                    </a>
                                </td>
                                <div class='w-100 justify-content-between'>
                                    <td class='mb-1'>
                                        <a href='singleCancion.php?id=<?= $datosCancion["id"] ?>' class="enlaces text-secondary" style="font-size:18px; text-transform: uppercase">
                                            <?php
                                            if (strlen($datosCancion["titulo"]) > 20) {
                                                $datosCancion["titulo"] = substr($datosCancion["titulo"], 0, 20) . '...';
                                                echo $datosCancion["titulo"];
                                            }
                                            else {
                                                echo $datosCancion["titulo"];
                                            }
                                            ?>
                                        </a>
                                    </td>
                                    <td class='mb-1'>
                                        <a href='perfil.php?id=<?= $datosCancion["idUsu"] ?>' class="enlaces text-secondary" style="font-size:18px;">
                                            <?php
                                            if (strlen($datosArtista["nickName"]) > 20) {
                                                $datosArtista["nickName"] = substr($datosArtista["nickName"], 0, 20) . '...';
                                                echo $datosArtista["nickName"];
                                            }
                                            else {
                                                echo $datosArtista["nickName"];
                                            }
                                            ?>
                                        </a>
                                    </td>
                                </div>
                                <td><?= $datosCancion["fechaSubida"] ?></td>
                            </tr>
                            <?php
                        }
                        echo "
                                </tbody>
                            </table>";
                    }
                    else {
                        //echo "el usuario no ha dado likes";
                            if (isset($_SESSION['userId'])) {
                                $identificadorUsuario = $_SESSION['userId'];

                                if ($id_url == $identificadorUsuario) {
                                    ?>
                                        <h4><?= $lang['aunNoLeHasDado'] ?><i class='fas fa-heart'></i><?= $lang['aNingunaCancion'] ?></h4>
                                          <span></span>";
                                    <h5><?= $lang['comienzaA'] ?>&nbsp<a href='explorar.php' class='btn-outline-warning' style='background: none;'><?= $lang['explorar'] ?></a>&nbsp;<?= $lang['cancionesEnPM'] ?></h5>
                                    <?php                                                                                                                                                                                           }
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
        <div class="d-flex justify-content-center flex-column text-center mt-5 mb-5" style="width: 10rem; margin: 0 auto;">
            <a style="text-decoration: none" href="perfil.php?id=<?= $id_url ?>" class="volver-atras-boton"><?= $lang['volverAtras'] ?></a>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/jquery.dataTables.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/perfil.js"></script>
<script src="./assets/scripts/listaLikesUsuario.js"></script>
<script src="./assets/scripts/canciones.js"></script>

</html>