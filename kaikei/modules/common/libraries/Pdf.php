<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

# include TCPDF
require_once(APPPATH . '../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once(APPPATH . '../../vendor/setasign/fpdi/fpdi.php');

/**
 * TCPDF - CodeIgniter Integration
 */
class Pdf extends TCPDF {

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
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
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
		$pdf->Text(155, 10, "伝票No:" . $iv_data['iv_slip_no']);

		$format = 'Y-m-d';
		$date = DateTime::createFromFormat($format, $iv_data["iv_issue_date"]);
		$pdf->Text(155, 15, "発効日:" . $date->format('Y年m月d日'));

		$pdf->Text(25, 10, "〒" . $iv_data["iv_zip01"] . '-' . $iv_data["iv_zip02"]);
		$pdf->Text(25, 15, $iv_data["iv_pref"] . $iv_data["iv_addr01"] . $iv_data["iv_addr02"] . $iv_data["iv_buil"]);
		if ($iv_data["iv_person01"] == "")
		{
			$pdf->Text(25, 20, $iv_data["iv_company"] . " 御中");
		} else {
			$pdf->Text(25, 20, $iv_data["iv_company"]);
			$pdf->Text(28, 25, $iv_data["iv_department"]);
			$pdf->Text(28, 30, $iv_data["iv_person01"] . ' ' . $iv_data["iv_person02"] . ' 様');
		}

		$pdf->line(10, 40, 200, 40);

		$pdf->SetFont($font1, 'B', 16);
		$pdf->Text(10, 52, "御　請　求　書");

		$pdf->SetFont($font1, 'BU', 12);
		$pdf->Text(15, 73, $iv_data["iv_company"] . '　御中');

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(149, 60, "株式会社ラベンダーマーケティング");
		$pdf->SetFont($font1, '', 8);
		$pdf->Text(158, 65, "〒150-0043");
		$pdf->Text(158, 69, "東京都渋谷区道玄坂 1-19-12");
		$pdf->Text(173, 73, "道玄坂今井ビル 4F");
		$pdf->Text(173, 77, "tel. 03-5784-3411");

		$pdf->Rect(165.0, 81.0, 17.0, 18.0, 'D');
		$pdf->Rect(182.0, 81.0, 17.0, 18.0, 'D');

		$pdf->Text(10, 105, "下記の通りご請求いたします。");

		$pdf->SetFont($font1, '', 12);
		$pdf->SetFillColor(211, 211, 211);
		$pdf->Rect(10.0, 110.0, 50.0, 9.0, 'DF');
		$pdf->Text(15, 112, "ご請求金額（税込み）");
		$pdf->Rect(60.0, 110.0, 50.0, 9.0, 'D');
		$pdf->Text(73, 112, number_format($iv_data["iv_total"]) . " 円");

		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetFont($font1, '', 9);
		$pdf->SetDrawColor(0, 0, 255);

		$w1 = 125;
		$w2 = 10;
		$w3 = 25;
		$w4 = 25;
		$h1 = 10;
		$h2 = 8;
		$h3 = 5;

		// 表タイトル
		$pdf->Cell($w1, $h2, '請　求　項　目', 1, 0, "C", 1);
		$pdf->Cell($w2, $h2, '数量', 1, 0, "C", 1);
		$pdf->Cell($w3, $h2, '単　価', 1, 0, "C", 1);
		$pdf->Cell($w4, $h2, '金　額', 1, 1, "C", 1);

		$pdf->SetFont($font1, '', 8);

		// 明細
		if ($iv_data["iv_accounting"] == 0)
		{
			$pdf->Cell($w1, $h2, 'SEO月額報酬 明細（' . $iv_data["iv_issue_yymm"] . '月度）', "LR", 0);
			$pdf->Cell($w2, $h2, '', "LR", 0, "C");
			$pdf->Cell($w3, $h2, '', "LR", 0, "R");
			$pdf->Cell($w4, $h2, '', "LR", 1, "R");
		}

		foreach ($ivd_data as $key => $val)
		{

			$_key_word = mb_convert_kana($val["ivd_item"], 'ASKV', "UTF-8");
			$_word_cnt = mb_strlen($_key_word);
			if ($_word_cnt <= 90)
			{
				$_key_word = $val["ivd_item"];
			} else {
				$_key_word = mb_substr($_key_word, 0, 90) . " ...";
			}

			if ($val["ivd_iv_accounting"] == 9)
			{
				$pdf->Cell($w1, $h3, '　' . $_key_word, "LR", 0);
			} else {
				$pdf->Cell($w1, $h3, '　対象キーワード：「' . $_key_word . '」', "LR", 0);
			}

			$pdf->Cell($w2, $h3, number_format($val["ivd_qty"]), "LR", 0, "C");
			$pdf->Cell($w3, $h3, number_format($val["ivd_price"]), "LR", 0, "R");
			$pdf->Cell($w4, $h3, number_format($val["ivd_total"]), "LR", 1, "R");
		}

		// 明細：最後の空白行
		$pdf->Cell($w1, $h3, ' ', "LRB", 0);
		$pdf->Cell($w2, $h3, ' ', "LRB", 0);
		$pdf->Cell($w3, $h3, ' ', "LRB", 0);
		$pdf->Cell($w4, $h3, ' ', "LRB", 1);

		$pdf->SetFont($font1, '', 9);

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

		$pdf->Ln();

		$pdf->SetFont($font1, '', 9);
		$pdf->SetDrawColor(0, 0, 0);

        // 複数ページ処理 : 明細20行位がmaxか？
		if (count($ivd_data) >= 10 && count($ivd_data) <= 20)
		{
			$pdf->AddPage();                                                        // 空のページを追加
		}

		if ($iv_data["iv_remark"] != '')
		{
			$pdf->MultiCell(185, 10, "【備　考　】\n" . $iv_data["iv_remark"], 1, 'L', 0);
		}

		$pdf->SetFont($font1, '', 8);
		$x = $pdf->GetX();
		$y = $pdf->GetY() + 5;
		$pdf->Text(10, $y, "※支払期日までに下記口座までお振込みくださいますようお願いいたします。尚、振込手数料は貴社にてご負担願います。");

		$w1 = $y+4;
		$w2 = $y+10;
		$w3 = $y+5;
		$h1 = 18.0;

		$pdf->SetFont($font1, '', 9);
		$pdf->SetFillColor(211, 211, 211);
		$pdf->Rect(10.0, $w1, 30.0, $h1, 'DF');
		$pdf->Text(12.0, $w2, "お振込期日");

		$format = 'Y-m-d';
		$date = DateTime::createFromFormat($format, $iv_data["iv_pay_date"]);
		$pdf->Rect(40.0, $w1, 50.0, $h1, 'D');
		$pdf->Text(50.0, $w2, $date->format('Y年m月d日'));

		$pdf->Rect(90.0, $w1, 30.0, $h1, 'DF');
		$pdf->Text(92.0, $w2, "お振込先");
		$pdf->Rect(120.0, $w1, 80.0, $h1, 'D');
		$pdf->Text(122, $w3,    "銀 行 名 ：　三井住友銀行（0009）");
		$pdf->Text(122, $w3+4,  "支 店 名 ：　渋谷駅前支店（234）");
		$pdf->Text(122, $w3+8,  "口座番号：　4792809（普通口座）");
		$pdf->Text(122, $w3+12, "口座名義：　株式会社ラベンダーマーケティング");


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
				$pdf->Text(155, 10, "伝票No:" . $val['iv_slip_no']);

				$format = 'Y-m-d';
				$date = DateTime::createFromFormat($format, $val["iv_issue_date"]);
				$pdf->Text(155, 15, "発効日:" . $date->format('Y年m月d日'));

				$pdf->Text(25, 10, "〒" . $val["iv_zip01"] . '-' . $val["iv_zip02"]);
				$pdf->Text(25, 15, $val["iv_pref"] . $val["iv_addr01"] . $val["iv_addr02"] . $val["iv_buil"]);
				if ($val["iv_person01"] == "")
				{
					$pdf->Text(25, 20, $val["iv_company"] . " 御中");
				} else {
					$pdf->Text(25, 20, $val["iv_company"]);
					$pdf->Text(28, 25, $val["iv_department"]);
					$pdf->Text(28, 30, $val["iv_person01"] . ' ' . $val["iv_person02"] . ' 様');
				}

				$pdf->line(10, 40, 200, 40);

				$pdf->SetFont($font1, 'B', 16);
				$pdf->Text(10, 52, "御　請　求　書");

				$pdf->SetFont($font1, 'BU', 12);
				$pdf->Text(15, 73, $val["iv_company"] . '　御中');

				$pdf->SetFont($font1, '', 9);
				$pdf->Text(149, 60, "株式会社ラベンダーマーケティング");
				$pdf->SetFont($font1, '', 8);
				$pdf->Text(158, 65, "〒150-0043");
				$pdf->Text(158, 69, "東京都渋谷区道玄坂 1-19-12");
				$pdf->Text(173, 73, "道玄坂今井ビル 4F");
				$pdf->Text(173, 77, "tel. 03-5784-3411");

				$pdf->Rect(165.0, 81.0, 17.0, 18.0, 'D');
				$pdf->Rect(182.0, 81.0, 17.0, 18.0, 'D');

				$pdf->Text(10, 105, "下記の通りご請求いたします。");

				$pdf->SetFont($font1, '', 12);
				$pdf->SetFillColor(211, 211, 211);
				$pdf->Rect(10.0, 110.0, 50.0, 9.0, 'DF');
				$pdf->Text(15, 112, "ご請求金額（税込み）");
				$pdf->Rect(60.0, 110.0, 40.0, 9.0, 'D');
				$pdf->Text(73, 112, number_format($val["iv_total"]) . " 円");

				$pdf->Ln();
				$pdf->Ln();

				$pdf->SetFont($font1, '', 9);
				$pdf->SetDrawColor(0, 0, 255);

				$w1 = 125;
				$w2 = 10;
				$w3 = 25;
				$w4 = 25;
				$h1 = 10;
				$h2 = 8;
				$h3 = 5;

				// 表タイトル
				$pdf->Cell($w1, $h2, '請　求　項　目', 1, 0, "C", 1);
				$pdf->Cell($w2, $h2, '数量', 1, 0, "C", 1);
				$pdf->Cell($w3, $h2, '単　価', 1, 0, "C", 1);
				$pdf->Cell($w4, $h2, '金　額', 1, 1, "C", 1);

				$pdf->SetFont($font1, '', 8);

				// 明細
				if ($val["iv_accounting"] == 0)
				{
					$pdf->Cell($w1, $h2, 'SEO月額報酬 明細（' . $val["iv_issue_yymm"] . '月度）', "LR", 0);
					$pdf->Cell($w2, $h2, '', "LR", 0, "C");
					$pdf->Cell($w3, $h2, '', "LR", 0, "R");
					$pdf->Cell($w4, $h2, '', "LR", 1, "R");
				}

				foreach ($ivd_data[$key] as $key_ivd => $val_ivd)
				{

					$_key_word = mb_convert_kana($val_ivd["ivd_item"], 'ASKV', "UTF-8");
					$_word_cnt = mb_strlen($_key_word);
					if ($_word_cnt <= 90)
					{
						$_key_word = $val_ivd["ivd_item"];
					} else {
						$_key_word = mb_substr($_key_word, 0, 90) . " ...";
					}

					if ($val_ivd["ivd_iv_accounting"] == 9)
					{
						$pdf->Cell($w1, $h3, '　' . $_key_word, "LR", 0);
					} else {
						$pdf->Cell($w1, $h3, '　対象キーワード：「' . $_key_word . '」', "LR", 0);
					}
					$pdf->Cell($w2, $h3, number_format($val_ivd["ivd_qty"]), "LR", 0, "C");
					$pdf->Cell($w3, $h3, number_format($val_ivd["ivd_price"]), "LR", 0, "R");
					$pdf->Cell($w4, $h3, number_format($val_ivd["ivd_total"]), "LR", 1, "R");

				}

				// 明細：最後の空白行
				$pdf->Cell($w1, $h3, ' ', "LRB", 0);
				$pdf->Cell($w2, $h3, ' ', "LRB", 0);
				$pdf->Cell($w3, $h3, ' ', "LRB", 0);
				$pdf->Cell($w4, $h3, ' ', "LRB", 1);

				$pdf->SetFont($font1, '', 9);

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

				$pdf->Ln();

				$pdf->SetFont($font1, '', 9);
				$pdf->SetDrawColor(0, 0, 0);

				// 複数ページ処理 : 明細20行位がmaxか？
				if (count($ivd_data) >= 10 && count($ivd_data) <= 20)
				{
					$pdf->AddPage();                                                        // 空のページを追加
				}

				if ($val["iv_remark"] != '')
				{
					$pdf->MultiCell(185, 10, "【備　考　】\n" . $val["iv_remark"], 1, 'L', 0);
				}

				$pdf->SetFont($font1, '', 8);
				$x = $pdf->GetX();
				$y = $pdf->GetY() + 5;
				$pdf->Text(10, $y, "※支払期日までに下記口座までお振込みくださいますようお願いいたします。尚、振込手数料は貴社にてご負担願います。");

				$w1 = $y+4;
				$w2 = $y+10;
				$w3 = $y+5;
				$h1 = 18.0;

				$pdf->SetFont($font1, '', 9);
				$pdf->SetFillColor(211, 211, 211);
				$pdf->Rect(10.0, $w1, 30.0, $h1, 'DF');
				$pdf->Text(12.0, $w2, "お振込期日");

				$format = 'Y-m-d';
				$date = DateTime::createFromFormat($format, $val["iv_pay_date"]);
				$pdf->Rect(40.0, $w1, 50.0, $h1, 'D');
				$pdf->Text(50.0, $w2, $date->format('Y年m月d日'));

				$pdf->Rect(90.0, $w1, 30.0, $h1, 'DF');
				$pdf->Text(92.0, $w2, "お振込先");
				$pdf->Rect(120.0, $w1, 80.0, $h1, 'D');
				$pdf->Text(122, $w3,    "銀 行 名 ：　三井住友銀行（0009）");
				$pdf->Text(122, $w3+4,  "支 店 名 ：　渋谷駅前支店（234）");
				$pdf->Text(122, $w3+8,  "口座番号：　4792809（普通口座）");
				$pdf->Text(122, $w3+12, "口座名義：　株式会社ラベンダーマーケティング");

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
