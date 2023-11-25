<?php

require("../../includes/config.php");
require("../../includes/functions.php");

header("Content-Type: application/json; charset=utf-8");


$message = [];
$productId = $_POST['id'];

// Consulta preparada para verificar si el producto ya está eliminado
$sqlCheckDeleted = "SELECT fecha_eliminacion FROM productos WHERE id = ?";
$stmtCheckDeleted = mysqli_prepare($conn, $sqlCheckDeleted);
mysqli_stmt_bind_param($stmtCheckDeleted, "i", $productId);
mysqli_stmt_execute($stmtCheckDeleted);
$resultCheckDeleted = mysqli_stmt_get_result($stmtCheckDeleted);
$rowCheckDeleted = mysqli_fetch_assoc($resultCheckDeleted);

if ($rowCheckDeleted['fecha_eliminacion'] !== null) {
	// El producto ya está eliminado, se procede a recuperarlo
	mysqli_begin_transaction($conn);

	// Consulta preparada para eliminar la fecha de eliminación del producto
	$sqlRecoverProduct = "UPDATE productos SET fecha_eliminacion = NULL WHERE id = ?";
	$stmtRecoverProduct = mysqli_prepare($conn, $sqlRecoverProduct);
	mysqli_stmt_bind_param($stmtRecoverProduct, "i", $productId);
	$resultRecoverProduct = mysqli_stmt_execute($stmtRecoverProduct);

	if ($resultRecoverProduct) {
		// Confirma la transacción si todo está bien
		mysqli_commit($conn);
		$message['success'] = true;
		$message['msj'] = "El producto fue recuperado correctamente";
		$message['action'] = "recover";
	} else {
		// Revierte la transacción si hay algún error
		mysqli_rollback($conn);
		$message['success'] = false;
		$message['msj'] = "Error al intentar recuperar el producto";
		$message['action'] = "recover";
	}

} else {
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
		$message['action'] = "delete";
	} else {
		// No hay pedidos actuales (no entregados) relacionados con el producto
		// Inicia la transacción
		mysqli_begin_transaction($conn);
	
		// Consulta preparada para actualizar la fecha de eliminación del producto
		$sqlUpdateProduct = "UPDATE productos SET fecha_eliminacion = NOW() WHERE id = ?";
		$stmtUpdateProduct = mysqli_prepare($conn, $sqlUpdateProduct);
		mysqli_stmt_bind_param($stmtUpdateProduct, "i", $productId);
		$resultUpdateProduct = mysqli_stmt_execute($stmtUpdateProduct);

		// Eliminar registros asociados en la tabla categoria_producto
		$sqlDeleteCategoriaProducto = "DELETE FROM categoria_producto WHERE id_producto = ?";
		$stmtDeleteCategoriaProducto = mysqli_prepare($conn, $sqlDeleteCategoriaProducto);
		mysqli_stmt_bind_param($stmtDeleteCategoriaProducto, "i", $productId);
		$resultDeleteCategoriaProducto = mysqli_stmt_execute($stmtDeleteCategoriaProducto);

		// Vaciar el directorio que contiene la imagen ilustrativa del producto
		deleteDirContents("../../img/products/" . $productId);
	
		if ($resultUpdateProduct) {
			// Confirma la transacción si todo está bien
			mysqli_commit($conn);
			$message['success'] = true;
			$message['msj'] = "El producto fue eliminado correctamente";
			$message['action'] = "delete";
		} else {
			// Revierte la transacción si hay algún error
			mysqli_rollback($conn);
			$message['success'] = false;
			$message['msj'] = "Error al intentar eliminar el producto";
			$message['action'] = "delete";
		}
	}
}


echo json_encode($message);

?>