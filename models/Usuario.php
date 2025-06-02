<?php 

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? null;
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //Validacion inicio de sesion
    public function validarLogin(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var( $this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email no es válido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        return self::$alertas;
    }

    // Validacion para cuentas nuevas
    public function validarNuevaCuenta(){
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        if (strlen($this->password) < 8 ) {
            self::$alertas['error'][] = 'La contraseña debe contener mínimo 8 carácteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las contraseñas deben ser iguales';
        }

        return self::$alertas;
    }

    //Hashea la contraseña
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);   
    }

    //Generar un token
    public function generarToken(){
        $this->token = uniqid();
    }

    //Validar un email
    public function validarEmail(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var( $this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email no es válido';
        }

        return self::$alertas;
    }

    //Validar nueva contraseña
    public function validarPassword(){
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        if (strlen($this->password) < 8 ) {
            self::$alertas['error'][] = 'La contraseña debe contener mínimo 8 carácteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las contraseñas deben ser iguales';
        }

        return self::$alertas;
    }

    public function validarPerfil(){
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    //Validar nueva contraseña
    public function nuevoPassword(){
        if (!$this->password_actual) {
            self::$alertas['error'][] = 'La contraseña actual es obligatoria';
        }
        if (!$this->password_nuevo) {
            self::$alertas['error'][] = 'La nueva contraseña es obligatoria';
        }
        if (strlen($this->password_nuevo) < 8 ) {
            self::$alertas['error'][] = 'La nueva contraseña debe contener mínimo 8 carácteres';
        }
        if ($this->password_nuevo !== $this->password2) {
            self::$alertas['error'][] = 'Las contraseñas deben ser iguales';
        }
        return self::$alertas;
    }

    //Comprobar la contraseña actual
    public function comprobarPassword(){
        return password_verify($this->password_actual, $this->password);
    }


}
