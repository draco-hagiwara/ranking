<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 汎用バリデータ用クラス
 * 文字列・数字・文字数などのチェックを行う
 */
class CommonValidator
{

    /**
     * 英数字チェック
     *
     * @param string $arg チェックする値
     * @return bool 英数字のみの場合true、そうでなければfalse
     */
    public static function checkAlphanumeric($arg)
    {
        if(CommonValidator::checkString($arg) && preg_match('/^[a-zA-Z0-9]+$/', $arg)){
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
        if (CommonValidator::checkString($arg) && ctype_digit((string)$arg)) {
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
        if (CommonValidator::checkString($arg) && is_numeric((string)$arg)) {
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
    	if (CommonValidator::checkString($arg)) {
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
        if (is_string($arg) && CommonValidator::checkDigit($min) && mb_strlen($arg) >= $min
        && (is_null($max) || (CommonValidator::checkDigit($max) && mb_strlen($arg) <= $max))){
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
        if (CommonValidator::checkDigit($arg) && CommonValidator::checkDigit($min) && $arg >= $min
        && (is_null($max) || (CommonValidator::checkDigit($max) && $arg <= $max))){
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
        if (CommonValidator::checkString($mailAddress) && preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $mailAddress)){
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
        if (CommonValidator::checkString($arg) && preg_match('/^[!-~]+$/i', $arg)) {
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
        if (CommonValidator::checkString($arg) && preg_match(';^(https?://).+|(mailto:).+@.+;', $arg)) {
            return true;
        } else {
            return false;
        }
    }

}
