<?php

declare(strict_types=1);

namespace App\Fajare\models;

use App\Fajare\databases\Database;
use PDO;
use PDOException;

class Login {

    public static function findOne(string $username, string $password): array {

        try {
            $password_hash = hash("sha256", $password);

            $connect = Database::connect();
            $statement = $connect->prepare("SELECT T1.`userid`, T1.`username`, T1.`description`, T1.`password`, T2.`description` AS 'profile' FROM `username` T1 INNER JOIN `profiles` T2 ON T1.`profileid`=T2.`profileid` WHERE T1.`username`=:username AND T1.`password`=:password AND T1.`statu`=1;");
            $statement->bindParam(":username", $username, PDO::PARAM_STR);
            $statement->bindparam(":password", $password_hash, PDO::PARAM_STR);
            $statement->execute();
            
            if ($statement->rowCount()) {
                $rows = $statement->fetch(PDO::FETCH_ASSOC);

                session_destroy();

                session_start();
                
                $_SESSION = [];

                $_SESSION["access"][0] = $rows["username"];
                $_SESSION["access"][1] = $rows["description"];
                $_SESSION["access"][2] = $rows["profile"];
                $_SESSION["access"][3] = $rows["userid"];
                $_SESSION["access"][4] = time();

                return [
                    "message" => "Access allowed",
                    "title" => "Login",
                    "status" => "success"
                ];

            } else {
                return [
                    "message" => "Usuario o contraseña no válido.",
                    "title" => "Login",
                    "status" => "error"
                ];

            }

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Methodo fineOne()",
                "status" => "error"
            ];
        }
    }

}