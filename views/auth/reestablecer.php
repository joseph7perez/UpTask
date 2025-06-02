<div class="contenedor reestablecer">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu nueva contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if($mostrar) { ?>


        <form class="formulario" method="post">
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="Tu contraseña">
            </div>
            <div class="campo">
                <label for="password2">Confirmar Contraseña</label>
                <input type="password" name="password2" id="password2" placeholder="Confirma tu contraseña">
            </div>

            <input type="submit" value="Cambiar" class="boton">
        </form>

        <?php } ?>
    </div>
</div>