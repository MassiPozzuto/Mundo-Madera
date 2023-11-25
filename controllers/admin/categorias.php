<?php
require('../../includes/config.php');

if (!isset($_GET['allowDeleted'])) {
    $_GET['allowDeleted'] = 'no';
}
if ($_GET['allowDeleted'] == 'yes') {
    $conditionDeleted = "";
} else {
    $conditionDeleted = "AND c.fecha_eliminacion IS NULL";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $conditionSearch = "AND c.nombre LIKE ?";
} else {
    $conditionSearch = "";
}

// Obtener el total de registros para calcular el número de páginas
$total_registros_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM categorias c WHERE 1 {$conditionDeleted} {$conditionSearch}");

// Definir los parámetros y sus tipos
$paramTypes = "";

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



if (isset($_GET['page']) && $_GET['page'] > 1) {
    if ($_GET['page'] > $total_paginas) {
        $_GET['page'] = $total_paginas;
    }
} else {
    $_GET['page'] = 1;
}
$page = $_GET['page'];


// Consulta para obtener los productos deseados
$sqlCategories = "SELECT c.*, COUNT(DISTINCT p.id) AS related_products, SUM(p.stock) AS stock_related_products FROM categorias c
                LEFT JOIN categoria_producto cp ON cp.id_categoria = c.id
                LEFT JOIN productos p ON p.id = cp.id_producto
                WHERE p.fecha_eliminacion IS NULL
                {$conditionDeleted} {$conditionSearch}
                GROUP BY c.id
                LIMIT ?, " . CANT_REG_PAG;
                
$stmtCategories = mysqli_prepare($conn, $sqlCategories);
$offset = CANT_REG_PAG * ($page - 1);
// Agrego el parametro del LIMIT, ya que los demas estan definidos antes (33-45)
$paramTypes .= "i";
$params[] = $offset;

// Agregar los parámetros o el parametro en la llamada a mysqli_stmt_bind_param
mysqli_stmt_bind_param($stmtCategories, $paramTypes, ...$params);

mysqli_stmt_execute($stmtCategories);
$resultCategories = mysqli_stmt_get_result($stmtCategories);
$rowCategories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);

$title = "Categorias";
$section = "categorias";
require_once "../../views/admin/layout.php";
?>