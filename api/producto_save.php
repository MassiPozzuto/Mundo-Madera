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
				$stmt= $conn->prepare("INSERT INTO productos (id_categoria,nombre,stock,precio,fecha_creacion) VALUES (?,?,?,?,?);");
				if($stmt->bind_param("isiis", $categoria,$nombre, $stock, $precio, $date)){
					if ($stmt->execute()) {
						$response->state=true;
					} else {
						echo "Falló la ejecución: (" . $stmt->errno . ") " . $stmt->error;
						$response->state=false;
						$response->detail="No se pudo guardar el producto";
					}
				}else{
					echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
				}
				
				
			}}

	$stmt->close();

	echo json_encode($response);