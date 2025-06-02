<div class="contenedor olvide">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Olvide mi contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>


        <form action="/olvide" class="formulario" method="post">
            <div class="campo">
                <label for="email">Email</label>
                <input type="mail" name="email" id="email" placeholder="Tu email">
            </div>

            <input type="submit" value="Enviar" class="boton">
        </form>

        <div class="acciones">
            <a href="/crear">No tengo una cuenta</a>
            <a href="/">Ya tengo una cuenta</a>
        </div>
    </div>
</div>