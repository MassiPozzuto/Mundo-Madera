<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

define('RUTA', '/Mundo-Madera');
define('CANT_REG_PAG', 30);

$conn = mysqli_connect('localhost', 'root', '', '--');

if (!$conn) {
  die('Error de Conexión (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

session_start();

if ((isset($_COOKIE['email']) || isset($_COOKIE['password'])) && !isset($_SESSION['user'])) {
  $sqlLogin = "SELECT users.* FROM users 
                  WHERE users.email='" . $_COOKIE['email'] . "' AND users.password='" . $_COOKIE['password'] . "' AND users.deleted_at IS NULL";
  $resultLogin = mysqli_query($conn, $sqlLogin);
  if (mysqli_num_rows($resultLogin) === 1) {
    $_SESSION['user'] = mysqli_fetch_assoc($resultLogin);
  }
}
// Change character set to utf8
mysqli_set_charset($conn, "utf8");