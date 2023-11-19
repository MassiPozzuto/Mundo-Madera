<?php

require("../includes/config.php");

header("Content-Type: application/json; charset=utf-8");

$response = new stdClass();


$sql = "SELECT pedidos.* FROM pedidos
		INNER JOIN pedidos_productos ON pedidos_productos.id_pedido = pedidos.id
		WHERE pedidos_productos.id_producto = " . $_POST['id'] . "  AND pedidos.fecha_entrega IS NULL";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	//Hay pedidos actuales (no entregados) relacionados con el producto
	//NO SE QUE HACER, LO DEJO ELIMINARLO IGUAL AVISANDOLE O NO LO DEJO ELIMINARLO? 
	$response->msj = "El producto aun tiene un pedido pendiente";
	$response->state = false;
	//$response->status = http_response_code(500);

} else {
	//No hay pedidos actuales (no entregados) relacionados con el producto
	$sql = "UPDATE productos SET fecha_eliminacion = NOW() WHERE id = " . $_POST['id'];
	$result = mysqli_query($conn, $sql);
	if ($result) {
		//Eliminar directorio que contiene la imagen ilustrativa de dicho producto
		deleteDir("../img/products/" . $_POST['id']);

		$response->msj = "El producto fue eliminado correctamente";
		$response->state=true;
		//$response->status = http_response_code(200);
	} else {
		$response->msj = "Error al intentar eliminar el producto";
		$response->state = false;
		//$response->status = http_response_code(500);
	}
}

print_r(json_encode($response));


function deleteDir($dir)
{
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
					deleteDir($dir . DIRECTORY_SEPARATOR . $object);
				else
					unlink($dir . DIRECTORY_SEPARATOR . $object);
			}
		}
		rmdir($dir);
	}
}