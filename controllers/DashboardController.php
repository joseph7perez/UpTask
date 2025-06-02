<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use Model\Proyecto;
use MVC\Router;

class DashboardController{

    public static function index(Router $router){

        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsto('propietarioId', $id); //Traer todos los proyectos de ese id

     //   debuguear($proyectos);
        //Render a la vista
        $router->render('dashboard/index',[
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);

            //Validación
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {

                //Generar una URL unica
                $proyecto->generarURL();

                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //debuguear($proyecto->url);
         //       debuguear($proyecto);
                //Guardar el proyecto
                $resultado = $proyecto->guardar();

                //Redireccionar 
                header('Location: /proyecto?url=' . $proyecto->url);
            }
        }

        //Render a la vista
        $router->render('dashboard/crear-proyecto',[
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();

        $token = $_GET['url'];
        if(!$token) header('Location: /dashboard');

        // Revisar que el id de la persona que ve el proyecto es el mismo que lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']){ //Si la sesion iniciada es diferente al propietario
            header('Location: /dashboard');
        }

        //Render a la vista
        $router->render('dashboard/proyecto',[
            'titulo' => $proyecto->proyecto
           // 'alertas' => $alertas
        ]);
    }

    public static function perfil(Router $router) {
        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $alertas = [];
        $usuario = Usuario::find($id);

       // debuguear($usuario);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarPerfil();
      
            if (empty($alertas)) {
                //Verificar que el email no este en uso
                $existeUsuario = Usuario::where('email', $usuario->email);
                // Y que el usuario que existe en la BD debe ser diferente al que esta autenticado, por si solo quiero cambiar el nombre
                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    Usuario::setAlerta('error', 'Usuario no válido, el email ya está en uso');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Guardar el usuario
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Perfil actualizado correctamente');
                    $alertas = $usuario->getAlertas();

                    // Reescribir los datos de la sesion
                    $_SESSION['nombre'] = $usuario->nombre; //Asignar el nombre a la barra
                }
            }
        }

        //Render a la vista
        $router->render('dashboard/perfil',[
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($id);

            //Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevoPassword();

            if (empty($alertas)) {
                //Validar contraseña actual
                $resultado = $usuario->comprobarPassword();
                
                if($resultado){
                    unset($usuario->password_actual);//borrar el registro de la contra actual
                    //Asignar la nueva contraseña 
                    $usuario->password = $usuario->password_nuevo;

                    //Hasher el nuevo password
                    $usuario->hashPassword();

                    //Actualiza en la BD
                    
                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', 'Contraseña actualizada correctamente');
                    }

                }else {
                    Usuario::setAlerta('error', 'La contraseña actual no coincide');
                }
            }
        }

        $alertas = Usuario::getAlertas();

         //Render a la vista
         $router->render('dashboard/cambiar-password',[
            'titulo' => 'Cambiar contraseña',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);

    }

}