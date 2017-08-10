<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO検索用クラス
 */
class Lib_searchrank_data
{


	/**
	 * グラフ用順位データ情報の取得
	 *
	 * @param  int
	 * @param  date
	 * @return char
	 */
	public static function create_rank_graph($kw_list, $term)
	{

		$CI =& get_instance();

		// 全件取得時のみ
		$set_comp_date = array();
		if ($term == 0)
		{
			// 前日＆1週間前＆1ヶ月前の順位比較
			$date = new DateTime();
			$set_comp_date['comp_today']     = $date->format('Y-m-d');
			$set_comp_date['comp_yesterday'] = $date->modify('-1 day')->format('Y-m-d');
			$set_comp_date['comp_week']      = $date->modify('-1 weeks')->format('Y-m-d');
			$set_comp_date['comp_month']     = $date->modify('-1 months')->format('Y-m-d');
		}


		// グラフ用データ取得
		$_line_cnt = 0;														// キーワード一覧表示用
		foreach ($kw_list as $key => $value)
		{
			$CI->lib_searchrank_data->_get_rank_graph($value['kw_seq'], $term, $_line_cnt, $set_comp_date);
			$_line_cnt++;
		}
	}

	/**
	 * 順位データ集計 （グラフ用/テーブル用）
	 * jqPlot.js を使用
	 *
	 * @param  int
	 * @param  date
	 * @return char
	 */
	public static function _get_rank_graph($kw_seq, $term, $line_cnt, $set_comp_date)
	{

		$CI =& get_instance();
		$CI->load->model('Ranking', 'rk', TRUE);
		$CI->load->model('Keyword', 'kw', TRUE);

		// キーワード設定情報を取得
		$get_kw_data =$CI->kw->get_kw_seq($kw_seq);

		// 順位データ情報の取得期間
		$date = new DateTime();
		if ($term == 0)
		{
			$_start_term = $date->format('Y-m-d');
			$date_end    = date_create($get_kw_data[0]['kw_create_date']);
			$_end_term   = date_format($date_end, 'Y-m-d');
		} else {
			$_set_cnt_date = "- " . ($term - 1) . " months";
			$_end_term     = $date->modify($_set_cnt_date)->format('Y-m-01');
			$_start_term   = $date->format('Y-m-t');
		}

		$_start_term_date = new DateTime($_start_term);
		$_end_term_date   = new DateTime($_end_term);

		$diff_date = $_end_term_date->diff($_start_term_date);
		$_date_cnt = $diff_date->format('%a') + 1;

		// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
		$set_kw_pair['kw_cl_seq']      = $get_kw_data[0]['kw_cl_seq'];
		$set_kw_pair['kw_url']         = $get_kw_data[0]['kw_url'];
		$set_kw_pair['kw_domain']      = $get_kw_data[0]['kw_domain'];
		$set_kw_pair['kw_rootdomain']  = $get_kw_data[0]['kw_rootdomain'];
		$set_kw_pair['kw_keyword']     = $get_kw_data[0]['kw_keyword'];
		$set_kw_pair['kw_matchtype']   = $get_kw_data[0]['kw_matchtype'];
		$set_kw_pair['kw_location_id'] = $get_kw_data[0]['kw_location_id'];

		$get_kw_pair =$CI->kw->get_kw_info($set_kw_pair);

		// グラフ＆テーブルデータの取得
		foreach ($get_kw_pair as $key => $value)
		{

			$res = $CI->lib_searchrank_data->_jqplot_data($value, $_start_term, $_end_term, $_date_cnt, $line_cnt, $set_comp_date);

		}

		return TRUE;

	}

