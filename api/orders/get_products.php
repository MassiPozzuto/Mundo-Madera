<?php
require("../../includes/config.php");
$response = new stdClass();

$sql = "SELECT * FROM productos WHERE fecha_eliminacion IS NULL";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_all($result, MYSQLI_ASSOC);
$response->success = true;
$response->products = $row;
echo json_encode($response);
