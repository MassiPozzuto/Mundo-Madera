<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>

  <title><?php echo $page; ?> • Mundo Madera</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Viex Inc.">

  <link rel="icon" sizes="192x192" href="">

  
  <!-- FONTS -->
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
  <!-- BOOTSTRAP -->
  <script src="../../js/bootstrap.bundle.js"></script>
  <link href="../../css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
  <!-- JQUERY -->
  <script src="../../js/jquery.min.js"></script>
  <!-- CSS -->
  <link href="../../css/main.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../../css/styleadm.css">

</head>

<body>
  
  <?php
  if($section != "login") {?>

    <header>
      <div class="container__header-logo">
        <img src="../../img/logo_inicial.png" alt="Log Mundo Madera">
      </div>
      <div class="container__header-menu">
        <ul class="header__menu-list">
          <li class="header__menu-item item-shipments <?php echo ($section == "pedidos") ? "active" : null; ?>"><a href="pedidos.php"><i class="bi bi-card-list"></i>Pedidos</a></li>
          <li class="header__menu-item item-logout <?php echo ($section == "envios") ? "active" : null; ?>"><a href="envios.php"><i class="bi bi-truck"></i>Envios</a></li>
          <li class="header__menu-item item-products <?php echo ($section == "productos") ? "active" : null; ?>"><a href="productos.php"><i class="bi bi-cart2"></i>Productos</a></li>
          <li class="header__menu-item item-categories <?php echo ($section == "categorias") ? "active" : null; ?>"><a href="categorias.php"><i class="bi bi-tags"></i>Categorias</a></li>
          <li class="header__menu-item item-users  <?php echo ($section == "usuarios") ? "active" : null; ?>"><a href="usuarios.php"><i class="bi bi-person"></i>Usuarios</a></li>
        </ul>

        <div class="header__menu-item item-logout" id="salir">
          <a href="#"><i class="bi bi-box-arrow-left"></i>Salir</a>
        </div>
      </div>
    </header>

  <?php
  }?>

  <!-- Empieza el contenido especifico -->
  <div class="container">
    <?php require_once($section . ".php") ?>
  </div>
  <!-- Termina el contenido especifico -->

</body>

</html>