<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 請求書作成用クラス
 */
class Lib_invoice
{

	/**
	 * 請求書番号：発行 【LA101-KT-BX001-1611】
	 *
	 * @param  int  : 顧客seq
	 * @param  int  : 枝番
	 * @param  int  : 発行年月
	 * @param  int  : 売上種別
	 * @param  char : 請求種別
	 * @param  int  : 再発行
	 * @param  int  : 発行月の発行数
	 * @param  char : 一括/個別
	 * @return int
	 */
	public static function issue_num($cm_seq, $date_yymm, $salse_info, $_invo_serial_num, $invo_info, $invo_re, $invo_class)
	{

		$CI =& get_instance();

		$issue_num        = $CI->config->item('INVOICE_ISSUE_NUM');       				// 接頭語:L
		$issue_code       = $CI->lib_invoice->issue_code($cm_seq);						// 会社名かな⇒記号
		$issue_client     = $_SESSION['c_memGrp'];                          			// クライアントNO
		$issue_customer   = $cm_seq;                       								// 顧客NO
		$issue_class      = $invo_class;                                        		// 一括発行=B,個別発行=C
// 		$issue_class      = "B";                                        				// 一括発行=B,個別発行=C
		$issue_accounting = $invo_info;													// X=通常(固定、成果)/Y=前受が含む場合/Z=赤伝用請求書（マイナス）
		$issue_serial_num = $_invo_serial_num;                        					// 発行通番
		$issue_yymm       = $date_yymm;                   								// 発行年月
		$issue_re         = $invo_re;                     								// 再発行

		// 売上種別
		switch( $salse_info )
		{
			case 0:
				$issue_kind = "KT";		// SEO月額固定報酬
				break;
			case 1:
				$issue_kind = "KT";		// 月額固定報酬
				break;
			case 2:
				$issue_kind = "SK";		// 成功報酬
				break;
			case 3:
				$issue_kind = "SK";		// 固定 + 成功報酬
				break;
			case 7:
				$issue_kind = "MA";		// 保守費用
				break;
			case 8:
				$issue_kind = "OT";		// 前受取
				break;
			case 9:
				$issue_kind = "OT";		// 赤伝票
				break;
			case 10:
				$issue_kind = "AF";		// アフィリエイト
				break;
			case 11:
				$issue_kind = "KK";		// 広告運用代行
				break;
			case 12:
				$issue_kind = "OT";		// その他
				break;
			default:
				$issue_kind = "OT";
				break;
		}

		$set_number  = $issue_num
		    		 . $issue_code														// 会社名記号
		    		 . $issue_customer													// 顧客NO
		    		 . '-' . $issue_kind												// KT(SEO固定)、SK（SEO成功）
		    		 . '-' . $issue_class												// 一括発行=B,個別発行=C
		    		 . $issue_accounting												// X=通常(固定、成果)/Y=前受が含む場合/Z=赤伝用請求書（マイナス）
		    		 . str_pad($issue_serial_num, 3, 0, STR_PAD_LEFT)					// 発行通番
		    		 . '-' . substr($issue_yymm, 2, 4);									// 発行年月

		return $set_number;

	}

	/**
	 * 請求書番号：発行月の発行通番を取得
	 *
	 * @param  int  : 発行年月
	 * @return int
	 */
	public static function issue_serial_num($issue_yymm)
	{

		$CI =& get_instance();

		// レコード有無のチェック
		$CI->load->model('Issue_num', 'in', TRUE);
		$in_data = $CI->in->issue_serial_num($issue_yymm);

		if (count($in_data) == 0)
		{
			return 1;
		} else {
			return $in_data[0]['in_cnt'];
		}

	}

