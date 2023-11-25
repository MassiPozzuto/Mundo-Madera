<?php
require('../../includes/config.php');

if (!isset($_GET['allowDeleted'])) {
    $_GET['allowDeleted'] = 'no';
}
if ($_GET['allowDeleted'] == 'yes') {
    $conditionDeleted = "";
} else {
    $conditionDeleted = "AND p.fecha_eliminacion IS NULL";
}


if (!isset($_GET['filterBy'])) {
    $_GET['filterBy'] = 'all';
}
if($_GET['filterBy'] != 'all'){
    $conditionFilterBy = "AND  p.id IN (
                            SELECT cp.id_producto
                            FROM categoria_producto cp
                            WHERE cp.id_categoria = ?
                        )";
} else {
    $conditionFilterBy = "";
}

if(isset($_GET['search']) && !empty($_GET['search'])){
    $conditionSearch = "AND p.nombre LIKE ?";
} else {
    $conditionSearch = "";
}

// Obtener el total de registros para calcular el número de páginas
$total_registros_stmt = mysqli_prepare($conn, "SELECT COUNT(DISTINCT p.id) as total
                                               FROM productos p
                                               LEFT JOIN categoria_producto cp ON cp.id_producto = p.id
                                               LEFT JOIN categorias c ON c.id = cp.id_categoria
                                               WHERE 1 {$conditionDeleted} {$conditionFilterBy} {$conditionSearch}");

// Definir los parámetros y sus tipos
$paramTypes = "";
if ($_GET['filterBy'] != 'all') {
    $paramTypes .= "i";
    $params[] = $_GET['filterBy'];
}
if (!empty($_GET['search'])) {
    $paramTypes .= "s";
    $params[] = '%' . $_GET['search'] . '%';
}
// Agregar los parámetros en la llamada a mysqli_stmt_bind_param si es necesario
if (!empty($params)) {
    mysqli_stmt_bind_param($total_registros_stmt, $paramTypes, ...$params);
}

mysqli_stmt_execute($total_registros_stmt);
$total_registros_result = mysqli_stmt_get_result($total_registros_stmt);
$total_registros = mysqli_fetch_assoc($total_registros_result)['total'];
$total_paginas = ceil($total_registros / CANT_REG_PAG);

if(isset($_GET['page']) && $_GET['page'] > 1){
    if($_GET['page'] > $total_paginas){
        $_GET['page'] = $total_paginas;
    } 
} else {
    $_GET['page'] = 1;
}
$page = $_GET['page'];


// Consulta para obtener todas las categorías
$sqlCategories = "SELECT * FROM categorias WHERE categorias.fecha_eliminacion IS NULL";
$resultCategories = mysqli_query($conn, $sqlCategories);
$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);


// Consulta para obtener los productos deseados
$sqlProducts = "SELECT p.*, GROUP_CONCAT(c.nombre) AS categorias
                FROM productos p
                LEFT JOIN categoria_producto cp ON cp.id_producto = p.id 
                LEFT JOIN categorias c ON c.id = cp.id_categoria 
                WHERE 1 {$conditionDeleted} {$conditionFilterBy} {$conditionSearch}
                GROUP BY p.id
                LIMIT ?, " . CANT_REG_PAG;

$stmtProducts = mysqli_prepare($conn, $sqlProducts);
$offset = CANT_REG_PAG * ($page - 1);
// Agrego el parametro del LIMIT, ya que los demas estan definidos antes (33-45)
$paramTypes .= "i";
$params[] = $offset;

// Agregar los parámetros o el parametro en la llamada a mysqli_stmt_bind_param
mysqli_stmt_bind_param($stmtProducts, $paramTypes, ...$params);

mysqli_stmt_execute($stmtProducts);
$resultProducts = mysqli_stmt_get_result($stmtProducts);
$rowProducts = mysqli_fetch_all($resultProducts, MYSQLI_ASSOC);

$title = "Productos";
$section = "productos";
require_once "../../views/admin/layout.php";