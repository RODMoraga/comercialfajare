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
 * Modelo para controlar las transacciones de pago del cliente
 * 
 * @package Payment
 * @author Rodrigo Moraga Garrido
 * @copyright 2025-03-28
 * @see annularTransaction
 * @see findAll
 * @see findAllBank
 * @see findAllCustomer
 * @see findOne
 * @see save
 * @version 1.0.1
 */
class Payment {

    /**
     * Método para eliminar transacciones
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-28
     * @param {string} $paymentid Id. de transacción
     * @return array
     */
    public static function annularTransaction(string $paymentid): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $query = "UPDATE `payments` SET `amount`=0 WHERE `paymentid`=:paymentid;";

            $logger->info("Package: Payment, Method: annularTransaction()");
            $logger->info("Script SQL: {$query}");

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->execute([":paymentid" => $paymentid]);

            $logger->info("Package: Payment, Method: annularTransaction() - Proceso terminado");

            return [
                "message" => "Transacción anulada exitosamente.",
                "title" => "Transacción",
                "status" => "success"
            ];
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para cargar todos los documentos pendientes
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-27
     * @param array $customers Id. del los clientes
     * @param string $datestart Fecha inicio
     * @param string $dateend Fecha termino
     * @return array
     */
    public static function findAll(array $customers, string $datestart, string $dateend): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $query = "SELECT 'CREDITO'
                , T1.`headerdocumentid`
                , T1.`folio`
                , T1.`deliverdate`
                , T2.`complex`
                , CASE WHEN T1.`total` > 0 THEN T1.`total` ELSE T1.`net` END 'total'
                , IFNULL(T3.`amount`, 0) AS 'payments'
                , IF (T1.`total` > 0, T1.`total` - IFNULL(T3.`amount`, 0), T1.`net` - IFNULL(T3.`amount`, 0)) AS 'balance'
            FROM `headerdocument`  T1
            INNER JOIN `customers` T2 USING(`customerid`)
            LEFT  JOIN (SELECT `headerdocumentid`, SUM(`amount`) AS 'amount' FROM `payments` GROUP BY `headerdocumentid`) T3 USING(`headerdocumentid`)
            WHERE T1.`statu`=1 AND T1.`deliverdate` BETWEEN '{$datestart}' AND '{$dateend}'
            ORDER BY 2
            ";

            if (count($customers)) {
                if (strlen($customers[0])) {
                    $ids = "";
    
                    foreach ($customers as $key) {
                        $ids .= (strlen($ids) ? ",{$key}": $key);
                    }
    
                    $query = str_replace("BETWEEN '{$datestart}' AND '{$dateend}'", "BETWEEN '{$datestart}' AND '{$dateend}' AND T1.`customerid` IN({$ids})", $query);
                }
            }

            $logger->info("Package: Payment, Method: findAll()");
            $logger->info("Id's        : " . json_encode($customers));
            $logger->info("Fec. Inicio : {$datestart}");
            $logger->info("Fec. Termino: {$dateend}");
            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();
            $statement = $connect->prepare(str_replace(" ,", ", ",preg_replace("/\s+/", " ", $query)));
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $button  = "<button type=\"button\" class=\"btn btn-default btn-transaction\" data-toggle=\"tooltip\" title=\"Ver transacciones\" data-placement=\"right\" data-toggle-transaction=\"{$key["headerdocumentid"]}\"><i class=\"fa fa-list\" aria-hidden=\"true\"></i></button>&nbsp";
                $button .= "<button type=\"button\" class=\"btn btn-warning btn-new-payment\" data-toggle=\"tooltip\" title=\"Crear transacción\" data-placement=\"right\" data-toggle-payment=\"{$key["headerdocumentid"]}\"><i class=\"fa fa-money\" aria-hidden=\"true\"></i></button>";
                
