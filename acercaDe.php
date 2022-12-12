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
    <title><?= $lang['about'] ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/img/fotos/pmVinilo.png">
    <link rel="stylesheet" href="./assets/styles/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles/normalize.css">
    <link rel="stylesheet" href="./assets/styles/style.css">
</head>
<?php
include_once 'header.php';
?>
<section class="masthead bg-primary text-white text-center">
    <div class="d-flex gap-3 justify-content-center align-items-center">
        <h1><?= $lang['about'] ?></h1>
    </div>
    <div class="d-flex gap-3 justify-content-center align-items-center">
        <h1 class="masthead-heading text-uppercase mb-0"><?= $lang['student'] ?>: "<i>Andrés Ruiz Ventura</i>"</h1>
        <img class="masthead-avatar mb-5" style="border-radius: 20px;" src="./assets/img/fotos/foto-andres.jpg" alt="andresxd" />
    </div>

    <div class="d-flex gap-3 justify-content-center align-items-center">
        <h1 class="masthead-heading text-uppercase mb-0"><?= $lang['school'] ?>: "<i>IES TRASSIERRA</i>"</h1>
        <img class="masthead-avatar mb-5" src="./assets/img/fotos/trass.png" alt="iestrassierra" />
    </div>
</section>
<?php
include_once 'footer.php';
?>
<script src="./assets/scripts/jquery-3.6.1.min.js"></script>
<script src="./assets/scripts/bootstrap.bundle.min.js"></script>
<script src="./assets/fontawesome/js/all.min.js"></script>
<script src="./assets/scripts/myScript.js"></script>

</html>