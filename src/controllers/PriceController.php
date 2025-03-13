<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Price;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class PriceController {

    public static function process() {
        // create a log channel
        $log = new Logger("log_controller");
        $log->pushHandler(new StreamHandler("src/logs/log_controller.log"), Level::Info);

        switch ($_SERVER["PATH_INFO"]) {
            case "/price/delete":
                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    if (isset($_GET["header"]) && isset($_GET["detail"]) && isset($_GET["product"])) {
                        $header = (int)$_GET["header"];
                        $detail = (int)$_GET["detail"];
                        $product = (int)$_GET["product"];

                        $log->info("Controller PriceControlller - /price/delete - Terminando");

                        $response = Price::delete($header, $detail, $product);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }

                        $log->info("Controller PriceControlller - /price/delete - Terminando");
                    }
                }
                break;
            case "/price/find/location":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $respose = Price::findLocationCustomer((int)$_GET["id"]);

                        if (is_array($respose)) {
                            echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }

                    }
                }
                break;
            case "/price/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $customer = array();
                    $products = array();

                    $log->info("Controller PriceControlller - /price/findall");

                    $respose = Price::findAll();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                    $log->info("Controller PriceControlller - /price/findall - Terminado");
                }

                break;
            case "/price/findall/complex":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Price::findAllComplex();
                    
                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                }

                break;
            case "/price/findall/customername":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Price::findAllCustomername();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                }
                break;
            case "/price/findall/products":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Price::findAllProduct();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                    
                }
                break;
            case "/price/load/customer":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Price::findPriceCustomer((int)$_GET["id"]);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }

                    }
                }
                break;
            case "/price/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();

                    parse_str(file_get_contents("php://input"), $data);

                    $log->info("Controller PriceControlller - /price/save");

                    $response = Price::save($data);

                    echo json_encode($response);
                }
                break;
        }

    }

}