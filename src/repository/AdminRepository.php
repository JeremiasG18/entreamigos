<?php

namespace Devscode\Entreamigos\repository;

use Devscode\Entreamigos\models\DBModel;
use PDO;

class AdminRepository extends DBModel{
 
    public function getDataFacility(int $id_usuario){
        $sql = "SELECT id, nombre, telefono, ubicacion, longitud, latitud, foto_url, id_mp FROM complejos WHERE id_usuario = :id_usuario";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function userExistsAndIsAdmin(int $id_usuario){
        $sql = "SELECT id_roles FROM usuarios WHERE id = :id_usuario";
        $stmt = $this->con()->prepare($sql);
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

}