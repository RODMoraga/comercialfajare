<?php

declare(strict_types=1);

namespace App\Fajare\models;

use App\Fajare\databases\Database;
use PDO;
use PDOException;

/**
 * @package Customer
 * @author Rodrigo Moraga Garrido
 * @since
 * @see findAll
 * @see findAllRegion
 * @see findAllCities
 * @see findAllCommunes
 * @see update
 * @see save
 * @access public
 * @version 1.0.1
 * @copyright 2025-03-06
 */
class Customer {

    /**
     * Retorna todos los clientes existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAll
     * @version 1.0.1
     * @copyright 2025-03-06
     * @return array
     */
    public static function findAll(): array {

        try {
            $query = "SELECT T1.*,
                    CASE WHEN T1.`employeeid`=1 THEN 'Lucas' WHEN T1.`employeeid`=2 THEN 'Izrael' WHEN T1.`employeeid`=3 THEN 'Todos' ELSE 'No establecido' END AS employeeid,
                    T2.`regioncode`,
                    T3.`cityname`,
                    T4.`communename`
                FROM `customers` T1
                INNER JOIN `region`   T2 ON T1.`regionid` =T2.`regionid`
                INNER JOIN `cities`   T3 ON T1.`cityid`   =T3.`cityid`
                INNER JOIN `communes` T4 ON T1.`communeid`=T4.`communeid`;
            ";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "0" => '<button class="btn btn-primary btn-sm" data-toggle="tooltip" title="Editar cliente" onclick="findOne('.$key["customerid"].')"><i class="fa fa-pencil"></i></button>'.
                        ' <button class="btn btn-success btn-sm" data-toggle="tooltip" title="Activar cliente" onclick="statusChange('.$key["customerid"].')"><i class="fa fa-check"></i></button>',
                    "1" => $key["customercode"],
                    "2" => $key["customername"],
                    "3" => $key["complex"],
                    "4" => $key["employeeid"],
                    "5" => $key["cityname"],
                    "6" => $key["communename"],
                    "7" => $key["street"],
                    "8" => $key["phone1"],
                    "9" => ((int)$key["typefolio"] === 0) ? "Sin Factura": "Con Factura",
                    "10" => ((int)$key["statu"] > 0) ? '<span class="label bg-green">Activo</span>': '<span class="label bg-red">Suspendido</span>'
                );
            }

