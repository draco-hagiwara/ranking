<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO検索用クラス
 */
class Lib_ranking_data
{


	/**
	 * 検索用URLを整形する
	 *
	 * @param  array()
	 * @param  char
	 * @return char
	 */
	public static function create_seach_url($list)
	{

		$CI =& get_instance();
		$CI->config->load('config_comm');

		$_url     = $CI->config->item('API_URL');
		$_code    = $CI->config->item('API_CODE');

		$_keyword = mb_convert_encoding($list['kw_keyword'], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');;

		$_engine  = $CI->config->item('API_SEARCH_ENGINE');
		$_device  = $CI->config->item('API_DEVICE');
		$_page    = $CI->config->item('API_PAGE');
		$_cashe   = $CI->config->item('API_CASHE');
		$_debug   = $CI->config->item('API_DEBAG');



		$_api_url = $_url . '?code=' .          $_code
						  . '&keyword=' .       urlencode($_keyword)
						  . '&search_engine=' . $_engine[$list['kw_searchengine']]
						  . '&device=' .        $_device[$list['kw_device']]
						  . '&location=' .      urlencode($list['kw_location_name'])
						  . '&page=' .          $_page[$list['kw_maxposition']]
// 						  . '&page=2'
						  . '&cashe=' .         $_cashe
						  . '&debag=' .         $_debug
		;



// 		print($_api_url);
// 		print("<br><br>");


		return $_api_url;

	}


	/**
	 * 検索結果のデータを取得する
	 *
	 * @param  char
	 * @return array()
	 */
	public static function get_seach_url($url)
	{

		$CI =& get_instance();
		$CI->load->library('lib_ranking_data');

		//「$http_response_header」の初期化
		$http_response_header = array();

		//file_get_contents関数でデータを取得
		if ($get_json = @file_get_contents($url))
		{

			// データ取得が成功
			$json = mb_convert_encoding($get_json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

			$get_data = json_decode($json, true);

			if ($get_data["api_status"] == 'error')
			{
				// 取得失敗
				$err_mess = "get-data-error";
				return array($err_mess, "");
			}

			if ($get_data === NULL)
			{
				// データがない
				$err_mess = "no-data";
				return array($err_mess, "");
			} else {

				// 取得OK
				$err_mess = $get_data["api_status"];
				return array($err_mess, $get_data["result"]["organic"]);
			}

		} else {

			//エラー処理
			if (count($http_response_header) > 0)
			{
				//「$http_response_header[0]」にはステータスコードがセットされているのでそれを取得
				//「$status_code[1]」にステータスコードの数字のみが入る
				$status_code = explode(' ', $http_response_header[0]);

				//エラーの判別
				switch ($status_code[1])
				{
					//404エラーの場合 : 指定したページが見つかりませんでした
					case 404:
						$err_mess = "error:404";
						break;

					//500エラーの場合 : 指定したページがあるサーバーにエラーがあります
					case 500:
						$err_mess = "error:500";
						break;

					//その他のエラーの場合 : 何らかのエラーによって指定したページのデータを取得できませんでした
					default:
						$err_mess = "error:other";
				}
			} else {
				//タイムアウトの場合 or 存在しないドメインだった場合
				$err_mess = "error:timeout or nodomain";
			}

			return array($err_mess, "");
		}


		return array($err_mess, "");


	}



	/**
	 * 検索＆順位取得を実行する
	 *
	 * @param  array()
	 * @param  int
	 * @return int
	 */
	public static function exec_ranking($get_kw_data, $cnt)
	{

		/*
		 * 1回目に順位取得に成功しているキーワードは2回目以降はスキップ。
		 * プラスして1日の取得回数をチェックする。
		 */


		$CI =& get_instance();

		// 対象となるKEYWORDデータのみ取得
		$date = new DateTime();
		$_start_date = $date->format('Y-m-d');
		$_start_time = $date->format('H:i:s');

		$_item_b     = '';													// keyword+searchengine+device+location
		$_get_cnt    = $cnt + 1;											// 実行回数。
		$_search_cnt = 0;													// 検索対象データ数
		$_rank_cnt   = 0;													// 順位取得データ数
		foreach ($get_kw_data as $key => $value)
		{

			if ($_get_cnt == 1)
			{

				// 初回実行時は無条件で順位取得（INSERT）する。

			} else {

				// 二回目以降は、順位が取得されていないデータを再取得する。

				$get_rk_position = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
				if ((count($get_rk_position) > 0) && ($get_rk_position[0]['rk_position']) < 9999)
				{
					continue;
				}
			}

			$_item_a = $value['kw_keyword']
						. $value['kw_searchengine']
						. $value['kw_device']
						. $value['kw_location_id']
			;

			if ($_item_b != $_item_a)
			{

				// 検索データ取得用URL情報をセット
				$_set_url['kw_keyword']       = $value['kw_keyword'];
				$_set_url['kw_searchengine']  = $value['kw_searchengine'];
				$_set_url['kw_device']        = $value['kw_device'];
				$_set_url['kw_location_name'] = $value['kw_location_name'];
				$_set_url['kw_maxposition']   = $value['kw_maxposition'];

				// URL整形
				$_url = $CI->lib_ranking_data->create_seach_url($_set_url);

				// 検索データを取得
				list($err_mess, $get_serach_data) = $CI->lib_ranking_data->get_seach_url($_url);

				// 検索データセット
				$_set_search_data = array();
				$_item = $_item_a . $_get_cnt . $_start_date . $_start_time;
				$_set_search_data['se_result_id']     = md5($_item);
				$_set_search_data['se_keyword']       = $value['kw_keyword'];
				$_set_search_data['se_searchengine']  = $value['kw_searchengine'];
				$_set_search_data['se_device']        = $value['kw_device'];
				$_set_search_data['se_location_id']   = $value['kw_location_id'];
				$_set_search_data['se_location_name'] = $value['kw_location_name'];
				$_set_search_data['se_maxposition']   = $value['kw_maxposition'];
				$_set_search_data['se_getcnt']        = $_get_cnt;

				if ($err_mess == 'success')
				{

					// 検索データを書き込み
					$no = 1;
					foreach ($get_serach_data as $key01 => $val)
					{

						$_set_search_data['se_position']  = $key01;
						$_set_search_data['se_url']       = $val['url'];
						$_set_search_data['se_title']     = $val['title'];
						$_set_search_data['se_domain']    = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $val['url']);

						/*
						 * Fatal error: Maximum execution time of 30 seconds exceeded
						 *
						 * /opt/lampp/etc/php.ini
						 *    max_execution_time = 180
						 *
						 * 1KW, 300件取得で、2分30秒前後
						 * rootdomain変換をなくして、1KW, 300件取得で、15秒前後
						 *
						 */
						//$_rootdomain = $CI->lib_rootdomain->get_rootdomain($val['url']);
						//$_set_search_data['se_rootdomain'] = $_rootdomain['rootdomain'];

						$_set_search_data['se_getdate']    = $_start_date;
						$_set_search_data['se_gettime']    = $_start_time;

						// INSERT
						$CI->sp->insert_seach_data($_set_search_data);

						$no++;

					}
				} else {

					// 'error' 処理はどうしよう？
					// INSERT
					$CI->sp->insert_seach_data($_set_search_data);

					// LOG に書き出し
					$set_log['lg_func']   = 'Lib_ranking_data';
					$set_log['lg_detail'] = 'ERROR :: ' . $err_mess . ' <= exec_ranking';
					$CI->sp->insert_log($set_log);

				}

				$_search_cnt++;

			}

			// 順位データセット
			$set_ranking_data = array();
			$set_ranking_data['rk_cl_seq']   = $value['kw_cl_seq'];
			$set_ranking_data['rk_kw_seq']   = $value['kw_seq'];
			$set_ranking_data['rk_getdate']  = $_start_date;
			if ($err_mess == 'success')
			{
				$set_ranking_data['rk_position'] = 9999;							// 順位データなし。 9999 < 90009
			} else {
				$set_ranking_data['rk_position'] = 90009;							// エラーのため検索データなし
			}

			// 順位データの取得
			$get_rank = $CI->lib_ranking_data->get_ranking($_set_search_data, $value);
			if (count($get_rank) > 0)
			{

				$set_ranking_data['rk_se_seq']        = $get_rank[0]['se_seq'];
				$set_ranking_data['rk_result_id']     = $get_rank[0]['se_result_id'];
				$set_ranking_data['rk_position']      = $get_rank[0]['se_position'];
				$set_ranking_data['rk_ranking_url']   = $get_rank[0]['se_url'];
				$set_ranking_data['rk_ranking_title'] = $get_rank[0]['se_title'];

				if ($_get_cnt == 1)
				{
					// INSERT
					$CI->rk->insert_ranking($set_ranking_data);

				} else {

					// 順位比較のため既存データの読み込み
					$get_rank_kw = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
					if (count($get_rank_kw) > 0)
					{

						// 既存順位より上位の場合書き換える
						if ($get_rank[0]['se_position'] < $get_rank_kw[0]['rk_position'])
						{
							// UPDATE
							$set_ranking_data['rk_seq'] = $get_rank_kw[0]['rk_seq'];
							$CI->rk->update_ranking($set_ranking_data, $get_rank_kw[0]['rk_seq']);
						}

					} else {

						// INSERT
						$CI->rk->insert_ranking($set_ranking_data);
					}
				}

			} else {

				if ($_get_cnt == 1)
				{
					// INSERT
					$CI->rk->insert_ranking($set_ranking_data);
				} else {

					// 順位比較のため既存データの読み込み
					$get_rank_kw = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
					if (count($get_rank_kw) == 0)
					{
						// INSERT
						$CI->rk->insert_ranking($set_ranking_data);
					}
				}
			}

			$_item_b = $_item_a;
			$_rank_cnt++;
		}

		return array($_get_cnt, $_search_cnt, $_rank_cnt);
	}


	/**
	 * SEO順位を取得する
	 *
	 * @param  char
	 * @param  char
	 * @param  int
	 * @return int
	 */
	public static function get_ranking($search_data, $kw_data)
	{

		$CI =& get_instance();
		$CI->load->model('Serps', 'sp', TRUE);

		// URLマッチタイプ別に処理を分ける
		switch( $kw_data['kw_matchtype'] )
		{
			case 3:
				// ルートドメイン一致（サブドメイン含む）
				/*
				 * rootdomainと完全一致するか否か
				 */

				$get_serps_data = $CI->sp->get_seo_rank3($search_data['se_result_id'], $kw_data['kw_rootdomain']);


// 				print("<br><br>ルートドメイン一致 ::<br>");

// 				print("search_data ::");
// 				print_r($search_data['se_result_id']);
// 				print("<br>");
// 				print("kw_data ::");
// 				print_r($kw_data['kw_rootdomain']);
// 				print("<br>");

// 				print_r($get_serps_data);
// 				print("<br><br>");




				return $get_serps_data;

				break;

			case 2:
				// ドメイン一致
				/*
				* ホストだけにして完全一致するか比較
				*/

				$get_serps_data = $CI->sp->get_seo_rank2($search_data['se_result_id'], $kw_data['kw_domain']);


// 				print("<br><br>ドメイン一致 ::<br>");

// 				print("search_data ::");
// 				print_r($search_data['se_result_id']);
// 				print("<br>");
// 				print("kw_data ::");
// 				print_r($kw_data['kw_domain']);
// 				print("<br>");

// 				print_r($get_serps_data);
// 				print("<br><br>");



				return $get_serps_data;

				break;

			case 0:
				// URL完全一致
				/*
				 * プロトコル・www取り除いた後に完全一致するか否か
				 */

				$get_serps_data = $CI->sp->get_seo_rank0($search_data['se_result_id'], $kw_data['kw_domain'], $kw_data['kw_url']);


// 				print("<br><br>URL完全一致 ::<br>");

// 				print("search_data ::");
// 				print_r($search_data['se_result_id']);
// 				print("<br>");
// 				print("kw_data ::");
// 				print_r($kw_data['kw_domain']);
// 				print("<br>");
// 				print_r($kw_data['kw_url']);
// 				print("<br>");

// 				print_r($get_serps_data);
// 				print("<br><br>");



				return $get_serps_data;

				break;

			case 1:
				// URL部分一致
				/*
				 * プロトコル・www取り除いた後に前方一致するかどうか
				 */

			default:

				$get_serps_data = $CI->sp->get_seo_rank1($search_data['se_result_id'], $kw_data['kw_domain'], $kw_data['kw_url']);


// 				print("<br><br>URL部分一致 ::<br>");

// 				print("search_data ::");
// 				print_r($search_data['se_result_id']);
// 				print("<br>");
// 				print("kw_data ::");
// 				print_r($kw_data['kw_domain']);
// 				print("<br>");
// 				print_r($kw_data['kw_url']);
// 				print("<br>");

// 				print_r($get_serps_data);
// 				print("<br><br>");




				return $get_serps_data;

		}

		return FALSE;

	}


	/**
	 * URLからドメインを抜き出す
	 *
	 * @param  char
	 * @return char
	 */
 	public static function get_domain_extract($url)
	{

		$CI =& get_instance();

		//URLをパースして分解する
		$parse_url=parse_url($url);

		if (!isset($parse_url["host"]))
		{
			print($url);
			print(":: NO-URL<br>");

			return "";
		}

		//$parse_url["host"]にドメイン・サブドメインの部分がパースされて格納される
		$url_host = array_reverse(explode('.', $parse_url["host"]));
		switch( count($url_host) )
		{
			case 0:
				$message .= $url_host[0] . "はドメインではありません。";
			case 1:
				break;
			case 2:
				$res = preg_match('/^(co|or|gr|ne|go|lg|ac|ed|ad)$/', $url_host[1], $matches);
				if ($res == 0)
				{
					$url_domain = $url_host[1] . '.' . $url_host[0];
				}
				break;
			default:
				$res = preg_match('/^(co|or|gr|ne|go|lg|ac|ed|ad)$/', $url_host[1], $matches);
				if ($res == 0)
				{
					$url_domain = $url_host[1] . '.' . $url_host[0];
				} else {
					$url_domain = $url_host[2] . '.' . $url_host[1] . '.' . $url_host[0];
				}
		}

		return $url_domain;

	}

	/**
	 * 順位データ情報の取得
	 *
	 * @param  int
	 * @param  date
	 * @return char
	 */
	public static function create_ranking_graph($kw_list, $cnt_date)
	{

		$CI =& get_instance();
// 		$CI->load->model('Ranking', 'rk', TRUE);
// 		$CI->load->library('lib_ranking_data');

// 		$cnt_date = 31;
// 		$date = new DateTime();
// 		$_start_date = $date->format('Y-m-d');
// 		$_set_cnt_date = "- " . $cnt_date+1 . " days";
// 		$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

		foreach ($kw_list as $key => $value)
		{
			$CI->lib_ranking_data->get_ranking_graph($value['kw_seq'], $cnt_date);
		}

	}

	/**
	 * 順位データ集計 （グラフ用/テーブル用）
	 * chart.jp を使用
	 *
	 * @param  int
	 * @param  date
	 * @return char
	 */
	public static function get_ranking_graph($kw_seq, $cnt_date=31)
	{

		$CI =& get_instance();
		$CI->load->model('Ranking', 'rk', TRUE);

		// 順位データ情報を取得 (31日分)
		$date = new DateTime();
		$_start_date = $date->format('Y-m-d');
		$_set_cnt_date = "- " . $cnt_date . " days";
		$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

		$get_rk_data = $CI->rk->get_kw_seq($kw_seq, $_start_date, $_end_date);

		$_cnt_rk = 0;														// 順位データの配列カウンター
		$_x_data[$kw_seq] = "x";											// X軸データ（日付）用配列。"x"は接頭語として後で外す。
		$_y_data[$kw_seq] = "y";											// Y軸データ（順位）用配列。"y"は接頭語として後で外す。
		for ($cnt = $cnt_date; $cnt > 0; $cnt--)
		{

			$_getdate = $date->modify('+1 days')->format('Y-m-d');
			$_x_data[$kw_seq] .= ',' . $date->format('d');

			if ((isset($get_rk_data[$_cnt_rk])) && ($get_rk_data[$_cnt_rk]['rk_getdate'] == $_getdate))
			{

				// 順位が300位以内
				if ($get_rk_data[$_cnt_rk]['rk_position'] <= 300)
				{
					$_y_data[$kw_seq] .=  ',' . $get_rk_data[$_cnt_rk]['rk_position'];
				} else {
					$_y_data[$kw_seq] .=  ',' . "";
				}

				$_cnt_rk++;

			} else {
				$_y_data[$kw_seq] .=  ',';
			}
		}

		// グラフ用データ
		$_x_data[$kw_seq] = str_replace("x,", "", $_x_data[$kw_seq]);
		$_y_data[$kw_seq] = str_replace("y,", "", $_y_data[$kw_seq]);
		$CI->smarty->assign('x_data' . $kw_seq, $_x_data[$kw_seq]);
		$CI->smarty->assign('y_data' . $kw_seq, $_y_data[$kw_seq]);

		$_tbl_x_data[$kw_seq] = explode(",", $_x_data[$kw_seq]);
		$_tbl_y_data[$kw_seq] = explode(",", $_y_data[$kw_seq]);

		// テーブル用データ
		$CI->smarty->assign('tbl_x_data' . $kw_seq, $_tbl_x_data[$kw_seq]);
		$CI->smarty->assign('tbl_y_data' . $kw_seq, $_tbl_y_data[$kw_seq]);

		return array($_x_data, $_y_data);

	}

	/**
	 * レポート作成：順位データ情報の取得＆集計 （グラフ用/テーブル用１件でーた）
	 * morris.js を使用
	 *
	 * @param  int
	 * @param  int
	 * @return char
	 */
	public static function create_report_graph($kw_seq, $cnt_date=31)
	{

		$CI =& get_instance();
		$CI->load->model('Ranking', 'rk', TRUE);

		// 順位データ情報を取得 (31日分)
		$date = new DateTime();
		$_start_date = $date->format('Y-m-d');
		$_set_cnt_date = "- " . $cnt_date . " days";
		$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

		$get_rk_data = $CI->rk->get_kw_seq($kw_seq, $_start_date, $_end_date);

		$i = 0;
		$_cnt_rk = 0;														// 順位データの配列カウンター
		$_tbl_x_data = array();												// X軸データ（日付）用配列。"x"は接頭語として後で外す。
		$_tbl_y_data = array();												// Y軸データ（順位）用配列。"y"は接頭語として後で外す。
		$_graph_data = array();
		for ($cnt = $cnt_date; $cnt > 0; $cnt--)
		{

			$_getdate = $date->modify('+1 days')->format('Y-m-d');
			$_data_d  = $date->format('d');
			$_tbl_x_data[$i] = $_data_d;

			if ((isset($get_rk_data[$_cnt_rk])) && ($get_rk_data[$_cnt_rk]['rk_getdate'] == $_getdate))
			{

				// 順位が300位以内
				if ($get_rk_data[$_cnt_rk]['rk_position'] <= 301)
				{
					$_tbl_y_data[$i] = $get_rk_data[$_cnt_rk]['rk_position'];
					$_graph_data[$i] = array("date" => $_getdate, "rank" => $get_rk_data[$_cnt_rk]['rk_position']);

				} else {
					$_tbl_y_data[$i] = "";
					$_graph_data[$i] = array("date" => $_getdate, "rank" => 301);
				}

				$_cnt_rk++;

			} else {
				$_tbl_y_data[$i] = "";
				$_graph_data[$i] = array("date" => $_getdate, "rank" => 301);
			}

			$i++;
		}

		// グラフ用データ
		$graph_data_json = json_encode($_graph_data);
		$CI->smarty->assign('graph_data', $graph_data_json);

		// テーブル用データ
		$CI->smarty->assign('tbl_x_data', $_tbl_x_data);
		$CI->smarty->assign('tbl_y_data', $_tbl_y_data);

	}

}