	/**
	 * 請求書番号：発行月の発行通番を更新
	 *
	 * @param  int  : 発行年月
	 * @param  int  : 発行番号
	 * @return int
	 */
	public static function issue_serial_num_update($issue_yymm, $serial_num)
	{

		$CI =& get_instance();

		// レコード有無のチェック
		$CI->load->model('Issue_num', 'in', TRUE);
		$in_data = $CI->in->issue_serial_num($issue_yymm);

		if (count($in_data) == 0)
		{
			// 新規作成
			$CI->in->insert_issue_num($issue_yymm, $serial_num);
		} else {
			// 更新
			$CI->in->update_issue_num($issue_yymm, $serial_num);
		}

	}

    /**
     * 消費税計算
     *
     * @param  int
     * @param  array()
     * @return int
     */
	public static function cal_tax($_subtotal, $_issue_tax)
	{

		$zeigaku = $_subtotal * $_issue_tax['zeiritsu'];

		if ($_issue_tax['zeinuki'] == 0)											// 税抜計算
		{
			// 端数計算
			switch( $_issue_tax['hasuu'] )
			{
				case 0:

					// 切り上げ
					$tax_total = $zeigaku + 0.9;
					break;

				case 1:

					// 切り捨て
					break;

				case 2:
				default:

					// 四捨五入
					$tax_total = $zeigaku + 0.5;
					break;
			}

			$result = floor( $tax_total );

		} else {																	// 税込計算

			$result = 0;

		}

		return $result;

	}

	/**
	 * 成功報酬のサブトータル金額の計算
	 *
	 * @param  int
	 * @param  array()
	 * @return int
	 */
	public static function cal_result_total($_subtotal, $_issue_tax)
	{

		if ($_issue_tax['zeinuki'] == 0)											// 税抜計算
		{
			// 端数計算
			switch( $_issue_tax['hasuu'] )
			{
				case 0:

					// 切り上げ
					$_total = $_subtotal + 0.9;
					break;

				case 1:

					// 切り捨て
					break;

				case 2:
				default:

					// 四捨五入
					$_total = $_subtotal + 0.5;
					break;
			}

			$result = floor( $_total );

		} else {																	// 税込計算

			$result = 0;

		}

		return $result;

	}

	/**
	 * 請求書番号：明細金額の再計算
	 *
	 * @param  array()
	 * @param  array()
	 * @return array()
	 */
	public static function issue_ivd_total($val, $input_post, $cnt)
	{

		$CI =& get_instance();

		// 成功報酬かのチェック
		$_tmp_item = 'qty' . ($cnt + 1);
		if ($val['ivd_iv_accounting'] == 2)
		{

			// 日数での計算
			$_create_date = substr($val['ivd_iv_issue_yymm'], 0, 4) . '-' . substr($val['ivd_iv_issue_yymm'], 4, 2);
			$date = new DateTime($_create_date);
			$_nisuu = date($date->modify('-1 months')->format('t'));										// 前月の日数
			$total = $val['ivd_price'] * ($input_post[$_tmp_item] / $_nisuu);

			$_issue_tax['zeiritsu'] = $CI->config->item('INVOICE_TAX');
			$_issue_tax['zeinuki']  = $CI->config->item('INVOICE_TAXOUT');
			$_issue_tax['hasuu']    = $CI->config->item('INVOICE_TAX_CAL');									// 四捨五入で計算

			$CI->load->library('lib_invoice');
			$total = $CI->lib_invoice->cal_result_total($total, $_issue_tax);

		} else {

			// 数量での計算
			$total = $val['ivd_price'] * $input_post[$_tmp_item];

		}

		return $total;

	}

	/**
	 * 請求書発行：回収サイクルより契約スタート月日とEND月日を求める
	 *
	 * @param  date()
	 * @param  date()
	 * @param  date()
	 * @param  int()
	 * @return array()
	 */
	public static function issue_project_date($issue_yymm, $start_date, $end_date, $cm_collect)
	{

		switch( $cm_collect )
		{
			case 0:					// 指定なし → 月末締め当月末

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of this months')->format('Y-m-d');		// 請求データ作成の指定年月の当月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			case 1:					// 月末締め当月末

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of this months')->format('Y-m-d');		// 請求データ作成の指定年月の当月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			case 2:					// 月末締め翌月末

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			case 3:					// 月末締め翌々月10日

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			case 4:					// 月末締め翌々月15日

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			case 5:					// 月末締め翌々月25日

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			case 6:					// 月末締め翌々月末

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

			default:				// エラー　→　月末締め当月末

				$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
				$date_now = new DateTime($_create_date);
				$project_date['now'] = $date_now->modify('first day of this months')->format('Y-m-d');		// 請求データ作成の指定年月の当月1日
				$date_str = new DateTime($start_date);
				$project_date['str'] = $date_str->format('Y-m-d');
				$date_end = new DateTime($end_date);
				$project_date['end'] = $date_end->format('Y-m-d');

				break;

		}

		return $project_date;

	}

