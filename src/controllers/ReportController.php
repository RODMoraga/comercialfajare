<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Report;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class ReportController {

    public static function process() {

        $logger = new Logger("log_model_reports");
        $logger->pushHandler(new StreamHandler("src/logs/c_reports.log", Level::Info));
        
        switch ($_SERVER["PATH_INFO"]) {
            case "/report/dailysales":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["customers"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        if (!is_array($customers))
                            $customers = explode(",", $customers);

                        $response = Report::dailySales($customers, $datestart, $dateend);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/report/findallcustomer":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Report::findAllCustomer();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
                break;
            case "/report/findallproduct":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Report::findAllProduct();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
                break;    
            case "/report/productsalessummary":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $logger->info($_SERVER["QUERY_STRING"]);

                    if (isset($_GET["customers"]) && isset($_GET["products"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $products  = $_GET["products"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        if (!is_array($customers))
                            $customers = explode(",", $customers);
                        if (!is_array($products))
                            $products = explode(",", $products);

                        $response = Report::productSalesSummary($customers, $products, $datestart, $dateend);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/report/viewproduct":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["datestart"]) && isset($_GET["dateend"]) && isset($_GET["productid"])) {
                        $productid = $_GET["productid"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        $response = Report::viewProduct($datestart, $dateend, (int)$productid);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/report/firstdateprocess":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Report::firstDateProcess();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
                break;
            case "/report/pendingdocument":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["customers"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        if (!is_array($customers))
                            $customers = explode(",", $customers);

                        $response = Report::pendingDocument($customers, $datestart, $dateend);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/report/totalpendingdocument":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["customers"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        if (!is_array($customers))
                            $customers = explode(",", $customers);

                        $response = Report::totalPendingDocument($customers, $datestart, $dateend);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/report/canceled":
                $logger->info("Entrando a la URL: /report/canceled");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["customers"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        if (!is_array($customers))
                            $customers = explode(",", $customers);

                        $response = Report::canceled($customers, $datestart, $dateend);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/report/canceledtotal":
                $logger->info("Entrando a la URL: /report/canceledtotal");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["customers"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];

                        if (!is_array($customers))
                            $customers = explode(",", $customers);

                        $response = Report::canceledTotals($customers, $datestart, $dateend);

                        if (is_array($response))
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
        }

    }
}