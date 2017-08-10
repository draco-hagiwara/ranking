<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

# include TCPDF
require_once(APPPATH . '../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once(APPPATH . '../../vendor/setasign/fpdi/fpdi.php');

/**
 * TCPDF - CodeIgniter Integration
 */
class Lib_pdf_report extends TCPDF {

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
     * 請求書PDF：１枚＆複数枚（明細別） 作成
     *
     */
    public function create_kw_report($kw_data, $me_data, $x_data, $y_data, $pdflist_path, $base_path)
    {

    	$CI =& get_instance();


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


    	// ここからPDF作成
    	$pdf->AddPage();                                                        // 空のページを追加

    	$pdf->SetFont($font1, '', 9);

    	$pdf->Text(146, 6, "伝票No:");




    	$kw_seq = $kw_data[0]['kw_seq'];

    	$CI->load->model('Keyword', 'kw', TRUE);
    	$CI->load->model('Ranking', 'rk', TRUE);
    	$CI->load->model('Memo',    'me', TRUE);

    	// キーワード設定情報を取得
    	$get_kw_data =$CI->kw->get_kw_seq($kw_seq);

    	// メモ情報を取得
    	$get_me_data =$CI->me->get_me_seq($kw_seq);

    	// 順位データ情報を取得 (31日分) ＆ グラフ表示
    	$CI->load->library('lib_ranking_data');
    	list($x_data, $y_data) = $CI->lib_ranking_data->get_ranking_graph($kw_seq, 31);

    	$CI->smarty->assign('info',     $get_kw_data[0]);
    	$CI->smarty->assign('info_me',  $get_me_data);
    	$CI->smarty->assign('mess',     FALSE);

    	// CodeIgniterのviewの第三引数をtrueにしてhtml文字列として扱う
    	$html = $CI->view('/keywordlist/detail.tpl');

    	$pdf->WriteHTML($html, true, 0, false, true, 'L');


//     	// print a some of text
//     	$text = '<canvas id="RankingChart01" height="150" width="300" ></canvas>';
//     	$pdf->writeHTML($text, true, 0, true, 0);

//     	// write some JavaScript code
//     	$js =
// <<<EOD
// var ctx01 = document.getElementById("RankingChart01");
// var RankingChart01 = new Chart(ctx01, {
//     type: 'line',
//     data: {
// 	    labels: [{$x_data{$kw_seq}}],
// 	    datasets: [
// 	        {
// 	            label: "順位の推移",
// 	            fill: false,										// グラフの背景を描画するかどうか
// 	            lineTension: 0.1,									// ラインのベジェ曲線の張り
// 	            backgroundColor: "rgba(54, 162, 235, 0.2)",			// ラインの下の塗りつぶしの色
// 	            pointBackgroundColor: "rgba(54, 162, 235, 1)", 		// ポインタの色
// 	            borderColor: "rgba(54, 162, 235, 1)",				// 線の色
// 	            pointHoverRadius: 5,								// グラフの点にカーソルを合わせた時
// 	            data: [{$y_data{$kw_seq}}],
// 	            spanGaps: true,										// 行がないか、またはヌルのデータと点の間に描画されます
// 	        },
// 	    ]
//    },
//    options: {
//         scales: {
//             xAxes: [{												// X軸のオプション
//                 display: true,
//                 stacked: false,										// 積み上げするかどうか
//                 gridLines: {
//                 display: true										// 目盛を描画するか
//                 },
//             }],
//             yAxes: [{												// Y軸のオプション
//                 display: true,
//                 stacked: false,
//                 scaleLabel: {
//                    display: true,									// ラベルを表示するか
//                    labelString: '順位',
//                    fontFamily: 'monospace',
//                    fontSize: 14
//                 },
//                 ticks: {
//                    reverse: true,									// 目盛を反転するか (降順/昇順)
//                    //callback: function(value){
//                    //   return value+'年月';
//                    //},
//                    //max: 3000000,
//                    min: 1,
//                    stepSize: 10,
//                 }
//              }]
//         }
//    }
// });
// EOD;

//     	// force print dialog
//     	//$js .= 'print(true);';

//     	// set javascript
//     	$pdf->IncludeJS($js);












    	$pdf->Close();
    	ob_end_clean();

    	$pdf->Output('report_kw_' . date("Ymdhis") . '.pdf', 'D');

    }












    /**
     * 請求書PDF：１枚＆複数枚（明細別） 作成
     *
     */
    public function create_pdf($iv_data, $ivd_data, $pdflist_path, $base_path)
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

        // 【１枚請求書の作成】
        foreach ($iv_data as $key => $value) {
            foreach ($value as $key_iv => $val) {

                // 各明細行データを先に分析＆データ化する
                $CI =& get_instance();
                $CI->load->library('lib_pdf_invoice');
                list($get_iv, $get_ivd, $line_cnt) = $CI->lib_pdf_invoice->create_detail_lines($val, $ivd_data[$key]);

                // 明細がこれ以上の場合、別明細用のフォーマットで作成
                if ($line_cnt <= 24)
                {

                    // ここからPDF作成
                    $pdf->AddPage();                                                        // 空のページを追加

                    $pdf->SetFont($font1, '', 9);

                    $_slip_cnt = strlen($val['iv_slip_no']);
                    $_space_wd = "";
                    for ($i=$_slip_cnt; $i<=22; $i++)
                    {
                        $_space_wd .= " ";
                    }
                    $pdf->Text(146, 6, $_space_wd . "伝票No:" . $val['iv_slip_no']);

                    $format = 'Y-m-d';
                    $date = DateTime::createFromFormat($format, $val["iv_issue_date"]);
                    $pdf->Text(163, 11, "発行日:" . $date->format('Y年m月d日'));

                    $pdf->Text(25, 6, "〒" . $val["iv_zip01"] . '-' . $val["iv_zip02"]);
                    $pdf->Text(25, 10, $val["iv_pref"] . $val["iv_addr01"] . $val["iv_addr02"]);
                    $pdf->Text(25, 14, "　　　　　　" . $val["iv_buil"]);
                    if ($val["iv_person01"] == "")
                    {
                        $pdf->SetFont($font1, '', 10);
                        $pdf->Text(25, 20, $val["iv_company"] . " 御中");
                    } else {
                        $pdf->SetFont($font1, '', 10);
                        $pdf->Text(25, 20, $val["iv_company"]);
                        $pdf->SetFont($font1, '', 9);
                        $pdf->Text(25, 26, $val["iv_department"]);
                        $pdf->SetFont($font1, '', 10);
                        $pdf->Text(25, 29, $val["iv_person01"] . ' ' . $val["iv_person02"] . ' 様');
                    }

                    $pdf->line(10, 40, 200, 40);

                    $pdf->SetFont($font1, 'B', 16);
                    $pdf->Text(10, 52, "御　請　求　書");

                    $pdf->SetFont($font1, 'BU', 12);
                    $pdf->Text(15, 70, $val["iv_company_cm"] . '　御中');
                    //$pdf->Text(15, 73, $val["iv_company"] . '　御中');

                    $pdf->SetFont($font1, '', 9);
                    $pdf->Text(148, 75, "株式会社ラベンダーマーケティング");
                    $pdf->SetFont($font1, '', 8);
                    $pdf->Text(161, 80, "〒150-0043");
                    $pdf->Text(161, 84, "東京都渋谷区道玄坂 1-19-12");
                    $pdf->Text(175, 88, "道玄坂今井ビル 4F");
                    $pdf->Text(173, 92, "tel. 03-3464-6115");

                    $pdf->Rect(165.0, 96.0, 17.0, 18.0, 'D');
                    $pdf->Rect(182.0, 96.0, 17.0, 18.0, 'D');

                    $pdf->SetFont($font1, '', 9);
                    $pdf->Text(15, 100, "下記の通りご請求いたします。");

                    $pdf->SetFont($font1, '', 12);
                    $pdf->SetFillColor(211, 211, 211);
                    $pdf->Rect(15.0, 105.0, 50.0, 9.0, 'DF');
                    $pdf->Text(20, 107, "ご請求金額（税込み）");
                    $pdf->Rect(65.0, 105.0, 50.0, 9.0, 'D');
                    $pdf->Text(83, 107, number_format($val["iv_total"]) . " 円");

                    $pdf->Ln();
                    $pdf->Ln();

                    $pdf->SetFont($font1, '', 8);
                    $pdf->SetDrawColor(0, 0, 255);

                    $w1 = 130;
                    $w2 = 10;
                    $w3 = 20;
                    $w4 = 25;
                    $h1 = 10;
                    $h2 = 5;
                    $h3 = 3;

                    // 表タイトル
                    $pdf->Cell($w1, $h2, '請　求　項　目', 1, 0, "C", 1);
                    $pdf->Cell($w2, $h2, '数量', 1, 0, "C", 1);
                    $pdf->Cell($w3, $h2, '単　価', 1, 0, "C", 1);
                    $pdf->Cell($w4, $h2, '金　額', 1, 1, "C", 1);

                    $pdf->SetFont($font1, '', 7);

                    // 明細 Ⅰ
                    foreach ($get_ivd as $key00 => $val00) {
                        foreach ($val00 as $key01 => $val01) {
                            foreach ($val01 as $key02 => $val02) {

                                if ((isset($val02[9])) && ($val02[9] = "LF"))
                                {
                                    $pdf->Cell($w1, $h3, ' ', "LR", 0);
                                    $pdf->Cell($w2, $h3, ' ', "LR", 0);
                                    $pdf->Cell($w3, $h3, ' ', "LR", 0);
                                    $pdf->Cell($w4, $h3, ' ', "LR", 1);
                                }

                                $pdf->Cell($w1, $h3, $val02[0], "LR", 0);

                                if (isset($val02[3]) && ($val02[3] != 0))
                                {
                                    $pdf->Cell($w2, $h3, $val02[1], "LR", 0, "C");
                                    $pdf->Cell($w3, $h3, number_format($val02[2]), "LR", 0, "R");
                                    $pdf->Cell($w4, $h3, number_format($val02[3]), "LR", 1, "R");
                                } else {
                                    /*
                                     * 単価が“0円でなく”、かつ金額が“0円”の明細行は数量＆単価＆金額を表示！
                                     */
                                    if (isset($val02[3]) && ($val02[3] == 0) && ($val02[2] != 0))
                                    {
                                        $pdf->Cell($w2, $h3, $val02[1], "LR", 0, "C");
                                        $pdf->Cell($w3, $h3, number_format($val02[2]), "LR", 0, "R");
                                        $pdf->Cell($w4, $h3, number_format($val02[3]), "LR", 1, "R");
                                    } else {
                                        $pdf->Cell($w2, $h3, "", "LR", 0, "C");
                                        $pdf->Cell($w3, $h3, "", "LR", 0, "R");
                                        $pdf->Cell($w4, $h3, "", "LR", 1, "R");
                                    }
//                                     $pdf->Cell($w2, $h3, "", "LR", 0, "C");
//                                     $pdf->Cell($w3, $h3, "", "LR", 0, "R");
//                                     $pdf->Cell($w4, $h3, "", "LR", 1, "R");
                                }
                            }
                        }

                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                        $pdf->Cell($w4, $h3, ' ', "LR", 1);
                    }

                    for ($i=$line_cnt; $i<=24; $i++)
                    {
                        // 明細：空白行で埋める
                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                        $pdf->Cell($w4, $h3, ' ', "LR", 1);
                    }

                    // 明細：最後の空白行
                    $pdf->Cell($w1, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w2, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w3, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w4, $h3, ' ', "LRB", 1);

                    $pdf->SetFont($font1, '', 8);

                    // 合計欄
                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '小　　計', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($val["iv_subtotal"]), 1, 1, "R");
                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '消費税等', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($val["iv_tax"]), 1, 1, "R");
                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '合　　計', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($val["iv_total"]), 1, 1, "R");

                    $pdf->SetFont($font1, '', 9);
                    $pdf->SetDrawColor(0, 0, 0);

                    $pdf->Ln();
                    $pdf->MultiCell(185, 20, "【備　考】\n" . $val["iv_remark"], 1, 'L', 0);
                    $pdf->SetFont($font1, '', 8);
                    $x = $pdf->GetX();
                    $y = $pdf->GetY() + 5;
                    $pdf->Text(15, $y, "※支払期日までに下記口座までお振込みくださいますようお願いいたします。尚、振込手数料は貴社にてご負担願います。");

                    $w1 = $y+4;
                    $w2 = $y+8;
                    $w3 = $y+4;
                    $h1 = 13.0;

                    $pdf->SetFont($font1, '', 8);
                    $pdf->SetFillColor(211, 211, 211);
                    $pdf->Rect(15.0, $w1, 30.0, $h1, 'DF');
                    $pdf->Text(17.0, $w2, "お振込期日");

                    $pdf->SetFont($font1, '', 9);
                    $format = 'Y-m-d';
                    $date = DateTime::createFromFormat($format, $val["iv_pay_date"]);
                    $pdf->Rect(45.0, $w1, 50.0, $h1, 'D');
                    $pdf->Text(55.0, $w2, $date->format('Y年m月d日'));

                    $pdf->SetFont($font1, '', 8);

                    $pdf->Rect(95.0,  $w1,   30.0, $h1, 'DF');
                    $pdf->Text(97.0,  $w2,   "お振込先");
                    $pdf->Rect(125.0, $w1,   75.0, $h1, 'D');
                    $pdf->Text(127,   $w3,   "銀 行 名 ：　三井住友銀行（0009）");
                    $pdf->Text(127,   $w3+3, "支 店 名 ：　渋谷駅前支店（234）");
                    $pdf->Text(127,   $w3+6, "口座番号：　4792809（普通口座）");
                    $pdf->Text(127,   $w3+9, "口座名義：　株式会社ラベンダーマーケティング");

                }
            }
        }


        // 【複数枚 請求書の作成】
        foreach ($iv_data as $key => $value) {
            foreach ($value as $key_iv => $val) {

                // 各明細行データを先に分析＆データ化する
                $CI =& get_instance();
                $CI->load->library('lib_pdf_invoice');
                list($get_iv, $get_ivd, $line_cnt) = $CI->lib_pdf_invoice->create_detail_lines($val, $ivd_data[$key]);

                // 明細がこれ以上の場合、別明細用のフォーマットで作成
                if ($line_cnt >= 25)
                {

                    $pdf->AddPage();                                                        // 空のページを追加

                    $pdf->SetFont($font1, '', 9);

                    $_slip_cnt = strlen($val['iv_slip_no']);
                    $_space_wd = "";
                    for ($i=$_slip_cnt; $i<=22; $i++)
                    {
                        $_space_wd .= " ";
                    }

                    $pdf->Text(146, 6, $_space_wd . "伝票No:" . $val['iv_slip_no']);

                    $format = 'Y-m-d';
                    $date = DateTime::createFromFormat($format, $val["iv_issue_date"]);
                    $pdf->Text(163, 11, "発行日:" . $date->format('Y年m月d日'));

                    $pdf->Text(25, 6, "〒" . $val["iv_zip01"] . '-' . $val["iv_zip02"]);
                    $pdf->Text(25, 10, $val["iv_pref"] . $val["iv_addr01"] . $val["iv_addr02"]);
                    $pdf->Text(25, 14, "　　　　　　" . $val["iv_buil"]);
                    if ($val["iv_person01"] == "")
                    {
                        $pdf->SetFont($font1, '', 10);
                        $pdf->Text(25, 20, $val["iv_company"] . " 御中");
                    } else {
                        $pdf->SetFont($font1, '', 10);
                        $pdf->Text(25, 20, $val["iv_company"]);
                        $pdf->SetFont($font1, '', 9);
                        $pdf->Text(25, 26, $val["iv_department"]);
                        $pdf->SetFont($font1, '', 10);
                        $pdf->Text(25, 29, $val["iv_person01"] . ' ' . $val["iv_person02"] . ' 様');
                    }

                    $pdf->line(10, 40, 200, 40);

                    $pdf->SetFont($font1, 'B', 16);
                    $pdf->Text(10, 52, "御　請　求　書");

                    $pdf->SetFont($font1, 'BU', 12);
                    $pdf->Text(15, 70, $val["iv_company_cm"] . '　御中');

                    $pdf->SetFont($font1, '', 9);
                    $pdf->Text(149, 75, "株式会社ラベンダーマーケティング");
                    $pdf->SetFont($font1, '', 8);
                    $pdf->Text(161, 80, "〒150-0043");
                    $pdf->Text(161, 84, "東京都渋谷区道玄坂 1-19-12");
                    $pdf->Text(175, 88, "道玄坂今井ビル 4F");
                    $pdf->Text(173, 92, "tel. 03-3464-6115");

                    $pdf->Rect(165.0, 96.0, 17.0, 18.0, 'D');
                    $pdf->Rect(182.0, 96.0, 17.0, 18.0, 'D');

                    $pdf->SetFont($font1, '', 9);
                    $pdf->Text(15, 100, "下記の通りご請求いたします。");

                    $pdf->SetFont($font1, '', 12);
                    $pdf->SetFillColor(211, 211,  211);
                    $pdf->Rect(15.0, 105.0, 50.0, 9.0, 'DF');
                    $pdf->Text(20,   107,   "ご請求金額（税込み）");
                    $pdf->Rect(65.0, 105.0, 50.0, 9.0, 'D');
                    $pdf->Text(83,   107,   number_format($val["iv_total"]) . " 円");

                    $pdf->Ln();
                    $pdf->Ln();

                    $pdf->SetFont($font1, '', 8);
                    $pdf->SetDrawColor(0, 0, 255);

                    $w1 = 130;
                    $w2 = 10;
                    $w3 = 20;
                    $w4 = 25;
                    $h1 = 10;
                    $h2 = 5;
                    $h3 = 3;

                    // 表タイトル
                    $pdf->Cell($w1, $h2, '請　求　項　目', 1, 0, "C", 1);
                    $pdf->Cell($w2, $h2, '数量', 1, 0, "C", 1);
                    $pdf->Cell($w3, $h2, '単　価', 1, 0, "C", 1);
                    $pdf->Cell($w4, $h2, '金　額', 1, 1, "C", 1);

                    $pdf->SetFont($font1, '', 7);


                    // 明細 Ⅱ
                    $i = 0;
                    foreach ($get_iv as $key => $value) {

                        if (isset($value['subtitle']) && ($value['subtitle'] != ""))
                        {
                            $pdf->Cell($w1, $h3, $value['subtitle'], "LR", 0);
                            $pdf->Cell($w2, $h3, "", "LR", 0, "C");
                            $pdf->Cell($w3, $h3, "", "LR", 0, "R");
                            $pdf->Cell($w4, $h3, number_format($value['subtotal']), "LR", 1, "R");
                            $i++;
                        }

                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                        $pdf->Cell($w4, $h3, ' ', "LR", 1);
                        $i++;
                    }

                    $pdf->Cell($w1, $h3, ' ', "LR", 0);
                    $pdf->Cell($w2, $h3, ' ', "LR", 0);
                    $pdf->Cell($w3, $h3, ' ', "LR", 0);
                    $pdf->Cell($w4, $h3, ' ', "LR", 1);
                    $i++;

                    $pdf->Cell($w1, $h3, '　明細は別紙参照。', "LR", 0, "L");
                    $pdf->Cell($w2, $h3, ' ', "LR", 0);
                    $pdf->Cell($w3, $h3, ' ', "LR", 0);
                    $pdf->Cell($w4, $h3, ' ', "LR", 1);
                    $i++;

                    for ($i; $i<=25; $i++)
                    {
                        // 明細：空白行で埋める
                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                        $pdf->Cell($w4, $h3, ' ', "LR", 1);
                    }

                    // 明細：最後の空白行
                    $pdf->Cell($w1, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w2, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w3, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w4, $h3, ' ', "LRB", 1);

                    $pdf->SetFont($font1, '', 8);

                    // 合計欄
                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '小　　計', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($val["iv_subtotal"]), 1, 1, "R");
                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '消費税等', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($val["iv_tax"]), 1, 1, "R");
                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '合　　計', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($val["iv_total"]), 1, 1, "R");

                    $pdf->SetFont($font1, '', 9);
                    $pdf->SetDrawColor(0, 0, 0);

                    // 「備考」欄はmax4行まで考慮。
                    $pdf->Ln();
                    $pdf->MultiCell(185, 20, "【備　考】\n" . $val["iv_remark"], 1, 'L', 0);

                    $pdf->SetFont($font1, '', 8);
                    $x = $pdf->GetX();
                    $y = $pdf->GetY() + 5;
                    $pdf->Text(15, $y, "※支払期日までに下記口座までお振込みくださいますようお願いいたします。尚、振込手数料は貴社にてご負担願います。");

                    $w1 = $y+4;
                    $w2 = $y+8;
                    $w3 = $y+4;
                    $h1 = 13.0;

                    $pdf->SetFont($font1, '', 8);
                    $pdf->SetFillColor(211, 211, 211);
                    $pdf->Rect(15.0, $w1, 30.0, $h1, 'DF');
                    $pdf->Text(17.0, $w2, "お振込期日");

                    $pdf->SetFont($font1, '', 9);
                    $format = 'Y-m-d';
                    $date = DateTime::createFromFormat($format, $val["iv_pay_date"]);
                    $pdf->Rect(45.0, $w1, 50.0, $h1, 'D');
                    $pdf->Text(55.0, $w2, $date->format('Y年m月d日'));

                    $pdf->SetFont($font1, '', 8);

                    $pdf->Rect(95.0,  $w1,   30.0, $h1, 'DF');
                    $pdf->Text(97.0,  $w2,   "お振込先");
                    $pdf->Rect(125.0, $w1,   75.0, $h1, 'D');
                    $pdf->Text(127,   $w3,   "銀 行 名 ：　三井住友銀行（0009）");
                    $pdf->Text(127,   $w3+3, "支 店 名 ：　渋谷駅前支店（234）");
                    $pdf->Text(127,   $w3+6, "口座番号：　4792809（普通口座）");
                    $pdf->Text(127,   $w3+9, "口座名義：　株式会社ラベンダーマーケティング");


                    // 別紙
                    $pdf->AddPage();

                    $pdf->SetFont($font1, '', 8);
                    $pdf->Text(153, 10, $_space_wd . "伝票No:" . $val['iv_slip_no']);

                    $pdf->SetFont($font1, 'BU', 12);
                    $_subtitle_cnt = 1;
                    $pdf->Text(15, 17, '請 求 内 訳 書');                                    // 1ページ目はカウント非表示!？
                    $pdf->Ln();

                    $pdf->SetFont($font1, '', 8);
                    $pdf->SetDrawColor(0, 0, 255);

                    $w1 = 130;
                    $w2 = 10;
                    $w3 = 20;
                    $w4 = 25;
                    $h1 = 10;
                    $h2 = 5;
                    $h3 = 3;

                    // 表タイトル
                    $pdf->Cell($w1, $h2, '請　求　項　目', 1, 0, "C", 1);
                    $pdf->Cell($w2, $h2, '数量', 1, 0, "C", 1);
                    $pdf->Cell($w3, $h2, '単　価', 1, 0, "C", 1);
                    $pdf->Cell($w4, $h2, '金　額', 1, 1, "C", 1);

                    $pdf->SetFont($font1, '', 7);

                    // 明細
                    $_subtotal = 0;
                    $_total    = 0;
                    $i = 0;
                    foreach ($get_ivd as $key00 => $val00) {
                        foreach ($val00 as $key01 => $val01) {
                            foreach ($val01 as $key02 => $val02) {

                                if ((isset($val02[9])) && ($val02[9] = "LF"))
                                {
                                    $pdf->Cell($w1, $h3, ' ', "LR", 0);
                                    $pdf->Cell($w2, $h3, ' ', "LR", 0);
                                    $pdf->Cell($w3, $h3, ' ', "LR", 0);
                                    $pdf->Cell($w4, $h3, ' ', "LR", 1);
                                    $i++;
                                }

                                $pdf->Cell($w1, $h3, $val02[0], "LR", 0);

                                if (isset($val02[3]) && ($val02[3] != 0))
                                {
                                    $pdf->Cell($w2, $h3, $val02[1], "LR", 0, "C");
                                    $pdf->Cell($w3, $h3, number_format($val02[2]), "LR", 0, "R");
                                    $pdf->Cell($w4, $h3, number_format($val02[3]), "LR", 1, "R");

                                    $_subtotal = $_subtotal + $val02[3];
                                    $_total    = $_total    + $val02[3];

                                    // 明細が複数枚になる場合
                                    if (($i / 71) > 1)
                                    {

                                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                                        $pdf->Cell($w4, $h3, ' ', "LR", 1);

                                        // 明細：最後の空白行
                                        $pdf->Cell($w1, $h3, ' ', "LRB", 0);
                                        $pdf->Cell($w2, $h3, ' ', "LRB", 0);
                                        $pdf->Cell($w3, $h3, ' ', "LRB", 0);
                                        $pdf->Cell($w4, $h3, ' ', "LRB", 1);

                                        // 小計欄
                                        //$pdf->Cell($w1, $h2, ' ', "", 0);
                                        //$pdf->Cell($w2, $h2, ' ', "", 0);
                                        //$pdf->Cell($w3, $h2, '小　　計', "", 0, "C");
                                        //$pdf->Cell($w4, $h2, number_format($_subtotal), 1, 1, "R");

                                        $_subtotal = 0;

                                        $pdf->AddPage();

                                        $pdf->SetFont($font1, '', 8);
                                        $pdf->Text(153, 10, $_space_wd . "伝票No:" . $val['iv_slip_no']);

                                        $pdf->SetFont($font1, 'BU', 12);
                                        $_subtitle_cnt++;
                                        $pdf->Text(15, 17, '請 求 内 訳 書' . "　（" . $_subtitle_cnt . "）");
                                        $pdf->Ln();

                                        $pdf->SetFont($font1, '', 8);
                                        $pdf->SetDrawColor(0, 0, 255);

                                        $w1 = 130;
                                        $w2 = 10;
                                        $w3 = 20;
                                        $w4 = 25;
                                        $h1 = 10;
                                        $h2 = 5;
                                        $h3 = 3;

                                        // 表タイトル
                                        $pdf->Cell($w1, $h2, '請　求　項　目', 1, 0, "C", 1);
                                        $pdf->Cell($w2, $h2, '数量', 1, 0, "C", 1);
                                        $pdf->Cell($w3, $h2, '単　価', 1, 0, "C", 1);
                                        $pdf->Cell($w4, $h2, '金　額', 1, 1, "C", 1);

                                        $pdf->SetFont($font1, '', 7);

                                        $i = 0;
                                    }

                                } else {

                                    /*
                                     * 単価が“0円でなく”、かつ金額が“0円”の明細行は数量＆単価＆金額を表示！
                                     */
                                    if (isset($val02[3]) && ($val02[3] == 0) && ($val02[2] != 0))
                                    {
                                        $pdf->Cell($w2, $h3, $val02[1], "LR", 0, "C");
                                        $pdf->Cell($w3, $h3, number_format($val02[2]), "LR", 0, "R");
                                        $pdf->Cell($w4, $h3, number_format($val02[3]), "LR", 1, "R");
                                    } else {
                                        $pdf->Cell($w2, $h3, "", "LR", 0, "C");
                                        $pdf->Cell($w3, $h3, "", "LR", 0, "R");
                                        $pdf->Cell($w4, $h3, "", "LR", 1, "R");
                                    }
                                }

                                $i++;

                            }
                        }

                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                        $pdf->Cell($w4, $h3, ' ', "LR", 1);

                        $i++;
                    }

                    for ($i=$line_cnt; $i<=75; $i++)                                        // 発行日なし
                    {
                        // 明細：空白行で埋める
                        $pdf->Cell($w1, $h3, ' ', "LR", 0);
                        $pdf->Cell($w2, $h3, ' ', "LR", 0);
                        $pdf->Cell($w3, $h3, ' ', "LR", 0);
                        $pdf->Cell($w4, $h3, ' ', "LR", 1);
                    }

                    // 明細：最後の空白行
                    $pdf->Cell($w1, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w2, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w3, $h3, ' ', "LRB", 0);
                    $pdf->Cell($w4, $h3, ' ', "LRB", 1);

                    // 合計欄
                    //$pdf->Cell($w1, $h2, ' ', "", 0);
                    //$pdf->Cell($w2, $h2, ' ', "", 0);
                    //$pdf->Cell($w3, $h2, '小　　計', "", 0, "C");
                    //$pdf->Cell($w4, $h2, number_format($_subtotal), 1, 1, "R");

                    $pdf->Cell($w1, $h2, ' ', "", 0);
                    $pdf->Cell($w2, $h2, ' ', "", 0);
                    $pdf->Cell($w3, $h2, '合　　計', "", 0, "C");
                    $pdf->Cell($w4, $h2, number_format($_total), 1, 1, "R");

                }
            }
        }

        $pdf->Close();
        ob_end_clean();

        $pdf->Output('invoice_' . date("Ymdhis") . '.pdf', 'D');

    }

    //private function cell_memo()
    //{

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

    //}

    /**
     * 各明細行データを先に分析＆再データ化する
     *
     * @param  array() : 請求書親データ
     * @param  array() : 請求書明細データ
     * @return array()
     */
    public static function create_detail_lines($_iv_data, $_ivd_data)
    {

        $CI =& get_instance();

        /*
         * ここはもう少し上手いやり方が無いか！？
         */

        $line_cnt  = 0;                                                        // 明細行のカウンタ

        $lcate_cnt = 0;                                                        // 明細大項目カウント（代理店）
        $mcate_cnt = 0;                                                        // 明細中項目カウント（課金）
        $scate_cnt = 0;                                                        // 明細小項目カウント

        $_subtotal = 0;                                                        // 明細中項目小計

        $_tmp_accounting = "";
        $_tmp_pjseq      = "";
        $_tmp_cmseq      = "";
        $_tmp_mcate_flg  = FALSE;
        $_tmp_seokey_flg = FALSE;
        $_tmp_res_flg    = FALSE;

        $iv_data  = array();
        $ivd_data = array();

        foreach ($_ivd_data as $key => $value)
        {

            // 代理店チェック
            if ($value['ivd_item_cmseq'] != 0)
            {
                if ($value['ivd_item_cmseq'] != $_tmp_cmseq)
                {

                    $CI->load->model('Customer', 'cm', TRUE);
                    $get_cm_data = $CI->cm->get_cm_seq($value['ivd_item_cmseq']);

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '【' . $get_cm_data[0]['cm_company'] . ' 様向け】';
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][9] = "LF";   // 強制改行
                    $scate_cnt++;

                    $line_cnt = $line_cnt + 2;

                    $_tmp_cmseq = $value['ivd_item_cmseq'];
                }
            }

            // 課金方式で判定
            if ($value['ivd_iv_accounting'] != $_tmp_accounting)
            {

                $iv_data[$mcate_cnt]['subtotal'] = $_subtotal;
                $_subtotal = 0;

                $mcate_cnt++;
                $scate_cnt = 0;

                switch( $value['ivd_iv_accounting'] )
                {
                    case 0:

                        $_subtitle = " SEO月額固定報酬";
                        break;

                    case 1:

                        $_subtitle = " 月額固定報酬";
                        break;

                    case 2:

                        $_subtitle = " SEO成功報酬";
                        break;

                    case 3:

                        $_subtitle = " SEO固定＆成功報酬";
                        break;

                    case 7:

                        $_subtitle = " 月額保守費用";
                        break;

                    case 10:

                        $_subtitle = " アフィリエイト　月額利用料金";
                        break;

                    case 11:

                        $_subtitle = " 広告運用代行サービス　月額利用料金";
                        break;

                    case 12:

                        $_subtitle = " その他　月額利用料金";
                        break;

                    default:

                        $_subtitle = " その他";
                        break;
                }

                $_sales_yymm = str_split($_iv_data["iv_sales_yymm"], 4);

                /*
                 * 「○○月度」の表示を止める。「キーワード」or「備考」欄に必要事項を記入してください。
                 * 受注案件の検索キーワードに入力する際、最後の文字として「#(半角)」を記入する
                 */
                if (mb_substr($value['ivd_item'], -1) == "#")
                {
                	$iv_data[$mcate_cnt]['subtitle'] = $_subtitle;
                	$ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = $_subtitle;
                } else {
                	$iv_data[$mcate_cnt]['subtitle'] = $_subtitle . ' 明細（' . $_sales_yymm[0] . '年' . $_sales_yymm[1] . '月度）';
                	$ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = $_subtitle . ' 明細（' . $_sales_yymm[0] . '年' . $_sales_yymm[1] . '月度）';
                }

                $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][9] = "LF";
                $scate_cnt++;

                if ($_tmp_mcate_flg == FALSE)
                {
                    $line_cnt++;
                    $_tmp_mcate_flg = TRUE;
                } else {
                    $line_cnt = $line_cnt + 2;
                }
            }

            // 対象URLの文字数チェック＆カット。日本語URLをデコードして表示。
            $_tmp_url_word = urldecode($value['ivd_item_url']);
            $_word_cnt = mb_strlen($_tmp_url_word);
            if ($_word_cnt >= 60)
            {
                $_url_word = substr($_tmp_url_word, 0, 60) . " ...";
            } else {
                $_url_word = $_tmp_url_word;
            }

            // 明細を追加
            switch( $value['ivd_iv_accounting'] )
            {
                case 0:

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '・対象KW：' . rtrim($value['ivd_item'], "#");
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][1] = $value['ivd_qty'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][2] = $value['ivd_price'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][3] = $value['ivd_total'];
                    $_subtotal = $_subtotal + $value['ivd_total'];
                    if (($value['ivd_total'] > 0) && ($_tmp_seokey_flg == FALSE))
                    {
                        $_tmp_seokey_flg = TRUE;
                    } elseif ($value['ivd_total'] > 0) {
                        $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][9] = "LF";
                        $line_cnt++;
                    }
                    $scate_cnt++;
                    $line_cnt++;

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '　対象URL：' . $_url_word;
                    $scate_cnt++;
                    $line_cnt++;
                    break;

                case 1:

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '・対象KW：' . rtrim($value['ivd_item'], "#");
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][1] = $value['ivd_qty'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][2] = $value['ivd_price'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][3] = $value['ivd_total'];
                    $_subtotal = $_subtotal + $value['ivd_total'];
                    $scate_cnt++;
                    $line_cnt++;

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '　対象URL：' . $_url_word;
                    $scate_cnt++;
                    $line_cnt++;
                    break;

                case 2:

                    if ($value['ivd_pj_seq'] != $_tmp_pjseq)
                    {
                        $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '・対象KW：' . rtrim($value['ivd_item'], "#");
                        if ($_tmp_res_flg == FALSE)
                        {
                            $_tmp_res_flg = TRUE;
                        } else {
                            $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][9] = "LF";
                            $line_cnt++;
                        }
                        $scate_cnt++;
                        $line_cnt++;

                        $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '　対象URL：' . $_url_word;
                        $scate_cnt++;
                        $line_cnt++;
                    }

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '　・' . $value['ivd_item_comment'];

                    // 売上月数から月末日数を計算
                    $_mouth_date = substr($_iv_data['iv_sales_yymm'], 0, 4) . '-' . substr($_iv_data['iv_sales_yymm'], 4, 2);
                    $date = new DateTime($_mouth_date);
                    $_nisuu = date($date->format('t'));
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][1] = $value['ivd_qty'] . '/' . $_nisuu;

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][2] = $value['ivd_price'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][3] = $value['ivd_total'];
                    $_subtotal = $_subtotal + $value['ivd_total'];
                    $scate_cnt++;
                    $line_cnt++;

                    $_tmp_pjseq = $value['ivd_pj_seq'];

                    break;

                case 3:

                    $_subtitle = "　固定＆成功報酬";
                    break;

                case 7:

                case 8:

                case 9:

                case 10:

                case 11:

                case 12:

                default:

                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][0] = '・' . rtrim($value['ivd_item'], "#");
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][1] = $value['ivd_qty'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][2] = $value['ivd_price'];
                    $ivd_data[$lcate_cnt][$mcate_cnt][$scate_cnt][3] = $value['ivd_total'];
                    $_subtotal = $_subtotal + $value['ivd_total'];
                    $scate_cnt++;
                    $line_cnt++;

                    break;
            }

            $_tmp_accounting = $value['ivd_iv_accounting'];

        }

        $iv_data[$mcate_cnt]['subtotal'] = $_subtotal;

        return array($iv_data, $ivd_data, $line_cnt);

    }

}

// END pdf Class

/* End of file pdf.php */
/* Location: ./application/libraries/pdf.php */
