<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Product;

class ProductController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/product/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Product::findAll();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                }
                break;
            case "/product/findallcategories":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Product::findAllCategories();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                }
                break;
            case "/product/findalluom":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $respose = Product::findAllUOM();

                    if (is_array($respose)) {
                        echo json_encode($respose, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/product/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Product::findOne($_GET["id"]);
                        
                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/product/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();

                    parse_str(file_get_contents("php://input"), $data);

                    $response = Product::save($data);

                    if (is_array($response)) {
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }

                } elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["id"])) {
                        $data = array();
    
                        parse_str(file_get_contents("php://input"), $data);
    
                        $response = Product::update($_GET["id"], $data);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/product/status":        
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Product::status((int)$_GET["id"]);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }
    }
}