<?php
/**
 * Created by PhpStorm.
 * Author: Le Minh Ho
 * Date: 6/25/2019
 * Time: 12:12 PM
 */

require_once "Models/Utils/Crawler/Crawler.php";
require_once "Models/Utils/simple_html_dom.php";
use util\crawler\Crawler;

class TikiCrawler extends Crawler
{
    private $uris = [
        "vi" => "https://tiki.vn/sach-truyen-tieng-viet/c316?page={num}",
        "eng" => "https://tiki.vn/sach-tieng-anh/c320?page={num}"
    ];
    private $uri;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @todo Get list book following number of result
     * @param int $numResult
     * @return array|bool
     */
    public function getListBook($lang = "vi", $numResult = 0, $isFilter = false, $getFrom = 0, $getTo = 0)
    {
        $result = array(); // Mảng kết quả thông tin đọc ban đầu
        $this->uri = $this->getURI($lang); // Thiết lập giá trị uri
        $source = $this->getSourcePage(1); // Mã nguồn trang bắt đầu đọc
        $page = $this->getIndexPage($source, $getFrom); // Page trang bắt đầu tiến hành đọc
        $count = $this->countNumberResult($source)*($page - 1); // Số lượng cũng sách đã đọc tới

        // Kiểm tra thông tin số lượng kết quả cần trả về
        if ($numResult == 0){
            $numResult = $this->countNumberAllResult();
            if ($numResult == false) return false;
        }

        // Kiểm tra yêu cầu có cần lấy trong khoảng hay không
        if ($isFilter == true){
            if ($getTo - $getFrom != $numResult) return false;
            if ($getTo < $getFrom) return false;
            if ($getTo < 0) return false;
        }

        // Tiến hành đọc kết quả
        while ($numResult > 0){
            if ($page != 1) {
                $source = $this->getSourcePage($page); // Get source of page
            }
            // Element cha chứa thông tin tất cả sách
            $parent = $source->find(".product-box-list")[0];
            // Lấy danh sách từng sách trong element cha
            $books = $parent->find(".product-item");
            // Số lượng sách cần đọc
            $size = count($books);

            // Nếu như có filter, kiểm tra có trong phạm vi sách cần đọc hay không
            $from = 0; //Vị trí bắt đầu đọc
            $to = $size; //Số lượng cần đọc từ from
            if ($isFilter == true){
                if ($count + $size >= $getFrom){ //Thuộc phạm vi cần đọc
                    $from = $getFrom - $count;
                    if ($from < 0) $from = 0; //Chỉ có denta ở lần đầu tiên vào khoảng cần đọc
                    if ($count + $size >= $getTo){ //Còn thuộc phạm vi cần đọc
                        $to -= ($count + $size) - $getTo; //Xác định lại số lượng cần đọc từ from
                    }
                }
                else {
                    $from = $size;
                }
            }
            // Cập nhật lại sơ lượng sách đã đọc
            $count += $size;
            // Đọc thông tin từng sách
            for ($i = $from; $i < $to; $i++){
                $book = $books[$i];
                //$id = $book->getAttribute("data-seller-product-id");
                $name = $book->getAttribute("data-title");
                $price =
                    str_replace(
                        ".",
                        "",
                        $book->find(".price-regular")[0]->plaintext
                    );
                $url = $book->firstChild()->getAttribute("href");
                $result[] = array_merge(
                    [
                        //"id" => $id,
                        "name" => $name,
                        "price" => (int)substr($price, 0, strlen($price) - 5)
                    ],
                    $this->getInfoBook($url)
                );
            }

            // Cập nhật số lượng sách cần đọc
            $numResult -= ($to - $from);
            // Thay đổi số lượng page
            $page += 1;
        }
        return $result;
    }

    /**
     * @todo Count number of all result in all page
     * @return bool|int
     */
    public function countNumberAllResult()
    {
        $source = $this->getSourcePage(1);
        if ($source == false) return false;
        $items = $source->find(".filter-list-box h4");
        if (count($items) == 0) return false;

        // Tách chuỗi lưu kết quả
        $text = $items[0]->plaintext;
        return (int)explode(" ", $text)[0];
    }

