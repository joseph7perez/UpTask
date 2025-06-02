<div class="contenedor login">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>


        <form action="/" class="formulario" method="post">
            <div class="campo">
                <label for="email">Email</label>
                <input type="mail" name="email" id="email" placeholder="Tu email">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="Tu contraseña">
            </div>

            <input type="submit" value="Entrar" class="boton">
        </form>

        <div class="acciones">
            <a href="/crear">No tengo una cuenta</a>
            <a href="/olvide">Olvide mi contraseña</a>
        </div>
    </div>
</div>