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
			if(isset($_FILES['imagen'])){
				//TU TAREA ES CAPTURAR LA FECHA Y HORA DEL SISTEMA
				//$nombre_imagen="20201011090730.jpg";
				$nombre_imagen = date("YmdHis").".jpg";  
				$stmt= $conn->prepare("INSERT INTO productos (id_categoria,nombre,stock,precio,fecha_creacion, rutimapro) VALUES (?,?,?,?,?,?);");
				if($stmt->bind_param("isiiss", $categoria,$nombre, $stock, $precio, $date, $nombre_imagen)){
					if ($stmt->execute()) {
						$response->state=true;
					//RECUERDA QUE MUEVE QUE NECESITES MENOS RETORNOS DE DIRECTORIO, es decir el "../"
					if(move_uploaded_file($_FILES['imagen']['tmp_name'], "../img/products/".$nombre_imagen)){
						$response->state=true;
					}else{
						$response->state=false;
						$response->detail="hubo un error al cargar la imagen";
					}
				}else{
					$response->state=false;
					$response->detail="No se pudo guardar el producto";
				}
			}else{
				$response->state=false;
				$response->detail="Falta la imagen";
			}
		}
	}
}
	$stmt->close();

	echo json_encode($response);


