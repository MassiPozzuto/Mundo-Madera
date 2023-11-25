<?php

require("../../includes/config.php");
require("../../includes/functions.php");

header("Content-Type: application/json; charset=utf-8");


$message = [];
$categoryId = $_POST['id'];


// Consulta preparada para verificar si la categoría ya está eliminado
$sqlCheckDeleted = "SELECT fecha_eliminacion FROM categorias WHERE id = ?";
$stmtCheckDeleted = mysqli_prepare($conn, $sqlCheckDeleted);
mysqli_stmt_bind_param($stmtCheckDeleted, "i", $categoryId);
mysqli_stmt_execute($stmtCheckDeleted);
$resultCheckDeleted = mysqli_stmt_get_result($stmtCheckDeleted);
$rowCheckDeleted = mysqli_fetch_assoc($resultCheckDeleted);


// Inicia la transacción
mysqli_begin_transaction($conn);

if ($rowCheckDeleted['fecha_eliminacion'] !== null) {
    // LOGICA PARA RECUPERAR CATEGORIA
    // Consulta preparada para eliminar la fecha de eliminación de la categoría
    $sqlRecoverProduct = "UPDATE categorias SET fecha_eliminacion = NULL WHERE id = ?";
    $stmtRecoverProduct = mysqli_prepare($conn,
        $sqlRecoverProduct
    );
    mysqli_stmt_bind_param($stmtRecoverProduct, "i", $categoryId);
    $resultRecoverProduct = mysqli_stmt_execute($stmtRecoverProduct);

    if ($resultRecoverProduct) {
        // Confirma la transacción si todo está bien
        mysqli_commit($conn);
        $message['success'] = true;
        $message['msj'] = "La categoría fue recuperado correctamente";
        $message['action'] = "recover";
    } else {
        // Revierte la transacción si hay algún error
        mysqli_rollback($conn);
        $message['success'] = false;
        $message['msj'] = "Error al intentar recuperar la categoría";
        $message['action'] = "recover";
    }

} else {
    // LOGICA PARA ELIMINAR CATEGORIA
    // Consulta preparada para actualizar la fecha de eliminación de la categoría
    $sqlUpdateCategory = "UPDATE categorias SET fecha_eliminacion = NOW() WHERE id = ?";
    $stmtUpdateCategory = mysqli_prepare($conn, $sqlUpdateCategory);
    mysqli_stmt_bind_param($stmtUpdateCategory, "i", $categoryId);
    $resultUpdateCategory = mysqli_stmt_execute($stmtUpdateCategory);

    // Eliminar registros asociados en la tabla categoria_producto
    $sqlDeleteCategoryProduct = "DELETE FROM categoria_producto WHERE id_categoria = ?";
    $stmtDeleteCategoryProduct = mysqli_prepare($conn, $sqlDeleteCategoryProduct);
    mysqli_stmt_bind_param($stmtDeleteCategoryProduct, "i", $categoryId);
    $resultDeleteCategoryProduct = mysqli_stmt_execute($stmtDeleteCategoryProduct);


    if ($resultUpdateCategory && $resultDeleteCategoryProduct) {
        // Confirma la transacción si todo está bien
        mysqli_commit($conn);
        $message['success'] = true;
        $message['msj'] = "La categoría fue eliminado correctamente";
        $message['action'] = "delete";
    } else {
        // Revierte la transacción si hay algún error
        mysqli_rollback($conn);
        $message['success'] = false;
        $message['msj'] = "Error al intentar eliminar la categoría";
        $message['action'] = "delete";
    }
}

echo json_encode($message);

?>