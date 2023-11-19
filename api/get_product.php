<?php

require("../includes/config.php");

header("Content-Type: application/json; charset=utf-8");

$response=new stdClass();

$id = $_POST['codpro'];
$sql = "SELECT * FROM productos WHERE id=$id";
$stmt = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($stmt);
$row['rutaImg'] = productImgPath($id);
$response = $row;

echo json_encode($response);



function productImgPath($productId)
{
	$dir = "../img/products/{$productId}";
	// Verifica si el directorio existe
	if (!is_dir($dir)) {
		return null;
	}

	// Escanea el directorio en busca de archivos
	$archives = scandir($dir);
	// Itera sobre los archivos para encontrar la primera imagen
	foreach ($archives as $archive) {
		// Ignora los directorios y archivos que no son imágenes
		if (!is_dir($archive)) {
			return "$dir/$archive";
		}
	}
	// Si no se encuentra ninguna imagen, devuelve un mensaje o un valor predeterminado
	return null;
}
