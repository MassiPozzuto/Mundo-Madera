<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

$conn = mysqli_connect("localhost", "root", "", "mundo-madera");

if(!$conn){
    echo "La conexion no se pudo establecer.<br />";
    die(print_r(mysqli_connect_error()));
}

?>