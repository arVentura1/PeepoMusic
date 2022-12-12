<?php

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'es';
}
else if ( isset($_REQUEST['lang']) && $_SESSION['lang'] != $_REQUEST['lang'] && !empty($_REQUEST['lang'])) {

    if ($_REQUEST['lang'] == "en" ) {
        $_SESSION['lang'] = "en";
    }
    else if ($_REQUEST['lang'] == "es" ) {
        $_SESSION['lang'] = "es";
    }

}

//
switch ($_SESSION['lang']){
    case "es":
        require_once 'assets/languages/es.php';
        break;
    case "en":
        require_once 'assets/languages/en.php';
        break;
}