<?php

require("../../includes/config.php");
require("../../includes/functions.php");

header("Content-Type: application/json; charset=utf-8");


$message = [];
$orderId = $_POST['id'];

// Consulta preparada para verificar si el pedido ya está eliminado
$sqlCheckDeliver = "SELECT * FROM pedidos WHERE id = ?";
$stmtCheckDeliver = mysqli_prepare($conn, $sqlCheckDeliver);
mysqli_stmt_bind_param($stmtCheckDeliver, "i", $orderId);
mysqli_stmt_execute($stmtCheckDeliver);
$resultCheckDeliver = mysqli_stmt_get_result($stmtCheckDeliver);
$rowCheckDeliver = mysqli_fetch_assoc($resultCheckDeliver);

if($rowCheckDeliver['fecha_cancelacion'] == null){
    // El pedido no esta cancelado
    mysqli_begin_transaction($conn);

    if ($rowCheckDeliver['fecha_entrega'] !== null) {
        // El pedido ya está eliminado, se procede a recuperarlo
        // Consulta preparada para actualizar la fecha de entrega y el estado del pedido
        $sqlRecoverOrder = "UPDATE pedidos SET fecha_entrega = NULL, id_estado = 2 WHERE id = ?";
        $stmtRecoverOrder = mysqli_prepare($conn, $sqlRecoverOrder);
        mysqli_stmt_bind_param($stmtRecoverOrder, "i", $orderId);
        $resultRecoverOrder = mysqli_stmt_execute($stmtRecoverOrder);
    
        // Consulta preparada para actualizar el estado del envio a 'Pendiente'
        $sqlRecoverDelivery = "UPDATE envios SET id_estado = 5 WHERE id_pedido = ?";
        $stmtRecoverDelivery = mysqli_prepare($conn, $sqlRecoverDelivery);
        mysqli_stmt_bind_param($stmtRecoverDelivery, "i", $orderId);
        $resultRecoverDelivery = mysqli_stmt_execute($stmtRecoverDelivery);
    
        if ($resultRecoverOrder && $resultRecoverDelivery) {
            // Confirma la transacción si todo está bien
            mysqli_commit($conn);
            $message['success'] = true;
            $message['msj'] = "El pedido fue recuperado correctamente";
            $message['action'] = "recover";
        } else {
            // Revierte la transacción si hay algún error
            mysqli_rollback($conn);
            $message['success'] = false;
            $message['msj'] = "Error al intentar recuperar el pedido";
            $message['action'] = "recover";
        }
    } else {    
        // El pedido NO está eliminado, se procede a eliminarlo
        // Consulta preparada para actualizar la fecha de entrega y el estado del pedido
        $sqlDeliverOrder = "UPDATE pedidos SET fecha_entrega = NOW(), id_estado = 7 WHERE id = ?";
        $stmtDeliverOrder = mysqli_prepare($conn, $sqlDeliverOrder);
        mysqli_stmt_bind_param($stmtDeliverOrder, "i", $orderId);
        $resultDeliverOrder = mysqli_stmt_execute($stmtDeliverOrder);
    
        // Consulta preparada para actualizar el estado del envio a 'Entregado'
        $sqlDeliverDelivery = "UPDATE envios SET id_estado = 7 WHERE id_pedido = ?";
        $stmtDeliverDelivery = mysqli_prepare($conn, $sqlDeliverDelivery);
        mysqli_stmt_bind_param($stmtDeliverDelivery, "i", $orderId);
        $resultDeliverDelivery = mysqli_stmt_execute($stmtDeliverDelivery);
    
        if ($resultDeliverOrder && $resultDeliverDelivery) {
            // Confirma la transacción si todo está bien
            mysqli_commit($conn);
            $message['success'] = true;
            $message['msj'] = "El pedido fue entregado correctamente";
            $message['action'] = "delivered";
        } else {
            // Revierte la transacción si hay algún error
            mysqli_rollback($conn);
            $message['success'] = false;
            $message['msj'] = "Error al intentar entregar el pedido";
            $message['action'] = "delivered";
        }
    }
} else {
    //Pedido cancelado
    $message['success'] = false;
    $message['msj'] = "No puedes entregar/recuperar un pedido cancelado";
    $message['action'] = "none";
}


echo json_encode($message);
