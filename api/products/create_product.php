<?php
require("../../includes/config.php");


$message = [];
$isValid = true;
$errors = [];

if (!empty($_FILES)) {
	if (!strpos($_FILES['img']['type'], "jpeg") && !strpos($_FILES['img']['type'], "png") && !strpos($_FILES['img']['type'], "webp")) {
		$isValid = false;
		// La extension no esta permitida
		$errors['new__product-img'] = 'Debe ingresar una imagen de formato JPEG, PNG o WEBP.';
	}else if ($_FILES['img']['size'] > 5000000) {
		$isValid = false;
		// Excede el tamaño limite
		$errors['new__product-img'] = 'Debe ingresar una imagen de máximo 5MB.';
	}
} else {
	$isValid = false;
	$errors['new__product-img'] = 'Debe ingresar una imagen.';
}

$nombre = $_POST['name'];
$stock= (trim($_POST['stock']) != "") ? (int)$_POST['stock'] : null;
$precio= (trim($_POST['price']) != "") ? (int)$_POST['price'] : null;
$categoria = (int)$_POST['category'];
//Cuando intentas convertir un string a int en php, en caso de no ser posible siempre valdra 0
if($nombre == null) {
	$isValid = false;
	$errors['new__product-name'] = "Debe ingresar un nombre.";

} else if (strlen($nombre) > 50) {
	$isValid = false;
	$errors['new__product-name'] = "Debe ingresar un nombre menor a 50 caracteres.";
}

if ($stock == null || $stock < 0 || $stock > 2147483647) {
	$isValid = false;
	$errors['new__product-stock'] = "Debe ingresar una cantidad de stock válida.";

} 

if ($precio == null || $precio < 0 || $precio > 2147483647) {
	$isValid = false;
	$errors['new__product-price'] = "Debe ingresar un precio válido.";

} 

if ($categoria == 0) {
	$isValid = false;
	$errors['new__product-cat'] = "Debe seleccionar una categoría.";

}

if(!$isValid){
	$message['success'] = false;
	$message['msj'] = "Error al intentar crear producto";
	$message['errors'] = $errors;
 	return print_r(json_encode($message));
}


// Iniciar transacción
mysqli_begin_transaction($conn);

try {
	// Consulta preparada para la inserción del producto
	$sqlCreateProduct = "INSERT INTO productos (id_categoria, nombre, stock, precio, fecha_creacion) VALUES (?, ?, ?, ?, NOW())";
	$stmtCreateProduct = mysqli_prepare($conn, $sqlCreateProduct);
	mysqli_stmt_bind_param($stmtCreateProduct, "isii", $categoria, $nombre, $stock, $precio);
	$resultCreateProduct = mysqli_stmt_execute($stmtCreateProduct);

	if (!$resultCreateProduct) {
		$errors['general'] = "Error al insertar el producto";
		throw new Exception('Error al insertar producto');
	}
	// Obtener el ID del producto recién insertado
	$productID = mysqli_insert_id($conn);

	// Subir la foto
	if (!is_dir('../img/products/' . $productID)) {
		mkdir('../img/products/' . $productID);
	}

	$fileName = "illustrative_image." . explode('/', $_FILES['img']['type'])[1];

	if (!move_uploaded_file($_FILES['img']['tmp_name'], "../img/products/" . $productID . "/" . $fileName)) {
		$errors['new__product-img'] = "Error al intentar subir la imagen '$fileName'";
		throw new Exception('Error al subir imagen');
	}

	// Confirmar transacción
	mysqli_commit($conn);
	$message['success'] = true;
	$message['msj'] = "El producto fue creado correctamente";
	$message['errors'] = null;
	return print_r(json_encode($message));
	
} catch (Exception $e) {
	// En caso de error, revertir transacción
	mysqli_rollback($conn);
	$message['success'] = false;
	$message['msj'] = "Error al intentar crear producto";
	$message['errors'] = $errors;
	return print_r(json_encode($message));
}



