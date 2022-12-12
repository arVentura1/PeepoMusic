<?php
session_start();
//session_destroy();
//en lugar de session destroy (unset la sesion de usuario), asi no pierdo la sesion del language
unset($_SESSION["userId"]);
header("Location:index.php?loggedOut=t");