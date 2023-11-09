<?php
session_start();
session_unset();
session_destroy();

if (isset($_COOKIE['username']) || isset($_COOKIE['password'])) {
    unset($_COOKIE['username']);
    unset($_COOKIE['password']);
    setcookie('username', $_POST['username'], time() - 20 * 86400, '/');
    setcookie('password', sha1($_POST['password']), time() - 20 * 86400, '/');
}

header("Location: login.php");