                $data[] = array(
                    "0" => $button,
                    "1" => "CREDITO",
                    "2" => $key["folio"],
                    "3" => $key["deliverdate"],
                    "4" => $key["complex"],
                    "5" => $key["total"],
                    "6" => $key["payments"],
                    "7" => $key["balance"],
                    "8" => ((int)$key["payments"] === 0 ? "<span class=\"label bg-maroon-gradient\">Pendiente</span>": "<span class=\"label bg-blue-gradient\">Con Transacciones</span>")
                );
            }

            $results = array(
                "sEcho" => 1,                           //Información para el datatables
                "iTotalRecords" => count($data),        //enviamos el total registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
                "aaData" => $data
            );

            $logger->info("Package: Payment, Method: findAll() - Proceso terminado");

            return $results;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para cargar todos los bancos en un combobox
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-27
     * @return array
     */
    public static function findAllBank(): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $query = "SELECT `bankid`
                , `description`
            FROM `banks`
            WHERE `bankid` IN(?, ?)
            ORDER BY 2;
            ";

            $logger->info("Package: Payment, Method: findAllBank()");
            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->bindValue(1, "4", PDO::PARAM_INT);
            $statement->bindValue(2, "7", PDO::PARAM_INT);
            $statement->execute();

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    $key["bankid"],
                    $key["description"]
                );
            }

            $logger->info("Package: Payment, Method: findAllBank() - Proceso terminado");

            return $data;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para cargar todos los cliente en un combobox
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-27
     * @return array
     */
    public static function findAllCustomer(): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $query = "SELECT `complex`
                , `customerid`
            FROM `customers`
            WHERE `statu`=?
            ORDER BY 1;
            ";

            $logger->info("Package: Payment, Method: findAllCustomer()");
            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->bindValue(1, "1", PDO::PARAM_INT);
            $statement->execute();

            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    $key["customerid"],
                    $key["complex"]
                );
            }

            $logger->info("Package: Payment, Method: findAllCustomer() - Proceso terminado");

            return $data;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para buscar el documento que se está cancelando
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-27
     * @return array
     */
    public static function findOne(string $headerid): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $query = "SELECT 'CREDITO' AS 'type'
                , T1.`folio`
                , T1.`deliverdate`
                , CASE WHEN T1.`total` > 0 THEN T1.`total` ELSE T1.`net` END 'total'
                , IFNULL(T3.`amount`, 0) AS 'payments'
                , IF (T1.`total` > 0, T1.`total` - IFNULL(T3.`amount`, 0), T1.`net` - IFNULL(T3.`amount`, 0)) AS 'balance'
            FROM `headerdocument`  T1
            INNER JOIN `customers` T2 USING(`customerid`)
            LEFT  JOIN (SELECT `headerdocumentid`, SUM(`amount`) AS 'amount' FROM `payments` GROUP BY `headerdocumentid`) T3 USING(`headerdocumentid`)
            WHERE T1.`headerdocumentid`={$headerid} AND T1.`statu`=1;
            ";

            $logger->info("Package: Payment, Method: findAllCustomer()");
            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            $data = array(
                "type" => $rows["type"],
                "folio" => $rows["folio"],
                "deliverdate" => $rows["deliverdate"],
                "total" => $rows["total"],
                "payments" => $rows["payments"],
                "balance" => $rows["balance"]
            );

            $logger->info("Package: Payment, Method: findAllCustomer() - Proceso terminado");

            return $data;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para buscar todas tas transacciones de un cliente
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-28
     * @return array
     */
    public static function transaction(string $headerid): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $query = "SELECT `headerdocumentid`
                , `paymentid`
                , `document`
                , `paymentdate`
                , `amount`
            FROM `payments`
            WHERE `headerdocumentid`=:headerdocumentid
            ORDER BY `paymentid` ASC
            ";

            $logger->info("Package: Payment, Method: transaction()");
            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->execute([":headerdocumentid" => $headerid]);
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    $key["headerdocumentid"],
                    $key["paymentid"],
                    $key["document"],
                    $key["paymentdate"],
                    $key["amount"]
                );
            }

            $logger->info("Package: Payment, Method: transaction() - Proceso terminado");

            return $data;
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }

    /**
     * Método para guardar las transacciones
     * 
     * @author Rodrigo Moraga Garrido
     * @copyright 2025-03-27
     * @return array
     */
    public static function save(array $data): array {

        try {
            $logger = new Logger("log_models");
            $logger->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);

            $data["bankid"] = ($data["bankid"] === "" ? "7": $data["bankid"]);

            $query = "INSERT INTO `payments` VALUES(NULL, {$data["headerdocumentid"]}, {$data["methodpayment"]}, '{$data["document"]}', CURDATE(), '{$data["paymentdate"]}', {$_SESSION["access"][3]}, {$data["bankid"]}, {$data["payment"]}, '{$data["comment"]}');";

            $logger->info("Package: Payment, Method: save");
            $logger->info("Script SQL: {$query}");

            $connect = Database::connect();

            $statement = $connect->prepare($query);
            $statement->execute();

            $query = "SELECT IF(T1.`total` > 0, T1.`total` - IFNULL(T2.`amount`, 0), T1.`net` - IFNULL(T2.`amount`, 0)) AS 'balance'
                FROM `headerdocument`  T1
                LEFT  JOIN (SELECT `headerdocumentid`, SUM(`amount`) AS 'amount' FROM `payments` GROUP BY `headerdocumentid`) T2 USING(`headerdocumentid`)
                WHERE T1.`headerdocumentid`={$data["headerdocumentid"]};
            ";

            $logger->info("Script SQL: " . str_replace(" ,", ", ", preg_replace("/\s+/", " ", $query)));

            $statement = $connect->prepare($query);
            $statement->execute();
            $balance = $statement->fetchColumn(0);

            $logger->info("Balance: {$balance}");

            if ((int)$balance === 0) {
                $query = "UPDATE `headerdocument` SET `statu`=9 WHERE `headerdocumentid`={$data["headerdocumentid"]}";

                $logger->info("Script SQL: {$query}");

                $statement = $connect->prepare($query);
                $statement->execute();
            }

            $logger->info("Package: Payment, Method: save() - Proceso terminado");

            return [
                "message" => "Los datos se guardaron satisfactoriamente.",
                "title" => "Transacción Realizada",
                "status" => "success"
            ];
        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Pagos",
                "status" => "error"
            ];
        }
    }
}