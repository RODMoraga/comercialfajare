<?php

namespace App\Fajare\databases;

use PDO;
use PDOException;

/**
 * Cadena de conexión a la base de datos.
 * 
 * @package Database
 * @author Rodrigo Moraga Garrido
 * @see connect
 * @copyright 2025-03-06
 * @version 1.0.1
 */
class Database {

    public static function connect(): object | array {

        try {
            $str_connect = "mysql:host=localhost;dbname=schoolsalesdb;charset=utf8mb4";
            $pdo = new PDO(
                $str_connect,
                "root",
                "MySQL#2024$",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $pdo;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error conexión PDO",
                "status" => "error"
            ];
        }

    }

}
