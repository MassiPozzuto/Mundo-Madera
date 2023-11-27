<?php
require("../../includes/config.php");


$message = [];
$isValid = true;
$errors = [];


$name = $_POST['name'];
$surname = $_POST['surname'];
$dni = (trim($_POST['dni']) != "") ? (int)$_POST['dni'] : null;
$tel = (trim($_POST['tel']) != "") ? (int)$_POST['tel'] : null;
$state = $_POST['state'];
$delivery = filter_var($_POST['delivery'], FILTER_VALIDATE_BOOLEAN);
$province = $_POST['province'];
$city = $_POST['city'];
$address = $_POST['address'];
$products = !empty($_POST['products']) ?  json_decode($_POST['products'], true) : [];


if ($name == null) {
    $isValid = false;
    $errors['create-name'] = "Debe ingresar un nombre.";
} else if (strlen($name) > 35) {
    $isValid = false;
    $errors['create-name'] = "Debe ingresar un nombre menor a 35 caracteres.";
}

if ($surname == null) {
    $isValid = false;
    $errors['create-surname'] = "Debe ingresar un apellido.";
} else if (strlen($surname) > 35) {
    $isValid = false;
    $errors['create-surname'] = "Debe ingresar un apellido menor a 35 caracteres.";
}

if (!preg_match('/^\d{7,8}$/', $dni)) {
    $isValid = false;
    $errors['create-dni'] = "Debe ingresar un DNI válido.";
}

if (!preg_match('/^\d{8,}$/', $tel)) {
    $isValid = false;
    $errors['create-tel'] = "Debe ingresar un telefono válido.";
}

if($state == 0 || $state == null) {
    $isValid = false;
    $errors['create-state'] = "Debe seleccionar un estado para el pedido.";
}

if($delivery == true){
    if ($province == 0 || $province == null) {
        $isValid = false;
        $errors['create-province'] = "Debe seleccionar una provincia para el envio.";
    }

    if ($city == null) {
        $isValid = false;
        $errors['create-city'] = "Debe ingresar una ciudad para el envio.";
    } else if (strlen($city) > 50) {
        $isValid = false;
        $errors['create-city'] = "Debe ingresar una ciudad menor a 50 caracteres.";
    }

    if ($address == null) {
        $isValid = false;
        $errors['create-address'] = "Debe ingresar una direccion de envio.";
    } else if (strlen($address) > 50) {
        $isValid = false;
        $errors['create-address'] = "Debe ingresar una direccion menor a 50 caracteres.";
    }
}

if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear producto";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}


// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la inserción del producto
    $sqlCreateProduct = "INSERT INTO pedidos (id_estado, dni, nombre, apellido, telefono, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmtCreateProduct = mysqli_prepare($conn, $sqlCreateProduct);
    mysqli_stmt_bind_param($stmtCreateProduct, "iissi", $state, $dni, $name, $surname, $tel);
    $resultCreateProduct = mysqli_stmt_execute($stmtCreateProduct);

    if (!$resultCreateProduct) {
        $errors['general'] = "Error al insertar el pedido";
        throw new Exception('Error al insertar pedido');
    }
    // Obtener el ID del producto recién insertado
    $orderID = mysqli_insert_id($conn);

    // Insertar las categorías en la tabla categoria_producto
    foreach ($products as $product) {
        $sqlInsertCategoriaProducto = "INSERT INTO pedido_producto (id_pedido, id_producto, cantidad) VALUES (?, ?, ?)";
        $stmtInsertCategoriaProducto = mysqli_prepare($conn, $sqlInsertCategoriaProducto);
        mysqli_stmt_bind_param($stmtInsertCategoriaProducto, "iii", $orderID, $product['id'], $product['cantidad']);
        $resultInsertCategoriaProducto = mysqli_stmt_execute($stmtInsertCategoriaProducto);

        if (!$resultInsertCategoriaProducto) {
            $errors['general'] = "Error al insertar productos del pedido";
            throw new Exception('Error al insertar productos del pedido');
        }
    }

    if($delivery == true) {
        $sqlInsertDelivery = "INSERT INTO envios (id_pedido, id_provincia, direccion, ciudad) VALUES (?, ?, ?, ?)";
        $stmtInsertDelivery = mysqli_prepare($conn, $sqlInsertDelivery);
        mysqli_stmt_bind_param($stmtInsertDelivery, "iiss", $orderID, $province, $address, $city);
        $resultInsertDelivery = mysqli_stmt_execute($stmtInsertDelivery);

        if (!$resultInsertDelivery) {
            $errors['general'] = "Error al intentar insertar el envio";
            throw new Exception('Error al intentar insertar el envio');
        }
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "El pedido fue creado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));
} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear el pedido";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
