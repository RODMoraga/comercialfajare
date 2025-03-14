<?php

use App\Fajare\controllers\CustomerController;
use App\Fajare\controllers\LoginController;
use App\Fajare\controllers\PriceController;
use App\Fajare\controllers\ProductController;

session_start();

switch ($_SERVER["PATH_INFO"]) {
    case "": case "/":
        include "src/views/login.php";
        break;
    case "/login/findone":
        LoginController::process();
        break;
    case "/login/close":
        LoginController::process();
        break;
    case "/dashboard":
        include "src/views/dashboard.php";
        break;
    case "/clientes":
        include "src/views/customers.php";
        break;
    case "/password":
        echo hash("sha256", "Hola.123");
        break;
    case "/sessions":
        echo "<pre>";
        var_dump($_SESSION);
        echo "</pre>";
        break;
    case "/customer/findall":
        CustomerController::process();
        break;
    case "/customer/findallregions":
        if (isset($_SESSION["access"][0])) {
            CustomerController::process();
        }
        break;
    case "/customer/findallcities":
        if (isset($_SESSION["access"][0])) {
            CustomerController::process();
        }
        break;
    case "/customer/findallcommunes":
        if (isset($_SESSION["access"][0])) {
            CustomerController::process();
        }
        break;
    case "/customer/findone":
        if (isset($_SESSION["access"][0])) {
            CustomerController::process();
        }
        break;
    case "/customer/save":
        if (isset($_SESSION["access"][0])) {
            CustomerController::process();
        }
        break;
    case "/customer/status":
        if (isset($_SESSION["access"][0])) {
            CustomerController::process();
        }
        break;
    case "/productos":
        include "src/views/product.php";
        break;
    case "/product/findall":
        ProductController::process();
        break;
    case "/product/findallcategories":
        if (isset($_SESSION["access"][0])) {
            ProductController::process();
        }
        break;
    case "/product/findalluom":
        if (isset($_SESSION["access"][0])) {
            ProductController::process();
        }
        break;
    case "/product/findone":
        if (isset($_SESSION["access"][0])) {
            ProductController::process();
        }
        break;
    case "/product/save":
        if (isset($_SESSION["access"][0])) {
            ProductController::process();
        }
        break;
    case "/product/status":
        if (isset($_SESSION["access"][0])) {
            ProductController::process();
        }
        break;
    case "/precios":
        include "src/views/prices.php";
        break;
    case "/price/findall":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/findall/complex":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/findall/customername":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/findall/products":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/find/location":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/load/customer":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/save":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    case "/price/delete":
        if (isset($_SESSION["access"][0])) {
            PriceController::process();
        }
        break;
    default:
        include "src/views/404.php";
        break;
}