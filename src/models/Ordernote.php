<?php

declare(strict_types=1);

namespace App\Fajare\models;

use App\Fajare\databases\Database;
use FPDF;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PDO;
use PDOException;

/**
 * Este paque controla todas las acciones para las notas de pedidos de la base de datos
 * 
 * @package Ordernote
 * @author Rodrio Antonio Moraga
 * @see deleteBy()
 * @see findAll()
 * @see findAllCustomer()
 * @see findOneCustomer()
 * @see findOneDocument()
 * @see getListPrice()
 * @see lastDocument()
 * @see save()
 * @see update()
 * @see printFPDF()
 * @see statusDocument()
 * @copyright 2025-03-13
 * @version 1.0.1
 */
class Ordernote {

    /**
     * Cambia el estado del documento nulo o vigente
     * 
     * @author Rodrigo Moraga Garrido
     * @see deleteBy
     * @copyright 2025-03-25
     * @return array
     */
    public static function deleteBy(int $headerid, array $detailid, array $params): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método deleteBy()");

        try {
            $detail_id = "";
            $product_id = "";

            foreach ($detailid as $k) {
                $detail_id .= (strlen($detail_id) ? ",": "") . $k;
            }

            foreach ($params as $k) {
                $product_id .= (strlen($product_id) ? ",": "") . $k;
            }

            $query = "DELETE FROM `detaildocument` WHERE `headerdocumentid`=? AND `detaildocumentid` IN($detail_id) AND `productid` IN($product_id);";

            $log->info("Script SQL: {$query}");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(1, $headerid, PDO::PARAM_INT);
            $statement->execute();

            $log->info("Script SQL ejecutado: {$query}");
            $log->info("Ha salido todo bien detro del método...");

            return [
                "message" => "Se ha eliminado el item o items del documento.",
                "title" => "Eliminando Producto",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo deleteBy()",
                "status" => "error"
            ];
        }
    }

    /**
     * Este método carga los documentos para la lista de nota de pedidos
     * 
     * @author Rodrigo Moraga Garrido
     * @see findAll
     * @copyright 2025-03-13
     * @param string $complex Nombre del estableciento
     * @param string $datestart Establece la fecha de inicio de la búsqueda
     * @param string $dateend Establece la fecha de termino de la búsqueda
     * @return array
     */
    public static function findAll(string $complex, string $datestart, string $dateend): array {

        try {
            $query = "SELECT T1.`headerdocumentid`
                , CASE WHEN T1.`type`='notapedido' THEN 'NOTA PEDIDO' ELSE 'NO DEFINIDO' END AS 'type' 
                , T1.`folio`
                , T1.`deliverdate`
                , T2.`complex`
                , CASE WHEN T1.`total` <> 0 THEN T1.`total` ELSE T1.`net` END AS 'total'
                , T1.`statu`
            FROM `headerdocument`  T1
            INNER JOIN `customers` T2 ON T1.`customerid`=T2.`customerid`
            WHERE T1.`deliverdate` BETWEEN ? AND ?
            ";

            if (strlen($complex)) {
                $query .= " AND T1.`customerid` IN({$complex})";
            }

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(1, $datestart, PDO::PARAM_STR);
            $statement->bindParam(2, $dateend, PDO::PARAM_STR);
            $statement->execute();

            $data = array();
            $button = "";
            $status = "";

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                switch ((int)$key["statu"]) {
                    case 0:
                        $button  = "<button type=\"button\" class=\"btn btn-warning\" disabled><i class=\"fa fa-edit\"></i></button>&nbsp;";
                        $button .= "<button type=\"button\" class=\"btn btn-info button-annular\" data-toggle=\"tooltip\" title=\"Anular Documento\" data-placement=\"bottom\" data-toggle-annular-document=\"{$key["headerdocumentid"]}\"><i class=\"fa fa-warning\"></i></button>&nbsp;";
                        $button .= "<button type=\"button\" class=\"btn btn-primary\" disabled><i class=\"fa fa-print\"></i></button>";

                        break;
                    case 1:
                        $button  = "<button type=\"button\" class=\"btn btn-warning button-edit\" data-toggle=\"tooltip\" title=\"Editar Documento\" data-placement=\"bottom\" data-toggle-edit-document=\"{$key["headerdocumentid"]}\"><i class=\"fa fa-edit\"></i></button>&nbsp;";
                        $button .= "<button type=\"button\" class=\"btn btn-info button-annular\" data-toggle=\"tooltip\" title=\"Anular Documento\" data-placement=\"bottom\" data-toggle-annular-document=\"{$key["headerdocumentid"]}\"><i class=\"fa fa-warning\"></i></button>&nbsp;";
                        $button .= "<button type=\"button\" class=\"btn btn-primary button-printer\" data-toggle=\"tooltip\" title=\"Imprimir Documento\" data-placement=\"bottom\" data-toggle-print-document=\"{$key["headerdocumentid"]}\"><i class=\"fa fa-print\"></i></button>";
                        break;
                    case 9:
                        $button  = "<button type=\"button\" class=\"btn btn-warning\" disabled><i class=\"fa fa-edit\"></i></button>&nbsp;";
                        $button .= "<button type=\"button\" class=\"btn btn-info\" disabled><i class=\"fa fa-warning\"></i></button>&nbsp;";
                        $button .= "<button type=\"button\" class=\"btn btn-primary\" disabled><i class=\"fa fa-print\"></i></button>";

                        break;
                }

                switch ((int)$key["statu"]) {
                    case 0:
                        $status = "<span class=\"label bg-maroon-gradient\">Nulo</span>";
                        break;
                    case 1:
                        $status = "<span class=\"label bg-blue-gradient\">Vigente</span>";
                        break;
                    default:
                        $status = "<span class=\"label bg-green-gradient\">Cancelado</span>";
                        break;
                }

                $data[] = array(
                    "0" => $button,
                    "1" => $key["type"],
                    "2" => $key["folio"],
                    "3" => $key["deliverdate"],
                    "4" => $key["complex"],
                    "5" => $key["total"],
                    "6" => $status
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
                "title" => "Error Metodo findAll()",
                "status" => "error"
            ];
        }
    }

    /**
     * Este método carga todos los clientes disponibles de la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @see findAllComplex
     * @copyright 2025-03-13
     * @return array
     */
    public static function findAllComplex(): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método findAll()");

        try {
            $query = "SELECT `customerid`, `complex` FROM `customers` WHERE `statu`=? ORDER BY 2;";

            $log->info("Script SQL: {$query}");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute([1]);

            $log->info("Script SQL ejecutado: {$query}");

            $data = array();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["customerid"],
                    "name" => $key["complex"]
                );
            }

            $log->info("Ha salido todo bien detro del método...");

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo findAllComplex()",
                "status" => "error"
            ];
        }
    }

    /**
     * Buscar el cliente seleccionado
     * 
     * @author Rodrigo Moraga Garrido
     * @see findOneCustomer
     * @copyright 2025-03-13
     * @param int $id El Id. del cliente seleccionado
     * @return array
     */
    public static function findOneCustomer(int $id): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método findOneCustomer()");

        try {
            $query = "SELECT T1.`customername`
                , UCASE(CONCAT(T1.`street`, ', ', T2.`communename`)) AS 'street'
                , IFNULL(T1.`email`, '')    AS 'email'
                , IFNULL(T1.`phone1`, '')   AS 'phone1'
                , IFNULL(T1.`phone2`, '')   AS 'phone2'
                , IFNULL(T1.`typefolio`, 0) AS 'typefolio'
            FROM `customers` T1
            INNER JOIN `communes` T2 ON T1.`communeid`=T2.`communeid`
            WHERE T1.`customerid`=:customerid
            ;";

            $log->info("Script SQL: SELECT T1.`customername`, CONCAT(T1.`street`, ', ', T2.`communename`) AS 'street', IFNULL(T1.`email`, '')  AS 'email', IFNULL(T1.`phone1`, '') AS 'phone1', IFNULL(T1.`phone2`, '') AS 'phone2' FROM `customers` T1 INNER JOIN `communes` T2 ON T1.`communeid`=T2.`communeid` WHERE T1.`customerid`=:customerid");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(":customerid", $id, PDO::PARAM_INT);
            $statement->execute();

            $log->info("Script SQL ejecutado: {$query}");

            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            $log->info("Ha salido todo bien detro del método...");

            return [
                "customername" => $rows["customername"],
                "street" => $rows["street"],
                "email" => $rows["email"],
                "phone1" => $rows["phone1"],
                "phone2" => $rows["phone2"],
                "typefolio" => $rows["typefolio"]   // Si el valor es 0 es Sin - Factura, 1 = Con - Factura
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo findOneCustomer()",
                "status" => "error"
            ];
        }
    }

    /**
     * Busca un documento indicado
     * 
     * @author Rodrigo Moraga Garrido
     * @see findOneDocument
     * @copyright 2025-03-25
     * @param int $id El Id. del documento
     * @return array
     */
    public static function findOneDocument(int $id): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método findOneDocument()");

        try {
            $query = "SELECT  T1.`headerdocumentid`
                , T1.`type`
                , T1.`folio`
                , T1.`deliverdate`
                , T1.`dateorder`
                , T1.`customerid`
                , IFNULL(T3.`customername`, '') AS 'customername'
                , UCASE(CONCAT(T3.`street`, ', ', T4.`communename`)) AS 'street'
                , IFNULL(T3.`email`, '') AS 'email'
                , IFNULL(T3.`phone1`, '') AS 'phone1'
                , IFNULL(T3.`phone2`, '') AS 'phone2'
                , CASE WHEN T3.`typefolio` = 0 THEN '0 - Sin Facura' ELSE '1 - Con Factura' END AS 'typefolio'
                , IFNULL(T1.`gloss`, '') AS 'gloss'
                , T1.`net`
                , T1.`tax`
                , T1.`total`
                , T2.`productid`
                , T5.`productcode`
                , T5.`productname`
                , T2.`quantity`
                , T2.`price`
                , T2.`discount1`
                , ROUND(T2.`quantity`*T2.`price`*(1 - (T2.`discount1` / 100)),0) AS 'subtotal'
                , T2.`detaildocumentid`
            FROM `headerdocument` T1
            INNER JOIN `detaildocument` T2 ON T1.`headerdocumentid`=T2.`headerdocumentid`
            INNER JOIN `customers`      T3 ON T1.`customerid`=T3.`customerid`
            INNER JOIN `communes`       T4 ON T3.`communeid`=T4.`communeid`
            INNER JOIN `products`       T5 ON T2.`productid`=T5.`productid`
            WHERE T1.`headerdocumentid`=?
            ;";

            $log->info("Script SQL: Se va a ejecutar");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(1, $id, PDO::PARAM_INT);
            $statement->execute();
            $data = array();

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $data[] = array(
                    "0" => $key["headerdocumentid"],
                    "1" => $key["type"],
                    "2" => $key["folio"],
                    "3" => $key["deliverdate"],
                    "4" => $key["dateorder"],
                    "5" => $key["customerid"],
                    "6" => $key["customername"],
                    "7" => $key["street"],
                    "8" => $key["email"],
                    "9" => $key["phone1"],
                    "10" => $key["phone2"],
                    "11" => $key["typefolio"],
                    "12" => $key["gloss"],
                    "13" => $key["net"],
                    "14" => $key["tax"],
                    "15" => $key["total"],
                    "16" => $key["productid"],
                    "17" => $key["productcode"],
                    "18" => $key["productname"],
                    "19" => $key["quantity"],
                    "20" => $key["price"],
                    "21" => $key["discount1"],
                    "22" => $key["subtotal"],
                    "23" => $key["detaildocumentid"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo findOneCustomer()",
                "status" => "error"
            ];
        }
    }

    /**
     * Busca el último documento ingresado en la tabla documentos.
     * 
     * @author Rodrigo Moraga Garrido
     * @see lastDocument
     * @copyright 2025-03-13
     * @return array
     */
    public static function lastDocument(): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método lastDocument()");

        try {
            $query = "SELECT IFNULL(MAX(`folio`), 0) AS 'folio' FROM `headerdocument`;";

            $log->info("Script SQL: {$query}");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $log->info("Script SQL ejecutado: {$query}");

            $rows = $statement->fetchColumn();

            $log->info("Ha salido todo bien detro del método...");

            return ["folio" => (string)$rows];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo lastDocument()",
                "status" => "error"
            ];
        }
    }

    /**
     * Busca el último documento ingresado en la tabla documentos.
     * 
     * @author Rodrigo Moraga Garrido
     * @see getListPrice
     * @copyright 2025-03-13
     * @return array
     */
    public static function getListPrice(int $id): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método getListPrice()");

        try {
            $query = "SELECT T2.`productid`
                , T4.`productcode`
                , T4.`productname`
                , T2.`price`
                , T2.`discount1` AS 'dcto1'
            FROM `headerprice` T1
            INNER JOIN `detailprice` T2 ON T1.`headerpriceid`=T2.`headerpriceid`
            INNER JOIN `customers` T3 ON T1.`customerid`=T3.`customerid`
            INNER JOIN `products` T4 ON T2.`productid`=T4.`productid`
            WHERE T1.`customerid`=:id;
            ";

            $log->info("Script SQL ejecutandose");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(":id", $id, PDO::PARAM_INT);
            $statement->execute();

            $log->info("Script SQL se ejecutó correctamente");

            $data = array();
            $button = "";

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $gloss   = $key["productid"] . "&&&";
                $gloss  .= $key["productcode"] . "&&&";
                $gloss  .= $key["productname"] . "&&&";
                $gloss  .= $key["price"] . "&&&";
                $gloss  .= $key["dcto1"];
                $button  = "<button type=\"button\" class=\"btn btn-success btn-sm button-add-product\" data-toggle-price=\"{$gloss}\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></button>";

                $data[] = array(
                    "0" => $button,
                    "1" => $key["productcode"],
                    "2" => $key["productname"],
                    "3" => $key["price"],
                    "4" => $key["dcto1"]
                );
            }

            $results = array(
                "sEcho" => 1,                           //Información para el datatables
                "iTotalRecords" => count($data),        //enviamos el total registros al datatable
                "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
                "aaData" => $data
            );

            $log->info("Ha salido todo bien detro del método...");

            return $results;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo getListPrice()",
                "status" => "error"
            ];
        }
    }

    /**
     * Actualizar el documento
     * 
     * @author Rodrigo Moraga Garrido
     * @see update
     * @copyright 2025-03-26
     * @return array
     */
    public static function update(int $headerid, array $data): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método update()");

        try {
            $query = "UPDATE `headerdocument` SET `deliverdate`='{$data["deliverdate"]}', `dateorder`='{$data["dateorder"]}', `customerid`={$data["establishment"]}, `userid`={$_SESSION["access"][3]}, `net`={$data["net"]}, `tax`={$data["tax"]}, `discount`=0, `total`={$data["total"]}, `gloss`='{$data["gloss"]}', `applytotal`={$data["applytotal"]} WHERE `headerdocumentid`={$headerid};";

            // $log->info("Script SQL: " . preg_replace('/\s+/', ' ', trim($query)));
            $log->info($query);

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();

            $log->info("Script SQL: Cabecera Documento Actualizado...");

            $prices = array();
            $details = array();
            $products = array();
            $discounts = array();
            $quantities = array();

            foreach ($data as $key => $value) {
                switch ($key) {
                    case "itemdetail5":     // Id. del producto
                        $products = $value;
                        break;
                    case "itemdetail1":     // Cantidad pedida
                        $quantities = $value;
                        break;
                    case "itemdetail2":     // Precio
                        $prices = $value;
                        break;
                    case "itemdetail3":     // Descuento 1
                        $discounts = $value;
                        break;
                    case "itemdetail4":
                        $details = $value;  // Id. del detalle
                        break;
                }
            }

            $i = 0;

            while ($i < count($products)) {
                $log->info("Id. Cabecera: {$headerid}, Id. Detalle: {$details[$i]}, Cantidad: {$quantities[$i]}, Price: {$prices[$i]}, Descuento: {$discounts[$i]}");

                if (intval($details[$i]) !== 0) {
                    $query = "UPDATE `detaildocument` SET `quantity`={$quantities[$i]}, `price`={$prices[$i]}, `discount1`={$discounts[$i]} WHERE `headerdocumentid`={$headerid} AND `detaildocumentid`={$details[$i]} AND `productid`={$products[$i]};";
                } else {
                    $query = "INSERT INTO `detaildocument` VALUES(NULL, {$headerid}, {$products[$i]}, {$quantities[$i]}, {$prices[$i]}, {$discounts[$i]}, 0, 0, 0);";
                }

                $log->info($query);

                $statement = $connect->prepare($query);
                $statement->execute();

                $log->info("Script SQL: Actualizando {$i}");

                $i++;
            }

            $log->info("Ha salido todo bien detro del método...");

            return [
                "message" => "Los datos se han actualizado exitosamente.",
                "title" => "Actualizando",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            $log->info($e->getMessage());
            
            return [
                "message" => $e->getMessage(),
                "title" => "Actualizando",
                "status" => "error"
            ];
        }
    }

    /**
     * Este método carga los documentos para la lista de nota de pedidos
     * 
     * @author Rodrigo Moraga Garrido
     * @see save
     * @copyright 2025-03-25
     * @param array $data
     * @return array
     */
    public static function save(array $data): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método save()");

        try {
            $i = 0;
            $max = 0;
            $user = $_SESSION["access"][3];
            $prices = array();
            $products = array();
            $discounts = array();
            $quantities = array();

            // Preparando los script
            $query_header = "INSERT INTO `headerdocument` VALUES(NULL, '{$data["type"]}', {$data["folio"]}, CURDATE(), '{$data["deliverdate"]}', '{$data["dateorder"]}', {$data["establishment"]}, {$user}, {$data["net"]}, {$data["tax"]}, 0, {$data["total"]}, NULL, '{$data["gloss"]}', 1, {$data["applytotal"]});";
            $query_detail = "INSERT INTO `detaildocument` VALUES";
            $query_result = array();

            foreach ($data as $key => $value) {
                switch ($key) {
                    case "itemdetail5":     // Id. del producto
                        $products = $value;
                        break;
                    case "itemdetail1":     // Cantidad pedida
                        $quantities = $value;
                        break;
                    case "itemdetail2":     // Precio
                        $prices = $value;
                        break;
                    case "itemdetail3":     // Descuento 1
                        $discounts = $value;
                        break;
                }
            }

            $max = count($products) - 1;

            while ($i < count($products)) {
                $item_price = $prices[$i];
                $item_product = $products[$i];
                $item_quantity = $quantities[$i];
                $item_discount = $discounts[$i];

                $query_detail .= "(NULL, LAST_INSERT_ID(), {$item_product}, {$item_quantity}, {$item_price}, {$item_discount}, 0, 0, 0)" . ($i === $max ? ";": ",");

                $i++;
            }

            $log->info("Script cabezera: {$query_header}");
            $log->info("Script detalle : {$query_detail}");
            $query_result = $query_header . $query_detail;

            $connect = Database::connect();
            $statement = $connect->prepare($query_result);
            $statement->execute();

            $log->info("Script totales : " . $query_result);
            $log->info("Ha salido todo bien detro del método...");

            return [
                "message" => "Los datos se guardaron exitosamente.",
                "title" => "Guardando",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo findAll()",
                "status" => "error"
            ];
        }
    }

    /**
     * Este método genera el documento PDF
     * 
     * @author Rodrigo Moraga Garrido
     * @see printFPDF
     * @copyright 2025-03-25
     * @param array $headerid Id. del documento cabecera
     * @return array
     */
    public static function printFPDF(int $headerid): array {
        if (file_exists("src/logs/printfpdf.log"))
            $stream = fopen("src/logs/printfpdf.log", "a+");
        else
            $stream = fopen("src/logs/printfpdf.log", "w");

        try {
            fwrite($stream, date("Y-m-d H:i:s") . " - Comenzado a generar el archivo pdf\n");
            fwrite($stream, date("Y-m-d H:i:s") . " - Creando una nueva instancia para generar pdf\n");

            // Buscar los datos del documento
            $query = "SELECT T1.`folio`
                , DATE_FORMAT(T1.`deliverdate`, '%d-%m-%Y') AS 'deliverdate'
                , T1.`dateorder`
                , DATE_FORMAT(T1.`entrydate`, '%d-%m-%Y') AS 'entrydate'
                , IFNULL(T1.`net`, 0) AS 'net'
                , IFNULL(T1.`discount`, 0) AS 'discount'
                , IFNULL(T1.`tax`, 0) AS 'tax'
                , IFNULL(T1.`total`, 0) AS 'total'
                , IFNULL(T1.`gloss`, '') AS 'gloss'
                , IFNULL(T2.`customercode`, '') AS 'customercode'
                , T2.`complex` AS 'customername'
                , UCASE(CONCAT(T2.`street`, ', ', T3.`communename`)) AS 'street'
                , IFNULL(T2.`phone1`, '') AS 'phone1'
            FROM `headerdocument` T1
            INNER JOIN `customers` T2 ON T1.`customerid`=T2.`customerid`
            INNER JOIN `communes` T3 ON T2.`communeid`=T3.`communeid`
            WHERE T1.`headerdocumentid`=?;
            ";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(1, $headerid, PDO::PARAM_INT);
            $statement->execute();
            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            // Datos del encabezado del documento
            $net = $rows["net"];
            $tax = $rows["tax"];
            $total = $rows["total"];
            $folio = $rows["folio"];
            $street = $rows["street"];
            $phone1 = $rows["phone1"];
            $discount = $rows["discount"];
            $entrydate = $rows["entrydate"];
            $deliverdate = $rows["deliverdate"];
            $customercode = $rows["customercode"];
            $customername = $rows["customername"];
    
            $pdf = new FPDF();
            $file_name = "c:\\pdf\\F" . date("YmdHis") . sprintf("%08d", $folio) . ".pdf";
    
            fwrite($stream, date("Y-m-d H:i:s") . " - " . $file_name . "\n");
    
            $pdf->AliasNbPages();
            $pdf->AddPage();
            // Encabezado
            $pdf->Image("src/public/img/image-cf.png",90,10,30,25);
            $pdf->SetFont("Arial","B",16) ;
            $pdf->Cell(120) ;
            $pdf->Cell(60,10,"GUIA DE DESPACHO",0,0,"C") ;
            $pdf->Ln(7) ;
            //
            $pdf->SetFont("Arial","",10) ;
            $pdf->Cell(20,10,"Empresa:",0,0,"") ;
            $pdf->Cell(30,10,"COMERCIAL FAJARE",0,0,"") ;
            $pdf->Cell(80) ;
            $pdf->Cell(30,10,"Folio:",0,0,"") ;
            $pdf->Cell(10,10,$folio,0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"Telefono:",0,0,"") ;
            $pdf->Cell(30,10,"(+56 9) 9536-0923",0,0,"") ;
            $pdf->Cell(80) ;
            $pdf->Cell(30,10,"Fecha Emision:",0,0,"") ;
            $pdf->Cell(10,10,$entrydate,0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"E-Mail:",0,0,"") ;
            $pdf->Cell(30,10,"contacto@comercialfajare.cl",0,0,"") ;
            $pdf->Cell(80) ;
            $pdf->Cell(30,10,"Fecha Entrega:",0,0,"") ;
            $pdf->Cell(10,10,$deliverdate,0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"Sitio Web:",0,0,"") ;
            $pdf->Cell(30,10,"comercialfajare.com",0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"Direccion:",0,0,"") ;
            $pdf->Cell(30,10,"CAMINO MELIPILLA # 7786, SANTIAGO",0,0,"") ;
            $pdf->Ln(10) ;
            //
            $pdf->SetTextColor(0,0,0) ;
            $pdf->Cell(190,25,"",1,0);
            $pdf->Ln(0) ;
            $pdf->Cell(20,10,"Rut:",0,0,"") ;
            $pdf->Cell(30,10,$customercode,0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"Cliente:",0,0,"") ;
            $pdf->Cell(30,10,iconv("utf-8", "cp1252", $customername),0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"Direccion:",0,0,"") ;
            $pdf->Cell(30,10,iconv("utf-8", "cp1252", $street),0,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(20,10,"Telefono:",0,0,"") ;
            $pdf->Cell(30,10,iconv("utf-8", "cp1252", $phone1),0,0,"") ;
            $pdf->Ln(12) ;
            //
            $pdf->Cell(190,100,"",1,0,);
            $pdf->Ln(0) ;
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(20,5,"CODIGO",1,0,"");
            $pdf->Cell(65,5,"DESCRIPCION",1,0,"");
            $pdf->Cell(20,5,"MEDIDA",1,0,"");
            $pdf->Cell(20,5,"CANTIDAD",1,0,"");
            $pdf->Cell(20,5,"PRECIO",1,0,"");
            $pdf->Cell(15,5,"DCTO",1,0,"");
            $pdf->Cell(30,5,"TOTAL",1,0,"");
            $pdf->Ln(5) ;

            $query_detail = "SELECT T2.`productcode`
                , T2.`productname`
                , T1.`quantity`
                , T1.`price`
                , T1.`discount1`
                , ROUND(T1.`quantity`*T1.`price`*(1-(T1.`discount1`/100)),0) AS 'subtotal'
            FROM `detaildocument` T1
            INNER JOIN `products` T2 ON T1.`productid`=T2.`productid`
            WHERE T1.`headerdocumentid`=?
            ORDER BY T1.`detaildocumentid` ASC;
            ";
            $statement = $connect->prepare($query_detail);
            $statement->bindParam(1, $headerid, PDO::PARAM_INT);
            $statement->execute();

            $num_line = 0;

            $pdf->SetFont('Arial','',9);

            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $rows) {
                $pdf->Cell(20,5,iconv("utf-8", "cp1252", $rows["productcode"]),0,0,"");
                $pdf->Cell(65,5,iconv("utf-8", "cp1252", $rows["productname"]),0,0,"");
                $pdf->Cell(20,5,"",0,0,"");
                $pdf->Cell(20,5,$rows["quantity"],0,0,"R");
                $pdf->Cell(20,5,number_format($rows["price"],0,',','.'),0,0,"R");
                $pdf->Cell(15,5,number_format($rows["discount1"],2,',','.').'%',0,0,"R");
                $pdf->Cell(30,5,number_format($rows["subtotal"],0,',','.'),0,0,"R");

                $pdf->Ln(5) ;

                $num_line++;
            }

            $max_line = 95 - $num_line;

            switch ($num_line) {
                case 2:
                    $max_line = $max_line - 4;
                    break;
                case 3:
                    $max_line = $max_line - 8;
                    break;
                case 4:
                    $max_line = $max_line - 12;
                    break;
                case 5:
                    $max_line = $max_line - 16;
                    break;
                case 6:
                    $max_line = $max_line - 20;
                    break;
                case 7:
                    $max_line = $max_line - 24;
                    break;
                case 8:
                    $max_line = $max_line - 28;
                    break;
                case 9:
                    $max_line = $max_line - 32;
                    break;
                case 10:
                    $max_line = $max_line - 36;
                    break;
            }

            // Glosas
            $pdf->Ln($max_line) ;
            $pdf->Cell(90,25,"",1,0,"");
            $pdf->Cell(10);
            $pdf->Cell(90,25,"",1,0,"");
            $pdf->Ln(0) ;
            $pdf->Cell(90,5,"Comentarios",1,0,"C");
            $pdf->Cell(10);
            $pdf->Cell(45,5,"Suma:",1,0, "L");
            $pdf->Cell(45,5,number_format($net,0,',','.'),1,0,"R") ;
            $pdf->Ln(5) ;
            $pdf->Cell(90,5,"",0,0,"C");
            $pdf->Cell(10);
            $pdf->Cell(45,5,"Descuento:",1,0, "L");
            $pdf->Cell(45,5,number_format($discount,0,',','.'),1,0,"R") ;
            $pdf->Ln(5) ;
            $pdf->Cell(90,5,"",0,0,"C");
            $pdf->Cell(10);
            $pdf->Cell(45,5,"Subtotal:",1,0, "L");
            $pdf->Cell(45,5,number_format($net - $discount,0,',','.'),1,0,"R") ;
            $pdf->Ln(5) ;
            $pdf->Cell(90,5,"",0,0,"C");
            $pdf->Cell(10);
            $pdf->Cell(45,5,"Iva (19%):",1,0, "L");
            $pdf->Cell(45,5,number_format($tax,0,',','.'),1,0,"R") ;
            $pdf->Ln(5) ;
            $pdf->Cell(90,5,"",0,0,"C");
            $pdf->Cell(10);
            $pdf->Cell(45,5,"Total:",1,0, "L");
            $pdf->Cell(45,5,number_format($total,0,',','.'),1,0,"R") ;
            $pdf->Ln(6) ;
            $pdf->Cell(90,20,"",1,0,"");
            $pdf->Ln(0) ;
            $pdf->Cell(90,5,"Notas Especiales e Instrucciones",1,0,"C");
            $pdf->Ln(35) ;
            $pdf->Cell(43,0,"",1,0,"") ;
            $pdf->Cell(4) ;
            $pdf->Cell(43,0,"",1,0,"") ;
            $pdf->Ln(5) ;
            $pdf->Cell(45,0,"Firma",0,0,"C") ;
            $pdf->Cell(45,0,"Recibido Por",0,0,"C") ;
            $pdf->Ln(2) ;

            $pdf->SetFont('Arial','',8);
            $pdf->Ln(5) ;
            $pdf->Cell(190,0,"",1,0,"") ;
            $pdf->Ln(2) ;
            $pdf->Cell(0,0,"Si tienes alguna pregunta por favor contactanos",0,0,"C") ;
            $pdf->Ln(3) ;
            $txt = iconv('utf-8', 'cp1252', "Teléfono Contacto: (+56 9) 9536-0923 - E-mail: contacto@comercialfajare.cl");
            $pdf->Cell(0,0,$txt,0,0,"C") ;
            $pdf->Ln(3) ;
            $pdf->Cell(0,0,"Gracias por su preferencia",0,0,"C") ;
            //
            $pdf->Output('F', $file_name);
    
            fclose($stream);
            return [
                "message" => "Documento pdf genereado exitosamente.",
                "title" => $folio,
                "status" => "success"
            ];
        } catch (PDOException | \Throwable $e) {
            return [
                "message" => $e->getMessage(),
                "title" => $e->getCode(),
                "status" => "Error"
            ];
        }
    }

    /**
     * Cambia el estado del documento nulo o vigente
     * 
     * @author Rodrigo Moraga Garrido
     * @see statusDocument
     * @copyright 2025-03-13
     * @return array
     */
    public static function statusDocument(int $headerid): array {
        $log = new Logger("log_models");
        $log->pushHandler(new StreamHandler("src/logs/log_model.log"), Level::Info);
        $log->info("Clase Ordernote() - Método statusDocument()");

        try {
            $query = "SELECT `statu` FROM `headerdocument` WHERE `headerdocumentid`=?;";

            $log->info("Script SQL: {$query}");

            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->bindParam(1, $headerid, PDO::PARAM_INT);
            $statement->execute();

            $log->info("Script SQL ejecutado: {$query}");

            $status = (int)$statement->fetchColumn(0);
            $status = ($status === 1 ? 0: 1);

            $query = "UPDATE `headerdocument` SET `statu`=? WHERE `headerdocumentid`=?;";

            $statement = $connect->prepare($query);
            $statement->bindParam(1, $status, PDO::PARAM_INT);
            $statement->bindParam(2, $headerid, PDO::PARAM_INT);
            $statement->execute();

            $log->info("Script SQL: {$query}");
            $log->info("Ha salido todo bien detro del método...");

            return [
                "message" => "El estado del documento se actualizó satisfactoriamente.",
                "title" => "Actualizando Estado Documento",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error Metodo statusDocument()",
                "status" => "error"
            ];
        }
    }
}