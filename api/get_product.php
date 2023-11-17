<?php
	include("../includes/config.php");
	$response=new stdClass();

	$codpro=$_POST['codpro'];
	$sql="SELECT * FROM productos WHERE id=$codpro";
	$stmt=mysqli_query($conn,$sql);
	$row=mysqli_fetch_array($stmt, MYSQLI_BOTH);
	$obj=new stdClass();
	$obj->nompro=$row['nombre'];
	$obj->despro=$row['stock'];
	$obj->prepro=$row['precio'];
	$obj->estado=$row['id_categoria'];
	$obj->rutimapro=$row['rutimapro'];
	$response->product=$obj;

	echo json_encode($response);