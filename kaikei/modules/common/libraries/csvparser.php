<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CsvParser
{
    private $handle;
    private $file;
    private $parse_header;
    private $length;
    private $delimiter;
    private $enclosure;

    public $header;

    public function __construct()
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');
    }

    public function __destruct()
    {
        if(is_resource($this->handle))
        {
            fclose($this->handle);
        }
    }

    public function load($file, $parse_header = FALSE, $length = 50000, $delimiter = ',', $enclosure = '"')
    {
        $this->file         = $file;
        $this->parse_header = $parse_header;
        $this->length       = $length;
        $this->delimiter    = $delimiter;
        $this->enclosure    = $enclosure;

        if(!file_exists($this->file)){
            throw new Exception(sprintf("%sが存在しません。", basename($this->file)));
        }

        if(($file = file_get_contents($this->file)) === FALSE){
            throw new Exception(sprintf("%sを読み込めません。", basename($this->file)));
        }else{
            //テンポラリファイルを作成
            $this->handle = tmpfile();
            // 文字コード変換
            $file = mb_convert_encoding($file, mb_internal_encoding(), mb_detect_encoding($file, 'UTF-8, EUC-JP, JIS, eucjp-win, sjis-win'));
            // バイナリセーフなファイル書き込み処理
            fwrite($this->handle, $file);
            // ファイルポインタの位置を先頭へ
            rewind($this->handle);
        }

        if ($this->parse_header) {
            $this->header = fgetcsv($this->handle, $this->length, $this->delimiter, $this->enclosure);
            $this->header = array_map(function($header){
                return preg_replace('/\n|\r/', '', $header);
            }, $this->header);
        }
    }

    public function parse()
    {
        if(!$this->handle){
            throw new Exception(sprintf("%s", "ファイルが読み込まれていません。"));
        }

        $data = array();

        while( ($row = fgetcsv($this->handle, $this->length, $this->delimiter, $this->enclosure)) !== FALSE )
        {
            if($this->header)
            {

            	if (count($row) == 1)
            	{
            		throw new Exception(sprintf('%s', "ファイルが不正です(空行が含まれています)。"));
            	}

            	// 配列名にヘッダー項目名
                foreach($this->header as $i => $heading_i)
                {
                    if(isset($row[$i])){
                        $line[$heading_i] = $row[$i];
                    }else{
                        throw new Exception(sprintf('%s', "ファイルが不正です(項目数の不一致など)。"));
                    }
                }

                $data[] = $line;

            	// 配列名が 0～
                //$data[] = $row;
            }
            else
            {

                $data[] = $row;
            }
        }

        fclose($this->handle);

        return $data;
    }
}
