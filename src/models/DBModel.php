<?php

namespace Devscode\Entreamigos\models;

use PDO;
use PDOException;

class DBModel{

    private string $host = 'localhost';
    private string $user = 'root';
    private string $password = '';
    private string $db = 'db_entreamigos';
    private ?PDO $con = null;

    public function con(): PDO {
        if($this->con === null){
            try{
                $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8";
                $this->con = new PDO($dsn, $this->user, $this->password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
                ]);
            }catch(PDOException $e){
                die('Error: ' . $e->getMessage());
            }
        }
        return $this->con;
    }

}