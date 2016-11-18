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
     * アドミン：クライアント請求＆ポイント明細
     *
     */
	public function pdf_receiptlist($pdf_list, $pdflist_path, $base_path)
	{

		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
		$pdf->setSourceFile($pdflist_path);
		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		$pdf->useTemplate($page);

		$pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

		// PDFドキュメントプロパティ設定
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));

		// 一覧出力開始位置
		$pdf->Text(0, 30, "" );
		$pdf->Ln();

		// 一覧データの読み込み
		$data = $this->LoadData($pdf_list);

		// 表ヘッダー部プロパティ設定
		$pdf->SetFillColor(255, 0, 0);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(128, 0, 0);
		$pdf->SetLineWidth(0.3);
		$pdf->SetFont($font2, 'B', 9);

		// 表ヘッダー部出力
		$header = array('', '支払状況', 'CL ID', '作業ID', 'ポイント', '調 整', '請求金額', '納品日', '請求日');
		$w = array(10, 20, 20, 20, 20, 20, 20, 25, 25);
		$num_headers = count($header);
		for($i = 0; $i < $num_headers; ++$i) {
			$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
		}
		$pdf->Ln();

		// 表データ部プロパティ設定
		$pdf->SetFillColor(224, 235, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont($font1, '', 9);

		// 表データ部出力
		$fill = 0;
		$i = 1;
		$arroptions_paystatus = array (
				'0' => '未支払',
				'1' => '支払済',
				'2' => '保　留',
				'3' => '返　金',
		);

		foreach($data as $row) {
			$pdf->Cell($w[0], 6, $i, 'LR', 0, 'C', $fill);
			$pdf->Cell($w[1], 6, $arroptions_paystatus[$row['pj_pay_status']], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[2], 6, $row['pj_en_cl_id'], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[3], 6, $row['pj_id'], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[4], 6, number_format($row['pj_wi_point']), 'LR', 0, 'R', $fill);
			$pdf->Cell($w[5], 6, number_format($row['pj_wi_point_adjust']), 'LR', 0, 'R', $fill);
			$pdf->Cell($w[6], 6, number_format($row['pj_pay_money']), 'LR', 0, 'R', $fill);

			$date = date_create_from_format('Y-m-d H:i:s', $row['pj_delivery_date']);
			$pdf->Cell($w[7], 6, date_format($date, 'Y-m-d'), 'LR', 0, 'C', $fill);

			$pdf->Cell($w[8], 6, $row['pj_pay_schedule'], 'LR', 0, 'C', $fill);
			$pdf->Ln();
			$fill=!$fill;
			$i++;
		}
		$pdf->Cell(array_sum($w), 0, '', 'T');

		// close and output PDF document
    	$pdf->Close();
    	ob_end_clean();
		$pdf->Output('pdf_cl_paydetail_' . date('YmdHis') . '.pdf' , 'D');      // D: ブラウザに送信し、nameパラメータにて指定された名前で生成したPDFファイルを強制的にダウンロード
//		$pdf->Output('pdf_cl_paydetail_' . date('YmdHis') . '.pdf' , 'I');      // I:ブラウザにインラインにて送信

	}

	/**
	 * アドミン：獲得ポイント明細
	 *
	 */
	public function pdf_pointlist($pdf_list, $pdflist_path, $base_path)
	{

		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
		$pdf->setSourceFile($pdflist_path);
		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		$pdf->useTemplate($page);

		$pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

		// PDFドキュメントプロパティ設定
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));

		// 一覧出力開始位置
		$pdf->Text(0, 30, "" );
		$pdf->Ln();

		// 一覧データの読み込み
		$data = $this->LoadData($pdf_list);

		// 表ヘッダー部プロパティ設定
		$pdf->SetFillColor(255, 0, 0);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(128, 0, 0);
		$pdf->SetLineWidth(0.3);
		$pdf->SetFont($font2, 'B', 9);

		// 表ヘッダー部出力
		$header = array('', '入金状況', 'WR ID', '作業ID', 'ポイント', '調 整', 'ポイント合計', 'ポイント獲得日', '締 日', '入金日');
		$w = array(10, 20, 20, 20, 15, 15, 20, 25, 15, 25);
		$num_headers = count($header);
		for($i = 0; $i < $num_headers; ++$i) {
			$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
		}
		$pdf->Ln();

		// 表データ部プロパティ設定
		$pdf->SetFillColor(224, 235, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont($font1, '', 9);

		// 表データ部出力
		$fill = 0;
		$i = 1;
		$arroptions_paystatus = array (
				'0' => '未支払',
				'1' => '支払済',
				'2' => '保　留',
				'3' => '返　金',
		);
		$arroptions_paylimit = array (
				'0' => '日　次',
				'1' => '週　次',
				'2' => '月　次',
				'3' => '曜　日',
				'4' => '10日〆',
		);

		foreach($data as $row) {
			$pdf->Cell($w[0], 6, $i, 'LR', 0, 'C', $fill);
			$pdf->Cell($w[1], 6, $arroptions_paystatus[$row['wi_pay_status']], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[2], 6, $row['wi_wr_id'], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[3], 6, $row['wi_pj_id'], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[4], 6, number_format($row['wi_point']), 'LR', 0, 'R', $fill);
			$pdf->Cell($w[5], 6, number_format($row['wi_point_adjust']), 'LR', 0, 'R', $fill);
			$pdf->Cell($w[6], 6, number_format($row['wi_pay_money']), 'LR', 0, 'R', $fill);

			$date = date_create_from_format('Y-m-d H:i:s', $row['wi_check_date']);
			$pdf->Cell($w[7], 6, date_format($date, 'Y-m-d'), 'LR', 0, 'C', $fill);

			$pdf->Cell($w[8], 6, $arroptions_paylimit[$row['wr_pay_limit_date']], 'LR', 0, 'C', $fill);

			if ($row['wi_pay_date'] != '')
			{
				$date = date_create_from_format('Y-m-d', $row['wi_pay_date']);
				$pdf->Cell($w[9], 6, date_format($date, 'Y-m-d'), 'LR', 0, 'C', $fill);
			} else {
				$pdf->Cell($w[9], 6, '', 'LR', 0, 'C', $fill);
			}

			$pdf->Ln();
			$fill=!$fill;
			$i++;
		}
		$pdf->Cell(array_sum($w), 0, '', 'T');

		// close and output PDF document
		$pdf->Close();
    	ob_end_clean();
		$pdf->Output('pdf_wr_paydetail_' . date('YmdHis') . '.pdf' , 'D');
//		$pdf->Output('pdf_wr_paydetail_' . date('YmdHis') . '.pdf' , 'I');

	}

	/**
	 * アドミン：獲得ポイント明細
	 *
	 */
	public function pdf_javascript($pdf_list, $pdflist_path, $base_path)
	{

		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
		$pdf->setSourceFile($pdflist_path);
		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		$pdf->useTemplate($page);

		$pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

		// PDFドキュメントプロパティ設定
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));









		$invoiceData = array(
				'user' => 'chrish@pobox.com',
				'date' => date_format( new DateTime(), DateTime::W3C ),
				'items' => array()
		);
		$invoiceData['items'][] = array( 'Café Latté', 2, 2.99, 2 * 2.99 );
		$invoiceData['items'][] = array( 'San Lorenzo Dark', 1, 1.35, 1 * 1.35 );
		$invoiceData['items'][] = array( 'Sumatra Especial', 24, 1.35, 24 * 1.35 );
		$invoiceData['items'][] = array( 'Rooibos', 5, 0.75, 5 * 0.75 );
		$invoiceData['total'] = 0;
		foreach( $invoiceData['items'] as $item ) {
			$invoiceData['total'] += $item[3];
		}



		$pdf->AddPage();
		$pdf->SetFont( $font1, '', 11 );
		$pdf->SetY( 144, true );

		# Table parameters
		#
		# Column size, wide (description) column, table indent, row height.
		$col = 20;
		$wideCol = 3 * $col;
		$indent = ( $this->getPageWidth() - 2 * 20 - $wideCol - 3 * $col ) / 2;
		$line = 18;

		# Table header
		$pdf->SetFont( $font1, 'b' );
		$pdf->Cell( $indent );
		$pdf->Cell( $wideCol, $line, 'Item', 1, 0, 'L' );
		$pdf->Cell( $col, $line, 'Quantity', 1, 0, 'R' );
		$pdf->Cell( $col, $line, 'Price', 1, 0, 'R' );
		$pdf->Cell( $col, $line, 'Cost', 1, 0, 'R' );
		$pdf->Ln();

		# Table content rows
		$pdf->SetFont( $font1, '' );
		foreach( $invoiceData['items'] as $item ) {
			$pdf->Cell( $indent );
			$pdf->Cell( $wideCol, $line, $item[0], 1, 0, 'L' );
			$pdf->Cell( $col, $line, $item[1], 1, 0, 'R' );
			$pdf->Cell( $col, $line, $item[2], 1, 0, 'R' );
			$pdf->Cell( $col, $line, $item[3], 1, 0, 'R' );
			$pdf->Ln();
		}

		# Table Total row
		$pdf->SetFont( $font1, 'b' );
		$pdf->Cell( $indent );
		$pdf->Cell( $wideCol + $col * 2, $line, 'Total:', 1, 0, 'R' );
		$pdf->SetFont( $font1, '' );
		$pdf->Cell( $col, $line, $invoiceData['total'], 1, 0, 'R' );






		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Close();
    	ob_end_clean();
    	$pdf->Output('example_063.pdf', 'I');
	}

	/**
	 * 文字フォント
	 *
	 */
	public function pdf_font($pdf_list, $pdflist_path, $base_path)
	{

		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
		$pdf->setSourceFile($pdflist_path);
		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		$pdf->useTemplate($page);

		$pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

		// PDFドキュメントプロパティ設定
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));








		// set font
		$pdf->SetFont('helvetica', 'B', 16);

		// add a page
		$pdf->AddPage();

		$pdf->Write(0, 'Example of Text Stretching and Spacing (tracking)', '', 0, 'L', true, 0, false, false, 0);
		$pdf->Ln(5);

		// create several cells to display all cases of stretching and spacing combinations.

		$fonts = array('times', 'dejavuserif');
		$alignments = array('L' => 'LEFT', 'C' => 'CENTER', 'R' => 'RIGHT', 'J' => 'JUSTIFY');


		// Test all cases using direct stretching/spacing methods
		foreach ($fonts as $fkey => $font) {
			$pdf->SetFont($font, '', 14);
			foreach ($alignments as $align_mode => $align_name) {
				for ($stretching = 90; $stretching <= 110; $stretching += 10) {
					for ($spacing = -0.254; $spacing <= 0.254; $spacing += 0.254) {
						$pdf->setFontStretching($stretching);
						$pdf->setFontSpacing($spacing);
						$txt = $align_name.' | Stretching = '.$stretching.'% | Spacing = '.sprintf('%+.3F', $spacing).'mm';
						$pdf->Cell(0, 0, $txt, 1, 1, $align_mode);
					}
				}
			}
			$pdf->AddPage();
		}


		// Test all cases using CSS stretching/spacing properties
		foreach ($fonts as $fkey => $font) {
			$pdf->SetFont($font, '', 11);
			foreach ($alignments as $align_mode => $align_name) {
				for ($stretching = 90; $stretching <= 110; $stretching += 10) {
					for ($spacing = -0.254; $spacing <= 0.254; $spacing += 0.254) {
						$html = '<span style="font-stretch:'.$stretching.'%;letter-spacing:'.$spacing.'mm;"><span style="color:red;">'.$align_name.'</span> | <span style="color:green;">Stretching = '.$stretching.'%</span> | <span style="color:blue;">Spacing = '.sprintf('%+.3F', $spacing).'mm</span><br />Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed imperdiet lectus. Phasellus quis velit velit, non condimentum quam. Sed neque urna, ultrices ac volutpat vel, laoreet vitae augue. Sed vel velit erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span>';
						$pdf->writeHTMLCell(0, 0, '', '', $html, 1, 1, false, true, $align_mode, false);
					}
				}
				if (!(($fkey == 1) AND ($align_mode == 'J'))) {
					$pdf->AddPage();
				}
			}
		}


		// reset font stretching
		$pdf->setFontStretching(100);

		// reset font spacing
		$pdf->setFontSpacing(0);

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('example_063.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	/**
	 * 領収書
	 *
	 */
	public function pdf_invoice($pdf_list, $pdflist_path, $base_path)
	{

		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
		$pdf->setSourceFile($pdflist_path);
		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		$pdf->useTemplate($page);

		$pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

		// PDFドキュメントプロパティ設定
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));






		$pdf->SetFont( $font1 , '' , 10 );
		$pdf->Cell( 130 , 6 , "" , 0 );
		$pdf->Cell( 60 , 6 , "発行日　　" . "2016/10/20" , 0 );
		$pdf->SetFont( $font1 , 'B' , 20 );
		$pdf->Ln();
		$pdf->Cell( 0, 16,'  領　　収　　書  '  , 0 , 0 , "C" );


		$pdf->Ln();
		$pdf->SetFont( $font1 , '' , 10 ) ;
		$pdf->Cell( 0 , 6 ,'〒'. preg_replace("/^(\d{3})(\d{4})$/", "$1-$2", "3600113") , 0 );

		$building_address = "dm_address_building";
		if ( empty($building_address) ) {
			$pdf->Ln();
			$pdf->Cell( 0 , 6 , "dm_prefecture" . "dm_address_1" , 0 );
		} else {
			$pdf->Ln();
			$pdf->Cell( 0 , 6 , "dm_prefecture" .  "dm_address_1"  , 0 );
			$pdf->Ln();
			$pdf->Cell( 0 , 6 , "dm_address_building", 0 );
		}

		// 2014.05.23 Chg : アルティス様仕様
		// 2014.05.16 Chg : アルティス様仕様
		//                  1.担当者　姓　と　名　の両方にスペースが入っている場合　会社名　様
		//                  2.担当者　姓　にスーペース　名　に何も入っていない場合　御中
		// × 会社名と担当者名に同じ名前が入っていた場合、名前のみを表示(アルティス様仕様)
		$contactname = trim(str_replace("　" , "" , "dm_contact_name"));
		if ( empty($contactname) ) {
			// "　 　"/"　 " 入力そのままで判定しますので、スペース(全角や半角)が２個以上入れられている場合は無視
			if (  "dm_contact_name"  == "　 　" ) {
				$pdf->Ln();
				$pdf->Cell( 3 , 6 , "", 0 );
				$pdf->Cell( 0, 6,  "dm_company_name"  . " 様", 0, 0, "" );
			} else {
				$pdf->Ln();
				$pdf->Cell( 3 , 6 , "", 0 );
				$pdf->Cell( 0, 6,  "dm_company_name"  . " 御中", 0, 0, "" );
			}
		} else {
			$pdf->Ln();
			$pdf->Cell( 3 , 6 , "", 0 );
			$pdf->Cell( 0, 6,  "dm_company_name"  , 0, 0, "" );
			$pdf->Ln();
			$pdf->Cell( 6 , 6 , "", 0 );
			$pdf->Cell( 0 , 6 ,  "dm_contact_name"  . " 様" , 0 );
		}

			$pdf->SetFont( $font1 , '' , 9 ) ;
			$pdf->Ln();
			$pdf->Cell( 100 , 5 , "", 0 );
			$pdf->Cell( 60 , 5 , "〒" . preg_replace("/^(\d{3})(\d{4})$/", "$1-$2", "3600114") , 0 );

			$pdf->Ln();
			$pdf->Cell( 100 , 5 , ""	, 0 );
			if ( empty($objSiteMaster->sm_building) ) {
				$pdf->Cell( 60 , 5 , "sm_prefecture] sm_address" , 0 );
			} else {
				$pdf->Cell( 60 , 5 , "sm_prefecture] sm_address" , 0 );
				$pdf->Ln();
				$pdf->Cell( 100 , 5 , ""	, 0 );
				$pdf->Cell( 60 , 5 , "sm_building", 0 );
			}

			$pdf->Ln();
			$pdf->Cell( 103 , 5 , "", 0 );
			$pdf->SetFont( $font1 , '' , 10 ) ;
			$pdf->Cell( 60 , 5 , "objSiteMaster->sm_operator_compny", 0 );
			$pdf->SetFont( $font1 , '' , 9 ) ;
			$pdf->Ln();
			$pdf->Cell( 103 , 5 , "", 0 );
			$pdf->Cell( 27 , 5 , "TEL: " . "objSiteMaster->sm_tel", 0 );
			if( "" != "objSiteMaster->sm_fax" )
			{
				//$pdf->Cell( 103 , 5 , "", 0 );
				$pdf->Cell( 0 , 5 , " / FAX: " . "objSiteMaster->sm_fax", 0 );
			}
			$pdf->Ln();
			$pdf->Cell( 103 , 5 , "", 0 );
			$pdf->Cell( 60 , 5 , "e-mail: " . "objSiteMaster->sm_mail", 0 );
			$pdf->SetFont( $font1 , '' , 10 ) ;
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Cell( 100 , 6 , "この度は、お買い上げ誠にありがとうございました。" , 0 );
			$pdf->Ln();
			$pdf->Cell( 100 , 6 , "下記の通りご請求申し上げます。" , 0 );


			$pdf->Ln();
			$pdf->Ln();

			$money_unit = " 円";
			$pdf->SetFont( $font1 , 'BU' , 10 ) ;
			$pdf->Cell( 5 , 2 , '' ,  0 );
			$pdf->Cell( 25 , 6 , "ご請求金額：" , 0 , 0 );
			$pdf->SetFont( $font1 , 'B' , 10 ) ;
			$pdf->Cell( 100 , 6 ,  "dm_total"  . $money_unit , 0 );


			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont( $font1 , '' , 10 ) ;
			$pdf->Cell( 100 , 6 , "ご　請　求　明　細" , 0 );
			$pdf->Ln();


			$pdf->SetFont( $font1 , '' , 9 ) ;
			$pdf->Cell( 5 , 2 , '' ,  0 );
			$pdf->Cell( 80 , 5 , "■ご注文日： " .  "dm_om_reg_time" , 0 );
			$pdf->Cell( 70 , 5 , "■お届け先："		, 0 );
			$pdf->Ln();
			$pdf->Cell( 5 , 2 , '' ,  0 );
			$pdf->Cell( 82 , 5 , "■受注番号： " .  "dm_order_number" , 0 );
			$pdf->Cell( 70 , 5 , "〒" . preg_replace("/^(\d{3})(\d{4})$/", "$1-$2",  "ds_zip" ) , 0 );


			$pdf->Ln();
			$pdf->Cell( 5 , 2 , '' , 0 );
			$building_address ="ds_address_building" ;
			if ( empty($building_address) ) {
				$pdf->Cell( 82 , 5 , "■お支払方法： " . "dm_payment"  , 0 );
				$pdf->Cell( 70 , 5 ,  "ds_prefecture"  .  "ds_address_1"  , 0 );
			} else {
				$pdf->Cell( 82 , 5 , "■お支払方法： " .  "dm_payment"  , 0 );
				$pdf->Cell( 70 , 5 ,  "ds_prefecture"  .  "ds_address_1"  , 0 );
				$pdf->Ln();
				$pdf->Cell( 87 , 2 , '' , 0 );
				$pdf->Cell( 70 , 5 ,  "ds_address_building" , 0 );
			}

			$pdf->Ln();
			$pdf->Cell( 90 , 2 , "", 0 );

			// 2014.05.23 Chg : アルティス様仕様
			//                  1.担当者　姓　と　名　の両方にスペースが入っている場合　会社名　様
			//                  2.担当者　姓　にスーペース　名　に何も入っていない場合　御中
			// × 会社名と担当者名に同じ名前が入っていた場合、名前のみを表示(アルティス様仕様)
			$contactname = trim(str_replace("　" , "" ,  "ds_contact_name" ));
			if ( empty($contactname) ) {
				// "　 　"/"　 " 入力そのままで判定しますので、スペース(全角や半角)が２個以上入れられている場合は無視
				if (  "ds_contact_name"  == "　 　" ) {
					$pdf->Cell( 70, 4,  "ds_company_name"  . " 様", 0, 0, "" );
				} else {
					$pdf->Cell( 70, 4, "ds_company_name"  . " 御中", 0, 0, "" );
				}
			} else {
				$pdf->Cell( 70, 4, "ds_company_name"  , 0, 0, "" );
				$pdf->Ln();
				$pdf->Cell( 92 , 2 , "", 0 );
				$pdf->Cell( 70 , 4 ,  "ds_contact_name"  . " 様" , 0 );
			}

				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont( $font1, '', 8 );

				// 表のサイズ ここで設定した値と、DrawBoxの戻り値の描画後表サイズと比較し、まったく同じである場合は、与えた時と現在XY位置が変化ないので描画失敗と見なす
				$avarRectWorld[ "TOP"    ] = $pdf->GetX();
				$avarRectWorld[ "LEFT"   ] = $pdf->GetY();
				$avarRectWorld[ "BOTTOM" ] = $avarRectWorld[ "TOP"  ];
				$avarRectWorld[ "RIGHT"  ] = $avarRectWorld[ "LEFT" ];

				// 全角１文字あたりの縦横サイズを設定
				$avarSizeFont[ "Width"  ] = $pdf->GetStringWidth( "あ" );

				// Position Y は内部調整するため投入しない 内部で処理しない		Widthには、全角１文字サイズを軸に、何文字記入できるかを設定している
				// Width値にFontのWidthを足しているのは、マージン分である
				$avarTitle[ 0 ][ "Title" ] = "商 品 番 号";
				$avarTitle[ 0 ][ "TextPosition" ] = "L"; //"LEFT";
				$avarTitle[ 0 ][ "CharByte" ] = $avarSizeFont[ "Width" ] / 2;// 半角文字で統一
				$avarTitle[ 0 ][ "Rect" ][ "Width" ] = $avarSizeFont[ "Width" ] * 5 + $avarSizeFont[ "Width" ];

				$avarTitle[ 1 ][ "Title" ] = "商　品　名　　< 入数 >";
				$avarTitle[ 1 ][ "TextPosition" ] = "L";	//"LEFT";
				$avarTitle[ 1 ][ "CharByte" ] = $avarSizeFont[ "Width" ];// 全角文字で統一
				$avarTitle[ 1 ][ "Rect" ][ "Width" ] = $avarSizeFont[ "Width" ] * 14 + $avarSizeFont[ "Width" ];

				$avarTitle[ 2 ][ "Title" ] = "単　価";
				$avarTitle[ 2 ][ "TextPosition" ] = "R"; 	//"RIGHT";
				$avarTitle[ 2 ][ "CharByte" ] = $avarSizeFont[ "Width" ] / 2;// 半角文字で統一
				$avarTitle[ 2 ][ "Rect" ][ "Width" ] = $avarSizeFont[ "Width" ] * 2 + $avarSizeFont[ "Width" ];

				$avarTitle[ 3 ][ "Title" ] = "数　量";
				$avarTitle[ 3 ][ "TextPosition" ] = "R"; 	//"RIGHT";
				$avarTitle[ 3 ][ "CharByte" ] = $avarSizeFont[ "Width"  ] / 2;// 半角文字で統一
				$avarTitle[ 3 ][ "Rect" ][ "Width" ] = $avarSizeFont[ "Width" ] * 1 + $avarSizeFont[ "Width" ];

				$avarTitle[ 4 ][ "Title" ] = "金　　額";
				$avarTitle[ 4 ][ "TextPosition" ] = "R"; 	//"RIGHT";
				$avarTitle[ 4 ][ "CharByte" ] = $avarSizeFont[ "Width" ] / 2;// 半角文字で統一
				$avarTitle[ 4 ][ "Rect" ][ "Width" ] = $avarSizeFont[ "Width" ] * 3 + $avarSizeFont[ "Width" ];

				// 表項目を作成
				$pdf->SetFillColor(0xc0, 0xc0, 0xff); 					// タイトル行に背景色
				setlocale(LC_MONETARY, 'ja_JP');							// 金額フォーマット設定
				$index = 0;
