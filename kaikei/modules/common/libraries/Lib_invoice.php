<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 請求書作成用クラス
 */
class Lib_invoice
{

	/**
	 * 請求書番号：発行
	 *
	 * @param  array()
	 * @return int
	 */
	public static function issue_num($_issue_num)
	{

		$set_number  = $_issue_num['issue_num']
		    		 . $_issue_num['issue_code']									// 会社名記号
		    		 . $_issue_num['issue_customer']								// 顧客NO
		    		 . '-' . $_issue_num['issue_kind']								// KT(SEO固定)、SK（SEO成功）
		    		 . '-' . $_issue_num['issue_class']								// 一括発行=B,個別発行=C
		    		 . $_issue_num['issue_accounting']								// X=通常(固定、成果)/Y=前受が含む場合/Z=赤伝用請求書（マイナス）
		    		 . str_pad($_issue_num['issue_suffix'], 3, 0, STR_PAD_LEFT)		// 枝番
		    		 . '-' . substr($_issue_num['issue_yymm'], 2, 4);				// 発行年月

		return $set_number;

	}

    /**
     * 消費税計算
     *
     * @param  int
     * @param  int
     * @param  int
     * @param  int
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
			case 0:					// 指定なし　→　月末締め当月末

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






// 		$_create_date = substr($input_post['iv_issue_yymm'], 0, 4) . '-' . substr($input_post['iv_issue_yymm'], 4, 2);
// 		$date_now = new DateTime($_create_date);
// 		$date_now = $date_now->modify('first day of this months')->format('Y-m-d');		// 請求データ作成の指定年月の当月1日
// 		$date_str = new DateTime($val['pj_start_date']);
// 		$date_str = $date_str->format('Y-m-d');
// 		$date_end = new DateTime($val['pj_end_date']);
// 		$date_end = $date_end->format('Y-m-d');

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
				$collect_date['pay_date']   = $date->modify('first day of next months')->format('Y-m-d');
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
				"ア" => "A10",
				"イ" => "A20",
				"ウ" => "A30",
				"エ" => "A40",
				"オ" => "A50",
				"カ" => "K10",
				"キ" => "K20",
				"ク" => "K30",
				"ケ" => "K40",
				"コ" => "K50",
				"サ" => "S10",
				"シ" => "S20",
				"ス" => "S30",
				"セ" => "S40",
				"ソ" => "S50",
				"タ" => "T10",
				"チ" => "T20",
				"ツ" => "T30",
				"テ" => "T40",
				"ト" => "T50",
				"ナ" => "N10",
				"ニ" => "N20",
				"ヌ" => "N30",
				"ネ" => "N40",
				"ノ" => "N50",
				"ハ" => "H10",
				"ヒ" => "H20",
				"フ" => "H30",
				"ヘ" => "H40",
				"ホ" => "H50",
				"マ" => "M10",
				"ミ" => "M20",
				"ム" => "M30",
				"メ" => "M40",
				"モ" => "M50",
				"ヤ" => "Y10",
				"ユ" => "Y30",
				"ヨ" => "Y50",
				"ラ" => "R10",
				"リ" => "R20",
				"ル" => "R30",
				"レ" => "R40",
				"ロ" => "R50",
				"ワ" => "Y10",
				"ヲ" => "O10",
				"ン" => "O20",
				"ガ" => "K10",
				"ギ" => "K20",
				"グ" => "K30",
				"ゲ" => "K40",
				"ゴ" => "K50",
				"ザ" => "S10",
				"ジ" => "S20",
				"ズ" => "S30",
				"ゼ" => "S40",
				"ゾ" => "S50",
				"ダ" => "T10",
				"ヂ" => "T20",
				"ヅ" => "T30",
				"デ" => "T40",
				"ド" => "T50",
				"バ" => "H10",
				"ビ" => "H20",
				"ブ" => "H30",
				"ベ" => "H40",
				"ボ" => "H50",
				"パ" => "H10",
				"ピ" => "H20",
				"プ" => "H30",
				"ペ" => "H40",
				"ポ" => "H50"
		);

		$moji = mb_substr($cm_data[0]['cm_company_kana'], 0, 1);

		if (preg_match("/^[ァ-ヶー]+$/u", $moji))									// 全角カナチェック
		{
			$get_code = $kana[$moji];
		} else {
			$get_code = "X00";
		}

		return $get_code;

	}

}