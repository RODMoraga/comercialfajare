<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Customer;

class CustomerController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/customer/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Customer::findAll();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                }
                break;
            case "/customer/findallregions":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Customer::findAllRegions();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                }
                break;
            case "/customer/findallcities":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $respose = Customer::findAllCities($_GET["id"]);

                        if (is_array($respose)) {
                            echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/customer/findallcommunes":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $respose = Customer::findAllCommunes($_GET["id"]);

                        if (is_array($respose)) {
                            echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/customer/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Customer::findOne($_GET["id"]);
                        
                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/customer/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();

                    parse_str(file_get_contents("php://input"), $data);

                    $response = Customer::save($data);

                    if (is_array($response)) {
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                } elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["id"])) {
                        $data = array();
    
                        parse_str(file_get_contents("php://input"), $data);
    
                        $response = Customer::update($_GET["id"], $data);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/customer/status":        
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Customer::status((int)$_GET["id"]);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }
    }
}