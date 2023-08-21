<?php
	include("../includes/config.php");
	$response=new stdClass();
	date_default_timezone_set('America/Argentina/Buenos_Aires');
	$date = date('d-m-y h:i:s');
	//$response->state=true;
	$codigo=$_POST['id'];
	$nombre=$_POST['nom'];
	$stock=$_POST['stock'];
	$precio=$_POST['precio'];
	$categoria=$_POST['cat'];

	if ($nombre=="") {
		$response->state=false;
		$response->detail="Falta el nombre";
	}else{
		if ($precio=="") {
			$response->state=false;
			$response->detail="Falta el precio";
		}else{
				$stmt="INSERT INTO productos (id,id_categoria,nombre,stock,precio,fecha_creacion)
				VALUES ('',$categoria,'$nombre',$stock,$precio,$date)";
				$result=sqlsrv_query($conn,$stmt);
				if ($result) {
						$response->state=true;
					}
				else{
					$response->state=false;
					$response->detail="No se pudo guardar el producto";
				}
			}}


	echo json_encode($response);