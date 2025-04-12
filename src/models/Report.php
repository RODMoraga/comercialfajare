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
 * Paquete para todos los reporte del sitio web
 * 
 * @package Report
 * @author Rodrigo Moraga Garrido
 * @copyright 2025-03-29
 * @see dailySales
 * @see findAllCustomer
 * @see findAllProduct
 * @see firstDateProcess
 * @see pendingDocument
 * @see totalPendingDocument
 * @see canceled
 * @see canceledTotals
 * @version 1.0.1
 */
class Report {

    /**
     * Busca todos los los documentos cancelados
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-10
     * @param array $customers Lista de id's de clientes
     * @param string $datestart Fecha inicio proceso
     * @param string $dateend Fecha termino proceso
     * @return array
     */
    public static function canceled(array $customers, string $datestart, string $dateend): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $arguments = "";

            if (count($customers) && strlen($customers[0])) {
                $list = "";

                foreach ($customers as $key) {
                    $list .= (!strlen($list) ? $key: ", {$key}");
                }

                $arguments = "T1.`customerid` IN({$list}) AND ";
            }

            $query = "SELECT 'CREDITO'
                , T1.`folio`
                , T1.`deliverdate`
                , T3.`paymentdate`
                , T3.`order`
                , DATEDIFF(T3.`paymentdate`, T1.`deliverdate`) AS 'days'
                , T2.`complex`
                , IF(T1.`total`>0,T1.`total`,T1.`net`) AS 'total'
                , 'CANCELADO' AS 'statu'
            FROM `headerdocument` T1
            INNER JOIN `customers` T2 USING(`customerid`)
            INNER JOIN (SELECT `headerdocumentid`, MAX(`paymentid`) AS 'order', MAX(`paymentdate`) AS 'paymentdate', SUM(`amount`) AS 'amount' FROM `payments` GROUP BY `headerdocumentid`) AS T3 USING(`headerdocumentid`)
            WHERE {$arguments} T1.`deliverdate` BETWEEN '{$datestart}' AND '{$dateend}' AND T1.`statu`=9
            ;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => "CREDITO",
                    "1" => $key["folio"],
                    "2" => $key["deliverdate"],
                    "3" => $key["paymentdate"],
                    "4" => $key["order"],
                    "5" => $key["days"],
                    "6" => $key["complex"],
                    "7" => $key["total"],
                    "8" => "<span class=\"label bg-blue-gradient\">{$key["statu"]}</span>"
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
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Busca todos los los documentos cancelados totales
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-04-11
     * @param array $customers Lista de id's de clientes
     * @param string $datestart Fecha inicio proceso
     * @param string $dateend Fecha termino proceso
     * @return array
     */
    public static function canceledTotals(array $customers, string $datestart, string $dateend): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $arguments = "";

            if (count($customers) && strlen($customers[0])) {
                $list = "";

                foreach ($customers as $key) {
                    $list .= (!strlen($list) ? $key: ", {$key}");
                }

                $arguments = "`customerid` IN({$list}) AND ";
            }