// 				foreach( $aobjOrderDetail as $iDetailIndex => $varDetail )
// 				{
// 					if ( ($varDetail[ "dd_qty" ] != 0) ) {		// 2013.02.01 Chg
						$avarData[ $index ][ "dd_pm_id_1" ] = "axaxaxaxa";
						$avarData[ $index ][ "dd_name"    ] = "qqqqqqqqqqqqqqqqqqqqqqq";
						$avarData[ $index ][ "dd_price"   ] =  "dd_price";
						$avarData[ $index ][ "dd_num"     ] =  "dd_num" ;
						$avarData[ $index ][ "dd_total"   ] =  "dd_total" ;
// 						$index ++;
// 					}
// 				}
				//$avarRectWorldNew = self::DrawBox( $pdf, $avarSizeFont, $avarRectWorld, $avarTitle, $avarData );

				$money_unit = " 円";
				$pdf->SetFont( $font1, '', 9 );

				$pdf->Cell( 100 , 2 , '',  0  );
				$pdf->Ln();
				$pdf->Cell( 120 , 2 , '',  0  );
				$pdf->Cell( 23 , 6 , '小　　　計', 1 , 0 , "L" );
				$pdf->Cell( 26 , 6  ,  "dm_subtotal"  . $money_unit , 1 , 0 , "R" );
				$pdf->Ln();

				$pdf->Cell( 120 , 2 , '' ,  0 );
				$pdf->Cell( 23 , 6 , '消　費　税' , 1 , 0 , "L");
				$pdf->Cell( 26 , 6 ,  "dm_tax" . $money_unit , 1 , 0 , "R");
				$pdf->Ln();

				$pdf->Cell( 120 , 2 , '' ,  0 );
				$pdf->Cell( 23 , 6 , '送　　　料' , 1 , 0 , "L");
				$pdf->Cell( 26  , 6 ,  "dm_postage" . $money_unit , 1 , 0 , "R");
				$pdf->Ln();

				if(  "dm_cod_price"  > "0" )
				//if( "代金引換" == $paymentOptions[ $varOrderMaster[ "dm_payment" ] ] )		// 2013.02.01 Chg
				{
					$pdf->Cell( 120 , 2 , '' ,  0 );
					$pdf->Cell( 23 , 6 , '手数料' , 1 , 0 , "L");
					//$pdf->Cell( 23 , 6 , '代引手数料' , 1 , 0 , "L");
					$pdf->Cell( 26 , 6 ,  "dm_cod_price"  . $money_unit , 1 , 0 , "R");
					$pdf->Ln();
				}

				//割引金額がある場合
				// ▼項番90対応
				if( 0 !=  "dm_discount" )
				{
					//割引金額の表示
					$pdf->Cell( 120 , 2 , '' ,  0 );
					$pdf->Cell(  23 , 6 , '金額調整' , 1 , 0 , "L");
					$pdf->Cell(  26 , 6 ,  "dm_discount" . $money_unit , 1 , 0 , "R");
					$pdf->Ln();
				}
				// ▲項番90対応
				$pdf->Cell( 100 , 3 , '' ,  0 );
				$pdf->Ln();
				$pdf->Cell( 120 , 2 , '' ,  0 );
				$pdf->Cell( 23 , 6 , '請 求 金 額', 1 , 0 , "L");
				$pdf->Cell( 26 , 6  ,  "dm_total"  . $money_unit , 1 , 0 , "R");

				$pdf->Ln();
				$pdf->Ln();

				for ( $intCnt = 0 ; $intCnt < 15 ; $intCnt++ )
				{
					$pdf->Ln();
				}




		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('example_063.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	/**
	 * DEMO : http://www.monzen.org/Refdoc/tcpdf/
	 *
	 */
	public function pdf_demo($pdf_list, $pdflist_path, $base_path)
	{

		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

		// ノーマルフォントとボールドフォントを追加
		$font_path1 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-regular.ttf';
		$font_path2 = $base_path . 'vendor/tecnickcom/tcpdf/fonts/migmix-2p-bold.ttf';
		$font = new TCPDF_FONTS();
		$font1 = $font->addTTFfont($font_path1, '', '', 32);
		$font2 = $font->addTTFfont($font_path2, '', '', 32);

		// PDFテンプレートの読み込み
		$pdf->setSourceFile($pdflist_path);
		$page = $pdf->importPage(1);                                            // PDFテンプレートの指定ページを使用する
		$pdf->useTemplate($page);

		$pdf->setCellHeightRatio(1.2);                                          // セルの行間を設定

		// PDFドキュメントプロパティ設定
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));







