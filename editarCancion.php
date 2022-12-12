<?php
require_once 'conexion.php';
session_start();
include "configLanguage.php";

$datosUsuario = "";
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
else {
    header("Location:403.php");
}


$idCancion_url = "";
if (isset($_REQUEST['accionEditarCancion'])) {
    $idCancion_url = htmlspecialchars($_REQUEST['accionEditarCancion']);
}

$datosCancion = "";
//esto es el id de la cancion
if (isset($_REQUEST['id'])) {
    $idCancion = $_REQUEST['id'];
    $database = openConection();
    try {
        $queryCancion = "SELECT * FROM canciones WHERE id = :idUrlCancion";
        $consultaCancion = $database->prepare($queryCancion);
        $consultaCancion->bindParam(":idUrlCancion", $idCancion);
        $consultaCancion->execute();

        if ($consultaCancion->rowCount() > 0) {
            $datosCancion = $consultaCancion->fetch(PDO::FETCH_ASSOC);
        }
        else {
            header("Location:404.php");
        }

    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}
else {

    //consulta datos cancion con $idCancion_url

    $consultaDatosCancionConIdUrl = $database->prepare("SELECT * FROM canciones WHERE id = :idUrlCancion");
    $consultaDatosCancionConIdUrl->bindParam(":idUrlCancion", $idCancion_url);
    $consultaDatosCancionConIdUrl->execute();

    if ($consultaDatosCancionConIdUrl->rowCount() > 0) {
        $datosCancion = $consultaDatosCancionConIdUrl->fetch(PDO::FETCH_ASSOC);
    }
    else {
        header("Location:404.php");
    }

    //echo "error 404";
//    header("Location:404.php");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['editar'] ?> "<?= $datosCancion['titulo'] ?>" <?= $lang['de'] ?> <?= $datosUsuario['nickName'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';


//tenemos el id de la cancion (y todos sus datos en $datosCancion)
//tenemos el id del usuario

//para editar: hacer formulario como el de subir cancion y que permita cambiarle el titulo, cambiarle la foto o cambiar el archivo

if (isset($_REQUEST['accionEditarCancion'])) {

    $tieneCaracteresInvalidos = false;
    $cancion = $datosCancion['ubicacion'];

    if (isset($_FILES['ficheroAudio'])) {
        $directory = "assets/sounds/";
        $sound = basename($_FILES['ficheroAudio']['name']);
        $cancionRoot = $directory.$sound;

        //caracteres ilegales para los archivos
        $caracteresIlegales = ["#","%","&","{","}","<",">","*","?","¿","$","!","'",":","@","+","`","|","="];
        //si el archivo de audio contiene uno de los caracteres ilegales
        if(preg_match('(#|&|%|=|@)', $sound) === 1) {
            $tieneCaracteresInvalidos = true;
            //header("Location: editarCancion.php?errChar=t");
        }

        if (move_uploaded_file($_FILES['ficheroAudio']['tmp_name'], $cancionRoot)) {
            $cancion = $cancionRoot;
        }
        else {
            //ha habido algun error subiendo el archivo
        }
    }

    //
    $fotoPortada = $datosCancion['fotoPortada'];
    if (isset($_FILES['ficheroPortada'])) {
        $accept = ["jpeg", "jpg", "png", "webp"];
        $extension = strtolower(pathinfo($_FILES["ficheroPortada"]["name"], PATHINFO_EXTENSION));

        $directory = "assets/img/cancionesFotos/";
        $img = basename($_FILES['ficheroPortada']['name']);
        $imgRoot = $directory.$img;

        if (in_array($extension, $accept)) {
            if (move_uploaded_file($_FILES['ficheroPortada']['tmp_name'], $imgRoot)) {
                //se ha subido correctamente
                $fotoPortada = $imgRoot;
            }
        }
    }

    $tituloCancion = htmlspecialchars($_REQUEST['tituloCancion']);

    if ($tieneCaracteresInvalidos) {
        header("Location: editarCancion.php?id=$idCancion_url&errChar=t");
    }
    else {
        $queryUpdate = "UPDATE canciones SET ubicacion=:ubicacion, titulo=:titulo, fotoPortada=:fotoPortada, fechaSubida=:fechaSubida WHERE id=:idCancionUrl";
        $consultaUpdateCancion = $database->prepare($queryUpdate);
        $consultaUpdateCancion->bindParam(":ubicacion",$cancion);
        $consultaUpdateCancion->bindParam(":titulo",$tituloCancion);
        $consultaUpdateCancion->bindParam(":fotoPortada",$fotoPortada);
        $consultaUpdateCancion->bindParam(":fechaSubida",$datosCancion['fechaSubida']);
        $consultaUpdateCancion->bindParam(":idCancionUrl",$idCancion_url);

        if ($consultaUpdateCancion->execute()){
            $usuId = $_SESSION['userId'];
            header("Location:listaCanciones.php?id=$usuId&updated=t");
        }
    }
}
//formulario de edición de canción
else {

    if (isset($_REQUEST['errChar'])) {
        ?>
        <div class='form-outline form-dark text-center p-0 mt-5 mb-3'>
            <span class='alert alert-danger'>
                <i class='far fa-times-circle'></i>
                <?= $lang['errChar'] ?>
            </span>
        </div>
        <?php
    }

    echo '
<section class="h-100 w-100 gradient-custom-2">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7">
    ';
    echo "<form action='$_SERVER[PHP_SELF]' method='post' enctype='multipart/form-data' class='card sombra-cards form-editar-perfil p-3'>";
    ?>
                <h2 class='page-section-heading pb-3 text-uppercase text-dark' style='padding-left: 46px; padding-top:20px;'><?= $lang['editar'] ?> <?= $datosCancion['titulo'] ?></h2>
                <div class='m-5 mt-0 mb-0'>
                    <label class='form-label' for='username'><?= $lang['tituloDeLaCancion'] ?>*</label>
                    <input maxlength='150' value="<?= $datosCancion['titulo'] ?>" type='text' name='tituloCancion' class='form-control' id='titulo' placeholder='<?= $lang['tituloDeLaCancion'] ?>' required>
                </div>
                <div class='m-5 mb-0'>
                    <label class='form-label' for='Archivo de audio'><?= $lang['archivoDeAudio'] ?></label>
                    <button class='file-upload-btn' type='button' onclick='$(".file-upload-input").trigger("click")'><?= $lang['anadirArchivo'] ?></button>
                    <div class='image-upload-wrap'>
                        <input class='file-upload-input' type='file' onchange='readURL(this);' class='form-control' name='ficheroAudio' id='ficheroAudio' accept='audio/*'/>
                        <div class='drag-text' style="padding-left: 25px; padding-right: 25px;">
                            <h3><i class="fas fa-cloud-upload-alt"></i>&nbsp;<?= $lang['arrastraAudio'] ?></h3>
                        </div>
                    </div>
                    <div class='file-upload-content fileUploadContent p-3 pb-0'>
                        <h3 class="image-title"></h3>
                    </div>
                </div>
                <div class='m-5 mb-0'>
                    <label class='form-label' for='Portada'><?= $lang['portadaDeLaCancion'] ?></label>
                    <button class='file-upload-btn2' type='button' onclick='$(".file-upload-input2").trigger("click")'><?= $lang['anadirPortada'] ?></button>
                    <div class='image-upload-wrap2'>
                        <input class='file-upload-input2' type='file' onchange='readURL2(this);' class='form-control' name='ficheroPortada' id='ficheroPortada' accept='image/jpeg,image/jpg,image/png,image/webp' />
                        <div class='drag-text2'>
                            <h3><i class="fas fa-file-image"></i>&nbsp;<?= $lang['arrastraPortada'] ?></h3>
                        </div>
                    </div>

                    <div class='file-upload-content2 fileUploadContent2 p-3 pb-0'>
                        <h3><?= $lang['previewPortada'] ?></h3>
                        <img class='file-upload-image2' src='#' alt='your image' />
                    </div>
                </div>
                <div class='d-flex justify-content-center align-items-center mt-5 p-3 gap-3'>
                    <!--<button type='submit' name='accionEditarCancion' value="<?= $datosCancion['id'] ?>" class='botonsito2 btn btn-outline-secondary' style='width: 250px;'>
                        <i class='far fa-check-circle'></i>
                        Editar
                    </button>-->
                    <button type='submit' name='accionEditarCancion' class='editar-okey carta2' value="<?= $datosCancion['id'] ?>" style='width: 350px; outline: none; border: none;'>
                        <i class='far fa-check-circle'></i>
                        <?= $lang['editar'] ?>
                    </button>
                    <!--<a href='listaCanciones.php?id=<?= $datosUsuario['id'] ?>' type='button' class='btn btn-outline-warning' style='width: 250px;'>
                        <i class="fas fa-backward"></i>
                        Volver atrás
                    </a>-->
                    <!--<a href='listaCanciones.php?id=<?= $datosUsuario['id'] ?>' class="editar-back carta2" style="text-decoration: none; display: flex; justify-content: center; align-items: center">-->
                    <a onclick="history.back()" class="editar-back carta2" style="text-decoration: none; display: flex; justify-content: center; align-items: center; cursor: pointer;">
                        <i class="fas fa-backward"></i>&nbsp;
                        <?= $lang['volverAtras'] ?>
                    </a>
                </div>
            </form>
<?php
     echo "</div>
        </div>
    </div>
</section>";
}



include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>
<script src="./assets/scripts/canciones.js"></script>
<script src="./assets/scripts/perfil.js"></script>

</html>