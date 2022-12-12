<?php

function openConection(): ?PDO
{
    /*
        $db_host = 'www.iestrassierra.net';
        $db_user = 'daw2122a16';
        $db_pass = 'QKr5YbadW#wVj';
        $db_name = "daw2122a16";
     */

    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = "peepomusic";
    try {
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=UTF8mb4", $db_user, $db_pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //return $conn;
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //
    } catch (PDOException $exception) {
        echo $exception->getMessage();
        die("Connection to database failed!");
    }

    return $conn;
}


