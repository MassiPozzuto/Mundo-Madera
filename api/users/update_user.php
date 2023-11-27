<?php
require("../../includes/config.php");


$message = [];
$isValid = true;
$errors = [];

$id = $_POST['id'];
$username = $_POST['username'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$newPassword = $_POST['newPass'];
$currentPassword = $_POST['oldPass'];

if ($username == null) {
    $isValid = false;
    $errors['update-username'] = "Debe ingresar un nombre de usuario.";
} else if (strlen($username) > 25) {
    $isValid = false;
    $errors['update-username'] = "Debe ingresar un nombre de usuario menor a 25 caracteres.";
} else {
    // Verificar si el nombre de usuario ya existe
    $sqlCheckUsername = "SELECT COUNT(*) FROM usuarios WHERE username = ? AND id != ?";
    $stmtCheckUsername = mysqli_prepare($conn, $sqlCheckUsername);
    mysqli_stmt_bind_param($stmtCheckUsername, "si", $username, $id);
    mysqli_stmt_execute($stmtCheckUsername);
    mysqli_stmt_bind_result($stmtCheckUsername, $usernameCount);
    mysqli_stmt_fetch($stmtCheckUsername);
    mysqli_stmt_close($stmtCheckUsername);

    if ($usernameCount > 0) {
        // El nombre de usuario ya existe
        $isValid = false;
        $errors['update-username'] = "El nombre de usuario ya está en uso.";
    }
}

if ($name != null && strlen($name) > 30) {
    $isValid = false;
    $errors['update-name'] = "El nombre debe tener menos de 30 caracteres.";
}

if ($surname != null && strlen($surname) > 30) {
    $isValid = false;
    $errors['update-surname'] = "El apellido debe tener menos de 30 caracteres.";
}

if ($newPassword == null) {
    $isValid = false;
    $errors['update-new_password'] = "Debe ingresar una contraseña.";
} else if (strlen($newPassword) < 5) {
    $isValid = false;
    $errors['update-new_password'] = "Debe ingresar una contraseña de mínimo 5 caracteres.";
}

$currentPassword = sha1($currentPassword);
// Verificar la contraseña actual antes de realizar la actualización
$sqlCheckCurrentPassword = "SELECT COUNT(*) FROM usuarios WHERE id = ? AND password = ?";
$stmtCheckCurrentPassword = mysqli_prepare($conn, $sqlCheckCurrentPassword);
mysqli_stmt_bind_param($stmtCheckCurrentPassword, "is", $id, $currentPassword);
mysqli_stmt_execute($stmtCheckCurrentPassword);
mysqli_stmt_bind_result($stmtCheckCurrentPassword, $passwordMatch);
mysqli_stmt_fetch($stmtCheckCurrentPassword);
mysqli_stmt_close($stmtCheckCurrentPassword);

if (!$passwordMatch) {
    // La contraseña actual no coincide
    $isValid = false;
    $errors['update-old_password'] = "La contraseña actual no coincide.";
}

if (!$isValid) {
    $message['success'] = false;
    $message['msj'] = "Error al intentar crear producto";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}

$newPassword = sha1($newPassword);
// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la actualización del usuario
    $sqlUpdateUser = "UPDATE usuarios SET nombre = ?, apellido = ?, username = ?, password = ? WHERE id = ?";
    $stmtUpdateUser = mysqli_prepare($conn, $sqlUpdateUser);
    mysqli_stmt_bind_param($stmtUpdateUser, "ssssi", $name, $surname, $username, $newPassword, $id);
    $resultUpdateUser = mysqli_stmt_execute($stmtUpdateUser);

    if (!$resultUpdateUser) {
        $errors['general'] = "Error al actualizar el usuario";
        throw new Exception('Error al actualizar usuario');
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "El usuario fue actualizado correctamente";
    $message['errors'] = null;
    return print_r(json_encode($message));
} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar actualizar usuario";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
?>