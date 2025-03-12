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
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

        $log->info("Metodo save() del package Price()");
        $log->info(json_encode($data));
        
        $insert = array();
        $update = array();
        $userid = (int)$_SESSION["access"][3];
        $prices = array();
        $details = array();
        $product = array();
        $customer = array();
        $discount = array();

        // Prepara los datos
        foreach ($data as $key => $value) {
            switch ($key) {
                case "customerid":
                    $customer[$key] = explode(",", $value);
                    break;
                case "product":
                    $product[$key] = explode(",", $value);
                    break;
                case "details":
                    $details[$key] = explode(",", $value);
                    break;
                case "prices":
                    $prices[$key] = explode(",", $value);
                    break;
                default:
                    $discount[$key] = explode(",", $value);
                    break;
            }
        }

        $log->info("Verificando el Id. del cliente {$data["customerid"][0]}");
        $log->info(json_encode($customer));
        $log->info(json_encode($product));
        $log->info(json_encode($details));
        $log->info(json_encode($prices));
        $log->info(json_encode($discount));

        $headerpriceid = self::getID((int)$data["customerid"][0]);
        
        $log->info("Obteniendo el Id. de la lista de precios: {$headerpriceid}");
        $log->info("Tamaño de la matriz: " . (string)count($details["details"]));


        // Si existe un precio actualizamos
        if ($headerpriceid > 0) {
            $update[] = array("UPDATE `headerprice` SET `updateof`=CURDATE(), `userid`={$userid} WHERE `headerpriceid`={$headerpriceid};");

            $i = 0;

            while ($i < count($details["details"])) {
                $priceid = (int)$details["details"][$i];
                $article = (int)$product["product"][$i];
                $price   = (int)$prices["prices"][$i];
                $dcto1   = (int)$discount["dcto1"][$i];

                if ($priceid === 0) {
                    $insert[] = array("INSERT INTO `detailprice` VALUES(NULL, {$headerpriceid}, {$article}, {$price}, {$dcto1}, 0, 0, 0);");
                } else {
                    $update[] = array("UPDATE `detailprice` SET `price`={$price}, `discount1`={$dcto1} WHERE `detailpriceid`={$priceid} AND `headerpriceid`={$headerpriceid} AND `productid`={$article};");
                }

                $i++;
            }
        } else {
            $client = (int)$customer["customerid"][0];
            $insert[] = array("INSERT INTO `headerprice` VALUES(NULL, {$client}, CURDATE(), CURDATE(), {$userid}, 1);");

            $i = 0;
            $max = count($details["details"]) - 1;
            $query_values = "";

            while ($i < count($details["details"])) {
                $priceid = (int)$details["details"][$i];
                $article = (int)$product["product"][$i];
                $price   = (int)$prices["prices"][$i];
                $dcto1   = (int)$discount["dcto1"][$i];
                
                $query_values .= "(NULL, LAST_INSERT_ID(), {$article}, {$price}, {$dcto1}, 0, 0, 0)" . ($i === $max ? "": ",");
                $i++;
            }

            $insert[] = array("INSERT INTO `detailprice` VALUES{$query_values};");
        }

        $result = array_merge($insert, $update);
        
        $log->info("Vamos a comenzar a revisar las queries para ejecutarlas");
        $log->info("Tamaño de la matriz de las queries: {$result}");

        foreach ($result as $key) {
            $log->info($key[0]);
            self::executeQuery($key[0]);
        }

        $log->info("Llegamos al final.");

        return [
            "message" => "Los datos se agregaron exitosamente.!",
            "title" => "Guardando precios",
            "status" => "success"
        ];
    }

    private static function executeQuery(string $query): void {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

        try {
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $log->info("El script {$query} se ejecutó corectamente.");
        } catch (PDOException $e) {
            $log->error("Error al ejecutar el script {$query}.");
            $log->error("{$e->getMessage()}");
        }
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