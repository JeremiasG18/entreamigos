<?php

namespace Devscode\Entreamigos\repository;

use Devscode\Entreamigos\models\DBModel;
use PDO;

class UserRepository extends DBModel{

    # Verificando si el correo esta en uso
    public function getEmail(string $correo){
        $sql = "SELECT correo FROM usuarios WHERE correo = :correo";
        $stmt = self::con()->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $respuesta;
    }

    # Proceso de inserción de datos en la db
    public function saveUser(int $id_rol, string $nombre, string $apellido, string $correo, string $hash){
        $sql = "INSERT INTO usuarios(id_roles, nombre, apellido, correo, contrasena) VALUES (:id_roles, :nombre, :apellido, :correo, :contrasena)";
        $sql = self::con()->prepare($sql);
        $respuesta = $sql->execute([
            ':id_roles' => $id_rol,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':correo' => $correo,
            ':contrasena' => $hash
        ]);
        return $respuesta;
    }

    public function userExistsAndIsAdmin(int $id_usuario){
        $sql = "SELECT id_roles FROM usuarios WHERE id = :id_usuario";
        $stmt = self::con()->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$datos) {
            return [
                'status' => 'error',
                'message' => 'No existe este usuario'
            ];
        }

        $id_rol = $datos['id_roles'];

        if ($id_rol !== 1) {
            return [
                'status' => 'error',
                'message' => 'El usuario no tiene el rol de administrador'
            ];
        }
    }

    # Proceso de inserción de datos a la base de datos
    public function saveFacility(int $id_usuario, string $nombre, string $telefono, string $ubicacion, string $longitud, string $latitud, string $foto_url, int $id_mp){
        $sql = "INSERT INTO complejos(id_usuario, nombre, telefono, ubicacion, longitud, latitud, foto_url, id_mp) VALUES (:id_usuario, :nombre, :telefono, :ubicacion, :longitud, :latitud, :foto_url, :id_mp)";

        $sql = $this->con()->prepare($sql);
        $respuesta = $sql->execute([
            ':id_usuario' => $id_usuario,
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':ubicacion' => $ubicacion,
            ':longitud' => $longitud,
            ':latitud' => $latitud,
            ':foto_url' => $foto_url,
            ':id_mp' => $id_mp,
        ]);

        return $respuesta;
    }

    public function getPassword(string $correo){
        $sql = "SELECT contrasena FROM usuarios WHERE correo = :correo";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $datos['contrasena'];
    }
    
}