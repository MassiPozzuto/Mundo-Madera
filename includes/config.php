<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

define('CANT_REG_PAG', 5);

$conn = mysqli_connect("localhost", "root", "", "mundo-madera");
if(!$conn){
    echo "La conexion no se pudo establecer.<br />";
    die(print_r(mysqli_connect_error()));
}

session_start();
if(!isset($_SESSION['user'])){
    if (isset($_COOKIE['username']) || isset($_COOKIE['password'])) {
        $sqlLogin = "SELECT usuarios.* FROM usuarios 
                      WHERE usuarios.username='" . $_COOKIE['username'] . "' AND usuarios.password='" . $_COOKIE['password'] . "' AND usuarios.fecha_eliminacion IS NULL";
        $resultLogin = mysqli_query($conn, $sqlLogin);
        if (mysqli_num_rows($resultLogin) === 1) {
            $_SESSION['user'] = mysqli_fetch_assoc($resultLogin);
        }
    } else {
        if (!strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "login.php")) {
            header("Location: login.php");
        } 
    }

}

// Change character set to utf8
mysqli_set_charset($conn, "utf8");
?>