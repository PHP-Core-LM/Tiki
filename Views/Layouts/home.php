<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tiki Crawler - Cào dữ liệu sách Tiki</title>
    <link rel="icon" type="image/png" href="./Assets/imgs/tiki.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="./Assets/css/style.css">
</head>

<body>
    <div class="container main">
        <h4 class="title">Nhập thông số để tiến hành cào dữ liệu sách</h4>
        <div class="row">
            <div class="col-sm-3">
                <label for="lang">Ngôn ngữ</label>
                <select name="lang" id="lang">
                    <option value="vi">Tiếng Việt</option>
                    <option value="eng">Tiếng Anh</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label for="soLuong">Số lượng sp</label>
                <input type="number" id="soLuong" class="soLuong" name="num" placeholder="Nhập số lượng sp..." value="10" min="1">
            </div>
            <div class="col-sm-3">
                <label for="from">Bắt đầu</label>
                <input type="number" id="from" class="from" name="from" placeholder="Bắt đầu từ..." value="0" min="0">
            </div>
            <div class="col-sm-3">
                <label for="to">Kết thúc</label>
                <input type="number" id="to" class="to" name="to" placeholder="Kết thúc ở..." value="10" min="0">
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-sm-4"></div>
            <div class="col-md-2 col-sm-4">
                <input type="submit" value="Tiến hành cào">
            </div>
            <div class="col-md-5 col-sm-4"></div>
        </div>

        <div class="status">
            <span>Đang tiến hành cào dữ liệu</span>
            <span>Đang lưu dữ liệu</span>
            <span>Đã cào xong</span>
            <span>Quá trình lấy dữ liệu bị lỗi</span>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="./Assets/js/script.js" type="text/javascript"></script>
    <script src="./Assets/js/input.js" type="text/javascript"></script>
</body>

</html>