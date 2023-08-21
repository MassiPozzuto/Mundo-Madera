<?php
	include("../config/conexion.php");
	$response=new stdClass();

	$codped=$_POST['codped'];
	$sql="UPDATE pedido set estado=5
	where codped=$codped";
	$result=sqlsrv_query($conn,$sql);
	if ($result) {
		$response->state=true;
	}else{
		$response->state=true;
		$response->detail="No se actualizo el estado";
	}

	echo json_encode($response);