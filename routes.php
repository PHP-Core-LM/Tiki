<?php
/**
 * Created by PhpStorm.
 * Author: Lê Minh Hổ
 * Date: 5/22/2019
 * Time: 11:06 AM
 */

    $nameController = "CrawlController";

    //Gọi file định nghĩa controller
    require_once "Controllers/".$nameController.".php";
    $controller = new $nameController();

    $controller->start($data);
