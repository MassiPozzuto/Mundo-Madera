<?php
require('../../includes/config.php');

if (isset($_SESSION['user'])) {
    header('location: inicio.php');
}

if(!empty($_POST)){
    if(!empty($_POST['username']) && !empty($_POST['password'])){
        $sqlLogin = "SELECT * FROM usuarios 
                        WHERE username='" . trim($_POST['username']) . "' AND usuarios.password='" . sha1($_POST['password']) . "' AND fecha_eliminacion IS NULL";
        $resultLogin = mysqli_query($conn, $sqlLogin);
    
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
    } else {
        //header("Content-Type: application/json; charset=utf-8");
        //return print_r(json_encode($message));
        //Campos vacios
        $error_msj = "Debe ingresar un nombre de usuario y una contraseña.";
    }
}


$page = "Iniciar sesión";
$section = "login";
require_once "../../views/admin/layout.php";
?>