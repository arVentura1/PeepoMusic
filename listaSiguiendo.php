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
    <title><?= $lang['aQuienSigue'] ?>&nbsp;<?= $datosUsuario['nickName'] ?>&nbsp;<?= $lang['sigue'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./assets/styles/searchPanes.dataTables.min.css">
    <link rel="stylesheet" href="./assets/styles/select.dataTables.min.css">
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
                <h2><?= $lang['aQuienSigue'] ?>&nbsp;<?= $datosUsuario['nickName'] ?>&nbsp;<?= $lang['sigue'] ?></h2>
                <h5 class="card-tittle"></h5>
                <div class="list-group">
                    <?php
                    //sacar los seguidores de este usuario

                    $querySiguiendo = "SELECT * FROM usuario_followers WHERE idUsuSeguidor = :idUsuSeguidor ORDER BY id DESC";
                    $consultaSiguiendo = $database->prepare($querySiguiendo);
                    $consultaSiguiendo->bindParam(":idUsuSeguidor", $datosUsuario['id']);
                    $consultaSiguiendo->execute();

                    if ($consultaSiguiendo->rowCount() > 0) {
                        //echo "el usuario sigue a otros usuarios";
                        $listaSiguiendo = $consultaSiguiendo->fetchAll(PDO::FETCH_ASSOC);

                        echo "
                            <table class='pt-3 table table-hover' id='listaSeguidos'>
                                <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>";
                            ?>
                                    <th><?= $lang['imagen'] ?></th>
                                    <th><?= $lang['nickName'] ?></th>
                                </thead>
                                <tbody>
                            <?php
                        foreach ($listaSiguiendo as $usuSiguiendo) {

                            //ahora, de cada seguidor tenemos el id, por lo tanto tenemos que hacer otra consulta de la tabla usuarios para sacar sus datos
                            $queryPersona = "SELECT * FROM usuarios WHERE id = :idSiguiendo";
                            $consultaPersona = $database->prepare($queryPersona);
                            $consultaPersona->bindParam(":idSiguiendo", $usuSiguiendo['idUsuSeguido']);
                            $consultaPersona->execute();

                            $datosPersonaSiguiendo = $consultaPersona->fetch(PDO::FETCH_ASSOC);

                            ?>
                            <tr class="bg-light list-group-item-primary">
                                <!--<div class='lista-elementos list-group-item list-group-item-action d-flex gap-3 justify-content-between' aria-current='true'>-->
                                <td>
                                    <a class="enlaces text-secondary" href="perfil.php?id=<?= $datosPersonaSiguiendo["id"] ?>">
                                        <img class="explorar-foto card-img" style="width:100px;height:100px;" src="<?= $datosPersonaSiguiendo["fotoPfp"] ?>" alt="<?= $datosPersonaSiguiendo["nickName"] ?>">
                                    </a>
                                </td>
                                <div class='w-100 justify-content-between'>
                                    <td class='mb-1'>
                                        <a href='perfil.php?id=<?= $datosPersonaSiguiendo["id"] ?>' class="enlaces text-secondary" style="font-size:18px; text-transform: uppercase">
                                            <?php
                                            if (strlen($datosPersonaSiguiendo["nickName"]) > 20) {
                                                $datosPersonaSiguiendo["nickName"] = substr($datosPersonaSiguiendo["nickName"], 0, 20) . '...';
                                                echo $datosPersonaSiguiendo["nickName"];
                                            }
                                            else {
                                                echo $datosPersonaSiguiendo["nickName"];
                                            }
                                            ?>
                                        </a>
                                    </td>
                                </div>
                            </tr>
                            <?php
                        }
                        echo "
                                </tbody>
                            </table>";
                    }
                    else {
                        //echo "el usuario no sigue a nadie";

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
<script src="./assets/scripts/searchPanes.dataTables.min.js"></script>
<script src="./assets/scripts/dataTables.select.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/perfil.js"></script>
<script src="./assets/scripts/listaSiguiendoUsuario.js"></script>
<script src="./assets/scripts/canciones.js"></script>

</html>