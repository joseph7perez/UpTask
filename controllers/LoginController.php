<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){

        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                //Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                } else {
                    // Verificar la contraseña
                    if (password_verify($_POST['password'], $usuario->password)) {
                        //Inicar la sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                
                        //Redireccionar
                        header('Location: /dashboard');

                    } else {
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }

            }
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }
    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /'); 
    }

    public static function crear(Router $router){

        $alertas = [];
        $usuario = new Usuario();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            //debuguear($alertas);

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario){
                    Usuario::setAlerta('error', 'El Usuario ya existe');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    //Eliminar password2
                    unset($usuario->password2);

                    //Generar el token
                    $usuario->generarToken();

                  //  debuguear($usuario);

                    //Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                   // debuguear($email);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }           
        }

        //Render a la vista
        $router->render('auth/crear',[
            'titulo' => 'Crear cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado === "1") {

                    unset($usuario->password2);
                    // Generar un nuevo token
                    $usuario->generarToken();

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                    
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }

            }

        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/olvide',[
            'titulo' => 'Olvide mi contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){

        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) header('Location: /');

        //Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // No se encontro un usuraio con ese token
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Añadir la  nueva contraseña a la BD
            $usuario->sincronizar($_POST);

            //Validar la contraseña
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                //Hashear la nueva contraseña

                $usuario->hashPassword();

                //Eliminar el token
                $usuario->token = null;
                unset($usuario->password2);              

                //Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                //Redireecionar
                if ($resultado) {
                    header('Location: /');
                }
            }

        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/reestablecer',[
            'titulo' => 'Crear contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        //Render a la vista
        $router->render('auth/mensaje',[
        'titulo' => 'Mensaje confirmación'
        
        ]);

    }

    public static function confirmar(Router $router){

        $token = s($_GET['token']);

        if (!$token) header('Location: /');

        //Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // No se encontro un usuraio con ese token
            Usuario::setAlerta('error', 'Token no válido');
        } else{
            // Confirmar la cuenta
            $usuario->confirmado = 1;

            //Elimminar token
            $usuario->token = null;
            unset($usuario->password2);          

            // Guardar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/confirmar',[
            'titulo' => 'Mensaje confirmación',
            'alertas' => $alertas
        ]); 
    }
}
