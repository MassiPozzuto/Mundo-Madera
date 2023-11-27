<?php

require("../../includes/config.php");

header("Content-Type: application/json; charset=utf-8");

$message = [];

// Consulta preparada para obtener datos del usuario
$sql = "SELECT id, username, nombre, apellido  
        FROM usuarios
        WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_POST['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Verifica si el usuario fue encontrado
if ($row) {
    $message['success'] = true;
    $message['user'] = $row;
} else {
    $message['success'] = false;
    $message['error'] = "No se pudo encontrar el usuario. Intentelo más tarde";
}

echo json_encode($message);
