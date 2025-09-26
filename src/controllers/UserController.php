<?php

namespace Devscode\Entreamigos\controllers;

use Devscode\Entreamigos\models\DBModel;
use PDO;

class UserController extends DBModel{

    public function index() {
        echo json_encode([["id"=>1, "name"=>"Juan"], ["id"=>2, "name"=>"Ana"]]);
    }

    public function show(string|int $id, ?string $nombre) {
        echo json_encode(["id"=>$id, "name"=>"Usuario ".$id]);
    }

    public function guardar(): void {
        # Recibir datos
        $data = json_decode(file_get_contents("php://input"), true);

        # Verificar que se haya recibido datos
        if (!is_array($data) || count($data) === 0) {
            respuesta([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['id_rol']) || empty($data['nombre']) || empty($data['apellido']) || empty($data['correo']) || empty($data['contrasena'])) {
            respuesta([
                'status' => 'error',
                'message' => 'Los campos requeridos están vacíos'
            ], 422);
        }

        # Limpiando y almacenando datos
        $id_rol = sanitizar_datos($data['id_rol'], 'int');
        $nombre = sanitizar_datos($data['nombre']);
        $apellido = sanitizar_datos($data['apellido']);
        $correo = sanitizar_datos($data['correo'], 'email');
        $contrasena = sanitizar_datos($data['contrasena']);

        # Verificar datos
        if ($id_rol < 1 || $id_rol > 2 ) {
            respuesta([
                'status' => 'error',
                'message' => 'El campo id_rol contiene caracteres inválidos'
            ], 422);
        }

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

        # Verificando si ya existe el correo en la base de datos
        $sql = "SELECT correo FROM usuarios WHERE correo = :correo";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (count($row) === 1) {
            respuesta([
                'status' => 'error',
                'message' => 'El correo electronico ya esta registrado'
            ], 409);
        }

        # Hasheando la contraseña
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        
        # Proceso de inserción de datos a la base de datos
        $sql = "INSERT INTO usuarios(id_roles, nombre, apellido, correo, contrasena) VALUES (:id_roles, :nombre, :apellido, :correo, :contrasena)";

        $sql = $this->con()->prepare($sql);
        $respuesta = $sql->execute([
            ':id_roles' => $id_rol,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':correo' => $correo,
            ':contrasena' => $hash
        ]);
        
        # Verficando si la operación salio correctamente
        if (!$respuesta) {
            respuesta([
                'status' => 'error',
                'message' => 'A ocurrido un error inesperado!, por favor intente mas tarde'
            ], 500);
        }

        respuesta([
            'status' => 'ok',
            'message' => 'El usuario se ha registrado exitosamente'
        ], 200);
    }

    public function guardarComplejo(): void{
        # Recibir datos
        $data = $_POST;
        $img = $_FILES;

        # Verificar que se haya recibido datos
        if (!is_array($data) || count($data) === 0 || empty($img)) {
            respuesta([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['id_usuario']) || empty($data['nombre']) || empty($data['telefono']) 
            || empty($data['ubicacion']) || empty($data['longitud']) || empty($data['latitud']) 
            || empty($img['foto_complejo']) || empty($data['id_mp'])) {
            respuesta([
                'status' => 'error',
                'message' => 'Los campos requeridos están vacíos'
            ], 422);
        }

        # Limpiando y almacenando datos
        $id_usuario = sanitizar_datos($data['id_usuario'], 'int');
        $nombre = sanitizar_datos($data['nombre']);
        $telefono = sanitizar_datos($data['telefono']);
        $ubicacion = sanitizar_datos($data['ubicacion']);
        $longitud = sanitizar_datos($data['longitud']);
        $latitud = sanitizar_datos($data['latitud']);
        // $foto_url = sanitizar_datos($data['foto_url']);
        $id_mp = sanitizar_datos($data['id_mp'], 'int');

        $sql = "SELECT id, id_roles FROM usuarios WHERE id = :id_usuario";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        respuesta([
            'datos' => $datos,
            'foto' => $_FILES
        ], 200);
        // $id_usuario = $datos['id'];
        // $id_rol = $datos['id_roles'];

        # Verificar datos
        // if (is_integer($id_usuario)) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'El campo id_usuario contiene caracteres inválidos'
        //     ], 422);
        // }

        // if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3, 50}', $nombre)) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'El campo nombre contiene caracteres inválidos'
        //     ], 422);
        // }

        // if (!verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]{3, 50}', $apellido)) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'El campo apellido contiene caracteres inválidos'
        //     ], 422);
        // }

        // if (!verificar_datos('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{10,60}', $correo)) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'El campo correo contiene caracteres inválidos'
        //     ], 422);
        // }

        // if (!verificar_datos('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'El campo contraseña contiene caracteres inválidos'
        //     ], 422);
        // }

        // # Verificando si ya existe el correo en la base de datos
        // $sql = "SELECT correo FROM usuarios WHERE correo = :correo";
        // $stmt = $this->con()->prepare($sql);
        // $stmt->execute([':correo' => $correo]);
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // if (count($row) === 1) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'El correo electronico ya esta registrado'
        //     ], 409);
        // }

        // # Hasheando la contraseña
        // $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        
        // # Proceso de inserción de datos a la base de datos
        // $sql = "INSERT INTO usuarios(id_roles, nombre, apellido, correo, contrasena) VALUES (:id_roles, :nombre, :apellido, :correo, :contrasena)";

        // $sql = $this->con()->prepare($sql);
        // $respuesta = $sql->execute([
        //     ':id_roles' => $id_rol,
        //     ':nombre' => $nombre,
        //     ':apellido' => $apellido,
        //     ':correo' => $correo,
        //     ':contrasena' => $hash
        // ]);
        
        // # Verficando si la operación salio correctamente
        // if (!$respuesta) {
        //     respuesta([
        //         'status' => 'error',
        //         'message' => 'A ocurrido un error inesperado!, por favor intente mas tarde'
        //     ], 500);
        // }

        // respuesta([
        //     'status' => 'ok',
        //     'message' => 'El usuario se ha registrado exitosamente'
        // ], 200);
        
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