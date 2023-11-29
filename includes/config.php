<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

define('CANT_REG_PAG', 30);

$conn = mysqli_connect("localhost", "root", "", "mundo-madera");
if(!$conn){
    echo "La conexion no se pudo establecer.<br />";
    die(print_r(mysqli_connect_error()));
}

session_start();
if(!isset($_SESSION['user'])){
    if (isset($_COOKIE['username']) || isset($_COOKIE['password'])) {
        $sqlLogin = "SELECT usuarios.* FROM usuarios 
                      WHERE username='" . $_COOKIE['username'] . "' AND password='" . $_COOKIE['password'] . "'";
        $resultLogin = mysqli_query($conn, $sqlLogin);
        if (mysqli_num_rows($resultLogin) === 1) {
            $_SESSION['user'] = mysqli_fetch_assoc($resultLogin);
        } else {
            if (!strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "login.php")) {
                header("Location: login.php");
            }
        }
    } else {
        if (!strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "login.php")) {
            header("Location: login.php");
        } 
    }
} else {
    //Verifico la validez de la sesión
    $sqlLogin = "SELECT usuarios.* FROM usuarios 
                      WHERE username='" . $_SESSION['user']['username'] . "' AND password='" . $_SESSION['user']['password'] . "'";
    $resultLogin = mysqli_query($conn, $sqlLogin);
    if (mysqli_num_rows($resultLogin) !== 1) {
        header("Location: logout.php");
    }
}

// Change character set to utf8
mysqli_set_charset($conn, "utf8");
?>