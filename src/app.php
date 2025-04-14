<?php

use App\Fajare\controllers\CustomerController;
use App\Fajare\controllers\LoginController;
use App\Fajare\controllers\OrdernoteController;
use App\Fajare\controllers\PaymentController;
use App\Fajare\controllers\PriceController;
use App\Fajare\controllers\ProductController;
use App\Fajare\controllers\ReportController;
use App\Fajare\controllers\UsernameController;
use App\Fajare\models\Username;

if (session_status() === PHP_SESSION_NONE)
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
        //var_dump($_SESSION);
        //["950","580","830","830"]
        //var_dump(["950","580","830","830"]);
        $query = "SELECT A
        , B
        , C
        WHERE C=1
        ";
        var_dump(str_replace(" ,", ",", preg_replace('/\s+/', ' ', trim($query))));
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
    case "/notapedidos":
        if (isset($_SESSION["access"][0])) {
            include "src/views/ordernote.php";
        }
        break;
    case "/ordernote/findall":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/findallcomplex":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/lastdocument":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/findonecustomer":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/getlistprice":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/save":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/generatefpdf":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/status":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/findonedocument":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/delete/item":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/ordernote/update":
        if (isset($_SESSION["access"][0])) {
            OrdernoteController::process();
        }
        break;
    case "/pagos":
        include "src/views/payment.php";
        break;
    case "/payment/findall":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/payment/findallcustomer":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/payment/findallbank":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/payment/findone":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/payment/save":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/payment/transaction":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/payment/annulartransaction":
        if (isset($_SESSION["access"][0])) {
            PaymentController::process();
        }
        break;
    case "/ventasdiarias":
        if (isset($_SESSION["access"][0])) {
            include "src/views/dailysales.php";
        }
        break;
    case "/report/dailysales":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/report/findallcustomer":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/report/findallproduct":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/resumenproducto":
        if (isset($_SESSION["access"][0]))
            include "src/views/productsalessummary.php";
        break;
    case "/report/productsalessummary":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/report/viewproduct":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/docpendientes":
        if (isset($_SESSION["access"][0]))
            include "src/views/pendingdocument.php";
        break;
    case "/report/firstdateprocess":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/report/pendingdocument":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/report/totalpendingdocument":
        if (isset($_SESSION["access"][0])) {
            ReportController::process();
        }
        break;
    case "/doccancelados":
        if (isset($_SESSION["access"][0]))
            include "src/views/canceled.php";
        break;
    case "/report/canceled":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/report/canceledtotal":
        if (isset($_SESSION["access"][0]))
            ReportController::process();
        break;
    case "/usuarios":
        if (isset($_SESSION["access"][0]))
            include "src/views/username.php";
        break;
    case "/username/findall":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    case "/username/save":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    case "/username/findallprofile":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    case "/username/delete":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    case "/username/status":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    case "/username/findone":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    case "/username/update":
        if (isset($_SESSION["access"][0]))
            UsernameController::process();
        break;
    default:
        include "src/views/404.php";
        break;
}