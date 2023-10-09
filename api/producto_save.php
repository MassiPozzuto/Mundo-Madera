<?php
	require("../includes/config.php");
	$response=new stdClass();
	
	$codigo=$_POST['id'];
	$nombre=$_POST['nombre'];
	$stock=$_POST['stock'];
	$precio=$_POST['precio'];
	$categoria=$_POST['cat'];
	$date = date('Y-d-m h:i:s');

	if ($nombre == null) {
		$response->state=false;
		$response->detail="Falta el nombre";
	}else{
		if ($precio == null) {
			$response->state=false;
			$response->detail="Falta el precio";
		}else{
				$stmt="INSERT INTO productos (id_categoria,nombre,stock,precio,fecha_creacion) VALUES ($categoria,'$nombre', $stock, $precio, '$date');";
				$result=mysqli_query($conn,$stmt);

				if ($result) {
						$response->state=true;
					}
				else{
					/*$response->categoria= $categoria;
					$response->nombre= $nombre;
					$response->stock= $stock;
					$response->precio= $precio;
					$response->date= $date;*/
					$response->state=false;
					$response->detail="No se pudo guardar el producto";
				}
			}}


	echo json_encode($response);