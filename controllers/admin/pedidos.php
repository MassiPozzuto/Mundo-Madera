<?php
require('../../includes/config.php');

if (!isset($_GET['allowAll'])) {
    $_GET['allowAll'] = 'no';
}
if ($_GET['allowAll'] == 'yes') {
    $conditionDeleted = "";
} else {
    $conditionDeleted = "AND pe.fecha_entrega IS NULL AND pe.fecha_cancelacion IS NULL";
}


if (!isset($_GET['filterBy'])) {
    $_GET['filterBy'] = 'all';
}
if ($_GET['filterBy'] != 'all') {
    $conditionFilterBy = "AND  pe.id IN (
                            SELECT pp.id_pedido
                            FROM pedido_producto pp
                            WHERE pp.id_producto = ?
                        )";
} else {
    $conditionFilterBy = "";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    if (isset($_GET['for']) && $_GET['for'] == 'id') {
        $conditionSearch = "AND pe.id = ?";
    } else {
        $conditionSearch = "AND (pe.id = ? OR pe.nombre LIKE ? OR pe.dni LIKE ? OR pe.telefono LIKE ?)";
    }
} else {
    $conditionSearch = "";
}

// Obtener el total de registros para calcular el número de páginas
$total_registros_stmt = mysqli_prepare($conn, "SELECT COUNT(DISTINCT pe.id) as total
                                                FROM pedidos pe
                                                INNER JOIN pedido_producto pp ON pp.id_pedido = pe.id
                                                INNER JOIN productos p ON pe.id = pp.id_producto
                                                INNER JOIN estados e ON pe.id_estado = e.id
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
        $params[] = $_GET['search'];
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
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


// Consulta para obtener todas las categorías
$sqlProducts = "SELECT * FROM productos WHERE productos.fecha_eliminacion IS NULL";
$resultProducts = mysqli_query($conn, $sqlProducts);
$products = mysqli_fetch_all($resultProducts, MYSQLI_ASSOC);

// Consulta para obtener todas las provincias
$sqlStates= "SELECT * FROM estados WHERE para != 'envio'";
$resultStates = mysqli_query($conn, $sqlStates);
$states = mysqli_fetch_all($resultStates, MYSQLI_ASSOC);

// Consulta para obtener todas las provincias
$sqlProvinces = "SELECT * FROM provincias";
$resultProvinces = mysqli_query($conn, $sqlProvinces);
$provinces = mysqli_fetch_all($resultProvinces, MYSQLI_ASSOC);

// Consulta para obtener los productos deseados
$sqlOrders = "SELECT 
                pe.id,
                e.descripcion AS estado,
                env.id AS id_envio,
                GROUP_CONCAT(p.nombre, ':', pp.cantidad, ':', p.id) AS productos,
                CONCAT(pe.nombre, ' ', pe.apellido) AS nombre_completo,
                pe.dni,
                pe.telefono,
                pe.fecha_creacion,
                pe.fecha_entrega,
                pe.fecha_cancelacion
            FROM pedidos pe
            INNER JOIN estados e ON pe.id_estado = e.id
            INNER JOIN pedido_producto pp ON pe.id = pp.id_pedido
            INNER JOIN productos p ON pp.id_producto = p.id
            LEFT JOIN envios env ON pe.id = env.id_pedido
            WHERE 1 {$conditionDeleted} {$conditionFilterBy} {$conditionSearch}
            GROUP BY pe.id
            LIMIT ?, " . CANT_REG_PAG;

$stmtOrders = mysqli_prepare($conn, $sqlOrders);
$offset = CANT_REG_PAG * ($page - 1);
// Agrego el parametro del LIMIT, ya que los demas estan definidos antes (33-45)
$paramTypes .= "i";
$params[] = $offset;

// Agregar los parámetros o el parametro en la llamada a mysqli_stmt_bind_param
mysqli_stmt_bind_param($stmtOrders, $paramTypes, ...$params);

mysqli_stmt_execute($stmtOrders);
$resultOrders = mysqli_stmt_get_result($stmtOrders);
$rowOrders = mysqli_fetch_all($resultOrders, MYSQLI_ASSOC);

foreach($rowOrders as $key => $order){
    $rowOrders[$key]['productos'] = explode(",", $order['productos']);

    foreach($rowOrders[$key]['productos'] as $key2 => $orderProduct){
        $aux = explode(":", $orderProduct);
        $orderProduct = "<a href='productos.php?search=" . $aux[2] . "&for=id&allowAll=yes'>" . $aux[0] . ":</a> " . $aux[1];
        $rowOrders[$key]['productos'][$key2] = $orderProduct;
    }
}

$title = "Pedidos";
$section = "pedidos";
require_once "../../views/admin/layout.php";
?>