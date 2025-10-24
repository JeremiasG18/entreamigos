<?php

namespace Devscode\Entreamigos\repository;

use Devscode\Entreamigos\models\DBModel;
use PDO;

class UserRepository extends DBModel{

    public function getFacilities()  {
        $sql = "SELECT id, nombre, ubicacion, foto_url FROM complejos";
        $stmt = $this->con()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function getFacilitiesByData(?array $datos) {

        // response([$datos[3]['valor']]);

        $sql = empty($datos[3]['valor']) ? "SELECT id, nombre, ubicacion, foto_url FROM complejos" : "SELECT id, nombre, ubicacion, foto_url, latitud, longitud, (6371 * acos(cos(radians(:latitud)) * cos(radians(latitud)) * cos(radians(longitud) - radians(:longitud)) + sin(radians(:latitud)) * sin(radians(latitud)))) AS distancia FROM complejos";

        $data = [];
        $i = 0;
        foreach ($datos as $key) {
            if (!empty($key['valor'])) {
                // if ($datos[]) {
                //     # code...
                // }
                if ($i === 0) {
                    $sql .= " WHERE ". $key['campo'] . " LIKE " . $key['marcador'];
                }else{
                    $sql .= " OR " . $key['campo'] . " LIKE " . $key['marcador'];
                }   
                $data[$key['marcador']] = "%" . $key['valor'] . "%";
                $i++;
            }
        }

        $sql .= ";";

        $stmt = $this->con()->prepare($sql);
        $stmt->execute($data);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        response($datos);
    }

    

}