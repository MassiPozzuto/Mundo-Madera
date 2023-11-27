<?php

require("../../includes/config.php");

header("Content-Type: application/json; charset=utf-8");

$message = [];

// Consulta preparada para obtener datos del pedido
$sql = "SELECT 
            p.id AS id_pedido,
            p.id_estado,
            p.dni,
            p.nombre,
            p.apellido,
            p.telefono,
            p.fecha_creacion,
            p.fecha_entrega,
            GROUP_CONCAT(CONCAT(pp.id_producto, ':', pp.cantidad) SEPARATOR ',') AS productos,
            e.id AS id_envio,
            e.id_provincia,
            e.direccion,
            e.ciudad,
            e.id_estado AS id_estado_envio
        FROM pedidos p
        LEFT JOIN pedido_producto pp ON p.id = pp.id_pedido
        LEFT JOIN envios e ON p.id = e.id_pedido
        WHERE p.id = ?
        GROUP BY p.id;";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_POST['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Verifica si el pedido fue encontrado
if ($row) {
    // Agrega la ruta de la imagen al resultado
    $message['success'] = true;
    $message['order'] = $row;
} else {
    $message['success'] = false;
    $message['error'] = "No se pudo encontrar el pedido. Intentelo más tarde";
}

echo json_encode($message);
