<?php

require("../../includes/config.php");

header("Content-Type: application/json; charset=utf-8");

$message = [];

// Consulta preparada para obtener datos de la categoría
$sql = "SELECT * FROM categorias WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_POST['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Verifica si la categoría fue encontrado
if ($row) {
    // Agrega la ruta de la imagen al resultado
    $message['success'] = true;
    $message['category'] = $row;
} else {
    $message['success'] = false;
    $message['error'] = "No se pudo encontrar la categoría. Intentelo más tarde";
}

echo json_encode($message);
