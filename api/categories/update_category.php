<?php
require("../../includes/config.php");

$message = [];
$isValid = true;
$errors = [];


$id = $_POST['id'];
$name = $_POST['name'];
$description = (!empty($_POST['description'])) ? $_POST['description'] : null;

//Cuando intentas convertir un string a int en php, en caso de no ser posible siempre valdra 0
if ($name == null) {
    $isValid = false;
    $errors['update-name'] = "Debe ingresar un nombre.";
} else if (strlen($name) > 50) {
    $isValid = false;
    $errors['update-name'] = "Debe ingresar un nombre menor a 50 caracteres.";
}

if (strlen($description) > 200) {
    $isValid = false;
    $errors['update-description'] = "La descripción debe ser de máximo 200 caracteres.";
}

if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar editar categoría";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}


// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la inserción de la categoría
    $sqlUpdateCategory = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?";
    $stmtUpdateCategory = mysqli_prepare($conn, $sqlUpdateCategory);
    mysqli_stmt_bind_param($stmtUpdateCategory, "ssi", $name, $description, $id);
    $resultUpdateCategory = mysqli_stmt_execute($stmtUpdateCategory);

    if (!$resultUpdateCategory) {
        throw new Exception('Error al editar categoría');
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "La categoría fue editado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));

} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar editar categoría";

    // Obtener el código de estado SQL
    if ($e->getCode() == 1062) {
        // Código de error 1062 corresponde a violación de la restricción de unicidad
        $errors['update-name'] = "Ya existe otra categoría con ese nombre.";
    } else {
        // Otro tipo de error
        $errors['general'] = "Error al intentar editar categoría";
    }

    $message['errors'] = $errors;
    return print_r(json_encode($message));
}