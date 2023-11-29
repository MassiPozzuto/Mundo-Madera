<?php
require('../../includes/config.php');

if (!isset($_GET['filterBy'])) {
    $_GET['filterBy'] = 'all';
}
if ($_GET['filterBy'] != 'all') {
    $conditionFilterBy = "AND  envios.id_estado = ?";
    if ($_GET['filterBy'] == 7) {
        $_GET['allowAll'] = 'yes';
    }
} else {
    $conditionFilterBy = "";
}

if (!isset($_GET['allowAll'])) {
    $_GET['allowAll'] = 'no';
}
if ($_GET['allowAll'] == 'yes') {
    $conditionDeleted = "";
} else {
    $conditionDeleted = "AND pedidos.fecha_entrega IS NULL";
}


if (isset($_GET['search']) && !empty($_GET['search'])) {
    if (isset($_GET['for']) && $_GET['for'] == 'id') {
        $conditionSearch = "AND envios.id = ?";
    } else {
        $conditionSearch = "AND (estados_envio.descripcion LIKE ? OR pedidos.id = ? OR envios.id = ? OR envios.direccion LIKE ?)";
    }
} else {
    $conditionSearch = "";
}


// Obtener el total de registros para calcular el número de páginas
$total_registros_stmt = mysqli_prepare($conn, "SELECT COUNT(DISTINCT envios.id) as total
                                                FROM envios
                                                JOIN estados AS estados_envio ON estados_envio.id = envios.id_estado
                                                LEFT JOIN pedidos ON pedidos.id = envios.id_pedido
                                                LEFT JOIN pedido_producto ON pedido_producto.id_pedido = pedidos.id
                                               WHERE 1 {$conditionDeleted} {$conditionFilterBy} {$conditionSearch}");

                                
// Definir los parámetros y sus tipos
$paramTypes = "";
if ($_GET['filterBy'] != 'all') {
    $paramTypes .= "i";
    $params[] = $_GET['filterBy'];
}
if (!empty($_GET['search'])) {
    if (isset($_GET['for']) && $_GET['for'] == 'id') {
        $paramTypes .= "s";
        $params[] = $_GET['search'];
    } else {
        $paramTypes .= "ssss";
        $params[] = '%'. $_GET['search'] . '%';
        $params[] = $_GET['search'];
        $params[] = $_GET['search'];
        $params[] = '%' . $_GET['search'] . '%';
    }
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


// Consulta para obtener todas las provincias
$sqlStates= "SELECT * FROM estados WHERE para != 'pedido'";
$resultStates = mysqli_query($conn, $sqlStates);
$states = mysqli_fetch_all($resultStates, MYSQLI_ASSOC);

// Consulta para obtener todas las provincias
$sqlProvinces = "SELECT * FROM provincias";
$resultProvinces = mysqli_query($conn, $sqlProvinces);
$provinces = mysqli_fetch_all($resultProvinces, MYSQLI_ASSOC);

// Consulta para obtener los productos deseados
$sqlDeliveries = "SELECT
                envios.id AS id_envio,
                pedidos.id AS id_pedido,
                estados_pedido.descripcion AS estado_pedido,
                estados_envio.id AS id_estado_envio,
                estados_envio.descripcion AS estado_envio,
                provincias.nombre AS provincia,
                envios.direccion,
                envios.ciudad,
                pedidos.fecha_creacion
            FROM envios
            JOIN pedidos ON envios.id_pedido = pedidos.id
            JOIN estados AS estados_pedido ON pedidos.id_estado = estados_pedido.id
            JOIN estados AS estados_envio ON envios.id_estado = estados_envio.id
            JOIN provincias ON envios.id_provincia = provincias.id
            WHERE 1 {$conditionDeleted} {$conditionFilterBy} {$conditionSearch}
            GROUP BY envios.id
            LIMIT ?, " . CANT_REG_PAG;


$stmtDeliveries = mysqli_prepare($conn, $sqlDeliveries);
$offset = CANT_REG_PAG * ($page - 1);
// Agrego el parametro del LIMIT, ya que los demas estan definidos antes (33-45)
$paramTypes .= "i";
$params[] = $offset;

// Agregar los parámetros o el parametro en la llamada a mysqli_stmt_bind_param
mysqli_stmt_bind_param($stmtDeliveries, $paramTypes, ...$params);

mysqli_stmt_execute($stmtDeliveries);
$resultDeliveries = mysqli_stmt_get_result($stmtDeliveries);
$rowDeliveries = mysqli_fetch_all($resultDeliveries, MYSQLI_ASSOC);


$title = "Envios";
$section = "envios";
require_once "../../views/admin/layout.php";
?>