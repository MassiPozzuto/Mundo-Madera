<?php
	include("../includes/config.php");
	$response=new stdClass();

	$codpro=$_POST['codpro'];
	$sql="SELECT * FROM productos WHERE id=$codpro";
	$stmt=mysqli_query($conn,$sql);
	$row=mysqli_fetch_assoc($stmt);
	$response=$row;

	echo json_encode($response);