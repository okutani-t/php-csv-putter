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
     *
     * @access public
     * @param array $records 1次配列か2次配列で渡す
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
     * @param string $keyNames
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

        // レコードが1次元配列か2次元配列かチェックして個別に並べ替え
        if ($this->array_depth($this->records) === 1) {
            foreach ($keyNames as $value) {
                $rmKeyRecords[] = $this->records[$value];
            }
        } elseif ($this->array_depth($this->records) === 2) {
            $i = 0;
            foreach ($this->records as $record) {
                for ($j = 0; $j < count($keyNames); $j++) {
                    $rmKeyRecords[$i][] = $record[$keyNames[$j]];
                }
                $i++;
            }
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
        
        // 改行コードを\nに統一
        foreach ($this->records as $key => &$value) {
            $value = str_replace("\r\n", "\n", $value);
        }
        
        // ヘッダーとレコードのエンコーディング処理
        $this->csvEncoding();

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
    private function csvEncoding()
    {
        // エンコーダーの無名関数
        $encoder = function($arr){
            return mb_convert_encoding('"' . implode('","', $arr ) . '"' . "\r\n", "SJIS", "auto");
        };

        // ヘッダーのエンコーディング
        $this->hList = $encoder($this->hList);

        // レコードが1次元配列か2次元配列かチェックして個別にエンコーディング
        if ($this->array_depth($this->records) === 1) {
            $this->records = $encoder($this->records);
        } elseif ($this->array_depth($this->records) === 2) {
            for ($i = 0; $i < count($this->records); $i++) {
                $this->records[$i] = $encoder($this->records[$i]);
            }
        }
    }

    /**
     * 配列の深さを調べる
     *
     * @param  array $arr
     * @return int 配列の深さ
     */
    private function array_depth($arr, $depth=0){
        if( !is_array($arr)){
            return $depth;
        } else {
            $depth++;
            $tmp = array();
            foreach($arr as $value){
                $tmp[] = $this->array_depth($value, $depth);
            }
            return max($tmp);
        }
    }

}
