<?php

$db = mysqli_connect('localhost', 'root', 'root', 'appsalon_mvc');
$db->set_charset("utf8");

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}

// try {
//     $db = new PDO("mysql:host=localhost;dbname=uwu;charset=utf8;", "root", "root");
// } catch(Throwable $th) {
//     echo $th;
//     exit;
// }