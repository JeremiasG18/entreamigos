<?php

namespace Devscode\Entreamigos\controllers;

use Devscode\Entreamigos\repository\UserRepository;

class UserController{

    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function listFacilities(array|null|string $data) {

        $data_user = validate_token();

        if ($data_user->rol != 2) {
            response([
                'status' => 'error',
                'message' => 'Eres un administrador no puedes usar estas funcionalidades!'
            ], 403);
        }

        if (empty($data)) {
            $complejos = $this->user_repository->getFacilities();
            
            response([
                'status' => 'ok',
                'facilities' => $complejos,
                'token' => $data_user
            ], 200);
        }

        $nombre = !empty($data['nombre']) ? sanitize_data($data['nombre']) : '';
        $direccion = !empty($data['direccion']) ? sanitize_data($data['direccion']) : '';
        $ubicacion = !empty($data['ubicacion']) ? sanitize_data($data['ubicacion']) : '';
        $precio = !empty($data['precio']) ? true : '';

        $ubicacion = explode(',', $ubicacion);
        $longitud = !empty($ubicacion[0]) ? $ubicacion[0] : '';
        $latitud = !empty($ubicacion[1]) ? $ubicacion[1] : '';

        $nombre = validate_data('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\&]{3,50}', $nombre) ? $nombre : '';
        $direccion = validate_data('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.,#\-\º°]{3,100}', $direccion) ? $direccion : '';
        $longitud = validate_data('(?:-?(?:180(?:\.0+)?|(?:1[0-7]\d|[1-9]?\d)(?:\.\d+)?))', $longitud) ? $longitud : '';
        $latitud = validate_data('(?:-?(?:[0-8]?\d(?:\.\d+)?|90(?:\.0+)?))', $ubicacion[1]) ? $ubicacion[1] : '';

        $datos = [
            [
                'campo' => 'nombre',
                'marcador' => ':nombre',
                'valor' => $nombre
            ],
            [
                'campo' => 'ubicacion',
                'marcador' => ':ubicacion',
                'valor' => $direccion
            ],
            [
                'campo' => 'longitud',
                'marcador' => ':longitud',
                'valor' => $longitud
            ],
            [
                'campo' => 'latitud',
                'marcador' => ':latitud',
                'valor' => $latitud
            ]
        ];

        $complejos = $this->user_repository->getFacilitiesByData($datos);


        // if (!empty($data['nombre']) && empty($data['ubicacion']) && empty($data['tipo']) && empty($data['precio'])) {

        // }
            // $nombre = sanitize_data($data['nombre']);
            // if (!validate_data('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\&]{3,50}', $nombre)) {
            //     response([
            //         'status' => 'error',
            //         'message' => 'El campo nombre del complejo contiene caracteres inválidos'
            //     ], 422);
            // }
        // }

        response($data, 200);

    }
    
}