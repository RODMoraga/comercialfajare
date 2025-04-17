<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Category;

class CategoryController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/category/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Category::findAll();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                }
                break;
            case "/category/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();
                    $error = array();
                    
                    parse_str(file_get_contents("php://input"), $data);

                    foreach ($data as $key => $value) {
                        switch ($key) {
                            case "description":
                                if (strlen($value) < 4)
                                    $error[] = "Nombre de la categoria no es válido";
                                if (Category::existCategory($value))
                                    $error[] = "El nombre de la categoria ya existe en la base de datos";
                                break;
                        }
                    }

                    if (count($error)) {
                        echo json_encode(["message" => $error, "title" => "Datos Invalidos", "status" => "error"]);
                    } else {
                        $response = Category::save($data);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/category/delete":
                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    if (isset($_GET["categoryid"])) {
                        $categoryid   = (int)$_GET["categoryid"];

                        if (Category::existProducts($categoryid)) {
                            $warning = [
                                "message" => "Esta categoria no se puede eliminar por que está unido a un a más productos",
                                "title" => "Atención",
                                "status" => "error"
                            ];

                            echo json_encode($warning, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        } else {
                            $response = Category::delete($categoryid);

                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/category/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["categoryid"])) {
                        $categoryid = (int)$_GET["categoryid"];
                        $response = Category::findOne($categoryid);

                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                    }
                }
                break;
            case "/category/update":
                if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["categoryid"])) {
                        $categoryid = (int)$_GET["categoryid"];
                        $errors = array();
                        $data   = array();

                        parse_str(file_get_contents("php://input"), $data);

                        foreach ($data as $key => $value) {
                            switch ($key) {
                                case "description":
                                    if (strlen($value) < 4)
                                        $errors[] = "La descripción de la categoria no es válido";
                                    break;
                            }
                        }

                        if (count($errors))
                            echo json_encode(array("message" => $errors, "title" => "Campos Invalidos", "status" => "error"));
                        else {
                            $response = Category::update($categoryid, $data);
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }
    }
}