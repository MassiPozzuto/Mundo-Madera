<?php
require('../../includes/config.php');


if (isset($_GET['search']) && !empty($_GET['search'])) {
    $conditionSearch = "AND (u.username LIKE ? OR u.nombre LIKE ? OR u.appellido LIKE ?)";
} else {
    $conditionSearch = "";
}

// Obtener el total de registros para calcular el número de páginas
$total_registros_stmt = mysqli_prepare($conn, "SELECT COUNT(DISTINCT u.id) as total
                                               FROM usuarios u
                                               WHERE 1 {$conditionSearch}");

// Definir los parámetros y sus tipos
$paramTypes = "";
if (!empty($_GET['search'])) {
    $paramTypes .= "sss";
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
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
$sqlUsers = "SELECT * FROM usuarios 
            WHERE 1 {$conditionSearch}
            LIMIT ?, " . CANT_REG_PAG;

$stmtUsers = mysqli_prepare($conn, $sqlUsers);
$offset = CANT_REG_PAG * ($page - 1);
// Agrego el parametro del LIMIT, ya que los demas estan definidos antes (33-45)
$paramTypes .= "i";
$params[] = $offset;

// Agregar los parámetros o el parametro en la llamada a mysqli_stmt_bind_param
mysqli_stmt_bind_param($stmtUsers, $paramTypes, ...$params);

mysqli_stmt_execute($stmtUsers);
$resultUsers = mysqli_stmt_get_result($stmtUsers);
$rowUsers = mysqli_fetch_all($resultUsers, MYSQLI_ASSOC);

$title = "Usuarios";
$section = "usuarios";
require_once "../../views/admin/layout.php";
?>