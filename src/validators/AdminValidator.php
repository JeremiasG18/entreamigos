<?php

namespace Devscode\Entreamigos\validators;

class AdminValidator{

    public function validateFacility($nombre, $telefono, $ubicacion, $longitud, $latitud, $id_mp){
        if (!validate_data('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\&]{3,50}', $nombre)) {
            return [
                'status' => 'error',
                'message' => 'El campo nombre del complejo contiene caracteres inválidos'
            ];
        }

        if (!validate_data('[+\d\s\-\(\)]{7,20}', $telefono)) {
            return [
                'status' => 'error',
                'message' => 'El campo telefono contiene caracteres inválidos'
            ];
        }

        if (!validate_data('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.,#\-\º°]{3,100}', $ubicacion)) {
            return [
                'status' => 'error',
                'message' => 'El campo ubicación contiene caracteres inválidos'
            ];
        }

        if (!validate_data('(?:-?(?:180(?:\.0+)?|(?:1[0-7]\d|[1-9]?\d)(?:\.\d+)?))', $longitud)) {
            return [
                'status' => 'error',
                'message' => 'El campo longitud contiene caracteres inválidos'
            ];
        }

        if (!validate_data('(?:-?(?:[0-8]?\d(?:\.\d+)?|90(?:\.0+)?))', $latitud)) {
            return [
                'status' => 'error',
                'message' => 'El campo latitud contiene caracteres inválidos'
            ];
        }

        if (!validate_data('[0-9]{5,15}', $id_mp)) {
            return [
                'status' => 'error',
                'message' => 'El campo id del mercado pago del comerciante contiene caracteres inválidos'
            ];
        }
    }

}