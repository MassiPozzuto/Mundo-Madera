<?php

require("../includes/config.php");
require("../includes/functions.php");

header("Content-Type: application/json; charset=utf-8");


$message = [];
$productId = $_POST['id'];

// Consulta preparada para verificar si hay pedidos actuales (no entregados) relacionados con el producto
$sqlCheckOrders = "SELECT pedidos.* FROM pedidos
						INNER JOIN pedidos_productos ON pedidos_productos.id_pedido = pedidos.id
					WHERE pedidos_productos.id_producto = ? AND pedidos.fecha_entrega IS NULL";

$stmtCheckOrders = mysqli_prepare($conn, $sqlCheckOrders);
mysqli_stmt_bind_param($stmtCheckOrders, "i", $productId);
mysqli_stmt_execute($stmtCheckOrders);
$resultCheckOrders = mysqli_stmt_get_result($stmtCheckOrders);

if (mysqli_num_rows($resultCheckOrders) > 0) {
	// Hay pedidos actuales (no entregados) relacionados con el producto
	$message['success'] = false;
	$message['msj'] = "El producto aún tiene un pedido pendiente. Entregalo para poder eliminar el producto";
} else {
	// No hay pedidos actuales (no entregados) relacionados con el producto
	// Inicia la transacción
	mysqli_begin_transaction($conn);

	// Consulta preparada para actualizar la fecha de eliminación del producto
	$sqlUpdateProduct = "UPDATE productos SET fecha_eliminacion = NOW() WHERE id = ?";
	$stmtUpdateProduct = mysqli_prepare($conn, $sqlUpdateProduct);
	mysqli_stmt_bind_param($stmtUpdateProduct, "i", $productId);
	$resultUpdateProduct = mysqli_stmt_execute($stmtUpdateProduct);

	// Vaciar el directorio que contiene la imagen ilustrativa del producto
	deleteDirContents("../img/products/" . $productId);

	if ($resultUpdateProduct) {
		// Confirma la transacción si todo está bien
		mysqli_commit($conn);
		$message['success'] = true;
		$message['msj'] = "El producto fue eliminado correctamente";
	} else {
		// Revierte la transacción si hay algún error
		mysqli_rollback($conn);
		$message['success'] = false;
		$message['msj'] = "Error al intentar eliminar el producto";
	}
}

echo json_encode($message);

?>