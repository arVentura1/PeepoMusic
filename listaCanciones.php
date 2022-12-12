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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['cancionesDe'] ?>&nbsp;<?= $datosUsuario['nickName'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

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
//
 if(isset($_REQUEST["nume"]) == "" ){
     $_REQUEST["nume"] = "1";
 }


$query = "SELECT * FROM canciones WHERE idUsu = :idurl";
$laConsulta = $database->prepare($query);
$laConsulta->bindParam(":idurl",$id_url);
$laConsulta->execute();

$num_registros = $laConsulta->rowCount();
$registros = '3';
$pagina = $_REQUEST["nume"];

if (is_numeric($pagina))
    $inicio = (($pagina-1)*$registros);
else
    $inicio=0;

$consultaBusqueda = $database->prepare("SELECT * FROM canciones WHERE idUsu = :id_url LIMIT $inicio,$registros");
$consultaBusqueda->bindParam(":id_url",$id_url);
$paginas = ceil($num_registros/$registros);

//
if (isset($_REQUEST['updated'])) {
    ?>
    <div class='form-outline form-dark text-center mt-4 mb-0 p-3'>
        <span class='alert alert-success'>
            <i class='far fa-check-circle'></i>
            <?= $lang['cancionesEditadaBien'] ?>
        </span>
    </div>
    <?php
}
if (isset($_REQUEST['deletedSong'])) {
    ?>
    <div class='form-outline form-dark text-center mt-4 mb-0 p-3'>
        <span class='alert alert-warning'>
            <i class='fas fa-exclamation-triangle'></i>
            <?= $lang['cancionesBorradaBien'] ?>
        </span>
    </div>
    <?php
}
?>
<section class="h-100 gradient-custom-2 mt-5 mb-5">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7 bg-light p-4 sombra-cards" style="border: 2px solid #0d5a4b; border-radius: 10px;">
                <h2><?= $lang['cancionesDe'] ?>&nbsp;<?= $datosUsuario['nickName'] ?></h2>
                <h5 class="card-tittle">Resultados (<?= $num_registros; ?>)</h5>
                <div class="list-group">

                    <?php
                    //sacar las canciones de este usuario
                    //1 haces tu consulta
                    // antes del bucle abres la tabla
                    $queryCanciones = "SELECT * FROM canciones WHERE idUsu = :idusu_url ORDER BY id DESC";
                    $consultaCanciones = $database->prepare($queryCanciones);
                    $consultaCanciones->bindParam(":idusu_url",$datosUsuario['id']);
                    $consultaCanciones->execute();

                    if ($consultaCanciones->rowCount() > 0) {
                        //echo "el usuario tiene canciones";
                        $listaCanciones = $consultaCanciones->fetchAll(PDO::FETCH_ASSOC);

                        echo "
                            <table class='pt-3 table table-hover' id='listaCanciones'>
                                <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>";
                                ?>
                                    <th><?= $lang['imagen'] ?></th>
                                    <th><?= $lang['titulo'] ?></th>
                                    <th><?= $lang['artista'] ?></th>
                                    <th><?= $lang['subida'] ?></th>
                                <?php
                                    //si el usuario es el logeado, mostrar opcion de editar cancion
                                    if
                                    (
                                        (isset($_SESSION['userId'])) &&
                                        ($_SESSION['userId'] === $datosUsuario['id'])
                                    ) {
?>
                                    <th><?= $lang['editar'] ?></th>
                                    <th><?= $lang['borrar'] ?></th>
<?php
                                    }
                        echo "</thead>
                                <tbody>
                        ";

                        foreach ($listaCanciones as $cancion) {
?>
                                <tr class="bg-light list-group-item-primary">
                                    <!--<div class='lista-elementos list-group-item list-group-item-action d-flex gap-3 justify-content-between' aria-current='true'>-->
                                        <td>
                                            <a class="enlaces text-secondary" href="singleCancion.php?id=<?= $cancion['id'] ?>">
                                                <img class="explorar-foto card-img" style="width:100px;height:100px;" src="<?= $cancion["fotoPortada"] ?>" alt="<?= $cancion["titulo"] ?>">
                                            </a>
                                        </td>
                                        <div class='w-100 justify-content-between'>
                                            <td class='mb-1'>
                                                <a href='singleCancion.php?id=<?= $cancion["id"] ?>' class="enlaces text-secondary" style="font-size:18px; text-transform: uppercase">
                                                    <?php
                                                    if (strlen($cancion["titulo"]) > 20) {
                                                        $cancion["titulo"] = substr($cancion["titulo"], 0, 20) . '...';
                                                        echo $cancion["titulo"];
                                                    }
                                                    else {
                                                        echo $cancion["titulo"];
                                                    }
                                                    ?>
                                                </a>
                                            </td>
                                            <td class='mb-1'>
                                                <a href="perfil.php?id=<?= $datosUsuario["id"] ?>" class="enlaces text-secondary"><?= $datosUsuario["nickName"] ?></a>
                                            </td>

                                        </div>
                                        <td><?= $cancion["fechaSubida"] ?></td>

                                        <?php
                                        //si el usuario es el logeado, mostrar opcion de editar cancion
                                        if
                                        (
                                            (isset($_SESSION['userId'])) &&
                                            ($_SESSION['userId'] === $datosUsuario['id'])
                                        ) {
                                            ?>

                                            <!--<td class='w-100 d-flex gap-3 justify-content-end align-items-start'>-->
                                                <td>
                                                    <form action='editarCancion.php?id=<?= $cancion['id'] ?>' method='post' enctype='multipart/form-data'>
                                                        <button type='submit' name='editarCancion' class="botonsito3" style="background: none; outline: none; border: none;">
                                                            <i class='fas fa-edit fa-2x'></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form action='borrarCancion.php?id=<?= $cancion['id'] ?>' method='post' enctype='multipart/form-data'>
                                                        <button type='submit' name='borrarCancion' class="botonsito3" style="background: none; outline: none; border: none;">
                                                            <i class='fas fa-trash-alt fa-2x'></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            <?php
                                        }
                                        ?>
                                    <!--</div>-->
                                </tr>
<?php
                                }
                        echo "
                                </tbody>
                            </table>";
                    }
                    else {
                        //echo "el usuario no tiene canciones";
                            if (isset($_SESSION['userId'])) {
                                $identificadorUsuario = $_SESSION['userId'];

                                if ($id_url == $identificadorUsuario) {
                                    ?>
                                        <h4><?= $lang['vaya'] ?>&nbsp;<?= $lang['aunNoTienesCanciones'] ?></h4>
                                          <span></span>";
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
<script src="./assets/scripts/listaCancionesUsuario.js"></script>
<script src="./assets/scripts/canciones.js"></script>

</html>