<?php
require("../../includes/config.php");


$message = [];
$isValid = true;
$errors = [];

$username = $_POST['username'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$password = $_POST['pass'];


if ($username == null) {
    $isValid = false;
    $errors['create-username'] = "Debe ingresar un nombre de usuario.";
} else if (strlen($username) > 25) {
    $isValid = false;
    $errors['create-username'] = "Debe ingresar un nombre de usuario menor a 25 caracteres.";
} else {
    // Verificar si el nombre de usuario ya existe
    $sqlCheckUsername = "SELECT COUNT(*) FROM usuarios WHERE username = ?";
    $stmtCheckUsername = mysqli_prepare($conn, $sqlCheckUsername);
    mysqli_stmt_bind_param($stmtCheckUsername, "s", $username);
    mysqli_stmt_execute($stmtCheckUsername);
    mysqli_stmt_bind_result($stmtCheckUsername, $usernameCount);
    mysqli_stmt_fetch($stmtCheckUsername);

    if ($usernameCount > 0) {
        // El nombre de usuario ya existe
        $isValid = false;
        $errors['create-username'] = "El nombre de usuario ya está en uso.";
    }
    
    // Liberar el conjunto de resultados y cerrar la consulta preparada
    mysqli_stmt_close($stmtCheckUsername);
}

if ($name != null && strlen($name) > 30) {
    $isValid = false;
    $errors['create-name'] = "El nombre debe tener menos de 30 caracteres.";
} 

if ($surname != null && strlen($surname) > 30) {
    $isValid = false;
    $errors['create-surname'] = "El apellido debe tener menos de 30 caracteres.";
} 

if ($password == null) {
    $isValid = false;
    $errors['create-password'] = "Debe ingresar una contraseña.";
} else if (strlen($password) < 5) {
    $isValid = false;
    $errors['create-password'] = "Debe ingresar una contraseña de mínimo 5 caracteres.";
}


if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear producto";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}

$password = sha1($password);
// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la inserción del usuario
    $sqlCreateProduct = "INSERT INTO usuarios (nombre, apellido, username, password, fecha_creacion) VALUES (?, ?, ?, ?, NOW())";
    $stmtCreateProduct = mysqli_prepare($conn, $sqlCreateProduct);
    mysqli_stmt_bind_param($stmtCreateProduct, "ssss", $name, $apellido, $username, $password);
    $resultCreateProduct = mysqli_stmt_execute($stmtCreateProduct);

    if (!$resultCreateProduct) {
        $errors['general'] = "Error al insertar el usuario";
        throw new Exception('Error al insertar usuario');
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "El usuario fue creado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));
} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear usuario";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
