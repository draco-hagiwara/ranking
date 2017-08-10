<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 汎用バリデータ用クラス
 * 文字列・数字・文字数などのチェックを行う
 */
class Lib_validator
{

    /**
     * 英数字チェック
     *
     * @param string $arg チェックする値
     * @return bool 英数字のみの場合true、そうでなければfalse
     */
    public static function checkAlphanumeric($arg)
    {
        if(Lib_validator::checkString($arg) && preg_match('/^[a-zA-Z0-9]+$/', $arg)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * 日付時間型のチェック<br>
     * フォーマットを指定した場合は指定したフォーマットでチェック<br>
     * フォーマットを指定しない場合はYYYY-MM-DD hh:mm:ssの形でチェック<br>
     * 存在しない日時はNGとする
     *
     * @param string $arg チェックする値
     * @param string $format フォーマット
     * @return bool フォーマットで指定された日付時間で且つ、存在する日付時間の場合true、そうでなければfalse
     */
    public static function checkDateFormat($arg, $format = 'Y-m-d H:i:s')
    {
        if ($arg === date($format, strtotime($arg))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 数字型チェック<br>
     * 数字だけからできていることをチェックする
     *
     * @param string $arg チェックする値
     * @return bool 数字だけの場合true、そうでなければfalse
     */
    public static function checkDigit($arg)
    {
        if (Lib_validator::checkString($arg) && ctype_digit((string)$arg)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * int型文字列のチェック<br>
     *
     * @param string $arg チェックする値
     * @return bool int型文字列の場合true、そうでなければfalse
     */
    public static function checkInt($arg)
    {
        if (Lib_validator::checkString($arg) && is_numeric((string)$arg)) {
            $arg += 0;
            if (is_int($arg)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Decimal型文字列＆桁数のチェック<br>
     *
     * @param string $arg チェックする値
     * @return bool decimal型文字列の場合true、そうでなければfalse
     */
    public static function checkDecimal($arg, $len)
    {
        if (Lib_validator::checkString($arg)) {
            if (preg_match( '/^[0-9]+(.[0-9]{1,' . $len . '})?$/', $arg )) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 文字列の長さチェック<br>
     * string型文字列の長さチェック<br>
     * 最小と最大を指定、最大を指定しない場合は無制限
     *
     * @param string $arg チェックする値(string型の文字列を設定すること)
     * @return bool 指定された範囲内の文字列長だった場合true、そうでなければfalse
     */
    public static function checkLength($arg, $min, $max = null)
    {
        if (is_string($arg) && Lib_validator::checkDigit($min) && mb_strlen($arg) >= $min
        && (is_null($max) || (Lib_validator::checkDigit($max) && mb_strlen($arg) <= $max))){
            return true;
        } else {
            return false;
        }
    }

    /**
     * 数値の範囲チェック<br>
     * 整数型数値の範囲チェック<br>
     * 最小と最大を指定、最大を指定しない場合は無制限
     *
     * @param int $arg チェックする値(整数型数値を設定すること)
     * @return bool 指定された範囲内の数値だった場合true、そうでなければfalse
     */
    public static function checkRange($arg, $min, $max = null)
    {
        if (Lib_validator::checkDigit($arg) && Lib_validator::checkDigit($min) && $arg >= $min
        && (is_null($max) || (Lib_validator::checkDigit($max) && $arg <= $max))){
            return true;
        } else {
            return false;
        }
    }

    /**
     * メールアドレス形チェック<br>
     * メールアドレスとして正しいかのチェックを行う
     *
     * @param string $arg チェックする値
     * @return bool メールアドレス形式の文字列だった場合true、そうでなければfalse
     */
    public static function checkMailAddress($mailAddress)
    {
        if (Lib_validator::checkString($mailAddress) && preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $mailAddress)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * シングルバイト文字列型チェック<br>
     * シングルバイト文字列かのチェックを行う
     *
     * @param string $arg チェックする値
     * @return bool シングルバイト文字列の場合true、そうでなければfalse
     */
    public static function checkSingleByte($arg)
    {
        if (Lib_validator::checkString($arg) && preg_match('/^[!-~]+$/i', $arg)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 文字列型チェック<br>
     * 文字列として扱えるかどうかのチェック<br>
     * 数字もOKとするが、arrayやクラスはNGとする
     *
     * @param string $arg チェックする値
     * @return bool 文字列の場合true、そうでなければfalse
     */
    public static function checkString($arg)
    {
        if (is_string($arg) || is_numeric($arg)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * URI型のチェック<br>
     * https://mailto: の形をチェックする
     *
     * @param string $arg チェックする値
     * @return bool URI型文字列の場合true、そうでなければfalse
     */
    public static function checkUri($arg)
    {
    	//if (Lib_validator::checkString($arg) && preg_match(';^(https?://).+|(mailto:).+@.+;', $arg)) {
    	if (Lib_validator::checkString($arg) && preg_match('/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $arg)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 入力値の整形（変換）
     *
     * @access public
     * @param  string
     * @param  string
     * @return string
     *
     *
     */
    public static function convert($str, $val)
    {
        if($str == '') {
            return '';
        }

        switch($val)
        {
            case 'single': // 半角文字列
                return mb_convert_kana($str, 'ras');
                break;
            case 'double': // 全角文字列
                return $val = mb_convert_kana($str, 'ASKV');
                break;
            case 'hiragana': // ひらがな
                return mb_convert_kana($str, 'HVc');
                break;
            case 'katakana': // 全角カタカナ
                return mb_convert_kana($str, 'KVC');
                break;
            case 'single_katakana': // 半角カタカナ
                return mb_convert_kana($str, 'kh');
                break;
            case 'phone': // 電話番号
                $str = mb_convert_kana($str, 'ras');
                return str_replace(array('ー','―','‐'), '-', $str);
                break;
            case 'postal': // 郵便番号
                $str = mb_convert_kana($str, 'ras');
                $str = str_replace(array('ー','―','‐'), '-', $str);
                if(strlen($str) == 7 AND preg_match("/^[0-9]+$/", $str))
                {
                    $str = substr($str, 0, 3) . '-' . substr($str, 3);
                }
                return $str;
                break;
            case 'ymd': // 西暦年月日
                $str = mb_convert_kana($str, 'ras');
                $str = str_replace('/', '-', $str);
                if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $str) AND strlen($str) != 10) {
                    $tmp = explode('-', $str);
                    return vsprintf("%4d-%02d-%02d", $tmp); // 月日の箇所をゼロ詰めに整形
                }
                break;
            case 'html': // HTMLタグからXSSなどの悪意のあるコードを除外
                $CI =& get_instance();
                $CI->load->helper('escape_helper'); // escape_helper.php については http://blog.aidream.jp/?p=1479 を参照ください
                $clean_html = purify($str);
                return ($clean_html == '<p></p>'.PHP_EOL) ? '' : $clean_html; // TinyMCEヘルパを使用している場合の対策
                break;
        }
    }

    /**
     * 半角チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function single($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        return (strlen($str) != mb_strlen($str)) ? FALSE: TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 全角チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function double($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $ratio = (mb_detect_encoding($str) == 'UTF-8') ? 3 : 2;
        return (strlen($str) != mb_strlen($str) * $ratio) ? FALSE : TRUE;
    }
    /*
     // 上記以外の判別方法
     function double($str)
     {
     if ($str == '')
     {
     return TRUE;
     }
     $str = mb_convert_encoding($str, 'UTF-8');
     // 半角文字が含まれていない場合 TRUE
     return (preg_match("/(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])|[\x20-\x7E]/", $str)) ? FALSE : TRUE;
     }
     */

    // --------------------------------------------------------------------

    /**
     * ひらがな チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function hiragana($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $str = mb_convert_encoding($str, 'UTF-8');
        return ( ! preg_match("/^(?:\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93]|ー)+$/", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 全角カタカナ チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function katakana($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $str = mb_convert_encoding($str, 'UTF-8');
        return ( ! preg_match("/^(?:\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|ー)+$/", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 半角カタカナ チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function single_katakana($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $str = mb_convert_encoding($str, 'UTF-8');
        return ( ! preg_match("/^(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])+$/", $str)) ? FALSE : TRUE;
    }

    /**
     * 半角英数記号カナ チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function single_eisukana($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $str = mb_convert_encoding($str, 'UTF-8');
        return ( ! preg_match("/^(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F]|[ -\~]|[()])+$/", $str)) ? FALSE : TRUE;
    }

    /**
     * クレジットカード 名義チェック（英字大文字）
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function creditcard_name($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        return ( ! preg_match("/^[A-Z]+[\s|　]+[A-Z]+[\s|　]*[A-Z]+$/", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * YYYY-MM-DD形式のチェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function ymd($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $tmp = explode('-', $str);
        if (count($tmp) != 3) {
            return false;
        }
        $tmp = array_map('intval', $tmp);
        return ( ! checkdate($tmp[1], $tmp[2], $tmp[0])) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 環境依存文字・旧漢字などJISに変換できない文字チェック
     *
     * @access public
     * @param  string
     * @return bool
     *
     */
    public static function jis($str)
    {
        if ($str == '')
        {
            return TRUE;
        }
        $str = str_replace(array('～', 'ー', '－', '∥', '￠', '￡', '￢'), '', $str);
        $str2 = mb_convert_encoding($str, 'iso-2022-jp', $encoding);
        $str2 = mb_convert_encoding($str2, $encoding,'iso-2022-jp');
        return ($str != $str2) ? FALSE : TRUE;
    }

}
