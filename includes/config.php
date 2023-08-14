<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

define('RUTA', '/Linea-Aerea');
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

/*if (isset($_SESSION['user'])) {
  $sqlIsBanned = "SELECT * FROM `bans` WHERE user_id = " . $_SESSION['user']['id'] . " AND (end_at > now() OR permaban IS NOT NULL)";
  $resIsBanned = mysqli_query($conn, $sqlIsBanned);
  if (!$resIsBanned) {
    exit("Error de consulta" . mysqli_error($conn));
  }

  if (mysqli_num_rows($resIsBanned) == 1) {
    // Cuenta se encuentra baneada
    session_unset();
    session_destroy();

    if (isset($_COOKIE['email']) || isset($_COOKIE['password'])) {
      unset($_COOKIE['email']);
      unset($_COOKIE['password']);
      setcookie('email', $_POST['email'], time() - 20 * 86400, '/');
      setcookie('password', sha1($_POST['password']), time() - 20 * 86400, '/');
    }

    header("Location: login.php");
  }
}*/

// Change character set to utf8
mysqli_set_charset($conn, "utf8");