<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
$servername = "localhost";
$connectioninfo = array("Database" => "mundo-madera");
$conn = sqlsrv_connect($servername, $connectioninfo);

if(!$conn){
    echo "La conexion no se pudo establecer.<br />";
    die(print_r(sqlsrv_errors(), true));
}

?>