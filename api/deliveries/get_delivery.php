<?php

require("../../includes/config.php");
require("../../includes/functions.php");

header("Content-Type: application/json; charset=utf-8");

$message = [];

// Consulta preparada para obtener datos del producto
$sql = "SELECT * FROM envios
        WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_POST['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Verifica si el producto fue encontrado
if ($row) {
    $message['success'] = true;
    $message['delivery'] = $row;
} else {
    $message['success'] = false;
    $message['error'] = "No se pudo encontrar el producto. Intentelo más tarde";
}

echo json_encode($message);
