<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Bank;

class BankController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/bank/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Bank::findAll();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                }
                break;
            case "/bank/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();
                    $error = array();
                    
                    parse_str(file_get_contents("php://input"), $data);

                    foreach ($data as $key => $value) {
                        switch ($key) {
                            case "description":
                                if (strlen($value) < 4)
                                    $error[] = "Nombre del banco no es válido";
                                if (Bank::existBankName($value))
                                    $error[] = "El nombre del bano ya existe en la base de datos";
                                break;
                        }
                    }

                    if (count($error)) {
                        echo json_encode(["message" => $error, "title" => "Datos Invalidos", "status" => "error"]);
                    } else {
                        $response = Bank::save($data);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/bank/delete":
                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    if (isset($_GET["bankid"])) {
                        $bankid   = (int)$_GET["bankid"];
                        $response = Bank::delete($bankid);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/bank/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["bankid"])) {
                        $bankid = (int)$_GET["bankid"];
                        $response = Bank::findOne($bankid);

                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                    }
                }
                break;
            case "/bank/update":
                if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["bankid"])) {
                        $bankid = (int)$_GET["bankid"];
                        $errors = array();
                        $data   = array();

                        parse_str(file_get_contents("php://input"), $data);

                        foreach ($data as $key => $value) {
                            switch ($key) {
                                case "description":
                                    if (strlen($value) < 5)
                                        $errors[] = "La descripción del usuario no es válido";
                                    break;
                            }
                        }

                        if (count($errors))
                            echo json_encode(array("message" => $errors, "title" => "Campos Invalidos", "status" => "error"));
                        else {
                            $response = Bank::update($bankid, $data);
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }
    }
}