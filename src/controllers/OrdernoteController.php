<?php

declare(strict_types=1);

namespace App\Fajare\controllers;

use App\Fajare\models\Ordernote;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class OrdernoteController {

    public static function process() {

        $log = new Logger("log_controller");
        $log->pushHandler(new StreamHandler("src/logs/log_controller.log", Level::Debug));
        $log->info("Class OrdernoteController()");

        switch ($_SERVER["PATH_INFO"]) {

            case "/ordernote/findall":
                $log->info("Método findall, url=/ordernote/findall - Iniciado");
                $log->info(isset($_GET["complex"]) ? "[complex] parámetro existe": "[complex] parámetro no existe");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["complex"]) && isset($_GET["datestart"]) && isset($_GET["dateend"])) {
                        $complex    = $_GET["complex"];
                        $date_start = $_GET["datestart"];
                        $date_end   = $_GET["dateend"];

                        $log->info("Nombre establecimiento: {$complex}");
                        $log->info("Periodo de proceso: {$date_start} Hasta {$date_end}");

                        $response = Ordernote::findAll($complex, $date_start, $date_end);

                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        
                    }
                }

                $log->info("Método findall, url=/ordernote/findall - Terminado");

                break;
            case "/ordernote/findallcomplex":
                $log->info("Método findall, url=/ordernote/findallcomplex - Iniciado");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Ordernote::findAllComplex();

                    echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }

                $log->info("Método findallcomplex, url=/ordernote/findallcomplex - Terminado");

                break;
            case "/ordernote/lastdocument":
                $log->info("Método lastdocument, url=/ordernote/lastdocument - Iniciado");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $response = Ordernote::lastDocument();
                    echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                }

                $log->info("Método lastdocument, url=/ordernote/lastdocument - Terminado");

                break;
            case "/ordernote/findonecustomer":
                $log->info("Método findonecustomer, url=/ordernote/findonecustomer - Iniciado");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Ordernote::findOneCustomer((int)$_GET["id"]);
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }

                $log->info("Método findonecustomer, url=/ordernote/findonecustomer - Terminado");

                break;
            case "/ordernote/getlistprice":
                $log->info("Método getlistprice, url=/ordernote/getlistprice - Iniciado");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["id"])) {
                        $response = Ordernote::getListPrice((int)$_GET["id"]);
                        echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                    }
                }

                $log->info("Método getlistprice, url=/ordernote/getlistprice - Iniciado");

                break;
            case "/ordernote/save":
                $log->info("Método save, url=/ordernote/save - Iniciado");
                $data = array();

                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    parse_str(file_get_contents("php://input"), $data);

                    $log->info("Datos: " . json_encode($data));

                    $response = Ordernote::save($data);

                    echo json_encode($response);
                }

                $log->info("Método save, url=/ordernote/save - Terminado");
                break;

            case "/ordernote/generatefpdf":
                $log->info("Método printFPDF, url=/ordernote/generatefpdf - Iniciado");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["headerid"])) {
                        $response = Ordernote::printFPDF((int)$_GET["headerid"]);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }

                $log->info("Método printFPDF, url=/ordernote/generatefpdf - Terminado");

                break;

            case "/ordernote/status":
                $log->info("Método statusDocument(), url=/ordernote/status - Iniciado");

                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["headerid"])) {
                        $response = Ordernote::statusDocument((int)$_GET["headerid"]);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }

                    }
                }

                $log->info("Método statusDocument(), url=/ordernote/status - Terminado");

                break;
            case "/ordernote/findonedocument":
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    if (isset($_GET["headerid"])) {
                        $response = Ordernote::findOneDocument((int)$_GET["headerid"]);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
            case "/ordernote/delete/item":
                $log->info("Método deleteBy(), url=/ordernote/delete/item - Inicio");

                if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
                    $log->info("Id. Cabecera  : " . $_GET["headerid"]);
                    $log->info("Id's Detalle  : " . json_encode(array_map("intval", explode(",", $_GET["detailid"]))));
                    $log->info("Id's Productos: " . json_encode(array_map("intval", explode(",", $_GET["params"]))));

                    if (isset($_GET["headerid"]) && isset($_GET["detailid"]) && isset($_GET["params"])) {
                        $headerid = (int)$_GET["headerid"];
                        $detailid = array_map("intval", explode(",", $_GET["detailid"]));
                        $params   = array_map("intval", explode(",", $_GET["params"]));

                        if (is_array($detailid) && is_array($params)) {
                            $response = Ordernote::deleteBy($headerid, $detailid, $params);
                        } else {
                            $response = [
                                "message" => "Uno de los parámetros indicados no son correctos",
                                "title" => "Eliminando",
                                "status" => "error"
                            ];
                        }

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                        
                    }
                }

                $log->info("Método deleteBy(), url=/ordernote/delete/item - Terminado");

                break;
            case "/ordernote/update":
                if ($_SERVER["REQUEST_METHOD"] === "PUT") {
                    $data = array();

                    if (isset($_GET["headerid"])) {
                        $headerid = (int)$_GET["headerid"];
                        
                        parse_str(file_get_contents("php://input"), $data);

                        $log->info("Id. de Cabecera: " . (string)$headerid);
                        $log->info(json_encode($data));

                        $response = Ordernote::update($headerid, $data);

                        if (is_array($response)) {
                            echo json_encode($response, JSON_ERROR_NONE | JSON_ERROR_UTF8);
                        }
                    }
                }
                break;
        }

    }
}