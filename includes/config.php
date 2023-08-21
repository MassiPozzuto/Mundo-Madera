<?php
$servername = "LABESTIA\MARIAPC";
$connectioninfo = array("Database" => "mundo-madera");
$conn = sqlsrv_connect($servername, $connectioninfo);

if($conn){
    echo "Conexion establecida.<br />";
}else{
    echo "La conexion no se pudo establecer.<br />";
    die(print_r(sqlsrv_errors(), true));
}

?>