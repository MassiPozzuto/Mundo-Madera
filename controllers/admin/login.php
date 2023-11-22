<?php
require('../../includes/config.php');

if (isset($_SESSION['user'])) {
    header('location: inicio.php');
}

if (!empty($_POST)) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        // Consulta preparada
        $sqlLogin = "SELECT * FROM usuarios WHERE username=? AND usuarios.password=? AND fecha_eliminacion IS NULL";
        $stmt = mysqli_prepare($conn, $sqlLogin);

        if ($stmt) {
            // Vincular parámetros
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
            // Asignar valores a los parámetros
            $username = trim($_POST['username']);
            $hashed_password = sha1($_POST['password']);
            // Ejecutar la consulta
            mysqli_stmt_execute($stmt);
            // Obtener resultados
            $resultLogin = mysqli_stmt_get_result($stmt);

            // Verificar el número de filas
            if (mysqli_num_rows($resultLogin) === 1) {
                $_SESSION['user'] = mysqli_fetch_assoc($resultLogin);

                if (isset($_POST['remember']) && $_POST['remember']) {
                    setcookie('username', $_POST['username'], time() + 20 * 86400, '/');
                    setcookie('password', sha1($_POST['password']), time() + 20 * 86400, '/');

                    $_COOKIE['username'] = $_POST['username'];
                    $_COOKIE['password'] = $_POST['password'];
                }

                //$message['message'] = "Se ha iniciado sesión correctamente";
                header("Location: inicio.php");
            } else {
                // Nombre de usuario incorrecto 
                //$message['username'] = "El nombre de usuario no es correcto";
                $error_msj = "El nombre de usuario y/o la contraseña no son correctos.";
            }

            // Cerrar la consulta preparada
            mysqli_stmt_close($stmt);
        } else {
            // Manejar el error de la consulta preparada
            $error_msj = "Error al iniciar sesión. Intentelo más tarde";
        }
    } else {
        // Campos vacíos
        $error_msj = "Debe ingresar un nombre de usuario y una contraseña.";
    }
}


$title = "Iniciar sesión";
$section = "login";
require_once "../../views/admin/layout.php";
?>