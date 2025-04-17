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
 * Modelo de datos para los bancos
 * 
 * @author Rodrigo Moraga Garrido
 * @copyright 2025-04-16
 * @see existBankName
 * @see findAll
 * @see save
 * @see update
 * @version 1.0.1
 * @package Bank
 */
class Bank {

    /**
     * Método para verificar si un banco ya existe.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param $description Nombre del banco a verificar
     * @return bool
     */
    public static function existBankName(string $description): bool {

        try {
            $query = "SELECT COUNT(*) FROM `banks` WHERE `description`=?;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $description, PDO::PARAM_STR);
            $statement->execute();
            $status = (int)$statement->fetchColumn(0);

            return ($status ? true: false);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Este método retorna todos los bancos de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT T1.`bankid`
                , T1.`description`
                , NOW() AS 'createat'
                , 1 AS 'status'
            FROM `banks` T1
            ;";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $button = "<button type=\"button\" class=\"btn btn-warning btn-edit-bank\" data-toggle=\"modal\" data-target=\"#bankModal\" data-table-edit=\"{$key["bankid"]}\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></button>&nbsp;";

                $useractive = (int)$_SESSION["access"][3];
                $params     = (string)$useractive . ";;;" . $key["bankid"] . ";;;" . $key["description"];

                if ($useractive === 1 || $useractive === 2)
                    $button .= "<button type=\"button\" class=\"btn btn-danger btn-delete-banck\" data-table-banck=\"{$params}\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
                else
                    $button .= "<button type=\"button\" class=\"btn btn-danger\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\" disabled></i></button>";

                $data[] = array(
                    "0" => $button,
                    "1" => $key["description"],
                    "2" => $key["createat"],
                    "3" => (int)$key["status"] ? "<span class=\"label bg-blue\">Activo</span>": "<span class=\"label bg-red\">Suspendido</span>"
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
     * Este método retorna un banco
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param int $bankid Id. del usuario
     * @return array
     */
    public static function findOne(int $bankid):array {

        try {
            $query = "SELECT T1.`bankid`
                , T1.`description`
            FROM `banks` T1
            WHERE T1.`bankid`=?
            ;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $bankid, PDO::PARAM_INT);
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            return [
                "bankid" => $data["bankid"],
                "description" => $data["description"]
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
     * Método para guardar los datos del banco en la base de datos.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param array $data Contiene la información del banco
     * @return array
     */
    public static function save(array $data): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));
        $logger->info(json_encode($data));

        try {
            $query = "INSERT INTO `banks` VALUES(NULL, ?)";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $data["description"], PDO::PARAM_STR);
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
     * Método para eliminar un banco
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param int $bankid Id. del banco a eliminar
     * @return array
     */
    public static function delete(int $bankid): array {

        try {
            $query = "DELETE FROM `banks` WHERE `bankid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $bankid, PDO::PARAM_INT);
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
     * Método actualizar el banco
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param int $bankid Id. del banco
     * @param array $data Información del banco
     * @return array
     */
    public static function update(int $bankid, array $data): array {

        try {
            $query = "UPDATE `banks` SET `description`=?
            WHERE `bankid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $data["description"], PDO::PARAM_STR);
            $statement->bindValue(2, $bankid, PDO::PARAM_INT);
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