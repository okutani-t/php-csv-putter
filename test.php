<?php
// CsvDownloader読み込み
require_once(__DIR__."/CsvPutter.class.php");

// POST時動作
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // テストレコード
    $records = array("id"=>1,"name"=>"tanaka");

    CsvPutter::_()
        ->setFilePath("./csv/test-csv.csv")
        ->setHeadList(array("name"=>"名前","id"=>"ID"))
        ->setRecords($records)
        #->sortRecordsByUsingKeys(array("name", "id"))
        ->execute();

echo "CSV Putterが実行されました。";
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>CSV Putter test</title>
    </head>
    <style>
        body {
            width: 940px;
            margin: 0 auto;
        }
    </style>
    <body>
        <h1>CSV Putterのテスト</h1>
        <form action="" method="post">
            <input type="submit" name="name" value="保存">
        </form>
    </body>
</html>
