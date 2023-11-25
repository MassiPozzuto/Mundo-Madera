<?php
require("../../includes/config.php");
require("../../includes/functions.php");


$message = [];
$isValid = true;
$errors = [];

if (!empty($_FILES)) {
	//CAMBIO la imagen ilustrativa
	if (!strpos($_FILES['img']['type'], "jpeg") && !strpos($_FILES['img']['type'], "png") && !strpos($_FILES['img']['type'], "webp")) {
		$isValid = false;
		// La extension no esta permitida
		$errors['update-img'] = 'Debe ingresar una imagen de formato JPEG, PNG o WEBP.';
	} else if ($_FILES['img']['size'] > 5000000) {
		$isValid = false;
		// Excede el tamaño limite
		$errors['update-img'] = 'Debe ingresar una imagen de máximo 5MB.';
	}
}

$id = $_POST['id'];
$nombre = $_POST['name'];
$stock = (trim($_POST['stock']) != "") ? (int)$_POST['stock'] : null;
$precio = (trim($_POST['price']) != "") ? (int)$_POST['price'] : null;
$categorias = !empty($_POST['categories']) ? json_decode($_POST['categories'], true) : [];

//Cuando intentas convertir un string a int en php, en caso de no ser posible siempre valdra 0
if ($nombre == null) {
	$isValid = false;
	$errors['update-name'] = "Debe ingresar un nombre.";
} else if (strlen($nombre) > 50) {
	$isValid = false;
	$errors['update-name'] = "Debe ingresar un nombre menor a 50 caracteres.";
}

if ($stock < 0|| $stock > 2147483647) {
	$isValid = false;
	$errors['update-stock'] = "Debe ingresar una cantidad de stock válida.";
}

if ($precio < 0 || $precio > 2147483647) {
	$isValid = false;
	$errors['update-price'] = "Debe ingresar un precio válido.";
}

/*if ($categoria == 0) {
	$isValid = false;
	$errors['update-cat'] = "Debe seleccionar una categoría.";
}*/

if (!$isValid) {
	$message['success'] = false;
	$message['msj'] = "Error al intentar editar producto";
	$message['errors'] = $errors;
	return print_r(json_encode($message));
}


// Iniciar transacción
mysqli_begin_transaction($conn);

try {
	// Consulta preparada para la inserción del producto
	$sqlUpdateProduct = "UPDATE productos SET nombre = ?, stock = ?, precio = ? WHERE id = ?";
	$stmtUpdateProduct = mysqli_prepare($conn, $sqlUpdateProduct);
	mysqli_stmt_bind_param($stmtUpdateProduct, "siii", $nombre, $stock, $precio, $id);
	$resultUpdateProduct = mysqli_stmt_execute($stmtUpdateProduct);
	if (!$resultUpdateProduct) {
		$errors['general'] = "Error al editar el producto";
		throw new Exception('Error al editar producto');
	}

	// Eliminar todas las categorías existentes del producto
	$sqlDeleteCategorias = "DELETE FROM categoria_producto WHERE id_producto = ?";
	$stmtDeleteCategorias = mysqli_prepare($conn, $sqlDeleteCategorias);
	mysqli_stmt_bind_param($stmtDeleteCategorias, "i", $id);
	$resultDeleteCategorias = mysqli_stmt_execute($stmtDeleteCategorias);
	if (!$resultDeleteCategorias) {
		$errors['general'] = "Error al actualizar las categorías del producto";
		throw new Exception('Error al actualizar las categorías del producto');
	}
	// Insertar las categorías en la tabla categoria_producto
	if (!empty($categorias)) {
		foreach ($categorias as $categoriaID) {
			$sqlInsertCategoriaProducto = "INSERT INTO categoria_producto (id_categoria, id_producto) VALUES (?, ?)";
			$stmtInsertCategoriaProducto = mysqli_prepare($conn, $sqlInsertCategoriaProducto);
			mysqli_stmt_bind_param($stmtInsertCategoriaProducto, "ii", $categoriaID, $id);
			$resultInsertCategoriaProducto = mysqli_stmt_execute($stmtInsertCategoriaProducto);

			if (!$resultInsertCategoriaProducto) {
				$errors['general'] = "Error al insertar categorías del producto";
				throw new Exception('Error al insertar categorías del producto');
			}
		}
	}

	if (!empty($_FILES)) {
		// Subir la foto
		$dir = '../../img/products/' . $id;
		if (!is_dir($dir)) {
			mkdir($dir);
		} else {
			deleteDirContents($dir);
		}

		$fileName = "illustrative_image." . explode('/', $_FILES['img']['type'])[1];
		if (!move_uploaded_file($_FILES['img']['tmp_name'],    $dir . "/" . $fileName)) {
			$errors['update-img'] = "Error al intentar actualizar la imagen '$fileName'";
			throw new Exception('Error al actualizar imagen');
		}
	}

	// Confirmar transacción
	mysqli_commit($conn);
	$message['success'] = true;
	$message['msj'] = "El producto fue editado correctamente";
	$message['errors'] = null;
	return print_r(json_encode($message));
} catch (Exception $e) {
	// En caso de error, revertir transacción
	mysqli_rollback($conn);
	$message['success'] = false;
	$message['msj'] = "Error al intentar editar producto";
	$message['errors'] = $errors;
	return print_r(json_encode($message));
}
