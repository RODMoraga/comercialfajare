<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Payment;

class PaymentController {

    public static function process() {

        switch ($_SERVER["PATH_INFO"]) {
            case "/payment/findall":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["customers"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $customers = $_GET["customers"];
                        $datestart = $_GET["datestart"];
                        $dateend   = $_GET["dateend"];
    
                        if (!is_array($customers))
                            $customers = explode(",", $customers);
    
                        $response = Payment::findAll($customers, $datestart, $dateend);
    
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
            break;
            case "/payment/findallcustomer":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Payment::findAllCustomer();

                    echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
            break;
            case "/payment/findallbank":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Payment::findAllBank();

                    echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
            break;
            case "/payment/findone":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["headerid"])) {
                        $response = Payment::findOne($_GET["headerid"]);
    
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
            break;
            case "/payment/save":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $data = array();

                    parse_str(file_get_contents("php://input"), $data);

                    $response = Payment::save($data);

                    echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }
            break;
            case "/payment/transaction":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["headerid"])) {
                        $response = Payment::transaction($_GET["headerid"]);
    
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
            break;
            case "/payment/annulartransaction":
                if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    if (isset($_GET["paymentid"])) {
                        $response = Payment::annularTransaction($_GET["paymentid"]);
    
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }
            break;
        }
    }
}