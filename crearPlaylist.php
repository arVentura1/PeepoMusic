<?php
require_once 'conexion.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea una playlist</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/styles/sweetalert2.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';

//si el usuario ha iniciado sesion
if (isset($_SESSION['userId'])) {

    /*
     * para crear una playlist
     *
     * > titulo
     * > idUsu
     * > fotoPlaylist (probablemente eliminare esta columna)
     * > fechaCreacion
     *
     *  ------------------------------------------------------
     *  formulario con:
     *  campo texto para el nombre de la playlist
     *  buscador para buscar todas las canciones de la BD
     *  listado de tipo tabla html (como lo de users.php de la biblioteca, con el checkbox multiple)
     *  ------------------------------------------------------
     */

    if (isset($_REQUEST['xd'])) {

    }
    //
    else {

    }

}
//si el usuario NO ha iniciado sesiónº
else {
    //no se puede acceder al contenido
    header("Location:403.php");
}


include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/scripts/sweetalert2.all.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>

</html>