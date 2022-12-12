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
    <title><?= $lang['headerExplorar'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';
//para explorar no hace falta haber iniciado sesion
$database = openConection();

if (!isset($_REQUEST['buscar'])){$_REQUEST['buscar'] = '';}
//departamento serian mi "todos, usuarios, canciones, playlists"
if (!isset($_REQUEST['tipobusqueda'])){$_REQUEST['tipobusqueda'] = '';}
//if (!isset($_REQUEST['buscadepartamento'])){$_REQUEST['buscadepartamento'] = '';}
//if (!isset($_REQUEST['color'])){$_REQUEST['color'] = '';}
/*
if (!isset($_REQUEST['buscafechadesde'])){$_REQUEST['buscafechadesde'] = '';}
if (!isset($_REQUEST['buscafechahasta'])){$_REQUEST['buscafechahasta'] = '';}
*/
if (!isset($_REQUEST["orden"])){$_REQUEST["orden"] = '';}

?>

<div class="alert mb-0">
    <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0"><?= $lang['queQuieresEscucharHoy'] ?></h2>
    <div class="divider-custom mb-0">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon" style="height: 50px;">
            <!--<i class="fas fa-music"></i>-->
            <img class="masthead-avatar mb-5 rotating" width="50" height="50" src="./assets/img/fotos/pmVinilo.png" alt="pm-vinilo" />
        </div>
        <div class="divider-custom-line"></div>
    </div>
</div>

<div class="container" style="padding-bottom: 25px;">
    <div class="col-12">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card sombra-cards">
                    <div class="card-body">
                        <!--<h4 class="card-title">Buscador</h4>-->
                        <form id="form2" name="form2" method="post" action="explorar.php">
                            <div class="col-12 row">
                                <!--
                                <div class="mb-3">
                                    <label class="form-label">¿Qué desea buscar?</label>
                                    <input type="text" class="form-control" id="buscar" name="buscar" value="<?= $_REQUEST["buscar"] ?>">
                                </div>
                                -->
                                <!--<h4 class="card-title">Filtro de búsqueda</h4>-->
                                <div class="col-11">
                                    <table class="table">
                                        <thead>
                                            <tr class="filters">
                                                <th>
                                                    <?= $lang['tipoDeBusqueda'] ?>
                                                    <select id="assigned-tutor-filter" id="tipobusqueda" name="tipobusqueda" class="form-control mt-2" style="border: #bababa 1px solid; color:#000000;" >
                                                        <?php if ($_REQUEST["tipobusqueda"] != ''){ ?>
                                                            <option value="<?= $_REQUEST["tipobusqueda"]; ?>"><?= $_REQUEST["tipobusqueda"]; ?></option>
                                                        <?php } ?>
                                                        <option value="Todos"><?= $lang['todos'] ?></option>
                                                        <option value="Usuarios"><?= $lang['usuarios'] ?></option>
                                                        <option value="Canciones"><?= $lang['canciones'] ?></option>
                                                    </select>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- -->
                                <div class="col-1">
                                    <input type="submit" class="btn " value="<?= $lang['buscar'] ?>" style="margin-top: 38px; background-color: purple; color: white;">
                                </div>
                            </div>
<?php
    if ($_REQUEST['buscar'] == ''){ $_REQUEST['buscar'] = '';}
    //parametros de busqueda
    $aKeyword = explode(" ", $_REQUEST['buscar']);
    foreach ($aKeyword as $kw) {
        echo $kw;
    }
    $filtroBusqueda = $_REQUEST['tipobusqueda'];
    //numero total de cosas
    $numeroFilas = 0;
    //array para shuffle
    $todosLosDatos = array();
    //
    $queryCanciones = "SELECT * FROM canciones ORDER BY id DESC";
    $consultaCanciones = $database->prepare($queryCanciones);
    $consultaCanciones->execute();
    $datosCanciones = $consultaCanciones->fetchAll(PDO::FETCH_ASSOC);
    $numeroFilas += $consultaCanciones->rowCount();
    foreach ($datosCanciones as $c){
        array_push($todosLosDatos,$c);
    }

    $queryUsuarios = "SELECT * FROM usuarios ORDER BY id DESC";
    $consultaUsuarios = $database->prepare($queryUsuarios);
    $consultaUsuarios->execute();
    $datosUsuarios = $consultaUsuarios->fetchAll(PDO::FETCH_ASSOC);
    $numeroFilas += $consultaUsuarios->rowCount();
    foreach ($datosUsuarios as $u){
        array_push($todosLosDatos,$u);
    }

    shuffle($todosLosDatos);
    //foreach ($todosLosDatos as $td) {
    //    echo "<br>xd: ".$td['id'];
    //}

    //////////////////////////////////////////

    if ($filtroBusqueda === "Todos") {
?>
                            <table class="pt-3 table table-hover" id="listaExplorar">
                                <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>
                                    <th><?= $lang['imagen'] ?></th>
                                    <th><?= $lang['titulo'] ?></th>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($datosUsuarios as $usu) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $usu['id'] ?>">
                                                <img class="card-img explorar-foto" style="width:125px;height:125px;" src="<?= $usu["fotoPfp"] ?>" alt="<?= $usu["nickName"] ?>">
                                            </a>
                                        </td>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $usu['id'] ?>">
                                                <span style="text-transform: uppercase; font-size: 22px;"><?= $usu["nickName"] ?></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                foreach ($datosCanciones as $canc) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="singleCancion.php?id=<?= $canc['id'] ?>">
                                                <img class="explorar-foto card-img" style="width:125px;height:125px;" src="<?= $canc["fotoPortada"] ?>" alt="<?= $canc["titulo"] ?>">
                                            </a>
                                        </td>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="singleCancion.php?id=<?= $canc['id'] ?>">
                                                <span style="text-transform: uppercase; font-size: 22px;">
                                                    <?php
                                                    if (strlen($canc["titulo"]) > 35) {
                                                        $canc["titulo"] = substr($canc["titulo"], 0, 35) . '...';
                                                        echo $canc["titulo"];
                                                    }
                                                    else {
                                                        echo $canc["titulo"];
                                                    }
                                                    ?>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
    <?php
    }
    else if ($filtroBusqueda === "Canciones") {
        ?>
        <table class="pt-3 table table-hover" id="listaExplorar">
            <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>
            <th><?= $lang['imagen'] ?></th>
            <th><?= $lang['titulo'] ?></th>
            <th><?= $lang['artista'] ?></th>
            <th><?= $lang['numeroLikes'] ?></th>
            <th><?= $lang['fechaSubida'] ?></th>
            </thead>
            <tbody>
            <?php
            foreach ($datosCanciones as $canc) {
            ?>
                <tr>
                    <td>
                        <a class="enlaces2 text-secondary" href="singleCancion.php?id=<?= $canc['id'] ?>">
                            <img class="explorar-foto card-img" style="width:125px;height:125px;" src="<?= $canc["fotoPortada"] ?>" alt="<?= $canc["titulo"] ?>">
                        </a>
                    </td>
                    <td>
                        <a class="enlaces2 text-secondary" href="singleCancion.php?id=<?= $canc['id'] ?>">
                            <span style="text-transform: uppercase; font-size: 22px;">
                                <?php
                                if (strlen($canc["titulo"]) > 35) {
                                    $canc["titulo"] = substr($canc["titulo"], 0, 35) . '...';
                                    echo $canc["titulo"];
                                }
                                else {
                                    echo $canc["titulo"];
                                }
                                ?>
                            </span>
                        </a>
                    </td>
                    <!-- artista -->
                    <?php
                    $consultaArtista = $database->prepare("SELECT * FROM usuarios WHERE id = :idArtista");
                    $consultaArtista->bindParam(":idArtista", $canc['idUsu']);
                    $consultaArtista->execute();
                    $elArtista = $consultaArtista->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <td>
                        <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $canc['idUsu'] ?>">
                            <span style="text-transform: uppercase; font-size: 22px;">
                                <?= $elArtista["nickName"] ?></span>
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
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }
    else if ($filtroBusqueda === "Usuarios") {
        ?>
        <table class="pt-3 table table-hover" id="listaExplorar">
            <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>
            <th><?= $lang['imagen'] ?></th>
            <th><?= $lang['titulo'] ?></th>
            <th><?= $lang['numeroCanciones'] ?></th>
            <th><?= $lang['fechaRegistro'] ?></th>
            </thead>
            <tbody>

            <?php
            foreach ($datosUsuarios as $usu) {
                ?>
                <tr>
                    <td>
                        <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $usu['id'] ?>">
                            <img class="explorar-foto card-img" style="width:125px;height:125px;" src="<?= $usu["fotoPfp"] ?>" alt="<?= $usu["nickName"] ?>">
                        </a>
                    </td>
                    <td>
                        <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $usu['id'] ?>">
                            <span style="text-transform: uppercase; font-size: 22px;"><?= $usu["nickName"] ?></span>
                        </a>
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
                    <td>
                        <p>
                            <?= $usu['fechaRegistro'] ?>
                        </p>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }
    else {
?>
                            <table class="pt-3 table table-hover" id="listaExplorar">
                                <thead style='background: rgba(77,210,184,0.5); color: #2C3E50;'>
                                <th><?= $lang['imagen'] ?></th>
                                <th><?= $lang['nombre'] ?></th>
                                </thead>
                                <tbody>

                                <?php
                                foreach ($datosUsuarios as $usu) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $usu['id'] ?>">
                                                <img class="explorar-foto card-img" style="width:125px;height:125px;" src="<?= $usu["fotoPfp"] ?>" alt="<?= $usu["nickName"] ?>">
                                            </a>
                                        </td>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="perfil.php?id=<?= $usu['id'] ?>">
                                                <span style="text-transform: uppercase; font-size: 22px;"><?= $usu["nickName"] ?></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                foreach ($datosCanciones as $canc) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="singleCancion.php?id=<?= $canc['id'] ?>">
                                                <img class="explorar-foto card-img" style="width:125px;height:125px;" src="<?= $canc["fotoPortada"] ?>" alt="<?= $canc["titulo"] ?>">
                                            </a>
                                        </td>
                                        <td>
                                            <a class="enlaces2 text-secondary" href="singleCancion.php?id=<?= $canc['id'] ?>">
                                                <span style="text-transform: uppercase; font-size: 22px;">
                                                    <?php
                                                    if (strlen($canc["titulo"]) > 35) {
                                                        $canc["titulo"] = substr($canc["titulo"], 0, 35) . '...';
                                                        echo $canc["titulo"];
                                                    }
                                                    else {
                                                        echo $canc["titulo"];
                                                    }
                                                    ?>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
    }
?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5 mb-5"></div>
<?php
include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/jquery.dataTables.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/listaExplorar.js"></script>
<script src="./assets/scripts/myScript.js"></script>

</html>