<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

# include TCPDF
require_once(APPPATH . '../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once(APPPATH . '../../vendor/setasign/fpdi/fpdi.php');

/**
 * TCPDF - CodeIgniter Integration
 */
class Lib_pdf_invoice extends TCPDF {

    /**
     * Initialize
     *
     */
    function __construct($params = array())
    {
		$orientation = 'P';                                                     // 用紙の向き[P=縦方向、L=横方向]
		$unit = 'mm';                                                           // 処理単位[mm=ミリメートル]
		$format = 'A4';                                                         // ページフォーマット[A4]
    	$unicode = true;
        $encoding = 'UTF-8';
        $diskcache = false;

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
	 * 請求書PDF：個別作成
	 *
	 */
	public function pdf_one($iv_data, $ivd_data, $pdflist_path, $base_path)
	{

 		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		$pdf->setPrintFooter(false);                                            // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加
		$pdf->SetAutoPageBreak(true);

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
// 		$pdf->setSourceFile($pdflist_path);
// 		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
// 		$pdf->useTemplate($page);

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

		$pdf->SetFont($font1, '', 9);

		$_slip_cnt = strlen($iv_data['iv_slip_no']);
		$_space_wd = "";
		for ($i=$_slip_cnt; $i<=22; $i++)
		{
			$_space_wd .= " ";
		}
		$pdf->Text(145, 10, $_space_wd . "伝票No:" . $iv_data['iv_slip_no']);

		$format = 'Y-m-d';
		$date = DateTime::createFromFormat($format, $iv_data["iv_issue_date"]);
		$pdf->Text(163, 15, "発行日:" . $date->format('Y年m月d日'));

		$pdf->Text(25, 10, "〒" . $iv_data["iv_zip01"] . '-' . $iv_data["iv_zip02"]);
		$pdf->Text(25, 15, $iv_data["iv_pref"] . $iv_data["iv_addr01"] . $iv_data["iv_addr02"] . " " . $iv_data["iv_buil"]);
		if ($iv_data["iv_person01"] == "")
		{
			$pdf->Text(25, 20, $iv_data["iv_company"] . " 御中");
		} else {
			$pdf->Text(25, 20, $iv_data["iv_company"]);
			$pdf->Text(25, 25, $iv_data["iv_department"]);
			$pdf->Text(25, 30, $iv_data["iv_person01"] . ' ' . $iv_data["iv_person02"] . ' 様');
		}

		$pdf->line(10, 40, 200, 40);

		$pdf->SetFont($font1, 'B', 16);
		$pdf->Text(10, 52, "御　請　求　書");

		$pdf->SetFont($font1, 'BU', 12);
		$pdf->Text(15, 70, $iv_data["iv_company_cm"] . '　御中');

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
		$pdf->SetFillColor(211, 211, 211);
		$pdf->Rect(15.0, 105.0, 50.0, 9.0, 'DF');
		$pdf->Text(20, 107, "ご請求金額（税込み）");
		$pdf->Rect(65.0, 105.0, 50.0, 9.0, 'D');
		$pdf->Text(83, 107, number_format($iv_data["iv_total"]) . " 円");

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

		// 明細
		if ($iv_data["iv_accounting"] == 0)
		{
			$_salse_yymm = str_split($iv_data["iv_salse_yymm"], 4);
			$pdf->Cell($w1, $h2, ' SEO月額固定報酬 明細（' . $_salse_yymm[0] . '年' . $_salse_yymm[1] . '月度）', "LR", 0);

			$pdf->Cell($w2, $h2, '', "LR", 0, "C");
			$pdf->Cell($w3, $h2, '', "LR", 0, "R");
			$pdf->Cell($w4, $h2, '', "LR", 1, "R");
		} elseif ($iv_data["iv_accounting"] == 1) {
			$_salse_yymm = str_split($iv_data["iv_salse_yymm"], 4);
			$pdf->Cell($w1, $h2, ' 月額固定報酬 明細（' . $_salse_yymm[0] . '年' . $_salse_yymm[1] . '月度）', "LR", 0);

			$pdf->Cell($w2, $h2, '', "LR", 0, "C");
			$pdf->Cell($w3, $h2, '', "LR", 0, "R");
			$pdf->Cell($w4, $h2, '', "LR", 1, "R");
		} elseif ($iv_data["iv_accounting"] == 7) {
			$_salse_yymm = str_split($iv_data["iv_salse_yymm"], 4);
			$pdf->Cell($w1, $h2, ' 月額保守費用 明細（' . $_salse_yymm[0] . '年' . $_salse_yymm[1] . '月度）', "LR", 0);

			$pdf->Cell($w2, $h2, '', "LR", 0, "C");
			$pdf->Cell($w3, $h2, '', "LR", 0, "R");
			$pdf->Cell($w4, $h2, '', "LR", 1, "R");
		} else {
			$pdf->Cell($w1, $h3, ' ', "LR", 0);
			$pdf->Cell($w2, $h3, ' ', "LR", 0);
			$pdf->Cell($w3, $h3, ' ', "LR", 0);
			$pdf->Cell($w4, $h3, ' ', "LR", 1);
		}




// 		print_r($ivd_data);
// 		print("<br><br>");
// 		print(count($ivd_data));
// 		print("<br><br>");
// 		exit;




		if (count($ivd_data) <= 17)
		{


			$_line_cnt = 0;
			foreach ($ivd_data as $key => $val_ivd)
			{

				// キーワード：文字数チェック
				$_key_word = $val_ivd["ivd_item"];
				// 					$_key_word = mb_convert_kana($val_ivd["ivd_item"], 'ASKV', "UTF-8");
				$_word_cnt = mb_strlen($_key_word);
				$_wordurl_cnt = strlen($val_ivd["ivd_item_url"]);

				// キーワードURL：文字数チェック
				$_word_cnt_max = 90;
				$_wordurl_cnt_max = 60;
				$_total_cnt_max = 150;

				if ($_word_cnt_max <= $_word_cnt)
				{
					$_key_word = mb_substr($_key_word, 0, 90) . " ...";
					$_url_word = substr($val_ivd["ivd_item_url"], 0, 30) . " ...";

				} elseif ($_wordurl_cnt_max <= $_wordurl_cnt) {

					$_amari_cnt = $_total_cnt_max - $_word_cnt;

					$_url_word = substr($val_ivd["ivd_item_url"], 0, $_amari_cnt/3) . " ...";

				} else {
					$_url_word = $val_ivd["ivd_item_url"];
				}

				// キーワード行明細の書き込み
				if (($val_ivd["ivd_iv_accounting"] == 9) || ($val_ivd["ivd_iv_accounting"] == 8))
				{
					$pdf->Cell($w1, $h3, '　' . $_key_word, "LR", 0);
				} elseif ($val_ivd["ivd_iv_accounting"] == 1) {
					$pdf->Cell($w1, $h3, '　' . $_key_word, "LR", 0);
				} elseif ($val_ivd["ivd_iv_accounting"] == 7) {
					$pdf->Cell($w1, $h3, '　対象URL：' . $_url_word, "LR", 0);
				} else {
					$pdf->Cell($w1, $h3, '　対象KW：' . $_key_word . '：' . $_url_word, "LR", 0);
				}

				if ($val_ivd["ivd_total"] != 0)
				{
					$pdf->Cell($w2, $h3, number_format($val_ivd["ivd_qty"]), "LR", 0, "C");
					$pdf->Cell($w3, $h3, number_format($val_ivd["ivd_price"]), "LR", 0, "R");
					$pdf->Cell($w4, $h3, number_format($val_ivd["ivd_total"]), "LR", 1, "R");
				} else {
					$pdf->Cell($w2, $h3, "", "LR", 0, "C");
					$pdf->Cell($w3, $h3, "", "LR", 0, "R");
					$pdf->Cell($w4, $h3, "", "LR", 1, "R");
				}

				$pdf->Cell($w1, $h3, ' ', "LR", 0);
				$pdf->Cell($w2, $h3, ' ', "LR", 0);
				$pdf->Cell($w3, $h3, ' ', "LR", 0);
				$pdf->Cell($w4, $h3, ' ', "LR", 1);

				$_line_cnt++;

			}

			if ($_line_cnt <= 14)
			{
				for ($_cnt=$_line_cnt; $_cnt<=12; $_cnt++)
				{
					// 明細：空白行で埋める
					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);

					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);
				}
			}



		} else {





			/*
			 * 2016-12-28
			 *
			 * 【今回限定処理（個別出力のみで対応）】
			 *
			 *   請求書を全て1枚の中に収めるため、共通なURLをまとめてKW+URLを明細に記載。
			 *   改ページが発生する場合「備考」欄をカット。
			 *
			 *   将来的には明細を別紙に分けるか？
			 *   成功報酬でレポートが添付されるので請求書は金額のみとするか？
			 *
			 *   要検討！
			 *
			 */
			$_tmp_url  = "";
			$_line_cnt = 0;
			$_url_word_flg = FALSE;
			foreach ($ivd_data as $key => $val)
			{

				// キーワード：文字数チェック
				$_key_word = $val["ivd_item"];
	// 			$_key_word = mb_convert_kana($val["ivd_item"], 'ASKV', "UTF-8");

				$_word_cnt = mb_strlen($_key_word);
				$_wordurl_cnt = strlen($val["ivd_item_url"]);

				// キーワードURL：文字数チェック
				$_word_cnt_max = 90;
				$_wordurl_cnt_max = 60;
				$_total_cnt_max = 150;

				if ($_word_cnt_max <= $_word_cnt)
				{
					$_key_word = mb_substr($_key_word, 0, 90) . " ...";
					$_url_word = substr($val["ivd_item_url"], 0, 30) . " ...";

				} elseif ($_wordurl_cnt_max <= $_wordurl_cnt) {

					$_amari_cnt = $_total_cnt_max - $_word_cnt;
					$_url_word = substr($val["ivd_item_url"], 0, $_amari_cnt/3) . " ...";

				} else {
					$_url_word = $val["ivd_item_url"];
				}


				// キーワード行明細の書き込み : URLが変わったところで明細に記載
				if ($_url_word_flg == FALSE)
				{
					$_tmp_url = $_url_word;
				}
				if ($_tmp_url != $_url_word)
				{

					if ($val["ivd_iv_accounting"] == 9)
					{
						$pdf->Cell($w1, $h3, '　' . $_tmp_key_word, "LR", 0);
					} else {
						$pdf->Cell($w1, $h3, '　対象KW：' . $_tmp_key_word . '：' . $_tmp_url, "LR", 0);
					}

					if ($_tmp_total != 0)
					{
						$pdf->Cell($w2, $h3, number_format($_tmp_qty),   "LR", 0, "C");
						$pdf->Cell($w3, $h3, number_format($_tmp_price), "LR", 0, "R");
						$pdf->Cell($w4, $h3, number_format($_tmp_total), "LR", 1, "R");
					} else {
						$pdf->Cell($w2, $h3, "", "LR", 0, "C");
						$pdf->Cell($w3, $h3, "", "LR", 0, "R");
						$pdf->Cell($w4, $h3, "", "LR", 1, "R");
					}

					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);

					$_tmp_key_word = '[' . $_key_word . '] ';
					$_tmp_total    = $val["ivd_total"];

					$_line_cnt++;

				} else {

					if ($val["ivd_total"] != 0)
					{
						$_tmp_qty   = $val["ivd_qty"];
						$_tmp_price = $val["ivd_price"];
						$_tmp_total = $val["ivd_total"];
					}

					$_tmp_key_word .= '[' . $_key_word . '] ';
					$_url_word_flg  = TRUE;
				}
				$_tmp_url = $_url_word;
			}


			if ($_line_cnt <= 16)
			{

				// 最終明細を記載
				if ($val["ivd_iv_accounting"] == 9)
				{
					$pdf->Cell($w1, $h3, '　' . $_tmp_key_word, "LR", 0);
				} else {
					$pdf->Cell($w1, $h3, '　対象KW：' . $_tmp_key_word . '：' . $_tmp_url, "LR", 0);
				}

				if ($_tmp_total != 0)
				{
					$pdf->Cell($w2, $h3, number_format($_tmp_qty),   "LR", 0, "C");
					$pdf->Cell($w3, $h3, number_format($_tmp_price), "LR", 0, "R");
					$pdf->Cell($w4, $h3, number_format($_tmp_total), "LR", 1, "R");
				} else {
					$pdf->Cell($w2, $h3, "", "LR", 0, "C");
					$pdf->Cell($w3, $h3, "", "LR", 0, "R");
					$pdf->Cell($w4, $h3, "", "LR", 1, "R");
				}

				$pdf->Cell($w1, $h3, ' ', "LR", 0);
				$pdf->Cell($w2, $h3, ' ', "LR", 0);
				$pdf->Cell($w3, $h3, ' ', "LR", 0);
				$pdf->Cell($w4, $h3, ' ', "LR", 1);

				for ($_cnt=$_line_cnt; $_cnt<=11; $_cnt++)
				{
					// 明細：空白行で埋める
					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);

					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);
				}
			}
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
		$pdf->Cell($w4, $h2, number_format($iv_data["iv_subtotal"]), 1, 1, "R");
		$pdf->Cell($w1, $h2, ' ', "", 0);
		$pdf->Cell($w2, $h2, ' ', "", 0);
		$pdf->Cell($w3, $h2, '消費税等', "", 0, "C");
		$pdf->Cell($w4, $h2, number_format($iv_data["iv_tax"]), 1, 1, "R");
		$pdf->Cell($w1, $h2, ' ', "", 0);
		$pdf->Cell($w2, $h2, ' ', "", 0);
		$pdf->Cell($w3, $h2, '合　　計', "", 0, "C");
		$pdf->Cell($w4, $h2, number_format($iv_data["iv_total"]), 1, 1, "R");

		// 空のページを追加