    /**
     * @todo Get info book from url
     * @param $url
     * @return array
     */
    private function getInfoBook($url)
    {
        $source = str_get_html($this->getSourceFromURL($url, TikiCrawler::$GET));
        $author = $this->getAuthor($source);
        $nxb = $this->getNXB($source);
        $descript = $this->getDescript($source);
        $rate = $this->getRating($source);
        $categories = $this->getCategories($source);
        $imgs = $this->getImages($source);
        return array(
            "rate" => $rate,
            "author" => $author,
            "categories" => $categories,
            "nxb" => $nxb,
            "descript" => $descript,
            "imgs" => $imgs
        );
    }

    /**
     * @todo Get author of book
     * @param $source
     * @return bool
     */
    private function getAuthor($source)
    {
        $table = $source->find(".table-detail");
        if (count($table) == 0) return false;
        $rows = $table[0]->find("tr");
        foreach ($rows as $row){
            if ($row->firstChild()->getAttribute("rel") == "author")
                return $row->lastChild()->plaintext;
        }
    }

    /**
     * @todo Get NXB of book
     * @param $source
     * @return bool
     */
    private function getNXB($source)
    {
        $table = $source->find(".table-detail");
        if (count($table) == 0) return false;
        $rows = $table[0]->find("tr");
        foreach ($rows as $row){
            if ($row->firstChild()->getAttribute("rel") == "manufacturer_book_vn")
                return $row->lastChild()->plaintext;
        }
    }

    /**
     * @todo Get description of book
     * @param $source
     * @return mixed
     */
    private function getDescript($source)
    {
        $descript = $source->getElementByID("gioi-thieu");
        return str_replace("\n", "", $descript->plaintext);
    }

    /**
     * @todo Get rating of book
     * @param $source
     * @return bool|float|int
     */
    private function getRating($source)
    {
        $rating_box = $source->find(".rating-box");
        if (count($rating_box) == 0) return false;
        // Lấy ra định dạng độ rộng của khung rating <=> mức rating
        $style = $rating_box[0]->lastChild()->getAttribute("style");
        // Tách riêng phần giá trị của chiều rộng để trả về kết quả
        return ((float)explode("%", explode(":", $style)[1])[0])/10;
    }

    /**
     * @todo Get type of book
     * @param $source
     * @return array|bool
     */
    private function getCategories($source)
    {
        $breadcrumb = $source->find(".breadcrumb");
        if (count($breadcrumb) == 0) return false;
        $categories = $breadcrumb[0]->getElementsByTagName("li");

        $size = count($categories);
        $result = array();
        for ($i = 2; $i < ($size - 1); $i++){
            $result[] = $categories[$i]->plaintext;
        }
        return $result;
    }

    /**
     * @todo Get image of book
     * @param $source
     * @return array|bool
     */
    private function getImages($source)
    {
        $imgs = $source->find(".product-magiczoom");
        if (count($imgs) == 0) return false;
        $result = array();
        foreach ($imgs as $img){
            $result[] = $img->getAttribute("src");
        }
        return $result;
    }

    /**
     * @todo Get source html of page number
     * @param $page
     * @return bool|simple_html_dom
     */
    private function getSourcePage($page)
    {
        $url = str_replace('{num}', $page, $this->uri);
        return str_get_html($this->getSourceFromURL($url, TikiCrawler::$GET));
    }

    /**
     * @todo Get index of page contain index getFrom
     * @param $getFrom
     * @return float|int
     */
    private function getIndexPage($source = null, $getFrom)
    {
        if ($source == null) {
            $source = $this->getSourcePage(1);
        }
        // Đếm số lượng kết quả có trên 1 page
        $count = $this->countNumberResult($source);
        if ($count == false) return 1;

        return (int)($getFrom/$count) + 1;
    }

    /**
     * @todo Count number result in page have index as $page
     * @param null|string $source
     * @param int $page
     * @return bool|int
     */
    private function countNumberResult($source = null, $page = 1){
        if ($source == null){
            $source = $this->getSourcePage($page);
        }

        // Lấy danh sách từng sách trong element cha
        $books = $source->find(".product-item");
        if ($books == false) return false;
        return count($books);
    }

    private function getURI($lang)
    {
        if ($lang == "eng") return $this->uris[$lang];
        return $this->uris["vi"];
    }

}