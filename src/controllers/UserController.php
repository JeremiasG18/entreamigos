<?php

namespace Devscode\Entreamigos\controllers;

class UserController {

    public function index() {
        echo json_encode([["id"=>1, "name"=>"Juan"], ["id"=>2, "name"=>"Ana"]]);
    }

    public function show(string|int $id, ?string $nombre) {
        echo json_encode(["id"=>$id, "name"=>"Usuario ".$id]);
    }

    public function guardar(string|null|array $data) {

        # Recibir datos
        $data = json_decode(file_get_contents("php://input"), true);

        # Verificar que se haya recibido datos
        if (!is_array($data) || count($data) === 0) {
            respuesta([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['correo']) || empty($data['contrasena'])) {
            respuesta([
                'status' => 'error',
                'message' => 'Los campos requeridos están vacíos'
            ], 422);
        }

        # Limpiar datos
        $nombre = sanitizar_datos($data['nombre']);
        $apellido = sanitizar_datos($data['apellido']);
        $correo = sanitizar_datos($data['correo'], 'email');
        $contrasena = sanitizar_datos($data['contrasena']);

        # Verificar datos
        if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3, 50}', $nombre)) {
            respuesta([
                'status' => 'error',
                'message' => 'El campo nombre contiene caracteres inválidos'
            ], 422);
        }

        if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3, 50}', $apellido)) {
            respuesta([
                'status' => 'error',
                'message' => 'El campo apellido contiene caracteres inválidos'
            ], 422);
        }

        if (!verificar_datos('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{10,60}', $correo)) {
            respuesta([
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ], 422);
        }

        if (!verificar_datos('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
            respuesta([
                'status' => 'error',
                'message' => 'El campo contraseña contiene caracteres inválidos'
            ], 422);
        }
        
        respuesta([
            'status' => 'ok',
            'data' => [
                'nombre' => $nombre,
                'apellido' => $apellido
            ]
        ], 200);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode(["message"=>"Usuario $id actualizado", "data"=>$data]);
    }

    public function destroy($id) {
        echo json_encode(["message"=>"Usuario $id eliminado"]);
    }

    public function showByName($nombre){
        echo json_encode(["message"=>"Usuario $nombre ha sido encontrado"]);
    }
}