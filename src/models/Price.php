<?php

declare(strict_types=1);

namespace App\Fajare\models;

use App\Fajare\databases\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PDO;
use PDOException;

session_start();

class Price {

    /**
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT T3.`customerid`
                , T3.`customername`
                , T3.`complex`
                , T4.`productcode`
                , T4.`productname`
                , T2.`price`
                , T2.`discount1`
            FROM `headerprice` T1
            INNER JOIN `detailprice` T2 ON T1.`headerpriceid`=T2.`headerpriceid`
            INNER JOIN `customers`   T3 ON T1.`customerid`=T3.`customerid`
            INNER JOIN `products`    T4 ON T2.`productid`=T4.`productid`
            ";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => "",
                    "1" => $key["customername"],
                    "2" => $key["complex"],
                    "3" => $key["productcode"],
                    "4" => $key["productname"],
                    "5" => $key["price"],
                    "6" => $key["discount1"]
                );
            }

            $result = array(
                "sEcho" => 1,                               // Información para el datatables
                "iTotalRecords" => count($data),            // Enviamos el total registros al datatable
                "iTotalDisplayRecords" => count($data),     // Enviamos el total registros a visualizar
                "aaData" => $data
            );

            return $result;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga de precios",
                "status" => "error"
            ];
        }
    }

    public static function findAllComplex(): array {
        try {
            $query = "SELECT `customerid`, `complex`
                FROM `customers`
                ORDER BY `complex` ASC;
            ";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["customerid"],
                    "name" => $key["complex"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga de clientes",
                "status" => "error"
            ];
        }
    }

    public static function findAllCustomername(): array {
        try {
            $query = "SELECT `customerid`, IFNULL(`customername`, 'NOMBRE NO DEFINIDO') AS 'customername'
                FROM `customers`
                ORDER BY `customername` ASC;
            ";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["customerid"],
                    "name" => $key["customername"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga de clientes",
                "status" => "error"
            ];
        }
    }

    public static function findAllProduct(): array {
        try {
            $query = "SELECT `productid`, `productname`
                FROM `products`
                ORDER BY `productname` ASC;
            ";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["productid"],
                    "name" => $key["productname"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga de productos",
                "status" => "error"
            ];
        }
    }

    public static function findLocationCustomer(int $id): array {

        try {
            $query = "SELECT CONCAT(T1.`street`, ', ', T2.`communename`) AS 'street'
                    , T1.`phone1`
                FROM `customers` T1
                INNER JOIN `communes` T2 ON T1.`communeid`=T2.`communeid`
                WHERE T1.`customerid`=:customerid LIMIT 1;
            ";
			$stream = fopen("src/logs/info.log", "w");
			fwrite($stream, "Estoy el package models");
			fclose($stream);

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(":customerid", $id, PDO::PARAM_INT);
            $statement->execute();
            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            return [
                "street" => $rows["street"],
                "phone1" => $rows["phone1"]
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Buscar localizacion cliente",
                "status" => "error"
            ];
        }

    }

    public static function findPriceCustomer(int $customerid): array {

        try {
            $query = "SELECT T2.`detailpriceid`
                , T2.`productid`
                , T3.`productcode`
                , T3.`productname`
                , T2.`price`
                , T2.`discount1`
            FROM `headerprice` T1
            INNER JOIN `detailprice` T2 ON T1.`headerpriceid`=T2.`headerpriceid`
            INNER JOIN `products`    T3 ON T2.`productid`=T3.`productid`
            WHERE T1.`customerid`=:customerid1
            UNION
            SELECT 0
                , `productid`
                , `productcode`
                , `productname`
                , 0
                , CAST(0 AS DECIMAL(5, 2))
            FROM `products`
            WHERE `productid` NOT IN(
            SELECT T3.`productid`
            FROM `headerprice` T1
            INNER JOIN `detailprice` T2 ON T1.`headerpriceid`=T2.`headerpriceid`
            INNER JOIN `products`    T3 ON T2.`productid`=T3.`productid`
            WHERE T1.`customerid`=:customerid2
            )
            ORDER BY 4;
            ";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(":customerid1", $customerid, PDO::PARAM_INT);
            $statement->bindParam(":customerid2", $customerid, PDO::PARAM_INT);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => $key["detailpriceid"],
                    "1" => $key["productid"],
                    "2" => $key["productcode"],
                    "3" => $key["productname"],
                    "4" => $key["price"],
                    "5" => $key["discount1"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Precio Cliente",
                "status" => "error"
            ];
        }
    }

    public static function save(array $data): array {
        $insert = array();
        $update = array();

        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Id. del cliente: {$data["customerid"][0]}");

        $headerpriceid = self::getID((int)$data["customerid"][0]);
        
        // Si existe la cabecera de la lista de precios
        if ($headerpriceid) {
            // Solamente actualizamos la fecha de modificación y usuario
            /*$query = "UPDATE `headerprice` SET `updateof`=?, `userid`=? WHERE `headerpriceid`=?;";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(1, date("Y-m-d"), PDO::PARAM_STR);
            $statement->bindParam(2, $_SESSION["access"][3], PDO::PARAM_INT);
            $statement->bindParam(3, $headerpriceid, PDO::PARAM_INT);
            $statement->exeute();*/

            // Actualizar o insertar el detalle de las lista de precios
            $i = 0;

            while($i < count($data["prices"])) {
                $detailpriceid = (int)$data["details"][$i];
                $productid     = (int)$data["product"][$i];
                $price         = doubleval($data["prices"][$i]);

                if ($detailpriceid) {
                    $update[] = array("UPDATE `detailprice` SET `price`={$price}, WHERE `detailpriceid`={$detailpriceid};");
                } else {
                    $insert[] = array("INSERT INTO `detailprice` VALUES(NULL, {$headerpriceid}, {$productid}, {price}, 0, 0, 0, 0);");
                }
            }
        } else {
            $clientid = (int)$data["customerid"][0];
            $userid = (int)$_SESSION["access"][3];
            $i = 0;

            $insert[] = array("INSERT INTO `headerprice` VALUES(NULL, {$clientid}, CURDATE(), CURDATE(), {$userid}, 1);");

            while ($i < count($data["details"])) {
                $detailpriceid = (int)$data["details"][$i];
                $productid     = (int)$data["product"][$i];
                $price         = doubleval($data["prices"][$i]);

                $insert[] = array(
                    "INSERT INTO `detailprice` VALUES(NULL, LAST_INSERT_ID(), {$productid}, {$price}, 0, 0, 0, 0);"
                );
            }
        }

        return array_merge($insert);
    }

    private static function getID(int $id): int {
        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT * FROM `headerprice` WHERE `customerid`=?;");
            $statement->bindParam(1, $id, PDO::PARAM_INT);
            $statement->execute();
            $row = $statement->fetchColumn(0);

            return $statement->rowCount() ? (int)$row: -1;

        } catch (PDOException $e) {
            return -1;
        }
    }
}