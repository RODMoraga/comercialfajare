<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Uom;

class UomController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/uom/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Uom::findAll();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                }
                break;
            case "/uom/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();
                    $error = array();
                    
                    parse_str(file_get_contents("php://input"), $data);

                    foreach ($data as $key => $value) {
                        switch ($key) {
                            case "description":
                                if (strlen($value) < 4)
                                    $error[] = "Nombre del banco no es válido";
                                if (Uom::existUOM($value))
                                    $error[] = "El nombre de la unidad de medida ya existe en la base de datos";
                                break;
                        }
                    }

                    if (count($error)) {
                        echo json_encode(["message" => $error, "title" => "Datos Invalidos", "status" => "error"]);
                    } else {
                        $response = Uom::save($data);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/uom/delete":
                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    if (isset($_GET["uomid"])) {
                        $uomid   = (int)$_GET["uomid"];

                        if (Uom::existProducts($uomid)) {
                            $warning = [
                                "message" => "Este método no se puede eliminar por que está unido a un a más productos",
                                "title" => "Atención",
                                "status" => "error"
                            ];

                            echo json_encode($warning, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        } else {
                            $response = Uom::delete($uomid);

                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/uom/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["uomid"])) {
                        $uomid = (int)$_GET["uomid"];
                        $response = Uom::findOne($uomid);

                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                    }
                }
                break;
            case "/uom/update":
                if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["uomid"])) {
                        $uomid = (int)$_GET["uomid"];
                        $errors = array();
                        $data   = array();

                        parse_str(file_get_contents("php://input"), $data);

                        foreach ($data as $key => $value) {
                            switch ($key) {
                                case "description":
                                    if (strlen($value) < 4)
                                        $errors[] = "La descripción de la unidad de medida no es válido";
                                    break;
                            }
                        }

                        if (count($errors))
                            echo json_encode(array("message" => $errors, "title" => "Campos Invalidos", "status" => "error"));
                        else {
                            $response = Uom::update($uomid, $data);
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }
    }
}