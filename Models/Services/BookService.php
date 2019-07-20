<?php
/**
 * Created by PhpStorm.
 * Author: Le Minh Ho
 * Date: 7/6/2019
 * Time: 12:18 PM
 */

require_once "Models/DAO/DBConnection.php";

class BookService
{
    private $connection = null;

    public function __construct()
    {
        $this->connection = new DBConnection();
    }

    public function saveNewBook($book)
    {
        //Xử lý mảng trước khi lưu
        $book['categories'] = implode(">", $book['categories']);
        if (isset($book['imgs']) && count($book['imgs']) > 0) {
            $book['imgs'] = urlencode($book['imgs'][0]);
        }

        $this->connection->openConnection();
        $result = $this->connection->executeProcedure("sp_saveNew_Book", array_values($book));
        $this->connection->closeConnection();

        return $result;
    }
}