	/**
	 * グラフ作成用データ取得 : jqPlot
	 *
	 * @param  array()
	 * @param  date
	 * @param  date
	 * @param  int
	 * @param  int
	 * @return
	 */
	private function _jqplot_data($set_kw_data, $start_date, $end_date, $date_cnt, $line_cnt, $set_comp_date)
	{

		$CI =& get_instance();

		// ランキングデータを取得
		$get_rk_data = $CI->rk->get_kw_seq($set_kw_data['kw_seq'], $start_date, $end_date);

		$date = new DateTime($end_date);
		$_getdate   = $end_date;
		$_cnt_rk    = 0;													// 順位データの配列カウンター
		$_plot_data = "[";													// jqPlot グラフデータ
		$_x_data    = "x";													// X軸データ（日付）用配列。"x"は接頭語として後で外す。
		$_y_data    = "y";													// Y軸データ（順位）用配列。"y"は接頭語として後で外す。
		for ($cnt = ($date_cnt-1); $cnt >= 0; $cnt--)
		{

			$_x_data .= ',' . $date->format('Y-m-d');
			//$_x_data .= ',' . $date->format('d');
			$_tmp_day = $date->format('Y-m-d');
			$_plot_data .= "['" . $_tmp_day . " 0:00AM',";

			if ((!empty($get_rk_data[$_cnt_rk])) && ($get_rk_data[$_cnt_rk]['rk_getdate'] == $_getdate))
			{

				// 順位が300位以内
				if ($get_rk_data[$_cnt_rk]['rk_position'] <= 300)
				{
					$_tmp_rank_data = $get_rk_data[$_cnt_rk]['rk_position'];
					$_plot_data    .= $_tmp_rank_data . "],";
					$_y_data       .=  ',' . $_tmp_rank_data;
				} else {
					$_tmp_rank_data = 300;
					$_plot_data    .= "300],";
					$_y_data       .=  ',' . "";
				}

				++$_cnt_rk;

			} else {
				$_tmp_rank_data = 300;
				$_plot_data    .= "300],";
				$_y_data       .=  ',';
			}

			// 全件取得時のみ
			if (!empty($set_comp_date))
			{
				// 前日＆1週間前＆1ヶ月前の順位比較
				if ($_tmp_day == $set_comp_date['comp_today'])
				{
					$_tmp_comp_rank['comp_today'] = $_tmp_rank_data;
				} elseif ($_tmp_day == $set_comp_date['comp_yesterday']) {
					$_tmp_comp_rank['comp_yesterday'] = $_tmp_rank_data;
				} elseif ($_tmp_day == $set_comp_date['comp_week']) {
					$_tmp_comp_rank['comp_week'] = $_tmp_rank_data;
				} elseif ($_tmp_day == $set_comp_date['comp_month']) {
					$_tmp_comp_rank['comp_month'] = $_tmp_rank_data;
				} else {

				}
			}




			$_getdate = $date->modify('+1 days')->format('Y-m-d');
		}

		// *** グラフ用データ
		if (($set_kw_data['kw_searchengine'] == 0) && ($set_kw_data['kw_device'] == 0))
		{
			// google-PC
			$term = $line_cnt . "00";
		} elseif ($set_kw_data['kw_searchengine'] == 1) {
			// Yahoo!
			$term = $line_cnt . "10";
		} else {
			// google-Mobile
			$term = $line_cnt . "01";
		}

		$_plot_data = rtrim($_plot_data, ",") . "]";



// 		print($term);
// 		print("<br>");
// 		print_r($_plot_data);
// 		print("<br><br>");



		$CI->smarty->assign('plot_data' . $term, $_plot_data);
		$CI->smarty->assign('plot_start_date',   $end_date);
		$CI->smarty->assign('plot_end_date',     $start_date);
		$CI->smarty->assign('plot_cnt',          $date_cnt);

		// *** テーブル用データ
		$_x_data = str_replace("x,", "", $_x_data);
		$_y_data = str_replace("y,", "", $_y_data);

		$_tbl_x_data = explode(",", $_x_data);
		$_tbl_y_data = explode(",", $_y_data);



// 		print($term);
// 		print("<br>");
// 		print_r($_tbl_x_data);
// 		print("<br>");
// 		print_r($_tbl_y_data);
// 		print("<br><br>");



		$CI->smarty->assign('tbl_x_data' . $term, $_tbl_x_data);
		$CI->smarty->assign('tbl_y_data' . $term, $_tbl_y_data);



		// 前日＆1週間前＆1ヶ月前の順位比較

// 		print($term);
// 		print("<br>");
// 		print_r($_tmp_comp_rank['comp_today']);
// 		print(" - ");
// 		print_r($_tmp_comp_rank['comp_yesterday']);
// 		print(" = ");
// 		print_r($_tmp_comp_rank['comp_yesterday']-$_tmp_comp_rank['comp_today']);
// 		print("<br><br>");
// 		var_dump($_tmp_comp_rank);

		if (isset($term, $_tmp_comp_rank['comp_today']))
		{
			$CI->smarty->assign('comp_today' . $term, $_tmp_comp_rank['comp_today']);

			if (isset($term, $_tmp_comp_rank['comp_yesterday']))
			{
				$CI->smarty->assign('comp_yesterday' . $term, ($_tmp_comp_rank['comp_yesterday'] - $_tmp_comp_rank['comp_today']));
			} else {
				$CI->smarty->assign('comp_yesterday' . $term, "-");
			}
			if (isset($term, $_tmp_comp_rank['comp_week']))
			{
				$CI->smarty->assign('comp_week' . $term, $_tmp_comp_rank['comp_week'] - $_tmp_comp_rank['comp_today']);
			} else {
				$CI->smarty->assign('comp_week' . $term, "-");
			}
			if (isset($term, $_tmp_comp_rank['comp_month']))
			{
				$CI->smarty->assign('comp_month' . $term, $_tmp_comp_rank['comp_month'] - $_tmp_comp_rank['comp_today']);
			} else {
				$CI->smarty->assign('comp_month' . $term, "-");
			}

		} else {
			$CI->smarty->assign('comp_today' . $term,     "-");
			$CI->smarty->assign('comp_yesterday' . $term, "-");
			$CI->smarty->assign('comp_week' . $term,      "-");
			$CI->smarty->assign('comp_month' . $term,     "-");
		}

		return TRUE;
	}

}