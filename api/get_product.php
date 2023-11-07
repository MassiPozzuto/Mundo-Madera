<?php
	include("../config/conexion.php");
	$response=new stdClass();

	$codpro=$_POST['codpro'];
	$sql="SELECT * FROM producto WHERE codpro=$codpro";
	$stmt=mysqli_query($conn,$sql);
	$row=mysqli_fetch_array($stmt, MYSQLI_BOTH);
	$obj=new stdClass();
	$obj->nompro=utf8_encode($row['nompro']);
	$obj->despro=utf8_encode($row['despro']);
	$obj->prepro=$row['prepro'];
	$obj->estado=$row['estado'];
	$obj->rutimapro=$row['rutimapro'];
	$response->product=$obj;

	echo json_encode($response);