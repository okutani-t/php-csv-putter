<?php
/**
 * CSVを指定したディレクトリに保存するクラス
 *
 * @access public
 * @author okutani
 * @package Class
 * @category Putter
 */
class CsvPutter
{
    /**
     * @var string $filePath 保存するCSVファイルのパス
     * @var array  $hList 出力されるヘッダーのリスト
     * @var array  $records 出力されるレコードのリスト
     */
    private $filePath = "";
    private $hList    = array();
    private $records  = array();

    /**
     * 自身のインスタンスを生成
     * @access public
     * @return object new self
     */
    public static function _() {
        return new self;
    }

    /**
     * CSVファイルを保存する場所のパスをセット
     *
     * @access public
     * @param string  $filePath 保存場所までのパス
     * @return object $this
     */
    public function setFilePath($filePath="")
    {
        // 空チェック
        if ($filePath === "") { trigger_error("empty filePath!", E_USER_NOTICE); }

        // .csvがついてなかったら添付
        if (!preg_match("/\.csv\z/", $filePath)) {
            $filePath .= ".csv";
        }

        $this->filePath = $filePath;

        return $this;
    }

    /**
     * ヘッダー情報を追加するセッター
     *
     * @access public
     * @param array $hList ヘッダーが格納された配列を入力
     */
    public function setHeadList($hList=array())
    {
        $this->hList = $hList;

        return $this;
    }

    /**
     * レコード情報を追加するセッター
     * レコードが1つでも必ず2次元配列で渡す
     *
     * @access public
     * @param array $records 2次元で渡す
     */
    public function setRecords($records=array())
    {
        $this->records = $records;

        return $this;
    }

    /**
     * 連想配列を使ったレコードをソートする関数
     * 渡した配列の順番で連想配列をソーティングする
     * 呼び出し前にsetRecords()しておく必要がある
     * setHeadList()でセットするヘッダーの順番と同じにしておくと良い
     *
     * @access public
     * @param string $keyNames 複数の引数を渡せる
     * @return object $this
     */
    public function sortRecordsByUsingKeys($keyNames=array())
    {
        // エラー処理
        if (empty($keyNames) || !is_array($keyNames)) {
            trigger_error("error key args!", E_USER_ERROR);
        }
        if (empty($this->records)) {
            trigger_error("empty records!", E_USER_ERROR);
        }

        $i = 0;
        foreach ($this->records as $record) {
            for($j = 0; $j < count($keyNames); $j++){
                $rmKeyRecords[$i][] = $record[$keyNames[$j]];
            }
            $i++;
        }

        $this->records = $rmKeyRecords;

        return $this;
    }

    /**
     * CSV保存実行部
     *
     * @access public
     */
    public function execute()
    {
        // 空チェック
        if ($this->filePath === "" ||
            empty($this->hList)    ||
            empty($this->records)) {
            trigger_error("empty any item!", E_USER_NOTICE);
        }

        // ヘッダーとレコードのエンコーディング処理
        $this->csvEcoding();

        // ファイルが存在していなかったらヘッダーを記入して新規作成
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, $this->hList, LOCK_EX);
        }

        // CSVファイルに書き込み(追記モード)
        file_put_contents($this->filePath, $this->records, FILE_APPEND | LOCK_EX);
    }

    /**
     * メッセージリストのエンコーディング処理
     *
     * @access private
     */
    private function csvEcoding()
    {
        // エンコーダーの無名関数
        $encoder = function($ary){
            return mb_convert_encoding('"' . implode('","', $ary ) . '"' . "\n", "SJIS", "auto");
        };

        // ヘッダーのエンコーディング
        $this->hList = $encoder($this->hList);

        // レコードのエンコーディング
        for ($i = 0; $i < count($this->records); $i++) {
            $this->records[$i] = $encoder($this->records[$i]);
        }
    }

}