	/**
	 * 請求書発行：振込期日＆月度計算
	 *
	 * @param  int
	 * @param  date
	 * @return array()
	 */
	public static function issue_collect($cm_collect, $issue_yymm)
	{

		$_create_date = substr($issue_yymm, 0, 4) . '-' . substr($issue_yymm, 4, 2);
		$date = new DateTime($_create_date);

		switch( $cm_collect ){																// 振込期日 ← 発行日基準
			case 0:
				// 指定なし　→　月末締め当月末
				$collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
				$collect_date['salse_yymm'] = $issue_yymm;
				break;
			case 1:
				// 月末締め当月末
				$collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
				$collect_date['salse_yymm'] = $issue_yymm;
				break;
			case 2:
				// 月末締め翌月末
				$collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
				$collect_date['salse_yymm'] = $date->modify('last day of last months')->format('Ym');
				break;
			case 3:
				// 月末締め翌々月10日
				$collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-10');
				$collect_date['salse_yymm'] = $date->modify('-2 months')->format('Ym');

// 				$set_data_iv['iv_pay_date'] = $date->modify('+2 months')->format('Y-m-10');
				break;
			case 4:
				// 月末締め翌々月15日
				$collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-15');
				$collect_date['salse_yymm'] = $date->modify('-2 months')->format('Ym');

// 				$set_data_iv['iv_pay_date'] = $date->modify('+2 months')->format('Y-m-15');
				break;
			case 5:
				// 月末締め翌々月25日
				$collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-25');
				$collect_date['salse_yymm'] = $date->modify('-2 months')->format('Ym');

// 				$set_data_iv['iv_pay_date'] = $date->modify('+2 months')->format('Y-m-25');
				break;
			case 6:
				// 月末締め翌々月末
				$collect_date['pay_date']   = $date->modify('last day of next months')->format('Y-m-d');
				$collect_date['salse_yymm'] = $date->modify('-2 months')->format('Ym');

// 				$_date_y = $date->modify('+3 months')->format('Y');
// 				$_date_m = $date->format('m');
// 				$_lastdate = new DateTime(date('Y-m-d', mktime(0, 0, 0, $_date_m, 0 , $_date_y)));
// 				$set_data_iv['iv_pay_date'] = $_lastdate->format('Y-m-d');
				break;
			default:
				// エラー　→　月末締め当月末
				$collect_date['pay_date']   = $date->modify('last day of this months')->format('Y-m-d');
				$collect_date['salse_yymm'] = $issue_yymm;

// 				$set_data_iv['iv_pay_date'] = $date->modify('last day of next months')->format('Y-m-d');
		}

		return $collect_date;

	}

	/**
	 * 請求書番号：会社名から記号変換
	 *
	 * @param  array()
	 * @return int
	 */
	public static function issue_code($cm_seq)
	{

		$CI =& get_instance();

		$CI->load->model('Customer', 'cm', TRUE);
		$cm_data = $CI->cm->get_cm_seq($cm_seq);

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
				"ポ" => "H5"
		);

		$moji = mb_substr($cm_data[0]['cm_company_kana'], 0, 1);

		if (preg_match("/^[ァ-ヶー]+$/u", $moji))									// 全角カナチェック
		{
			$get_code = $kana[$moji];
		} else {
			$get_code = "X0";
		}

		return $get_code;

	}

}