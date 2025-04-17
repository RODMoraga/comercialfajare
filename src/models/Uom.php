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
 * Modelo de datos para las Unidad de medidas
 * 
 * @author Rodrigo Moraga Garrido
 * @copyright 2025-04-16
 * @see existUOM
 * @see findAll
 * @see save
 * @see existProducts
 * @see update
 * @version 1.0.1
 * @package Uom
 */
class Uom {

    /**
     * Método para verificar si una unidad de medida ya existe.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param $description Nombre del UOM a verificar
     * @return bool
     */
    public static function existUOM(string $description): bool {

        try {
            $query = "SELECT COUNT(*) FROM `unitofmeasure` WHERE `description`=?;";
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
     * Método para verificar si una unidad de medida ya existe.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param $description Nombre del UOM a verificar
     * @return bool
     */
    public static function existProducts(int $uomid): bool {

        try {
            $query = "SELECT COUNT(*) FROM `products` WHERE `uomid`=?;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $uomid, PDO::PARAM_INT);
            $statement->execute();
            $status = (int)$statement->fetchColumn(0);

            return ($status ? true: false);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Este método retorna todos las UOM de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT T1.`uomid`
                , T1.`description`
                , NOW() AS 'createat'
                , 1 AS 'status'
            FROM `unitofmeasure` T1
            ;";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $button = "<button type=\"button\" class=\"btn btn-warning btn-edit-uom\" data-toggle=\"modal\" data-target=\"#uomModal\" data-table-edit=\"{$key["uomid"]}\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></button>&nbsp;";

                $useractive = (int)$_SESSION["access"][3];
                $params     = (string)$useractive . ";;;" . $key["uomid"] . ";;;" . $key["description"];

                if ($useractive === 1 || $useractive === 2)
                    $button .= "<button type=\"button\" class=\"btn btn-danger btn-delete-uom\" data-table-uom=\"{$params}\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
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
     * Este método retorna un UOM
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param int $uomid Id. de la UOM
     * @return array
     */
    public static function findOne(int $uomid):array {

        try {
            $query = "SELECT T1.`uomid`
                , T1.`description`
            FROM `unitofmeasure` T1
            WHERE T1.`uomid`=?
            ;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $uomid, PDO::PARAM_INT);
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            return [
                "uomid" => $data["uomid"],
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
     * Método para guardar los datos de la UOM en la base de datos.
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
            $query = "INSERT INTO `unitofmeasure` VALUES(NULL, ?)";
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
     * Método para eliminar una UOM
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-16
     * @param int $uomid Id. del banco a eliminar
     * @return array
     */
    public static function delete(int $uomid): array {

        try {
            $query = "DELETE FROM `unitofmeasure` WHERE `uomid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $uomid, PDO::PARAM_INT);
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
     * @param int $uomid Id. de la UOM
     * @param array $data Información de la UOM
     * @return array
     */
    public static function update(int $uomid, array $data): array {

        try {
            $query = "UPDATE `unitofmeasure` SET `description`=?
            WHERE `uomid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $data["description"], PDO::PARAM_STR);
            $statement->bindValue(2, $uomid, PDO::PARAM_INT);
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