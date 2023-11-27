<?php
require("../../includes/config.php");
require("../../includes/functions.php");


$message = [];
$isValid = true;
$errors = [];


$id = $_POST['id'];
$state = $_POST['state'];
$province = $_POST['province'];
$city = $_POST['city'];
$address = $_POST['address'];

if ($id == null) {
    $isValid = false;
    $errors['general'] = "El ID de la orden no puede estar vacío.";
}

if ($state == 0 || $state == null) {
    $isValid = false;
    $errors['update-state'] = "Debe seleccionar un estado para el pedido.";
}

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


if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar editar envio";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}


// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la inserción del producto
    $sqlUpdateProduct = "UPDATE envios SET id_estado = ?, id_provincia = ?, direccion = ?, ciudad = ? WHERE id = ?";
    $stmtUpdateProduct = mysqli_prepare($conn, $sqlUpdateProduct);
    mysqli_stmt_bind_param($stmtUpdateProduct, "iissi", $state, $province, $address, $city, $id);
    $resultUpdateProduct = mysqli_stmt_execute($stmtUpdateProduct);
    if (!$resultUpdateProduct) {
        $errors['general'] = "Error al editar el envio";
        throw new Exception('Error al editar envio');
    }

    if($state == 7){
        $sqlGetDelivery = "SELECT * FROM envios WHERE id = ?";
        $stmtGetDelivery = mysqli_prepare($conn, $sqlGetDelivery);
        mysqli_stmt_bind_param($stmtGetDelivery, "i", $id);
        mysqli_stmt_execute($stmtGetDelivery);
        $delivery = mysqli_stmt_get_result($stmtGetDelivery);
        $delivery = mysqli_fetch_assoc($delivery);
        mysqli_stmt_close($stmtGetDelivery);

        $sqlUpdateOrder = "UPDATE pedidos SET id_estado = 7, fecha_entrega = NOW() WHERE id = ?";
        $stmtUpdateOrder = mysqli_prepare($conn, $sqlUpdateOrder);
        mysqli_stmt_bind_param($stmtUpdateOrder, "i", $delivery['id_pedido']);
        $resultUpdateOrder = mysqli_stmt_execute($stmtUpdateOrder);
        if (!$resultUpdateOrder) {
            $errors['general'] = "Error al editar el envio";
            throw new Exception('Error al editar envio');
        }
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "El envio fue editado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));
} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar editar envio";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
