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
 * Modelo de datos para las Categorias
 * 
 * @author Rodrigo Moraga Garrido
 * @copyright 2025-04-17
 * @see existCategory
 * @see findAll
 * @see save
 * @see existProducts
 * @see update
 * @version 1.0.1
 * @package Categories
 */
class Category {

    /**
     * Método para verificar si la categoria ya existe.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @param $description Nombre de la categoria a verificar
     * @return bool
     */
    public static function existCategory(string $description): bool {

        try {
            $query = "SELECT COUNT(*) FROM `categories` WHERE `description`=?;";
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
     * Método para verificar si una categoria ya existe en la maestra de productos.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @param $description Nombre de la categoria a verificar
     * @return bool
     */
    public static function existProducts(int $categoryid): bool {

        try {
            $query = "SELECT COUNT(*) FROM `products` WHERE `categoryid`=?;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $categoryid, PDO::PARAM_INT);
            $statement->execute();
            $status = (int)$statement->fetchColumn(0);

            return ($status ? true: false);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Este método retorna todos las categorias de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT T1.`categoryid`
                , T1.`description`
                , NOW() AS 'createat'
                , 1 AS 'status'
            FROM `categories` T1
            ;";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $button = "<button type=\"button\" class=\"btn btn-warning btn-edit-category\" data-toggle=\"modal\" data-target=\"#categoryModal\" data-table-edit=\"{$key["categoryid"]}\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i></button>&nbsp;";

                $useractive = (int)$_SESSION["access"][3];
                $params     = (string)$useractive . ";;;" . $key["categoryid"] . ";;;" . $key["description"];

                if ($useractive === 1 || $useractive === 2)
                    $button .= "<button type=\"button\" class=\"btn btn-danger btn-delete-category\" data-table-category=\"{$params}\"><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></button>";
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
     * Este método retorna los datos individuales de la categoria
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @param int $categoryid Id. de la category
     * @return array
     */
    public static function findOne(int $categoryid):array {

        try {
            $query = "SELECT T1.`categoryid`
                , T1.`description`
            FROM `categories` T1
            WHERE T1.`categoryid`=?
            ;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $categoryid, PDO::PARAM_INT);
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            return [
                "categoryid" => $data["categoryid"],
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
     * Método para guardar los datos de las categorias en la base de datos.
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @param array $data Contiene la información de las categorias
     * @return array
     */
    public static function save(array $data): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));
        $logger->info(json_encode($data));

        try {
            $query = "INSERT INTO `categories` VALUES(NULL, ?)";
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
     * Método para eliminar una categoria
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @param int $categoryid Id. de la categoria a eliminar
     * @return array
     */
    public static function delete(int $categoryid): array {

        try {
            $query = "DELETE FROM `categories` WHERE `categoryid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $categoryid, PDO::PARAM_INT);
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
     * Método actualizar la categoría
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-17
     * @param int $categoryid Id. de la category
     * @param array $data Información de la categoria
     * @return array
     */
    public static function update(int $categoryid, array $data): array {

        try {
            $query = "UPDATE `categories` SET `description`=?
            WHERE `categoryid`=?";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindValue(1, $data["description"], PDO::PARAM_STR);
            $statement->bindValue(2, $categoryid, PDO::PARAM_INT);
            $statement->execute();

            return [
                "message" => "El registro se actualizó exitosamente.",
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