<?php

namespace Devscode\Entreamigos\controllers;

use Devscode\Entreamigos\repository\AdminRepository;
use Devscode\Entreamigos\validators\AdminValidator;

class AdminController{

    private AdminRepository $admin_repository;
    private AdminValidator $admin_validator;

    public function __construct(AdminRepository $admin_repository, AdminValidator $admin_validator)
    {
        $this->admin_repository = $admin_repository;
        $this->admin_validator = $admin_validator;
    }

    public function registerFacility(): void {
        $data_user = validate_token();

        if ($data_user->rol !== 1) {
            response([
                'status' => 'error',
                'message' => 'Eres un usuario no puedes usar estas funcionalidades!'
            ], 403);
        }

        # Recibiendo datos
        $data = $_POST;
        $img = $_FILES;

        # Verificando que se haya recibido datos
        if (!is_array($data) || count($data) === 0 || empty($img)) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['id_usuario']) || empty($data['nombre']) || empty($data['telefono']) 
            || empty($data['ubicacion']) || empty($data['longitud']) || empty($data['latitud']) 
            || empty($img['foto_complejo']) || empty($data['id_mp'])) {
            response([
                'status' => 'error',
                'message' => 'Los campos requeridos están vacíos'
            ], 422);
        }

        # Limpiando y almacenando datos
        $id_usuario = sanitize_data($data['id_usuario'], 'int');
        $nombre = sanitize_data($data['nombre']);
        $telefono = sanitize_data($data['telefono']);
        $ubicacion = sanitize_data($data['ubicacion']);
        $longitud = sanitize_data($data['longitud']);
        $latitud = sanitize_data($data['latitud']);
        $id_mp = sanitize_data($data['id_mp'], 'int');

        # Verificando que exista el usuario y que su rol sea de administrador
        $respuesta = $this->admin_repository->userExistsAndIsAdmin($id_usuario);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 404);
        }

        # Verificando la integridad de los datos
        $respuesta = $this->admin_validator->validateFacility($nombre, $telefono, $ubicacion, $longitud, $latitud, $id_mp);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 422);
        }

        if($this->admin_repository->getDataFacility($id_usuario)){
            response([
                'status' => 'error',
                'message' => 'Ya tienes un complejo registrado. Solo puedes tener uno.'
            ], 409);
        }

        # Subiendo la imagen del complejo
        $foto_url = upload_image($img['foto_complejo']);
        $foto_url = $foto_url['status'] === 'ok' ? $foto_url['url'] 
        : response($foto_url, 422);
        
        # Verficando si la operación salio correctamente
        if ($this->admin_repository->saveFacility($id_usuario, $nombre, $telefono, $ubicacion, $longitud, $latitud, $foto_url, $id_mp)) {
            response([
                'status' => 'ok',
                'message' => 'El complejo se ha registrado exitosamente'
            ], 200);
        }
        
        response([
            'status' => 'error',
            'message' => 'A ocurrido un error inesperado!, por favor intente mas tarde'
        ], 500);
    }

    public function facility(){
        $data_user = validate_token();

        if ($data_user->rol != 1) {
            response([
                'status' => 'error',
                'message' => 'Eres un usuario no puedes usar estas funcionalidades!'
            ], 403);
        }

        $respuesta = $this->admin_repository->getDataFacility($data_user->id);
        if($respuesta){
            response([
                'status' => 'ok',
                'facility' => $respuesta,
                'token' => $data_user
            ], 200);
        }

        response([
            'status' => 'ok',
            'message' => 'Por favor registra tu complejo!',
        ], 200);

    }

}