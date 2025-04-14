<?php

declare(strict_types=1);

namespace App\Fajare\models;

use App\Fajare\databases\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PDO;
use PDOException;

/**
 * Modelo de datos para username
 * 
 * @author Rodrigo Moraga Garrido
 * @copyright 2025-04-12
 * @see existUsername
 * @see findAll
 * @see findAllProfile
 * @see save
 * @version 1.0.1
 * @package Username
 */
class Username {

    /**
     * Método para verificar si un usuario ya existe.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-13
     * @param $username Nombre del usuario a verificar
     * @return bool
     */
    public static function existUsername(string $username): bool {

        try {
            $query = "SELECT COUNT(*) FROM `username` WHERE `username`=?;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $username, PDO::PARAM_STR);
            $statement->execute();
            $status = (int)$statement->fetchColumn(0);

            return ($status ? true: false);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Este método retorna todos los usuarios de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-12
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT T1.`userid`
                , T1.`username`
                , T1.`description` AS 'fullname'
                , T1.`createat`
                , T2.`description` AS 'profile'
                , T1.`statu`
            FROM `username` T1
            INNER JOIN `profiles` T2 USING(`profileid`)
            WHERE T1.`userid`<>1
            ;";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $button  = "<button type=\"button\" class=\"btn btn-warning btn-edit-user\" data-toggle=\"modal\" data-target=\"#usernameModal\" data-table-edit=\"{$key["userid"]}\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></button>&nbsp;";
                $button .= "<button type=\"button\" class=\"btn btn-success btn-status-user\" data-table-status=\"{$key["userid"]}\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i></button>&nbsp;";

                $useractive = (int)$_SESSION["access"][3];
                $params     = (string)$useractive . ";;;" . $key["userid"] . ";;;" . $key["fullname"];

                if ($useractive === 1 || $useractive === 2)
                    $button .= "<button type=\"button\" class=\"btn btn-danger btn-delete-user\" data-table-user=\"{$params}\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
                else
                    $button .= "<button type=\"button\" class=\"btn btn-danger\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\" disabled></i></button>";

                $data[] = array(
                    "0" => $button,
                    "1" => $key["username"],
                    "2" => $key["fullname"],
                    "3" => $key["createat"],
                    "4" => $key["profile"],
                    "5" => (int)$key["statu"] ? "<span class=\"label bg-blue\">Activo</span>": "<span class=\"label bg-red\">Suspendido</span>"
                );
            }

            $results = array(
                "sEcho" => 1,                           //Información para el datatables
                "iTotalRecords" => count($data),        //enviamos el total registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
                "aaData" => $data
            );

            return $results;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error de ecepción",
                "status" => "Error"
            ];
        }
    }

    /**
     * Este método retorna todos los usuarios de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-12
     * @return string
     */
    public static function findAllProfile():string {

        try {
            $query = "SELECT * FROM `profiles`;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $data = "";

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data .= "<option value=\"{$key["profileid"]}\">{$key["description"]}</option>\n";
            }

            return $data;
        } catch (PDOException $e) {
            return "{$e->getMessage()};;;Error: {$e->getCode()};;;error";
        }
    }

    /**
     * Este método retorna los datos de un usuario
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-13
     * @param int $userid Id. del usuario
     * @return array
     */
    public static function findOne(int $userid):array {

        try {
            $query = "SELECT T1.`userid`
                , T1.`username`
                , T1.`description` AS 'fullname'
                , T1.`profileid`   AS 'profile'
            FROM `username` T1
            INNER JOIN `profiles` T2 USING(`profileid`)
            WHERE T1.`userid`=?
            ;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $userid, PDO::PARAM_INT);
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            return [
                "userid" => $data["userid"],
                "username" => $data["username"],
                "fullname" => $data["fullname"],
                "profile"  => $data["profile"]
            ];
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error: {$e->getCode()}",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para guardar los datos del usuario en la base de datos.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-13
     * @param array $data Contiene la información del usuario
     * @return array
     */
    public static function save(array $data): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));
        $logger->info(json_encode($data));

        try {
            $query = "INSERT INTO `username` VALUES(NULL, ?, ?, ?, NOW(), 1, ?)";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $data["username"], PDO::PARAM_STR);
            $statement->bindValue(2, $data["description"], PDO::PARAM_STR);
            $statement->bindValue(3, hash("sha256", $data["password"]), PDO::PARAM_STR);
            $statement->bindValue(4, $data["profile"], PDO::PARAM_STR);
            $statement->execute();

            return [
                "message" => "Los datos se guardaron exitosamente.",
                "title" => "Atención",
                "status" => "success"
            ];
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error {$e->getCode()}",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para eliminar un usuario
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-13
     * @param int $userid Id. del usuario a eliminar
     * @return array
     */
    public static function delete(int $userid): array {

        try {
            $query = "DELETE FROM `username` WHERE `userid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $userid, PDO::PARAM_INT);
            $statement->execute();

            return [
                "message" => "El registro se eliminó exitosamente.",
                "title" => "Eliminado",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error: {$e->getCode()}",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para cambiar el estado del usuario usuario
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-13
     * @param int $userid Id. del usuario
     * @return array
     */
    public static function status(int $userid): array {

        try {
            $query = "UPDATE `username` SET `statu`=IF(`statu`=1, 0, 1) WHERE `userid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $userid, PDO::PARAM_INT);
            $statement->execute();

            return [
                "message" => "El estado del usuario se actualizó exitosamente.",
                "title" => "Estado Usuario",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error: {$e->getCode()}",
                "status" => "error"
            ];
        }
    }

    /**
     * Método actualizar el usuario
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-13
     * @param int $userid Id. del usuario
     * @param array $data Información del usuario
     * @return array
     */
    public static function update(int $userid, array $data): array {

        try {
            $query = "UPDATE `username` SET `username`=?
                , `description`=?
                , `password`=?
                , `profileid`=?
            WHERE `userid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $data["username"], PDO::PARAM_STR);
            $statement->bindValue(2, $data["description"], PDO::PARAM_STR);
            $statement->bindValue(3, hash("sha256", $data["password"]), PDO::PARAM_STR);
            $statement->bindValue(4, $data["profile"], PDO::PARAM_INT);
            $statement->bindValue(5, $userid, PDO::PARAM_INT);
            $statement->execute();

            return [
                "message" => "El usuario se actualizó exitosamente.",
                "title" => "Actualizando",
                "status" => "success"
            ];
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error: {$e->getCode()}",
                "status" => "error"
            ];
        }
    }
}