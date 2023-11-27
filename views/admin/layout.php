<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>

  <title><?php echo $title; ?> • Mundo Madera</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Viex Inc.">

  <link rel="icon" sizes="192x192" href="">


  <!-- FONT: 'MONSERRAT', AGREGADO EN 'MAIN.CSS'-->
  <!-- BOOTSTRAP -->
  <script src="../../js/bootstrap.bundle.js"></script>
  <link href="../../css/bootstrap.css" rel="stylesheet">
  <!-- PAGINA DE ICONOS: https://tabler-icons.io/ -->
  <!-- JQUERY -->
  <script src="../../js/jquery.min.js"></script>
  <!-- SELECT2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
  <!-- CSS -->
  <link href="../../css/main.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../../css/styleadm.css">
  <?php
  if ($section ==  'login' || $section == 'pedidos') { ?>
    <link href="../../css/<?php echo $section; ?>.css" rel="stylesheet">
  <?php
  } ?>
  <!-- AXIOS -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <!-- SISTEMA -->
  <script type="text/javascript" src="../../js/main.js"></script>

</head>

<body>

  <?php
  if ($section != "login") { ?>

    <header>
      <div class="container__header-logo">
        <img src="../../img/logo_inicial.jpg" alt="Log Mundo Madera">
      </div>
      <div class="container__header-menu">
        <ul class="header__menu-list">
          <li class="header__menu-item item-shipments <?php echo ($section == "pedidos") ? "active" : null; ?>">
            <a href="pedidos.php">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clipboard-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                  <path d="M9 12l.01 0" />
                  <path d="M13 12l2 0" />
                  <path d="M9 16l.01 0" />
                  <path d="M13 16l2 0" />
                </svg>
              </div>
              Pedidos
            </a>
          </li>
          <li class="header__menu-item item-logout <?php echo ($section == "envios") ? "active" : null; ?>">
            <a href="envios.php">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                  <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                  <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                  <path d="M3 9l4 0" />
                </svg>
              </div>
              Envios
            </a>
          </li>
          <li class="header__menu-item item-products <?php echo ($section == "productos") ? "active" : null; ?>">
            <a href="productos.php?page=1&filterBy=all&allowDeleted=no">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                  <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                  <path d="M17 17h-11v-14h-2" />
                  <path d="M6 5l14 1l-1 7h-13" />
                </svg>
              </div>
              Productos
            </a>
          </li>
          <li class="header__menu-item item-categories <?php echo ($section == "categorias") ? "active" : null; ?>">
            <a href="categorias.php">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tags" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M3 8v4.172a2 2 0 0 0 .586 1.414l5.71 5.71a2.41 2.41 0 0 0 3.408 0l3.592 -3.592a2.41 2.41 0 0 0 0 -3.408l-5.71 -5.71a2 2 0 0 0 -1.414 -.586h-4.172a2 2 0 0 0 -2 2z" />
                  <path d="M18 19l1.592 -1.592a4.82 4.82 0 0 0 0 -6.816l-4.592 -4.592" />
                  <path d="M7 10h-.01" />
                </svg>
              </div>
              Categorias
            </a>
          </li>
          <li class="header__menu-item item-users  <?php echo ($section == "usuarios") ? "active" : null; ?>">
            <a href="usuarios.php">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>
              </div>
              Usuarios
            </a>
          </li>
        </ul>

        <div class="header__menu-item item-logout" id="salir">
          <a href="logout.php">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                <path d="M15 12h-12l3 -3" />
                <path d="M6 15l-3 -3" />
              </svg>
            </div>
            Salir
          </a>
        </div>
      </div>
    </header>

  <?php
  } ?>

  <!-- Empieza el contenido especifico -->
  <div class="container">
    <?php require_once($section . ".php") ?>
  </div>
  <!-- Termina el contenido especifico -->

</body>

</html>