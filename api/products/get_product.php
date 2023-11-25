<?php

require("../../includes/config.php");
require("../../includes/functions.php");

header("Content-Type: application/json; charset=utf-8");

$message = [];

// Consulta preparada para obtener datos del producto
$sql = "SELECT p.*, GROUP_CONCAT(c.id) AS categorias 
        FROM productos p
        LEFT JOIN categoria_producto cp ON p.id = cp.id_producto
        LEFT JOIN categorias c ON cp.id_categoria = c.id
        WHERE p.id = ?
        GROUP BY p.id";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_POST['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Verifica si el producto fue encontrado
if ($row) {
	// Agrega la ruta de la imagen al resultado
	$row['rutaImg'] = productImgPath($_POST['id'], "../../");
	// Convierte la cadena de categorías en un array
	$row['categorias'] = explode(',', $row['categorias']);


	$message['success'] = true;
	$message['product'] = $row;
} else {
	$message['success'] = false;
	$message['error'] = "No se pudo encontrar el producto. Intentelo más tarde";
}

echo json_encode($message);