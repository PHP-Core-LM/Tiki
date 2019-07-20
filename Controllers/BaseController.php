<?php
/**
 * Created by PhpStorm.
 * Author: Lê Minh Hổ
 * Date: 5/22/2019
 * Time: 11:06 AM
 */

abstract class BaseController
{
    protected $folder; //Đường dẫn tới thư mục chứa View trong folder Views

    // Hàm hiển thị kết quả
    function render($file, $data = array())
    {
        // Kiểm tra file view gọi tới có tồn tại hay không
        $view_file = "Views/Layouts/".$file.".php";
        if (is_file($view_file))
        {
            // Tạo ra các biến từ mảng với tên biến là key và giá trị của biến là value
            if ($data != null) extract($data);
            require_once "$view_file";
        }
        else
        {
            // Chuyển hướng đến trang báo lỗi
            echo "Trang lỗi";
        }
    }

    abstract function start($data = array()); //Hàm được khởi chạy mặc định của controller
}