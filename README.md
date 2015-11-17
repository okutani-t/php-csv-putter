# PHP CSV SETTER

PHPで使えるCSVファイルを保存するやつ

---

## 使い方

```php
// CsvDownloader読み込み
require_once(__DIR__."/CsvPutter.class.php");
CsvPutter::_()
    ->setFilePath(保存するCSVファイルのパス)
    ->setHeadList(ヘッダーのリスト)
    ->setRecords(レコードのリスト)
    ->sortRecordsByUsingKeys(レコードのKEY名のリスト) #連想配列を並べ替えたい時
    ->execute();
```

### 使用例

```php
// CsvDownloader読み込み
require_once(__DIR__."/CsvPutter.class.php");

$records = array(
            array("id"=>1,"name"=>"tanaka"),
            array("id"=>2,"name"=>"yamada"),
            array("id"=>3,"name"=>"nakano")
        );

CsvPutter::_()
    ->setFilePath("./csv/test-csv.csv")
    ->setHeadList(array("名前","ID"))
    ->setRecords($records)
    ->sortRecordsByUsingKeys(array("name","id"))
    ->execute();
```

連想配列じゃなくても問題なく動作します。

author: okutani
