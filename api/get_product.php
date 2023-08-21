<?php
	include("../config/conexion.php");
	$response=new stdClass();

	$codpro=$_POST['codpro'];
	$sql="select * from producto where codpro=$codpro";
	$stmt=sqlsrv_query($conn,$sql);
	$row=ssqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH);
	$obj=new stdClass();
	$obj->nompro=utf8_encode($row['nompro']);
	$obj->despro=utf8_encode($row['despro']);
	$obj->prepro=$row['prepro'];
	$obj->estado=$row['estado'];
	$obj->rutimapro=$row['rutimapro'];
	$response->product=$obj;

	echo json_encode($response);