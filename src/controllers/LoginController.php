<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Login;

class LoginController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/login/findone":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $username = isset($_POST["username"]) ? $_POST["username"]: "";
                    $password = isset($_POST["password"]) ? $_POST["password"]: "";

                    $response = Login::findOne($username, $password);
                    
                    if (is_array($response)) {
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }

                break;
            case "/login/close":
                session_unset();    // Limpiamos las variables de sesión.
                session_destroy();  // Destruimos la sesión

                header("Location: /");

                break;
        }

    }

}