<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

# include TCPDF
require_once(APPPATH . '../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once(APPPATH . '../../vendor/setasign/fpdi/fpdi.php');

/**
 * TCPDF - CodeIgniter Integration
 */
class Lib_pdf_shokai extends TCPDF {

    /**
     * Initialize
     *
     */
    function __construct($params = array())
    {
        $orientation = 'P';                                                     // 用紙の向き[P=縦方向、L=横方向]
        $unit        = 'mm';                                                    // 処理単位[mm=ミリメートル]
        $format      = 'A4';                                                    // ページフォーマット[A4]
        $unicode     = true;
        $encoding    = 'UTF-8';
        $diskcache   = false;

        if (isset($params['orientation'])) {
            $orientation = $params['orientation'];
        }
        if (isset($params['unit'])) {
            $unit = $params['unit'];
        }
        if (isset($params['format'])) {
            $format = $params['format'];
        }
        if (isset($params['encoding'])) {
            $encoding = $params['encoding'];
        }
        if (isset($params['diskcache'])) {
            $diskcache = $params['diskcache'];
        }

        # initialize TCPDF
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
    }

    /**
     * 支払通知書PDF：１枚＆複数枚（明細別） 作成
     *
     */
    public function create_pdf($get_skf_data, $get_skc_data, $pdflist_path, $base_path)
    {

        $pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
        $pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
        $pdf->SetCellPadding(0);                                                // セルパディングの設定
        $pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
        $pdf->setPrintHeader(false);                                            // ページヘッダを無効
        $pdf->setPrintFooter(false);                                            // ページフッタを無効
        //$pdf->AddPage();                                                      // 空のページを追加

        // ノーマルフォントとボールドフォントを追加
        $font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
        $font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
        $font = new TCPDF_FONTS();
        $font1 = $font->addTTFfont($font_path1, '', '', 32);
        $font2 = $font->addTTFfont($font_path2, '', '', 32);

        // PDFテンプレートの読み込み
        //$pdf->setSourceFile($pdflist_path);
        //$page = $pdf->importPage(1);                                          // PDFテンプレートの指定ページを使用する
        //$pdf->useTemplate($page);

        $pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

        // PDFドキュメントプロパティ設定
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Themis Inc.');
        $pdf->SetTitle('invoice');
        $pdf->SetSubject('invoice');

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // 【１枚支払通知書の作成】
        foreach ($get_skf_data as $key => $value) {
            foreach ($value as $key_skf => $val) {

                // 紹介(売上)先会社を取得
                $CI =& get_instance();
                $CI->load->model('Shokai', 'sk', TRUE);
                $get_skc_data = $CI->sk->get_skc_list($val['skf_sk_seq']);

                // ここからPDF作成
                $pdf->AddPage();                                                        // 空のページを追加

                $pdf->SetFont($font1, '', 10);

                $pdf->Text(136, 6, $_space_wd . "発行No:" . $val['skf_pay_no']);

                $format = 'Y-m-d';
                $date = DateTime::createFromFormat($format, $val["skf_issue_date"]);
                $pdf->Text(136, 11, "発行日:" . $date->format('Y年m月d日'));

                $pdf->SetFont($font1, 'BU', 16);
                $pdf->Text(25, 30, $val["skf_sk_company"] . '　御中');

                $pdf->SetFont($font1, '', 10);
                $pdf->Text(130, 40, "株式会社ラベンダーマーケティング");
                $pdf->SetFont($font1, '', 9);
                $pdf->Text(144, 45, "〒150-0043");
                $pdf->Text(144, 49, "東京都渋谷区道玄坂 1-19-12");
                $pdf->Text(159, 53, "道玄坂今井ビル 4F");
                $pdf->Text(157, 57, "tel. 03-3464-6115");

                $pdf->Ln();

                $pdf->SetFont($font1, 'B', 16);
                $pdf->Text(80, 80, "支　払　通　知　書");

                $pdf->SetFont($font1, '', 11);
                $pdf->Text(25, 100, "拝啓　貴社ますますご繁栄のこととお喜び申し上げます。平素は格別のお引き立てを賜り、");
                $pdf->Text(25, 104, "誠にありがとうございます。");
                $pdf->Text(25, 108, "");
                $pdf->Text(25, 112, "さて、弊社のSEOサービスをご案内いただきましたお客様のご紹介料として下記のとおり、");
                $pdf->Text(25, 116, "支払いいたしますので、お知らせします。");
                $pdf->Text(25, 120, "");
                $pdf->Text(25, 124, "なお、お手数をおかけいたしますが、折り返し請求書をお送りくださいますようお願い申");
                $pdf->Text(25, 128, "し上げます。");
                $pdf->Text(25, 132, "　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　　敬具");

                $pdf->Text(25, 140, "　　　　　　　　　　　　　　　　　　　　記");

                $pdf->Ln();
                $pdf->Ln();

//                 $pdf->SetFont($font1, '', 8);
                $pdf->SetFillColor(211, 211,  211);
                $pdf->SetDrawColor(0, 0, 255);

                    $w1 = 10;            // ダミー
                    $w2 = 50;
                    $w3 = 4;
                    $w4 = 100;
                    $h1 = 10;
                    $h2 = 7;
                    $h3 = 5;

                $pdf->Cell($w1, $h1, '', 0);
                $pdf->Cell($w2, $h1, '項　　　　　目', "LRT", 0, "C", 1);
                $pdf->Cell($w3, $h1, '', "T", 0);
                $_pay_yymm = str_split($val['skf_pay_yymm'], 4);
                $pdf->Cell($w4, $h1, $_pay_yymm[0] . "年" . $_pay_yymm[1] . "月度 ご紹介料", "RT", 1, "L");

                $i = 0;
                foreach ($get_skc_data as $key_skc => $val_skc)
                {
                    if ($i == 0)
                    {
                        $pdf->Cell($w1, $h3, '', 0);
                        $pdf->Cell($w2, $h3, '紹　　介　　先', "LRT", 0, "C", 1);
                        $pdf->Cell($w3, $h3, '', "T", 0);
                        $pdf->Cell($w4, $h3, $val_skc['skc_cm_company'], "RT", 1, "L");
                    } elseif ($i == 1) {
                        $pdf->Cell($w1, $h3, '', 0);
                        $pdf->Cell($w2, $h3, '（請 求 先）', "LR", 0, "C", 1);
                        $pdf->Cell($w3, $h3, '', 0);
                        $pdf->Cell($w4, $h3, $val_skc['skc_cm_company'], "R", 1, "L");
                    } else {
                        $pdf->Cell($w1, $h3, '', 0);
                        $pdf->Cell($w2, $h3, '', "LR", 0, "C", 1);
                        $pdf->Cell($w3, $h3, '', 0);
                        $pdf->Cell($w4, $h3, $val_skc['skc_cm_company'], "R", 1, "L");
                    }
                    $i++;
                }
                if ($i == 1)
                {
                    $pdf->Cell($w1, $h3, '', 0);
                    $pdf->Cell($w2, $h3, '（請求先）', "LR", 0, "C", 1);
                    $pdf->Cell($w3, $h3, '', 0);
                    $pdf->Cell($w4, $h3, '', "R", 1, "L");
                }

                $pdf->Cell($w1, $h1, '', 0);
                $pdf->Cell($w2, $h1, '振　　込　　日', "LRT", 0, "C", 1);
                $pdf->Cell($w3, $h1, '', "T", 0);
                $date = DateTime::createFromFormat($format, $val["skf_pay_date"]);
                $pdf->Cell($w4, $h1, $date->format('Y年m月d日'), "RT", 1, "L");

                $pdf->Cell($w1, $h1, '', 0);
                $pdf->Cell($w2, $h1, '振　込　金　額', "LRT", 0, "C", 1);
                $pdf->Cell($w3, $h1, '', "T", 0);
                if ($val['skf_pay_tax'] == 0)
                {
                    $pdf->Cell($w4, $h1, number_format($val['skf_pay_total']) . " 円 （内税）", "RT", 1, "L");
                } else {
                    $pdf->Cell($w4, $h1, number_format($val['skf_pay_total']+$val['skf_pay_tax']) . " 円 （税込）", "RT", 1, "L");
                }

                $pdf->Cell($w1, $h1, '', 0);
                $pdf->Cell($w2, $h1, '振 込 人　名 義', "LRT", 0, "C", 1);
                $pdf->Cell($w3, $h1, '', "T", 0);
                $pdf->Cell($w4, $h1, $val['skf_account_nm'], "RT", 1, "L");

                $pdf->Cell($w1, $h1, '', 0);
                $pdf->Cell($w2, $h1, '備　　　　　考', 1, 0, "C", 1);
                $pdf->Cell($w3, $h1, '', "TB", 0);
                $pdf->SetFont($font1, '', 9);
                $pdf->Cell($w4, $h1, $val['skf_remark'], "RTB", 1, "L");

                $x = $pdf->GetX();
                $y = $pdf->GetY() + 5;
                $pdf->SetFont($font1, '', 11);
                $pdf->Text(172, $y, "以上");

                $pdf->Close();
                ob_end_clean();

                $pdf->Output('shokai' . date("Ymdhis") . '.pdf', 'D');

                return;

            }
        }


        // 【複数枚 支払通知書の作成】
        /*
         *
         * まずは様子を見て。
         * とりあえずは、表部分の文字が大きいので調整か？ 別紙参照？
         *
         */


    }


        //**********************************************************
        // Cell メソッドの引数
        //**********************************************************
        // $w 矩形領域の幅
        // $h 矩形領域の高さ
        // $txt 印字するテキスト
        // $border 境界線で囲むか否かを指定する。以下のどちらかを指定:
        // 0: 境界線なし(既定)
        // 1: 枠で囲む
        // または、以下の組み合わせで境界線を指定する:
        // L: 左
        // T: 上
        // R: 右
        // B: 下
        //
        // $ln 出力後のカーソルの移動方法を指定する:
        // 0: 右へ移動(既定)、但しアラビア語などRTLの場合は左へ移動
        // 1: 次の行へ移動
        // 2: 下へ移動
        //
        // $align テキストの整列を以下のいずれかで指定する
        // L or 空文字: 左揃え(既定)
        // C: 中央揃え
        // R: 右揃え
        // J: 両端揃え

        // $fill 矩形領域の塗つぶし指定 [0:透明(既定) 1:塗つぶす]
        // $link 登録するリンク先のURL、もしくはAddLink()で作成したドキュメント内でのリンク
        // $stretch テキストの伸縮(ストレッチ)モード:
        // 0 = なし
        // 1 = 必要に応じて水平伸縮
        // 2 = 水平伸縮
        // 3 = 必要に応じてスペース埋め
        // 4 = スペース埋め
        //
        // $ignore_min_height 「true」とすると矩形領域の高さの最小値調整をしない


}

// END pdf Class

/* End of file pdf.php */
/* Location: ./application/libraries/pdf.php */
