<div class="container__data">
    <div class="container__logo">
        <img src="../../img/logo_inicial.jpg" alt="Logo Mundo Madera" width="300px">
    </div>

    <div class="container__title">
        <p>Inicia sesión en Mundo Madera</p>
    </div>

    <form action="login.php" method="POST" class="login__form" id="formLogin">

        <?php
        if (isset($error_msj)) { ?>
            <div class="form-group">
                <p class="errormessage__form" id="msj_error_login"><?php echo $error_msj ?></p>
            </div>
        <?php
        } ?>

        <div class="form-group">
            <label for="username" class="input-title">Nombre de usuario</label>
            <input type="text" name="username" id="username" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : null; ?>">
        </div>

        <div class="form-group">
            <label for="password" class="input-title">Contraseña</label>
            <input class="pass" id="password" type="password" name="password">
        </div>

        <div class="form-group remember">
            <label class="label__remember">
                Recordarme
                <input type="checkbox" id="remember" name="remember">
                <span class="checkmark"></span>
            </label>
        </div>

        <div class="form-group">
            <button type="submit" class="" id="login-submit">Iniciar sesión</button>
        </div>

    </form>
</div>

<script src="../../js/password.js" asp-append-version="true"></script>