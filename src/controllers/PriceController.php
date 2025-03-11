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
                    $respose = Price::findAll();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

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

                    foreach ($_POST as $key) {
                        $data[] = explode(",", $key[0]);
                    }

                    //$response = Price::save($data);

                    /*foreach ($response as $key) {
                        $log->info($key[0]);
                    }*/

                    $log->info("Información enviada por json");

                    echo json_encode($data, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
                break;
        }

    }

}