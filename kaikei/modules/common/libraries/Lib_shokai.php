<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 支払通知書作成用クラス
 */
class Lib_shokai
{

    /**
     * 支払通知書番号：発行月の発行通番を取得
     *
     * @param  int  : 発行年月
     * @return int
     */
    public static function shiharai_serial_num($issue_yymm)
    {

        $CI =& get_instance();

        // レコード有無のチェック
        $CI->load->model('Issue_num', 'in', TRUE);
        $in_data = $CI->in->shiharai_serial_num($issue_yymm);

        if (count($in_data) == 0)
        {
            return 1;
        } else {
            return $in_data[0]['in_cnt'];
        }

    }

    /**
     * 支払通知書番号：発行月の発行通番を更新
     *
     * @param  int  : 発行年月
     * @param  int  : 発行番号
     * @return int
     */
    public static function shiharai_serial_num_update($issue_yymm, $serial_num)
    {

        $CI =& get_instance();

        // レコード有無のチェック
        $CI->load->model('Issue_num', 'in', TRUE);
        $in_data = $CI->in->shiharai_serial_num($issue_yymm);

        if (count($in_data) == 0)
        {
            // 新規作成
            $CI->in->insert_shiharai_num($issue_yymm, $serial_num);
        } else {
            // 更新
            $CI->in->update_shiharai_num($issue_yymm, $serial_num);
        }

    }

    /**
     * 支払通知書：売上月度計算
     *
     * @param  int
     * @param  date
     * @return array()
     */
    public static function issue_collect($sk_payment, $issue_yymm)
    {

        $_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
        $date = new DateTime($_create_date);

        switch( $sk_payment ){
            case 0:
                // 指定なし　→　月末締め当月末
                $collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
                $collect_date['sales_yymm'] = $issue_yymm;
                break;
            case 1:
                // 月末締め当月末
                $collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
                $collect_date['sales_yymm'] = $issue_yymm;
                break;
            case 2:
                // 月末締め翌月末
                $collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
                $collect_date['sales_yymm'] = $date->modify('last day of last months')->format('Ym');
                break;
            case 3:
                // 月末締め翌々月10日
                $collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-10');
                $collect_date['sales_yymm'] = $date->modify('-2 months')->format('Ym');
                break;
            case 4:
                // 月末締め翌々月15日
                $collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-15');
                $collect_date['sales_yymm'] = $date->modify('-2 months')->format('Ym');
                break;
            case 5:
                // 月末締め翌々月25日
                $collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-25');
                $collect_date['sales_yymm'] = $date->modify('-2 months')->format('Ym');
                break;
            case 6:
                // 月末締め翌々月末
                $collect_date['pay_date']   = $date->modify('last day of next months')->format('Y-m-d');
                $collect_date['sales_yymm'] = $date->modify('-2 months')->format('Ym');
                break;
            default:
                // エラー　→　月末締め当月末
                $collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
                $collect_date['sales_yymm'] = $issue_yymm;
        }

        return $collect_date;

    }

    /**
     * 支払通知書番号：発行 【LA101-SH-BX001-1611】
     *
     * @param  int  : 支払先seq
     * @param  int  : 発行年月
     * @param  int  : 発行月の発行数
     * @param  char : 一括/個別
     * @return int
     */
    public static function shiharai_num($sk_seq, $date_yymm, $_serial_num, $invo_class)
    {

        $CI =& get_instance();

        $issue_num        = $CI->config->item('INVOICE_ISSUE_NUM');                     // 接頭語:L
        $issue_code       = $CI->lib_shokai->issue_code($sk_seq);                       // 会社名かな⇒記号
        $issue_customer   = $sk_seq;                                                    // 支払先会社NO
        $issue_kind       = "SH";                                                       // 売上種別
        $issue_class      = $invo_class;                                                // 一括発行=B,個別発行=C
        $issue_accounting = "X";                                                        // X=通常
        $issue_serial_num = $_serial_num;                                               // 発行通番
        $issue_yymm       = $date_yymm;                                                 // 発行年月

        $set_number  = $issue_num
                     . $issue_code                                                      // 会社名記号
                     . $issue_customer                                                  // 顧客NO
                     . '-' . $issue_kind                                                // KT(SEO固定)、SK（SEO成功）
                     . '-' . $issue_class                                               // 一括発行=B,個別発行=C
                     . $issue_accounting                                                // X=通常(固定、成果)/Y=前受が含む場合/Z=赤伝用請求書（マイナス）
                     . str_pad($issue_serial_num, 3, 0, STR_PAD_LEFT)                   // 発行通番
                     . '-' . substr($issue_yymm, 2, 4);                                 // 発行年月

        return $set_number;

    }

