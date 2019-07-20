<?php
/**
 * Created by PhpStorm.
 * Author: Le Minh Ho
 * Date: 7/11/2019
 * Time: 12:12 PM
 */

ini_set('max_execution_time', 300);

$data = array();
if (isset($_GET["filter"])){
    $data["controller"] = "filter";

    $data["isFilter"] = true;
    $data["language"] = "vi";
    if (isset($_POST["soLuong"])){
        $data["soLuong"] = $_POST["soLuong"];
        $data["from"] = 0; //Giá trị mặc định
        $data["to"] = $data["soLuong"]; //Giá trị mặc định
        if (isset($_POST["from"]) && isset($_POST["to"])) {
            $data["from"] = $_POST["from"];
            $data["to"] = $_POST["to"];
        }
        if (isset($_POST["lang"])){
            $data["language"] = $_POST["lang"];
        }
    }
    else {
        $data["isFilter"] = false;
    }

    require_once "routes.php";
}
else {
    header("Content-Type: application/json"); //Set return type is json
    echo json_encode([
       "status" => false
    ]);
}