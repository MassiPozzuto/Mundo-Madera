<?php
require("../../includes/config.php");

$message = [];
$errors = [];

$id = $_POST['id'];
$loggedInUserId = $_SESSION['user']['id'];


// Verificar la cantidad de usuarios antes de intentar eliminar
$sqlCountUsers = "SELECT COUNT(*) FROM usuarios";
$resultCountUsers = mysqli_query($conn, $sqlCountUsers);
$rowCountUsers = mysqli_fetch_assoc($resultCountUsers);

if ($rowCountUsers['COUNT(*)'] <= 1) {
    $message['success'] = false;
    $message['msj'] = "No se puede eliminar el único usuario.";
    $errors['general'] = "No se puede eliminar el único usuario.";
    return print_r(json_encode($message));
}

// Iniciar transacción
mysqli_begin_transaction($conn);

try {
    // Consulta preparada para la eliminación del usuario
    $sqlDeleteUser = "DELETE FROM usuarios WHERE id = ?";
    $stmtDeleteUser = mysqli_prepare($conn, $sqlDeleteUser);
    mysqli_stmt_bind_param($stmtDeleteUser, "i", $id);
    $resultDeleteUser = mysqli_stmt_execute($stmtDeleteUser);

    if (!$resultDeleteUser) {
        $errors['general'] = "Error al eliminar el usuario.";
        throw new Exception('Error al eliminar usuario');
    }

    // Confirmar transacción
    mysqli_commit($conn);
    $message['success'] = true;
    $message['msj'] = "El usuario fue eliminado correctamente.";
    $message['errors'] = null;

    // Verificar si el usuario está intentando eliminar su propia cuenta
    if ($id == $loggedInUserId) {
        $message['redirectUrl'] = 'logout.php';
    }

    return print_r(json_encode($message));
} catch (Exception $e) {
    // En caso de error, revertir transacción
    mysqli_rollback($conn);
    $message['success'] = false;
    $message['msj'] = "Error al intentar eliminar usuario.";
    $message['errors'] = $errors;
    return print_r(json_encode($message));
}
