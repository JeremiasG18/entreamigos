<?php

namespace Devscode\Entreamigos\validators;

class UserValidator{

    public function validateUser($id_rol, $nombre, $apellido, $correo, $contrasena){
        if ($id_rol < 1 || $id_rol > 2 ) {
            return [
                'status' => 'error',
                'message' => 'El campo id_rol contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3,50}', $nombre)) {
            return [
                'status' => 'error',
                'message' => 'El campo nombre contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3,50}', $apellido)) {
            return [
                'status' => 'error',
                'message' => 'El campo apellido contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}', $correo) || strlen($correo) > 100) {
            return [
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
            return [
                'status' => 'error',
                'message' => 'El campo contraseña contiene caracteres inválidos'
            ];
        }
    }

    public function validateFacility($nombre, $telefono, $ubicacion, $longitud, $latitud, $id_mp){
        if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\&]{3,50}', $nombre)) {
            return [
                'status' => 'error',
                'message' => 'El campo nombre del complejo contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('[+\d\s\-\(\)]{7,20}', $telefono)) {
            return [
                'status' => 'error',
                'message' => 'El campo telefono contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.,#\-\º°]{3,100}', $ubicacion)) {
            return [
                'status' => 'error',
                'message' => 'El campo ubicación contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('(?:-?(?:180(?:\.0+)?|(?:1[0-7]\d|[1-9]?\d)(?:\.\d+)?))', $longitud)) {
            return [
                'status' => 'error',
                'message' => 'El campo longitud contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('(?:-?(?:[0-8]?\d(?:\.\d+)?|90(?:\.0+)?))', $latitud)) {
            return [
                'status' => 'error',
                'message' => 'El campo latitud contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('[0-9]{5,15}', $id_mp)) {
            return [
                'status' => 'error',
                'message' => 'El campo id del mercado pago del comerciante contiene caracteres inválidos'
            ];
        }
    }

    public function validateLogin($correo, $contrasena){
        if (!verificar_datos('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}', $correo) || strlen($correo) > 100) {
            return [
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ];
        }

        if (!verificar_datos('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
            return [
                'status' => 'error',
                'message' => 'El campo contraseña contiene caracteres inválidos'
            ];
        }
    }

}