// 		if (($_line_cnt >= 13) && ($_line_cnt <= 20))
// 		{
// 			$pdf->AddPage();
// 		}

// 		$pdf->Ln();

		$pdf->SetFont($font1, '', 8);
		$pdf->SetDrawColor(0, 0, 0);


		// 改ページ発生時は備考欄を削除：一時的処置
		if (($_line_cnt <= 13))
		{
			$pdf->Ln();

			// 「備考」欄はmax4行まで考慮。
			$pdf->MultiCell(185, 20, "【備　考　】\n" . $iv_data["iv_remark"], 1, 'L', 0);
		}


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
		$date = DateTime::createFromFormat($format, $iv_data["iv_pay_date"]);
		$pdf->Rect(45.0, $w1, 50.0, $h1, 'D');
		$pdf->Text(55.0, $w2, $date->format('Y年m月d日'));

		$pdf->SetFont($font1, '', 8);

		$pdf->Rect(95.0, $w1, 30.0, $h1, 'DF');
		$pdf->Text(97.0, $w2, "お振込先");
		$pdf->Rect(125.0, $w1, 75.0, $h1, 'D');
		$pdf->Text(127, $w3,    "銀 行 名 ：　三井住友銀行（0009）");
		$pdf->Text(127, $w3+3,  "支 店 名 ：　渋谷駅前支店（234）");
		$pdf->Text(127, $w3+6,  "口座番号：　4792809（普通口座）");
		$pdf->Text(127, $w3+9, "口座名義：　株式会社ラベンダーマーケティング");

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Close();
		ob_end_clean();

		$pdf->Output($iv_data['iv_slip_no'] . '.pdf', 'D');
