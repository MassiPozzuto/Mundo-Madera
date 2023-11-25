<?php
require("../../includes/config.php");

$message = [];
$isValid = true;
$errors = [];


$name = $_POST['name'];
$description = (!empty($_POST['description'])) ? $_POST['description'] : null;

//Cuando intentas convertir un string a int en php, en caso de no ser posible siempre valdra 0
if ($name == null) {
    $isValid = false;
    $errors['create-name'] = "Debe ingresar un nombre.";
} else if (strlen($name) > 50) {
    $isValid = false;
    $errors['create-name'] = "Debe ingresar un nombre menor a 50 caracteres.";
}

if (strlen($description) > 200) {
    $isValid = false;
    $errors['create-description'] = "La descripción debe ser de máximo 200 caracteres.";
}

if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear categoría";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}

// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la inserción de la categoría
    $sqlCreateCategory = "INSERT INTO categorias (nombre, descripcion, fecha_creacion) VALUES (?, ?, NOW())";
    $stmtCreateCategory = mysqli_prepare($conn, $sqlCreateCategory);
    mysqli_stmt_bind_param($stmtCreateCategory, "ss", $name, $description);
    $resultCreateCategory = mysqli_stmt_execute($stmtCreateCategory);

    if (!$resultCreateCategory) {
        throw new Exception('Error al insertar categoría');
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "La categoría fue creado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));

} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear categoría";

    // Obtener el código de estado SQL
    if ($e->getCode() == 1062) {
        // Código de error 1062 corresponde a violación de la restricción de unicidad
        $errors['create-name'] = "Ya existe otra categoría con ese nombre.";
    } else {
        // Otro tipo de error
        $errors['general'] = "Error al intentar crear categoría";
    }

    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
