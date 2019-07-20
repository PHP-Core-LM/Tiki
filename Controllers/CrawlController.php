<?php
/**
 * Created by PhpStorm.
 * Author: Le Minh Ho
 * Date: 7/7/2019
 * Time: 4:24 PM
 */

require_once "Controllers/BaseController.php";
require_once "Models/Utils/Crawler/TikiCrawler.php";
require_once "Models/Services/BookService.php";

class CrawlController extends BaseController
{

    function start($data = array())
    {
        if ($data["controller"] == "filter"){
            $tikiCrawler = new TikiCrawler();
            $lang = "vi";
            if (isset($data["language"])){
                $lang = $data["language"];
            }
            $jsonData = json_encode($tikiCrawler->getListBook($lang, $data["soLuong"], $data["isFilter"], $data["from"], $data["to"]));

            header("Content-Type: application/json"); //Set return type is json
            if ($jsonData == false) {
                echo json_encode([
                    "status" => false
                ]);
            }
            else {
                echo $jsonData;
            }
        }
        elseif ($data["controller"] == "saving") {
            header("Content-Type: application/json"); //Set return type is json

            if (isset($data["data"]) && $data["data"] != false) {
                $books = json_decode($data["data"], true);
                $bookService = new BookService();
                foreach ($books as $book) {
                    $bookService->saveNewBook($book);
                }
                echo json_encode([
                    "status" => true
                ]);
            }
            else {
                echo json_encode([
                    "status" => false
                ]);
            }
        }
        else {
            $this->render("home", null);
        }
    }
}