// 		$pdf->Output('example_20161121.pdf', 'D');
// 		$pdf->Output($iv_data['iv_slip_no'] . '.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	/**
	 * 請求書PDF：一括作成
	 *
	 */
	public function pdf_batch($iv_data, $ivd_data, $pdflist_path, $base_path, $page_add = FALSE)
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
		// 		$pdf->setSourceFile($pdflist_path);
		// 		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		// 		$pdf->useTemplate($page);

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

		foreach ($iv_data as $key => $value) {
			foreach ($value as $key_iv => $val) {

				$pdf->AddPage();                                                        // 空のページを追加

				$pdf->SetFont($font1, '', 9);

				$_slip_cnt = strlen($val['iv_slip_no']);
				$_space_wd = "";
				for ($i=$_slip_cnt; $i<=22; $i++)
				{
					$_space_wd .= " ";
				}
				$pdf->Text(145, 10, $_space_wd . "伝票No:" . $val['iv_slip_no']);

				$format = 'Y-m-d';
				$date = DateTime::createFromFormat($format, $val["iv_issue_date"]);
				$pdf->Text(163, 15, "発行日:" . $date->format('Y年m月d日'));

				$pdf->Text(25, 10, "〒" . $val["iv_zip01"] . '-' . $val["iv_zip02"]);
				$pdf->Text(25, 15, $val["iv_pref"] . $val["iv_addr01"] . $val["iv_addr02"] . " " . $val["iv_buil"]);
				if ($val["iv_person01"] == "")
				{
					$pdf->Text(25, 20, $val["iv_company"] . " 御中");
				} else {
					$pdf->Text(25, 20, $val["iv_company"]);
					$pdf->Text(25, 25, $val["iv_department"]);
					$pdf->Text(25, 30, $val["iv_person01"] . ' ' . $val["iv_person02"] . ' 様');
				}

				$pdf->line(10, 40, 200, 40);

				$pdf->SetFont($font1, 'B', 16);
				$pdf->Text(10, 52, "御　請　求　書");

				$pdf->SetFont($font1, 'BU', 12);
				$pdf->Text(15, 70, $val["iv_company_cm"] . '　御中');
// 				$pdf->Text(15, 73, $val["iv_company"] . '　御中');



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

// 				$pdf->SetFont($font1, '', 8);
				$pdf->SetFont($font1, '', 7);

				// 明細
				if ($val["iv_accounting"] == 0)
				{
					$_salse_yymm = str_split($val["iv_salse_yymm"], 4);
					$pdf->Cell($w1, $h2, ' SEO月額固定報酬 明細（' . $_salse_yymm[0] . '年' . $_salse_yymm[1] . '月度）', "LR", 0);

					$pdf->Cell($w2, $h2, '', "LR", 0, "C");
					$pdf->Cell($w3, $h2, '', "LR", 0, "R");
					$pdf->Cell($w4, $h2, '', "LR", 1, "R");
				} elseif ($val["iv_accounting"] == 1) {
					$_salse_yymm = str_split($val["iv_salse_yymm"], 4);
					$pdf->Cell($w1, $h2, ' 月額固定報酬 明細（' . $_salse_yymm[0] . '年' . $_salse_yymm[1] . '月度）', "LR", 0);

					$pdf->Cell($w2, $h2, '', "LR", 0, "C");
					$pdf->Cell($w3, $h2, '', "LR", 0, "R");
					$pdf->Cell($w4, $h2, '', "LR", 1, "R");
				} else {
					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);
				}

				$_line_cnt = 0;
				foreach ($ivd_data[$key] as $key_ivd => $val_ivd)
				{

					// キーワード：文字数チェック
					$_key_word = $val_ivd["ivd_item"];
// 					$_key_word = mb_convert_kana($val_ivd["ivd_item"], 'ASKV', "UTF-8");
					$_word_cnt = mb_strlen($_key_word);
					$_wordurl_cnt = strlen($val_ivd["ivd_item_url"]);

					// キーワードURL：文字数チェック
					$_word_cnt_max = 90;
					$_wordurl_cnt_max = 60;
					$_total_cnt_max = 150;

					if ($_word_cnt_max <= $_word_cnt)
					{
						$_key_word = mb_substr($_key_word, 0, 90) . " ...";
						$_url_word = substr($val_ivd["ivd_item_url"], 0, 30) . " ...";

					} elseif ($_wordurl_cnt_max <= $_wordurl_cnt) {

						$_amari_cnt = $_total_cnt_max - $_word_cnt;

						$_url_word = substr($val_ivd["ivd_item_url"], 0, $_amari_cnt/3) . " ...";

					} else {
						$_url_word = $val_ivd["ivd_item_url"];
					}

					// キーワード行明細の書き込み
					if ($val_ivd["ivd_iv_accounting"] == 9)
					{
						$pdf->Cell($w1, $h3, '　' . $_key_word, "LR", 0);
					} else {
						$pdf->Cell($w1, $h3, '　対象キーワード：「' . $_key_word . '」：' . $_url_word, "LR", 0);
					}

					if ($val_ivd["ivd_total"] != 0)
					{
						$pdf->Cell($w2, $h3, number_format($val_ivd["ivd_qty"]), "LR", 0, "C");
						$pdf->Cell($w3, $h3, number_format($val_ivd["ivd_price"]), "LR", 0, "R");
						$pdf->Cell($w4, $h3, number_format($val_ivd["ivd_total"]), "LR", 1, "R");
					} else {
						$pdf->Cell($w2, $h3, "", "LR", 0, "C");
						$pdf->Cell($w3, $h3, "", "LR", 0, "R");
						$pdf->Cell($w4, $h3, "", "LR", 1, "R");
					}

					$pdf->Cell($w1, $h3, ' ', "LR", 0);
					$pdf->Cell($w2, $h3, ' ', "LR", 0);
					$pdf->Cell($w3, $h3, ' ', "LR", 0);
					$pdf->Cell($w4, $h3, ' ', "LR", 1);

					$_line_cnt++;

				}

				if ($_line_cnt <= 14)
				{
					for ($_cnt=$_line_cnt; $_cnt<=12; $_cnt++)
					{
						// 明細：空白行で埋める
						$pdf->Cell($w1, $h3, ' ', "LR", 0);
						$pdf->Cell($w2, $h3, ' ', "LR", 0);
						$pdf->Cell($w3, $h3, ' ', "LR", 0);
						$pdf->Cell($w4, $h3, ' ', "LR", 1);

						$pdf->Cell($w1, $h3, ' ', "LR", 0);
						$pdf->Cell($w2, $h3, ' ', "LR", 0);
						$pdf->Cell($w3, $h3, ' ', "LR", 0);
						$pdf->Cell($w4, $h3, ' ', "LR", 1);
					}
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


				// 空のページを追加
// 				if (($_line_cnt >= 13) && ($_line_cnt <= 20))
// 				{
// 					$pdf->AddPage();
// 				}

// 				$pdf->Ln();

				$pdf->SetFont($font1, '', 9);
				$pdf->SetDrawColor(0, 0, 0);

				// 「備考」欄はmax4行まで考慮。
				if (($_line_cnt <= 13) || ($_line_cnt >= 20))
				{
					$pdf->Ln();
					$pdf->MultiCell(185, 20, "【備　考　】\n" . $val["iv_remark"], 1, 'L', 0);
				}

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

				$pdf->Rect(95.0, $w1, 30.0, $h1, 'DF');
				$pdf->Text(97.0, $w2, "お振込先");
				$pdf->Rect(125.0, $w1, 75.0, $h1, 'D');
				$pdf->Text(127, $w3,    "銀 行 名 ：　三井住友銀行（0009）");
				$pdf->Text(127, $w3+3,  "支 店 名 ：　渋谷駅前支店（234）");
				$pdf->Text(127, $w3+6,  "口座番号：　4792809（普通口座）");
				$pdf->Text(127, $w3+9, "口座名義：　株式会社ラベンダーマーケティング");

			}
		}

		$pdf->Close();
		ob_end_clean();

		$pdf->Output('invoice_' . date("Ymdhis") . '.pdf', 'D');

	}

	private function cell_memo()
	{

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


	private function MultiRow($left, $right) {
		// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)

		$page_start = $this->getPage();
		$y_start = $this->GetY();

		// write the left cell
		$pdf->MultiCell(40, 0, $left, 1, 'R', 1, 2, '', '', true, 0);

		$page_end_1 = $this->getPage();
		$y_end_1 = $this->GetY();

		$pdf->setPage($page_start);

		// write the right cell
		$pdf->MultiCell(0, 0, $right, 1, 'J', 0, 1, $pdf->GetX() ,$y_start, true, 0);

		$page_end_2 = $this->getPage();
		$y_end_2 = $this->GetY();

		// set the new row position by case
		if (max($page_end_1,$page_end_2) == $page_start) {
			$ynew = max($y_end_1, $y_end_2);
		} elseif ($page_end_1 == $page_end_2) {
			$ynew = max($y_end_1, $y_end_2);
		} elseif ($page_end_1 > $page_end_2) {
			$ynew = $y_end_1;
		} else {
			$ynew = $y_end_2;
		}

		$pdf->setPage(max($page_end_1,$page_end_2));
		$pdf->SetXY($this->GetX(),$ynew);
	}

}

// END pdf Class

/* End of file pdf.php */
/* Location: ./application/libraries/pdf.php */
