<?php
	include("../config/conexion.php");
	$response=new stdClass();

	$codpro=$_POST['codpro'];
	$sql="select * from pedido where codpro=$codpro";
	$stmt=sqlsrv_query($conn,$sql);
	$row=sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH);
	$conntador=sqlsrv_num_rows($result);
	if ($conntador>0) {
		$sql="update producto set estado=0 where codpro=$codpro";
		$result=sqlsrv_query($conn,$sql);
		if ($result) {
			$response->state=true;
		}else{
			$response->state=false;
			$response->detail="No se puede eliminar el producto";
		}
	}else{
		$sql="select rutimapro from producto where codpro=$codpro";
		$stmt=sqlsrv_query($conn,$sql);
		$row=sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH);
		$rutimapro=$row['rutimapro'];

		$sql="delete from producto
		where codpro=$codpro";
		$result=sqlsrv_query($conn,$sql);
		if ($result) {
			$response->state=true;
			//recuerda que debes redireccionar al nombre de proyecto correcto
			// ejm: sistema-ecommerce-master
			unlink("../../sistema-ecommerce/assets/products/".$rutimapro);
		}else{
			$response->state=false;
			$response->detail="No se puede eliminar el producto";
		}
	}

	echo json_encode($response);