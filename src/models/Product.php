<?php

declare(strict_types=1);

namespace App\Fajare\models;

use App\Fajare\databases\Database;
use PDO;
use PDOException;

/**
 * @package Product
 * @author Rodrigo Moraga Garrido
 * @since
 * @see findAll
 * @see findAllCategories
 * @see findAllUOM
 * @see update
 * @see save
 * @see status
 * @access public
 * @version 1.0.1
 * @copyright 2025-03-07
 */
class Product {

    /**
     * Retorna todos los productos existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAll
     * @version 1.0.1
     * @copyright 2025-03-06
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT * FROM `products`";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();
            $btns = "";

            foreach ($rows as $key) {
                $btns  = "<button class=\"btn btn-primary btn-sm\" data-toggle=\"tooltip\" title=\"Editar producto\" onclick=\"findOne({$key["productid"]})\"><i class=\"fa fa-pencil\"></i></button>";
                $btns .= "&nbsp;<button class=\"btn btn-success btn-sm\" data-toggle=\"tooltip\" title=\"Activar producto\" onclick=\"statusChange({$key["productid"]})\"><i class=\"fa fa-check\"></i></button>";

                $data[] = array(
                    "0" => $btns,
                    "1" => $key["productcode"],
                    "2" => $key["productname"],
                    "3" => $key["barcode"],
                    "4" => $key["brand"],
                    "5" => $key["createat"],
                    "6" => $key["updateof"],
                    "7" => ((int)$key["statu"] > 0) ? "<span class=\"label bg-green\">Activo</span>": "<span class=\"label bg-red\">Suspendido</span>"
                );
            }

            $result = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data);
            return $result;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Regiones",
                "status" => "error"
            ];
        }
    }

    /**
     * Retorna todas las categories existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAllCategories
     * @version 1.0.1
     * @copyright 2025-03-06
     * @return array
     */
    public static function findAllCategories(): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT * FROM `Categories` ORDER BY `description`;");
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["categoryid"],
                    "name" => $key["description"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Regiones",
                "status" => "error"
            ];
        }
    }

    /**
     * Retorna todas las unidades de medicas existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAllUOM
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function findAllUOM(): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT * FROM `unitofmeasure` ORDER BY `description`;");
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["uomid"],
                    "name" => $key["description"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga ciudades",
                "status" => "error"
            ];
        }
    }

    /**
     * Busca un producto por su Id
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findOne
     * @version 1.0.1
     * @copyright 2025-03-06
     * @return array
     */
    public static function findOne(string $id): array {

        try {
            $query = "SELECT * FROM `products` WHERE `productid`=:productid;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute([":productid" => $id]);
            $rows = $statement->fetch(PDO::FETCH_ASSOC);
            $data = array();

            $data = array(
                "productid" => $rows["productid"],
                "productcode" => $rows["productcode"],
                "productname" => $rows["productname"],
                "barcode" => $rows["barcode"],
                "uomid" => $rows["uomid"],
                "categoryid" => $rows["categoryid"],
                "weight" => $rows["weight"],
                "volume" => $rows["volume"],
                "long" => $rows["long"],
                "width" => $rows["width"],
                "height" => $rows["height"],
                "brand" => $rows["brand"]
            );

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Regiones",
                "status" => "error"
            ];
        }
    }

    /**
     * Actualiza un registro a la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see save
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function update(string $id, array $data): array {

        try {
            session_start();

            $connect = Database::connect();
            $statement = $connect->prepare("UPDATE `products` SET `productcode`=:productcode, `productname`=:productname, `barcode`=:barcode, `uomid`=:uomid, `categoryid`=:categoryid, `weight`=:weight, `volume`=:volume, `long`=:long, `width`=:width, `height`=:height, `brand`=:brand, `updateof`=CURDATE() WHERE `productid`=:productid;");
            $statement->bindParam(":productid", $id, PDO::PARAM_INT);
            $statement->bindParam(":productcode", $data["productcode"], PDO::PARAM_STR);
            $statement->bindParam(":productname", $data["productname"], PDO::PARAM_STR);
            $statement->bindParam(":barcode", $data["barcode"], PDO::PARAM_STR);
            $statement->bindParam(":uomid", $data["uomid"], PDO::PARAM_INT);
            $statement->bindParam(":categoryid", $data["categoryid"], PDO::PARAM_INT);
            $statement->bindParam(":weight", $data["weight"], PDO::PARAM_INT);
            $statement->bindParam(":volume", $data["volume"], PDO::PARAM_INT);
            $statement->bindParam(":long", $data["long"], PDO::PARAM_INT);
            $statement->bindParam(":width", $data["width"], PDO::PARAM_INT);
            $statement->bindParam(":height", $data["height"], PDO::PARAM_INT);
            $statement->bindParam(":brand", $data["brand"], PDO::PARAM_STR);
            $statement->execute();

            return [
                "message" => "Los datos se actualizaron exitosamente.!",
                "title" => "Datos actualizados",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            $stream = fopen("src/logs/info.log", "w");
            fwrite($stream, $data . "\n");
            fwrite($stream, "{$e->getMessage()}\n");
            fclose($stream);

            return [
                "message" => $e->getMessage(),
                "title" => "Actualizadon productos",
                "status" => "error"
            ];
        }
    }

    /**
     * Insertar un registro a la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see save
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function save(array $data): array {

        try {
            session_start();

            $connect = Database::connect();
            $statement = $connect->prepare("INSERT INTO `products` VALUES(NULL, :productcode, :productname, :barcode, :uomid, :categoryid, :weight, :volume, :long, :width, :height, :brand, NULL, CURDATE(), CURDATE(), 1);");
            $statement->bindParam(":productcode", $data["productcode"], PDO::PARAM_STR);
            $statement->bindParam(":productname", $data["productname"], PDO::PARAM_STR);
            $statement->bindParam(":barcode", $data["barcode"], PDO::PARAM_STR);
            $statement->bindParam(":uomid", $data["uomid"], PDO::PARAM_INT);
            $statement->bindParam(":categoryid", $data["categoryid"], PDO::PARAM_INT);
            $statement->bindParam(":weight", $data["weight"], PDO::PARAM_INT);
            $statement->bindParam(":volume", $data["volume"], PDO::PARAM_INT);
            $statement->bindParam(":long", $data["long"], PDO::PARAM_INT);
            $statement->bindParam(":width", $data["width"], PDO::PARAM_INT);
            $statement->bindParam(":height", $data["height"], PDO::PARAM_INT);
            $statement->bindParam(":brand", $data["brand"], PDO::PARAM_STR);
            $statement->execute();

            return [
                "message" => "Los datos se agregaron exitosamente.!",
                "title" => "Datos agregados",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga ciudades",
                "status" => "error"
            ];
        }
    }

    /**
     * Buscar el estado del producto y lo cambia...
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see status
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function status(int $id): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("UPDATE `products` SET `statu`=:statu WHERE `productid`=:productid;");
            $statement->bindParam(":productid", $id, PDO::PARAM_INT);
            $statement->bindParam(":statu", self::statusChange($id), PDO::PARAM_INT);
            $statement->execute();

            return [
                "message" => "Se ha actualizado el estado del cliente.",
                "title" => "Actualizando estado",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error al actualizar el estado",
                "status" => "error"
            ];
        }
    }

    /**
     * Buscar el estado del producto y lo cambia...
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see statusChange
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    private static function statusChange(int $id): int {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT `statu` FROM `products` WHERE `productid`=:productid");
            $statement->execute([":productid" => $id]);
            $statusVal = $statement->fetchColumn(0);

            return ($statusVal === 0 ? 1: 0);

        } catch (PDOException $th) {
            return 1;
        }
    }

}