    /**
     * 紹介料のサブトータル金額の計算
     *
     * @param  int
     * @param  array()
     * @return int
     */
    public static function cal_result_total($_total, $_rate, $_fix, $_issue_tax)
    {

        $_subtotal = ($_total * $_rate) + $_fix;

        // 端数計算
        switch( $_issue_tax['hasuu'] )
        {
            case 0:

                // 切り上げ
                $total = $_subtotal + 0.9;
                break;

            case 1:

                // 切り捨て
                break;

            case 2:
            default:

                // 四捨五入
                $total = $_subtotal + 0.5;
                break;
        }

        $result = floor( $total );

        return $result;

    }

    /**
     * 支払通知書番号：会社名から記号変換
     *
     * @param  array()
     * @return int
     */
    public static function issue_code($sk_seq)
    {

        $CI =& get_instance();

        $CI->load->model('Shokai', 'sk', TRUE);
        $sk_data = $CI->sk->get_sk_seq($sk_seq);

        $kana = array(
                "ア" => "A1",
                "イ" => "A2",
                "ウ" => "A3",
                "エ" => "A4",
                "オ" => "A5",
                "カ" => "K1",
                "キ" => "K2",
                "ク" => "K3",
                "ケ" => "K4",
                "コ" => "K5",
                "サ" => "S1",
                "シ" => "S2",
                "ス" => "S3",
                "セ" => "S4",
                "ソ" => "S5",
                "タ" => "T1",
                "チ" => "T2",
                "ツ" => "T3",
                "テ" => "T4",
                "ト" => "T5",
                "ナ" => "N1",
                "ニ" => "N2",
                "ヌ" => "N3",
                "ネ" => "N4",
                "ノ" => "N5",
                "ハ" => "H1",
                "ヒ" => "H2",
                "フ" => "H3",
                "ヘ" => "H4",
                "ホ" => "H5",
                "マ" => "M1",
                "ミ" => "M2",
                "ム" => "M3",
                "メ" => "M4",
                "モ" => "M5",
                "ヤ" => "Y1",
                "ユ" => "Y3",
                "ヨ" => "Y5",
                "ラ" => "R1",
                "リ" => "R2",
                "ル" => "R3",
                "レ" => "R4",
                "ロ" => "R5",
                "ワ" => "Y1",
                "ヲ" => "O1",
                "ン" => "O2",
                "ガ" => "K1",
                "ギ" => "K2",
                "グ" => "K3",
                "ゲ" => "K4",
                "ゴ" => "K5",
                "ザ" => "S1",
                "ジ" => "S2",
                "ズ" => "S3",
                "ゼ" => "S4",
                "ゾ" => "S5",
                "ダ" => "T1",
                "ヂ" => "T2",
                "ヅ" => "T3",
                "デ" => "T4",
                "ド" => "T5",
                "バ" => "H1",
                "ビ" => "H2",
                "ブ" => "H3",
                "ベ" => "H4",
                "ボ" => "H5",
                "パ" => "H1",
                "ピ" => "H2",
                "プ" => "H3",
                "ペ" => "H4",
                "ポ" => "H5",
                "ヴ" => "B1"
        );

        $moji = mb_substr($sk_data[0]['sk_company_kana'], 0, 1);

        if (preg_match("/^[ァ-ヶー]+$/u", $moji))                                    // 全角カナチェック
        {
            $get_code = $kana[$moji];
        } else {
            $get_code = "X0";
        }

        return $get_code;

    }

}