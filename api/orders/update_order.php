<?php
require("../../includes/config.php");


$message = [];
$isValid = true;
$errors = [];


$id = $_POST['id'];
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

if ($id == null) {
    $isValid = false;
    $errors['general'] = "El ID de la orden no puede estar vacío.";
}

if ($name == null) {
    $isValid = false;
    $errors['update-name'] = "Debe ingresar un nombre.";
} else if (strlen($name) > 35) {
    $isValid = false;
    $errors['update-name'] = "Debe ingresar un nombre menor a 35 caracteres.";
}

if ($surname == null) {
    $isValid = false;
    $errors['update-surname'] = "Debe ingresar un apellido.";
} else if (strlen($surname) > 35) {
    $isValid = false;
    $errors['update-surname'] = "Debe ingresar un apellido menor a 35 caracteres.";
}

if (!preg_match('/^\d{7,8}$/', $dni)) {
    $isValid = false;
    $errors['update-dni'] = "Debe ingresar un DNI válido.";
}

if (!preg_match('/^\d{8,}$/', $tel)) {
    $isValid = false;
    $errors['update-tel'] = "Debe ingresar un telefono válido.";
}

if ($state == 0 || $state == null) {
    $isValid = false;
    $errors['update-state'] = "Debe seleccionar un estado para el pedido.";
}

if ($delivery == true) {
    if ($province == 0 || $province == null) {
        $isValid = false;
        $errors['update-province'] = "Debe seleccionar una provincia para el envio.";
    }

    if ($city == null) {
        $isValid = false;
        $errors['update-city'] = "Debe ingresar una ciudad para el envio.";
    } else if (strlen($city) > 50) {
        $isValid = false;
        $errors['update-city'] = "Debe ingresar una ciudad menor a 50 caracteres.";
    }

    if ($address == null) {
        $isValid = false;
        $errors['update-address'] = "Debe ingresar una direccion de envio.";
    } else if (strlen($address) > 50) {
        $isValid = false;
        $errors['update-address'] = "Debe ingresar una direccion menor a 50 caracteres.";
    }
}

if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar editar orden";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}

// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Actualizar la información principal de la orden
    $sqlUpdateOrder = "UPDATE pedidos SET id_estado = ?, dni = ?, nombre = ?, apellido = ?, telefono = ? WHERE id = ?";
    $stmtUpdateOrder = mysqli_prepare($conn, $sqlUpdateOrder);
    mysqli_stmt_bind_param($stmtUpdateOrder, "iissii", $state, $dni, $name, $surname, $tel, $id);
    $resultUpdateOrder = mysqli_stmt_execute($stmtUpdateOrder);

    if (!$resultUpdateOrder) {
        $errors['general'] = "Error al actualizar la orden.";
        throw new Exception('Error al actualizar la orden');
    }

    // Obtener productos actuales asociados a la orden
    $sqlGetOrderProducts = "SELECT id_producto, cantidad FROM pedido_producto WHERE id_pedido = ?";
    $stmtGetOrderProducts = mysqli_prepare($conn, $sqlGetOrderProducts);
    mysqli_stmt_bind_param($stmtGetOrderProducts, "i", $id);
    mysqli_stmt_execute($stmtGetOrderProducts);
    $currentOrderProducts = mysqli_stmt_get_result($stmtGetOrderProducts);

    // Crear un array asociativo de productos actuales (id_producto => cantidad)
    $currentProducts = [];
    while ($row = mysqli_fetch_assoc($currentOrderProducts)) {
        $currentProducts[$row['id_producto']] = $row['cantidad'];
    }
    
    // Comparar productos actuales con nuevos productos
    foreach ($currentProducts as $productId => $currentQuantity) {
        if (!isset($products[$productId])) {
            // El producto actual no está presente en los nuevos productos
            // Agregar la cantidad eliminada al stock del producto
            $sqlGetProductStock = "SELECT stock FROM productos WHERE id = ?";
            $stmtGetProductStock = mysqli_prepare($conn, $sqlGetProductStock);
            mysqli_stmt_bind_param($stmtGetProductStock, "i", $productId);
            mysqli_stmt_execute($stmtGetProductStock);
            $currentStock = mysqli_stmt_get_result($stmtGetProductStock);
            $currentStock = mysqli_fetch_assoc($currentStock);

            $newStock = $currentStock['stock'] + $currentQuantity;

            $sqlUpdateStock = "UPDATE productos SET stock = ? WHERE id = ?";
            $stmtUpdateStock = mysqli_prepare($conn, $sqlUpdateStock);
            mysqli_stmt_bind_param($stmtUpdateStock, "ii", $newStock, $productId);
            mysqli_stmt_execute($stmtUpdateStock);
        }
    }
    

    // Eliminar productos que no están en los nuevos productos
    $sqlDeleteOrderProducts = "DELETE FROM pedido_producto WHERE id_pedido = ?";
    $stmtDeleteOrderProducts = mysqli_prepare($conn, $sqlDeleteOrderProducts);
    mysqli_stmt_bind_param($stmtDeleteOrderProducts, "i", $id);
    $resultDeleteOrderProducts = mysqli_stmt_execute($stmtDeleteOrderProducts);
    if (!$resultDeleteOrderProducts) {
        $errors['general'] = "Error al actualizar los productos de la orden.";
        throw new Exception('Error al actualizar los productos de la orden');
    }

    // Insertar los nuevos productos en la orden
    foreach ($products as $product) {
        $productId = $product['id'];
        $quantity = $product['cantidad'];
        // Verificar si hay suficiente stock para el pedido
        $sqlGetProductStock = "SELECT nombre, stock FROM productos WHERE id = ?";
        $stmtGetProductStock = mysqli_prepare($conn, $sqlGetProductStock);
        mysqli_stmt_bind_param($stmtGetProductStock, "i", $productId);
        mysqli_stmt_execute($stmtGetProductStock);
        $currentProductStock = mysqli_stmt_get_result($stmtGetProductStock);
        $currentProductStock = mysqli_fetch_assoc($currentProductStock);

        // Verificar si hay suficiente stock para el pedido
        if ($currentProductStock['stock'] >= $quantity) {
            // Calcular la nueva cantidad de stock después de realizar el pedido
            $newStock = $currentProductStock['stock'] - $quantity;
            // Actualizar la cantidad de stock en la tabla de productos
            $sqlUpdateStock = "UPDATE productos SET stock = ? WHERE id = ?";
            $stmtUpdateStock = mysqli_prepare($conn, $sqlUpdateStock);
            mysqli_stmt_bind_param($stmtUpdateStock, "ii", $newStock, $productId);
            mysqli_stmt_execute($stmtUpdateStock);

            // Insertar el producto en la orden
            $sqlInsertOrderProduct = "INSERT INTO pedido_producto (id_pedido, id_producto, cantidad) VALUES (?, ?, ?)";
            $stmtInsertOrderProduct = mysqli_prepare($conn, $sqlInsertOrderProduct);
            mysqli_stmt_bind_param($stmtInsertOrderProduct, "iii", $id, $productId, $quantity);
            $resultInsertOrderProduct = mysqli_stmt_execute($stmtInsertOrderProduct);
            if (!$resultInsertOrderProduct) {
                $errors['general'] = "Error al insertar los productos de la orden.";
                throw new Exception('Error al insertar los productos de la orden');
            }
        } else {
            // No hay suficiente stock para el pedido
            $message['success'] = false;
            $errors['general'] = "Solo hay " . $currentProductStock['stock'] . " del producto '" . $currentProductStock['nombre'] . "'";
            throw new Exception('Error al insertar productos de la orden');
        }
    }
    
    // Actualizar información de envío si es necesario
    if ($delivery == true) {
        // Verificar si ya hay información de envío asociada con la orden
        $sqlCheckExistingDelivery = "SELECT id FROM envios WHERE id_pedido = ?";
        $stmtCheckExistingDelivery = mysqli_prepare($conn, $sqlCheckExistingDelivery);
        mysqli_stmt_bind_param($stmtCheckExistingDelivery, "i", $id);
        mysqli_stmt_execute($stmtCheckExistingDelivery);
        $existingDelivery = mysqli_stmt_get_result($stmtCheckExistingDelivery);
        $existingDelivery = mysqli_fetch_assoc($existingDelivery);

        if ($existingDelivery) {
            // Si hay información de envío existente, actualizarla
            $sqlUpdateDelivery = "UPDATE envios SET id_provincia = ?, direccion = ?, ciudad = ? WHERE id_pedido = ?";
            $stmtUpdateDelivery = mysqli_prepare($conn, $sqlUpdateDelivery);
            mysqli_stmt_bind_param($stmtUpdateDelivery, "issi", $province, $address, $city, $id);
            $resultUpdateDelivery = mysqli_stmt_execute($stmtUpdateDelivery);

            if (!$resultUpdateDelivery) {
                $errors['general'] = "Error al actualizar la información de envío.";
                throw new Exception('Error al actualizar la información de envío');
            }
        } else {
            // Si no hay información de envío existente, insertarla
            $sqlInsertDelivery = "INSERT INTO envios (id_pedido, id_provincia, direccion, ciudad) VALUES (?, ?, ?, ?)";
            $stmtInsertDelivery = mysqli_prepare($conn, $sqlInsertDelivery);
            mysqli_stmt_bind_param($stmtInsertDelivery, "iiss", $id, $province, $address, $city);
            $resultInsertDelivery = mysqli_stmt_execute($stmtInsertDelivery);

            if (!$resultInsertDelivery) {
                $errors['general'] = "Error al insertar la información de envío.";
                throw new Exception('Error al insertar la información de envío');
            }
        }
    } else {
        // Verificar si ya hay información de envío asociada con la orden
        $sqlDeleteDelivery = "DELETE FROM envios WHERE id_pedido = ?";
        $stmtDeleteDelivery = mysqli_prepare($conn, $sqlDeleteDelivery);
        mysqli_stmt_bind_param($stmtDeleteDelivery, "i", $id);
        mysqli_stmt_execute($stmtDeleteDelivery);
    }


    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "El pedido fue editado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));
} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar editar el pedido";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
