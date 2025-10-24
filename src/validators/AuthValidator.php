<?php

namespace Devscode\Entreamigos\validators;

class AuthValidator{

    public function validateUser($id_rol, $nombre, $apellido, $correo, $contrasena){
        if ($id_rol < 1 || $id_rol > 2 ) {
            return [
                'status' => 'error',
                'message' => 'El campo id_rol contiene caracteres inválidos'
            ];
        }

        if (!validate_data('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3,50}', $nombre)) {
            return [
                'status' => 'error',
                'message' => 'El campo nombre contiene caracteres inválidos'
            ];
        }

        if (!validate_data('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3,50}', $apellido)) {
            return [
                'status' => 'error',
                'message' => 'El campo apellido contiene caracteres inválidos'
            ];
        }

        if (!validate_data('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}', $correo) || strlen($correo) > 100) {
            return [
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ];
        }

        if (!validate_data('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
            return [
                'status' => 'error',
                'message' => 'El campo contraseña contiene caracteres inválidos'
            ];
        }
    }

    public function validateLogin($correo, $contrasena){
        if (!validate_data('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}', $correo) || strlen($correo) > 100) {
            return [
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ];
        }

        if (!validate_data('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
            return [
                'status' => 'error',
                'message' => 'El campo contraseña contiene caracteres inválidos'
            ];
        }
    }

}