<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Username;

class UsernameController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/username/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Username::findAll();

                    if (is_array($response))
                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                }
                break;
            case "/username/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();
                    $error = array();
                    
                    parse_str(file_get_contents("php://input"), $data);

                    foreach ($data as $key => $value) {
                        switch ($key) {
                            case "username":
                                if (strlen($value) < 4)
                                    $error[] = "Nombre de usuario no es válido";
                                if (Username::existUsername($value))
                                    $error[] = "El usuario ya existe en la base de datos";
                                break;
                            case "description":
                                if (strlen($value) < 4)
                                    $error[] = "La descripción del usuario no es válido";
                                break;
                            case "password":
                                if (strlen($value) < 5)
                                    $error[] = "La contraseña no es válida";
                                break;
                            case "confirmpassword":
                                $password = $data["password"];

                                if ($password !== $value || strlen($value) < 5)
                                    $error[]  = "Las contraseñas no coinciden";
                                break;
                            case "profile":
                                if ($value === "0" || $value === "")
                                    $error[] = "No ha seleccionado un perfil para este susario";
                                break;
                        }
                    }

                    if (count($error)) {
                        echo json_encode(["message" => $error, "title" => "Datos Invalidos", "status" => "error"]);
                    } else {
                        $response = Username::save($data);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/username/findallprofile":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Username::findAllProfile();
                    echo $response;
                }
                break;
            case "/username/delete":
                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    if (isset($_GET["userid"])) {
                        $userid   = (int)$_GET["userid"];
                        $response = Username::delete($userid);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/username/status":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["userid"])) {
                        $userid   = (int)$_GET["userid"];
                        $response = Username::status($userid);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
                break;
            case "/username/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["userid"])) {
                        $userid = (int)$_GET["userid"];
                        $response = Username::findOne($userid);

                        echo json_encode($response, JSON_ERROR_UTF8 | JSON_ERROR_NONE);
                    }
                }
                break;
            case "/username/update":
                if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["userid"])) {
                        $userid = (int)$_GET["userid"];
                        $errors = array();
                        $data   = array();

                        parse_str(file_get_contents("php://input"), $data);

                        foreach ($data as $key => $value) {
                            switch ($key) {
                                case "username":
                                    if (strlen($value) < 5)
                                        $errors[] = "El nombre de usuario no es válido";
                                    break;
                                case "description":
                                    if (strlen($value) < 5)
                                        $errors[] = "La descripción del usuario no es válido";
                                    break;
                                case "password":
                                    if (strlen($value) < 5)
                                        $errors[] = "El valor de la contraseña no es válido";
                                    break;
                                case "confirmpassword":
                                    $password = $data["password"];

                                    if ($password !== $value)
                                        $errors[] = "Las password no coinciden";
                                    break;
                                case "profile":
                                    if ($value === "")
                                        $errors[] = "No ha seleccionado el perfil del usuario";
                                    break;
                            }
                        }

                        if (count($errors))
                            echo json_encode(array("message" => $errors, "title" => "Campos Invalidos", "status" => "error"));
                        else {
                            $response = Username::update($userid, $data);
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }
    }
}