// set font
$pdf->SetFont('helvetica', 'B', 20);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Example of PieSector() method.');

$xc = 105;
$yc = 100;
$r = 50;

$pdf->SetFillColor(0, 0, 255);
$pdf->PieSector($xc, $yc, $r, 20, 120, 'FD', false, 0, 2);

$pdf->SetFillColor(0, 255, 0);
$pdf->PieSector($xc, $yc, $r, 120, 250, 'FD', false, 0, 2);

$pdf->SetFillColor(255, 0, 0);
$pdf->PieSector($xc, $yc, $r, 250, 20, 'FD', false, 0, 2);

// write labels
$pdf->SetTextColor(255,255,255);
$pdf->Text(105, 65, 'BLUE');
$pdf->Text(60, 95, 'GREEN');
$pdf->Text(120, 115, 'RED');






		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('example_063.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	/**
	 * HTML
	 *
	 */
	public function pdf_html($pdf_list, $pdflist_path, $base_path)
	{

 		$pdf = new FPDI();                                                      // 組み込んだらFPDIを呼び出す
		$pdf->SetMargins(0, 0, 0);                                              // PDFの余白(上左右)を設定
		$pdf->SetCellPadding(0);                                                // セルパディングの設定
		$pdf->SetAutoPageBreak(false);                                          // 自動改ページを無効(writeHTMLcellはこれを無効にしても自動改行される)
		$pdf->setPrintHeader(false);                                            // ページヘッダを無効
		//$pdf->setPrintFooter(false);                                          // ページフッタを無効
		$pdf->AddPage();                                                        // 空のページを追加

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
		$pdf->SetAuthor('lavendermarketing');
		$pdf->SetTitle('Crowd Sourcing');
		$pdf->SetSubject('Crowd Sourcing');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->SetFont($font1, '', 9);
		$pdf->Text(170, 10, "作成日:" . date("Y/m/d"));



		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();


//css
$css = '<style>
  	table {
  		text-align: left;
  		width: 100%;"
  	}
	th {
		vertical-align: middle;
		background-color: rgb(153, 153, 153);
		text-align: center;"
	}
	th.num {
		vertical-align: middle;
		background-color: rgb(153, 153, 153);
		text-align: right;"
	}
	td {
		vertical-align: middle;
		text-align: center;"
	}
	td.num {
		vertical-align: middle;
  		text-align: right;
	}
   </style>';
//html content
$html = '<div align="right">No. ＊＊＊</div>'
     . '<div align="left">□□□株式会社御中</div>'
     . '<div align="right">□□□□年□□月□□日<br />'
     . '○○株式会社<br />'
     . '○○支店○○部<br />'
     . 'tel. 030-1111-2222<br />'
     . '日本太郎</div>'
     . '<div style="text-align: center;text-decoration: underline;font-size: 16pt;font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;御請求書&nbsp;&nbsp;&nbsp;&nbsp;</div>'
     . '<div>&nbsp;&nbsp;&nbsp;&nbsp;この度は、弊社に見積の機会をお与え下さいまして誠にありがとうございます。下記の通りご請求申し上げます。<br />'
     . 'ご検討の程よろしくお願い申し上げます。</div>'
     . '<div style="text-align: left;font-size: 13pt;font-weight: bold;">納品場所：□□□□<br />'
     . '納&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;期： 2016年10月20日<br />'
     . '本請求有効期限： 本請求提出後2週間</div>'
     . '<div style="text-align: center;">'
     . '<table  border="1" cellpadding="0" cellspacing="0">'
     . '<tbody>'
     . '<tr>'
     . '<th>No</th>'
     . '<th>項目</th>'
     . '<th>単価</th>'
     . '<th>数量</th>'
     . '<th>単位</th>'
     . '<th class="num">金額</th>'
     . '<th>備考</th>'
     . '</tr>'
     . '<tr>'
     . '<td>1</td>'
     . '<td>商品A</td>'
     . '<td>□□</td>'
     . '<td>□□</td>'
     . '<td>個</td>'
     . '<td class="num">□,□□□</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">2</td>'
     . '<td>商品B</td>'
     . '<td>□□</td>'
     . '<td>□□</td>'
     . '<td>箱</td>'
     . '<td class="num">□,□□□</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">3</td>'
     . '<td>商品C</td>'
     . '<td>□□</td>'
     . '<td>□□</td>'
     . '<td>枚</td>'
     . '<td class="num">□,□□□</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">4</td>'
     . '<td>商品D</td>'
     . '<td>□□</td>'
     . '<td>□□</td>'
     . '<td>式</td>'
     . '<td class="num">□,□□□</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '<td class="num">&nbsp;</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '<tr>'
     . '<td colspan="3" rowspan="1">合&nbsp;&nbsp;計</td>'
     . '<td colspan="2" rowspan="1">&nbsp;</td>'
     . '<td class="num">□,□□□</td>'
     . '<td>&nbsp;</td>'
     . '</tr>'
     . '</tbody></table>'
     . '</div>'
     ;
//output
$pdf->writeHTML($css . $html, true, 0, true, 0);


				// ---------------------------------------------------------

				//Close and output PDF document
				$pdf->Output('example_063.pdf', 'I');

				//============================================================+
				// END OF FILE
				//============================================================+
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

    /**
     * Load table data from file
     *
     * @param array() $pdf_list
     * @return array()
     */
    public function LoadData($pdf_list)
    {

        // Read file lines
        $data = array();
        foreach($pdf_list as $line) {
            $data[] = $line;
        }
        return $data;
    }

}

// END pdf Class

/* End of file pdf.php */
/* Location: ./application/libraries/pdf.php */
