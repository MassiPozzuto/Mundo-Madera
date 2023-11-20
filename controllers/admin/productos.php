<?php
require('../../includes/config.php');

$sqlProducts = "SELECT p.*, c.tipo 
                    FROM productos p
                    INNER JOIN categorias c ON c.id = p.id_categoria
                    WHERE p.fecha_eliminacion IS NULL";
$stmtProducts = mysqli_query($conn, $sqlProducts);
$rowProducts = mysqli_fetch_all($stmtProducts, MYSQLI_ASSOC);

$page = "Productos";
$section = "productos";
require_once "../../views/admin/layout.php";