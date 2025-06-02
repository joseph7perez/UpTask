<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use Model\Proyecto;
use Model\Tarea;
use MVC\Router;

class TareaController{

    public static function index(){

        //debuguear($_GET);
        $proyectoUrl = $_GET['url'];
        if (!$proyectoUrl) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoUrl);

        session_start();
        if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belongsto('proyectoId', $proyecto->id); //Traer solo las tareas de ese proyecto
        //debuguear($tareas);
        echo json_encode(['tareas' => $tareas]);

    }
  
    public static function crear(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            session_start();

            $proyectoId = $_POST['proyectoId'];

            $proyecto = Proyecto::where('url',$proyectoId);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                //Esta respuesta se le envia al backend
                $respuesta = [ 
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al almacenar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            //Instanciar y crear la tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
        //    echo json_encode($resultado);
            $respuesta = [ 
                'tipo' => 'exito',
                'mensaje' => 'Tarea almacenada correctamente',
                'id' => $resultado['id'],
                'proyectoId' => $proyecto->id
            ];

            echo json_encode($respuesta);      
        }
    }

    public static function actualizar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Validar que el proyecto existe
            session_start();

            $proyectoId = $_POST['proyectoId'];
            $proyecto = Proyecto::where('url',$proyectoId);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                //Esta respuesta se le envia al backend
                $respuesta = [ 
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            if ($resultado) {
                $respuesta = [ 
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'mensaje' => 'Tarea actualizada correctamente',
                    'proyectoId' => $proyecto->id
                ];
                echo json_encode(['respuesta' => $respuesta]);      
            }

       //     echo json_encode(['resultado' => $resultado]);
        }
    }

    public static function eliminar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Validar que el proyecto existe
            session_start();

            $proyectoId = $_POST['proyectoId'];
            $proyecto = Proyecto::where('url',$proyectoId);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                //Esta respuesta se le envia al backend
                $respuesta = [ 
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
           // echo json_encode(['tarea' => $tarea]);      

            $resultado = $tarea->eliminar();
            if ($resultado) {
                $respuesta = [ 
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'mensaje' => 'Tarea eliminada correctamente',
                    'proyectoId' => $proyecto->id
                ];
                echo json_encode(['respuesta' => $respuesta]);      
            }
        }
    }
}