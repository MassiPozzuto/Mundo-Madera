<?php

// Función para obtener la ruta de la imagen del producto
function productImgPath($productId, $position = "../")
{
    $dir = "{$position}img/products/{$productId}";
    // Verifica si el directorio existe
    if (!is_dir($dir)) {
        return null;
    }

    // Escanea el directorio en busca de archivos
    $archives = scandir($dir);
    // Itera sobre los archivos para encontrar la primera imagen
    foreach ($archives as $archive) {
        // Ignora los directorios y archivos que no son imágenes
        if (!is_dir($archive)) {
            return "$dir/$archive";
        }
    }
    // Si no se encuentra ninguna imagen, devuelve un mensaje o un valor predeterminado
    return null;
}

function deleteDirContents($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                    deleteDirContents($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
    }
}