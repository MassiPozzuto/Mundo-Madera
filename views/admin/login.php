<div class="container__data">
    <div class="container__logo">
        <img src="../../img/logo_inicial.png" alt="Logo Mundo Madera" width="300px">
    </div>

    <div class="container__title">
        <p>Inicia sesión en Mundo Madera</p>
    </div>

    <form method="POST" class="login__form" id="formLogin">

        <div class="form-group" style="display: none;">
            <p class="" id="msj_error_login"></p>
        </div>

        <div class="form-group">
            <label for="email" class="input-title">Nombre de usuario</label>
            <input type="email" name="email" id="email">
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