            $result = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data);
            return $result;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Regiones",
                "status" => "error"
            ];
        }
    }

    /**
     * Retorna todas las regiones existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAllRegion
     * @version 1.0.1
     * @copyright 2025-03-06
     * @return array
     */
    public static function findAllRegions(): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT `regionid`, CONCAT(`regioncode`, ' - ', `regionname`) AS 'description' FROM `region` ORDER BY `regionid`;");
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["regionid"],
                    "name" => $key["description"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Regiones",
                "status" => "error"
            ];
        }
    }

    /**
     * Retorna todas las regiones existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAllCities
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function findAllCities(string $id): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT `cityid`, `cityname` FROM `cities` WHERE `regionid`=:id ORDER BY `cityname`;");
            $statement->execute([":id" => $id]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["cityid"],
                    "name" => $key["cityname"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga ciudades",
                "status" => "error"
            ];
        }
    }

    /**
     * Retorna todas las regiones existentes
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAllCommune
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function findAllCommunes(string $id): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT `communeid`, `communename` FROM `communes` WHERE `cityid`=:id ORDER BY `communename`;");
            $statement->execute([":id" => $id]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            foreach ($rows as $key) {
                $data[] = array(
                    "code" => $key["communeid"],
                    "name" => $key["communename"]
                );
            }

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga ciudades",
                "status" => "error"
            ];
        }
    }

    /**
     * Busca un cliente por su Id
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see findAll
     * @version 1.0.1
     * @copyright 2025-03-06
     * @return array
     */
    public static function findOne(string $customerid): array {

        try {
            $query = "SELECT T1.*
                FROM `customers` T1
                INNER JOIN `region`   T2 ON T1.`regionid` =T2.`regionid`
                INNER JOIN `cities`   T3 ON T1.`cityid`   =T3.`cityid`
                INNER JOIN `communes` T4 ON T1.`communeid`=T4.`communeid`
                WHERE T1.`customerid`=:customerid;
            ";
            $connect = Database::connect();
            $statement = $connect->prepare($query);
            $statement->execute([":customerid" => $customerid]);
            $rows = $statement->fetch(PDO::FETCH_ASSOC);
            $data = array();

            $data = array(
                "customerid" => $rows["customerid"],
                "customercode" => $rows["customercode"],
                "customername" => $rows["customername"],
                "complex" => $rows["complex"],
                "commercialbusiness" => $rows["commercialbusiness"],
                "regionid" => $rows["regionid"],
                "cityid" => $rows["cityid"],
                "communeid" => $rows["communeid"],
                "street" => $rows["street"],
                "paymentid" => $rows["paymentid"],
                "expiration" => $rows["expiration"],
                "phone1" => $rows["phone1"],
                "phone2" => $rows["phone2"],
                "email" => $rows["email"],
                "credit" => $rows["credit"],
                "createat" => $rows["createat"],
                "updateof" => $rows["updateof"],
                "employeeid" => $rows["employeeid"],
                "weekday" => $rows["weekday"],
                "typefolio" => $rows["typefolio"],
                "typeorder" => $rows["typeorder"]
            );

            return $data;

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga Regiones",
                "status" => "error"
            ];
        }
    }

    /**
     * Actualiza un registro a la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see save
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function update(string $id, array $data): array {

        try {
            session_start();

            $connect = Database::connect();
            $statement = $connect->prepare("UPDATE `customers` SET customercode=:customercode, customername=:customername, complex=:complex, commercialbusiness=:commercialbusiness, regionid=:regionid, cityid=:cityid, communeid=:communeid, street=:street, paymentid=:paymentid, expiration=:expiration, phone1=:phone1, phone2=:phone2, email=:email, credit=:credit, updateof=CURDATE(), userid=:userid, employeeid=:employeeid, weekday=:weekday, typefolio=:typefolio, typeorder=:typeorder WHERE customerid=:customerid;");
            $statement->bindParam(":customerid", $id, PDO::PARAM_INT);
            $statement->bindParam(":customercode", $data["customercode"], PDO::PARAM_STR);
            $statement->bindParam(":customername", $data["customername"], PDO::PARAM_STR);
            $statement->bindParam(":complex", $data["complex"], PDO::PARAM_STR);
            $statement->bindParam(":commercialbusiness", $data["commercialbusiness"], PDO::PARAM_STR);
            $statement->bindParam(":regionid", $data["regionid"], PDO::PARAM_INT);
            $statement->bindParam(":cityid", $data["cityid"], PDO::PARAM_INT);
            $statement->bindParam(":communeid", $data["communeid"], PDO::PARAM_INT);
            $statement->bindParam(":street", $data["street"], PDO::PARAM_STR);
            $statement->bindParam(":paymentid", $data["paymentid"], PDO::PARAM_INT);
            $statement->bindParam(":expiration", $data["expiration"], PDO::PARAM_STR);
            $statement->bindParam(":phone1", $data["phone1"], PDO::PARAM_STR);
            $statement->bindParam(":phone2", $data["phone2"], PDO::PARAM_STR);
            $statement->bindParam(":email", $data["email"], PDO::PARAM_STR);
            $statement->bindParam(":credit", $data["credit"], PDO::PARAM_INT);
            $statement->bindParam(":userid", $_SESSION["access"][3], PDO::PARAM_INT);
            $statement->bindParam(":employeeid", $data["employeeid"], PDO::PARAM_STR);
            $statement->bindParam(":weekday", $data["weekday"], PDO::PARAM_STR);
            $statement->bindParam(":typefolio", $data["typefolio"], PDO::PARAM_STR);
            $statement->bindParam(":typeorder", $data["typeorder"], PDO::PARAM_STR);
            $statement->execute();

            return [
                "message" => "Los datos se actualizaron exitosamente.!",
                "title" => "Datos actualizados",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga ciudades",
                "status" => "error"
            ];
        }
    }

    /**
     * Insertar un registro a la base de datos
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see save
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function save(array $data): array {

        try {
            session_start();

            $connect = Database::connect();
            $statement = $connect->prepare("INSERT INTO `customers` VALUES(NULL, :customercode, :customername, :complex, :commercialbusiness, :regionid, :cityid, :communeid, :street, :paymentid, :expiration, :phone1, :phone2, :email, :credit, CURDATE(), CURDATE(), 1, :userid, :employeeid, :weekday, :typefolio, :typeorder);");
            $statement->bindParam(":customercode", $data["customercode"], PDO::PARAM_STR);
            $statement->bindParam(":customername", $data["customername"], PDO::PARAM_STR);
            $statement->bindParam(":complex", $data["complex"], PDO::PARAM_STR);
            $statement->bindParam(":commercialbusiness", $data["commercialbusiness"], PDO::PARAM_STR);
            $statement->bindParam(":regionid", $data["regionid"], PDO::PARAM_INT);
            $statement->bindParam(":cityid", $data["cityid"], PDO::PARAM_INT);
            $statement->bindParam(":communeid", $data["communeid"], PDO::PARAM_INT);
            $statement->bindParam(":street", $data["street"], PDO::PARAM_STR);
            $statement->bindParam(":paymentid", $data["paymentid"], PDO::PARAM_INT);
            $statement->bindParam(":expiration", $data["expiration"], PDO::PARAM_STR);
            $statement->bindParam(":phone1", $data["phone1"], PDO::PARAM_STR);
            $statement->bindParam(":phone2", $data["phone2"], PDO::PARAM_STR);
            $statement->bindParam(":email", $data["email"], PDO::PARAM_STR);
            $statement->bindParam(":credit", $data["credit"], PDO::PARAM_INT);
            $statement->bindParam(":userid", $_SESSION["access"][3], PDO::PARAM_INT);
            $statement->bindParam(":employeeid", $data["employeeid"], PDO::PARAM_STR);
            $statement->bindParam(":weekday", $data["weekday"], PDO::PARAM_STR);
            $statement->bindParam(":typefolio", $data["typefolio"], PDO::PARAM_STR);
            $statement->bindParam(":typeorder", $data["typeorder"], PDO::PARAM_STR);
            $statement->execute();

            return [
                "message" => "Los datos se agregaron exitosamente.!",
                "title" => "Datos agregados",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Carga ciudades",
                "status" => "error"
            ];
        }
    }

    /**
     * Buscar el estado del cliente y lo cambia...
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see statusChange
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    public static function status(int $id): array {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("UPDATE `customers` SET `statu`=:statu WHERE `customerid`=:customerid;");
            $statement->bindParam(":customerid", $id, PDO::PARAM_INT);
            $statement->bindParam(":statu", self::statusChange($id), PDO::PARAM_INT);
            $statement->execute();

            return [
                "message" => "Se ha actualizado el estado del cliente.",
                "title" => "Actualizando estado",
                "status" => "success"
            ];

        } catch (PDOException $e) {
            return [
                "message" => $e->getMessage(),
                "title" => "Error al actualizar el estado",
                "status" => "error"
            ];
        }
    }

    /**
     * Buscar el estado del cliente y lo cambia...
     * 
     * @author Rodrigo Moraga Garrido
     * @since
     * @see statusChange
     * @version 1.0.1
     * @copyright 2025-03-07
     * @return array
     */
    private static function statusChange(int $id): int {

        try {
            $connect = Database::connect();
            $statement = $connect->prepare("SELECT `statu` FROM `customers` WHERE `customerid`=:customerid");
            $statement->execute([":customerid" => $id]);
            $statusVal = $statement->fetchColumn(0);

            return ($statusVal === 0 ? 1: 0);

        } catch (PDOException $th) {
            return 1;
        }
    }

}