            $query = "SELECT SUM(IF(`total` > 0, `total`, `net`)) AS 'total'
                , COUNT(*) AS 'quantity'
                , COUNT(DISTINCT `customerid`) AS 'customers'
            FROM `headerdocument`
            WHERE {$arguments} `deliverdate` BETWEEN '{$datestart}' AND '{$dateend}' AND `statu`=9
            ;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));
            
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            $data = array(
                "total" => $rows["total"],
                "items" => $rows["quantity"],
                "customers" => $rows["customers"]
            );

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Metodo para obtener las ventas diarias a la fecha
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-29
     * @param array $customers Trae todos los Id's del cliente
     * @param string $datestart Fecha inicio de proceso
     * @param string $dateend Fecha termino de proceso
     * @return array
     */
    public static function dailySales(array $customers, string $datestart, string $dateend): array {

        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $arguments = "";

            if (count($customers) && strlen($customers[0])) {
                $ids = "";

                foreach ($customers as $key) {
                    $ids .= (strlen($ids) === 0 ? $key: ", {$key}");
                }

                $arguments = "T1.`customerid` IN($ids) AND ";
            }

            $query = "SELECT T1.`deliverdate`
                , SUM(IF(T1.`total`<>0,T1.`total`,T1.`net`)) AS 'total'
            FROM `headerdocument` T1
            WHERE {$arguments} T1.`deliverdate` BETWEEN '{$datestart}' AND '{$dateend}' AND T1.`statu` IN(1, 9)
            GROUP BY T1.`deliverdate`
            ORDER BY T1.`deliverdate`";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    $key["deliverdate"],
                    $key["total"]
                );
            }

            return $data;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error: {$e->getCode()}",
                "status" => "error"
            ];
        }
    }

    /**
     * Busca todos los clientes de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-30
     * @return array
     */
    public static function findAllCustomer(): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $query = "SELECT `customerid`, `complex` FROM `customers` ORDER BY `complex`;";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    $key["customerid"],
                    $key["complex"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Busca todos los productos de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-30
     * @return array
     */
    public static function findAllProduct(): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $query = "SELECT `productid`, `productname` FROM `products` ORDER BY `productname`;";

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    $key["productid"],
                    $key["productname"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Obtener la primera fecha de pago
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-31
     * @return array
     */
    public static function firstDateProcess(): array {

        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $query = "SELECT IFNULL(MIN(`deliverdate`), CAST(DATE_FORMAT(CURDATE(), '%Y-%m-01') AS DATE)) AS 'firstdatepayment'
            FROM `headerdocument`
            WHERE `statu`=1;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->execute();
            $data = $statement->fetchColumn(0);

            return [
                "dateprocess" => $data
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
     * Busca todos los los documentos pendientes de pago
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-31
     * @param array $customers Lista de id's de clientes
     * @param string $datestart Fecha inicio proceso
     * @param string $dateend Fecha termino proceso
     * @return array
     */
    public static function pendingDocument(array $customers, string $datestart, string $dateend): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $arguments = "";

            if (count($customers) && strlen($customers[0])) {
                $list = "";

                foreach ($customers as $key) {
                    $list .= (!strlen($list) ? $key: ", {$key}");
                }

                $arguments = "T1.`customerid` IN({$list}) AND ";
            }

            $query = "SELECT T1.`headerdocumentid`
                , T1.`folio`
                , T1.`deliverdate`
                , DATEDIFF(CURDATE(), T1.`deliverdate`) AS 'days'
                , T2.`complex`
                , IF(T1.`total`<>0,T1.`total`,T1.`net`) AS 'total'
                , IFNULL(T3.`amount`, 0) AS 'payment'
                , IF(T1.`total`<>0,T1.`total`-IFNULL(T3.`amount`, 0),T1.`net`-IFNULL(T3.`amount`, 0)) AS 'balance'
            FROM `headerdocument`  T1
            INNER JOIN `customers` T2 USING(`customerid`)
            LEFT  JOIN (SELECT `headerdocumentid`, MAX(`paymentdate`) AS 'paymentdate', SUM(`amount`) AS 'amount' FROM `payments` GROUP BY `headerdocumentid`) AS T3 USING(`headerdocumentid`)
            WHERE {$arguments} T1.`deliverdate` BETWEEN '{$datestart}' AND '{$dateend}' AND T1.`statu`=1
            ;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => "CREDITO",
                    "1" => $key["folio"],
                    "2" => $key["deliverdate"],
                    "3" => $key["days"],
                    "4" => $key["complex"],
                    "5" => $key["total"],
                    "6" => $key["payment"],
                    "7" => $key["balance"],
                    "8" => ((int)$key["payment"] === 0 ? "<span class=\"label bg-maroon-gradient\">Pendiente</span>": "<span class=\"label bg-blue-gradient\">Con Transacciones</span>")
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
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Busca todos los clientes de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-30
     * @param array $customers Lista de id's de clientes
     * @param array $products Lista de id's de productos
     * @param string $datestart Fecha inicio proceso
     * @param string $dateend Fecha termino proceso
     * @return array
     */
    public static function productSalesSummary(array $customers, array $products, string $datestart, string $dateend): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $arguments = array("", "");

            if (count($customers) && strlen($customers[0])) {
                $list = "";

                foreach ($customers as $key) {
                    $list .= (!strlen($list) ? $key: ", {$key}");
                }

                $arguments[0] = "T1.`customerid` IN({$list}) AND ";
            }

            if (count($products) && strlen($products[0])) {
                $list = "";

                foreach ($products as $key) {
                    $list .= (!strlen($list) ? $key: ", {$key}");
                }

                $arguments[1] = "T2.`productid` IN({$list}) AND ";
            }

            $query = "SELECT T2.`productid`
                , T3.`productcode`
                , T3.`productname`
                , SUM(T2.`quantity`) AS 'quantity'
            FROM `headerdocument` T1
            INNER JOIN `detaildocument` T2 USING(`headerdocumentid`)
            INNER JOIN `products` T3 USING(`productid`)
            WHERE {$arguments[0]}{$arguments[1]} T1.`deliverdate` BETWEEN '{$datestart}' AND '{$dateend}' AND T1.`statu` IN(1, 9)
            GROUP BY T3.`productcode`, T3.`productname`
            ;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => "<button type=\"button\" class=\"btn btn-default btn-product-views\" data-toggle-views=\"{$key["productid"]}\" data-toggle-name=\"{$key["productname"]}\">Ver</button>",
                    "1" => $key["productcode"],
                    "2" => $key["productname"],
                    "3" => $key["quantity"]
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
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Ver transacciones por productos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-31
     * @param string $datestart Fecha inicio proceso
     * @param string $dateend Fecha termino proceso
     * @param int $productid Id. del producto
     * @return array
     */
    public static function viewProduct(string $datestart, string $dateend, int $productid): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $query = "SELECT T1.`folio`
                , T1.`deliverdate`
                , T3.`complex`
                , T2.`quantity`
            FROM `headerdocument` T1
            INNER JOIN `detaildocument` T2 USING(`headerdocumentid`)
            INNER JOIN `customers`      T3 USING(`customerid`)
            WHERE T1.`deliverdate` BETWEEN '{$datestart}' AND '{$dateend}' AND T2.`productid`={$productid} AND T1.`statu` IN(1, 9)
            ;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => $key["folio"],
                    "1" => $key["deliverdate"],
                    "2" => $key["complex"],
                    "3" => $key["quantity"]
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
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }

    /**
     * Obtener los tolates de los documentos
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-31
     * @param array $customers Lista de id's de clientes
     * @param string $datestart Fecha inicio proceso
     * @param string $dateend Fecha termino proceso
     * @return array
     */
    public static function totalPendingDocument(array $customers, string $datestart, string $dateend): array {
        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/m_reports.log", Level::Info));

        try {
            $arguments = "";

            if (count($customers) && strlen($customers[0])) {
                $list = "";

                foreach ($customers as $key) {
                    $list .= (!strlen($list) ? $key: ", {$key}");
                }

                $arguments = "T1.`customerid` IN({$list}) AND ";
            }

            $query = "SELECT SUM(IF(T1.`total`<>0,T1.`total`,T1.`net`)) AS 'total'
                , SUM(IFNULL(T3.`amount`, 0)) AS 'payment'
                , SUM(IF(T1.`total`<>0,T1.`total`-IFNULL(T3.`amount`, 0),T1.`net`-IFNULL(T3.`amount`, 0))) AS 'balance'
                , COUNT(T1.`headerdocumentid`) AS 'quantity'
            FROM `headerdocument`  T1
            INNER JOIN `customers` T2 USING(`customerid`)
            LEFT  JOIN (SELECT `headerdocumentid`, MAX(`paymentdate`) AS 'paymentdate', SUM(`amount`) AS 'amount' FROM `payments` GROUP BY `headerdocumentid`) AS T3 USING(`headerdocumentid`)
            WHERE {$arguments} T1.`deliverdate` BETWEEN '{$datestart}' AND '$dateend' AND T1.`statu`=1
            ;";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            return [
                "total" => $rows["total"],
                "payment" => $rows["payment"],
                "balance" => $rows["balance"],
                "quantity" => $rows["quantity"]
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => $e->getCode(),
                "status" => "error"
            ];
        }
    }
}