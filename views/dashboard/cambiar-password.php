<?php @include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php @include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver al perfil</a>

    <form action="/cambiar-password" class="formulario" method="post">
            <!-- <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Tu nombre" value="<?php echo $usuario->nombre; ?>">
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="mail" name="email" id="email" placeholder="Tu email" value="<?php echo $usuario->email; ?>">
            </div> -->
            <div class="campo">
                <label for="password">Contraseña Actual</label>
                <input type="password" name="password_actual" id="password" placeholder="Tu contraseña actual">
            </div>
            <div class="campo">
                <label for="password">Contraseña Nueva</label>
                <input type="password" name="password_nuevo" id="password" placeholder="Nueva contraseña">
            </div>
            <div class="campo">
                <label for="password2">Confirmar Contraseña</label>
                <input type="password" name="password2" id="password2" placeholder="Confirma tu contraseña">
            </div>

            <input type="submit" value="Guardar Cambios" class="boton">
        </form>

</div>


<?php @include_once __DIR__ . '/footer-dashboard.php'; ?>

