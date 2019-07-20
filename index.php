<?php
/**
 * Created by PhpStorm.
 * Author: Le Minh Ho
 * Date: 6/25/2019
 * Time: 11:30 AM
 */

ini_set('max_execution_time', 300);
$GLOBALS['host'] = "Tiki_Sach";

$data = array();
$data["controller"] = "home";
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
}
else if (isset($_GET["saving"])){
    $data["controller"] = "saving";
    if (isset($_POST["data"])){
        $data["data"] = $_POST["data"];
    }
}

require_once "routes.php";