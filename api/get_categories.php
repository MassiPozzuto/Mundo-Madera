<?php
	require("../includes/config.php");
	$response=new stdClass();

	$sql = "SELECT * FROM categorias";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_all($result, MYSQLI_ASSOC);
	$response = $row;
	echo json_encode($response);