<?php
require('../../includes/config.php');

// Obtener el total de registros para calcular el número de páginas
$total_registros = mysqli_query($conn, "SELECT COUNT(*) as total FROM productos WHERE productos.fecha_eliminacion IS NULL");
$total_registros = mysqli_fetch_assoc($total_registros)['total'];
$total_paginas = ceil($total_registros / CANT_REG_PAG);

if(isset($_GET['page']) && $_GET['page'] > 1){
    if($_GET['page'] > $total_paginas){
        $page = $total_paginas;
    } else {
        $page = $_GET['page'];
    }
} else {
    $page = 1;
}


$sqlProducts = "SELECT p.*, c.tipo 
                FROM productos p
                INNER JOIN categorias c ON c.id = p.id_categoria
                WHERE p.fecha_eliminacion IS NULL
                LIMIT ?, " . CANT_REG_PAG;
$stmtProducts = mysqli_prepare($conn, $sqlProducts);
$offset = CANT_REG_PAG * ($page - 1);
mysqli_stmt_bind_param($stmtProducts, "i", $offset);
mysqli_stmt_execute($stmtProducts);
$resultProducts = mysqli_stmt_get_result($stmtProducts);
$rowProducts = mysqli_fetch_all($resultProducts, MYSQLI_ASSOC);

$title = "Productos";
$section = "productos";
require_once "../../views/admin/layout.php";