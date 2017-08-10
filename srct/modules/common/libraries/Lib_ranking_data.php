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

		/*
		 * 取得ページ数を固定とする
		 *   ・Googleの取得は100件表示x3ページとする。（num=100&page=3）（10ページ単位だと神隠しが起こる）
		 *   ・Yahooは既存のまま、10件表示x10ページとする。
		 */
		if ($list['kw_searchengine'] == 0)
		{
			$_num_cnt  = 100;
			$_page_cnt = 3;
		} else {
			$_num_cnt  = 0;
			$_page_cnt = 10;
		}

		$_api_url = $_url . '?code=' .          $_code
						  . '&keyword=' .       urlencode($_keyword)
						  . '&search_engine=' . $_engine[$list['kw_searchengine']]
						  . '&device=' .        $_device[$list['kw_device']]
						  . '&location=' .      urlencode($list['kw_location_name'])
						  . '&num=' .           $_num_cnt
						  . '&page=' .          $_page_cnt
// 						  . '&page=' .          $_page[$list['kw_maxposition']]
// 						  . '&page=2'
						  . '&cashe=' .         $_cashe
						  . '&debag=' .         $_debug
		;

		return $_api_url;
	}

	/**
	 * 検索結果のデータを取得する
	 *
	 * @param  char
	 * @param  char
	 * @return array()
	 */
	public static function get_seach_url($url, $result_id)
	{

		$CI =& get_instance();
		$CI->load->model('Serpslog', 'sl', TRUE);

		//「$http_response_header」の初期化
		$http_response_header = array();

		//file_get_contents関数でデータを取得
		if ($get_json = @file_get_contents($url))
		{

			// データ取得が成功
			$json = mb_convert_encoding($get_json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

			$get_data = json_decode($json, true);

			/*
			 * API のログを保存
			 */
			$CI->sl->delete_serpslog();										// 31日削除

			$date = new DateTime();
			$set_log_data['sl_date']         = $date->format('Y-m-d H:i:s');
			$set_log_data['sl_result_id']    = $result_id;
			$set_log_data['sl_api_url']      = $url;
			$set_log_data['sl_api_getjson']  = $get_json;
			$set_log_data['sl_api_evidence'] = implode("", $get_data["result"]["evidence"]);
			$serialize_data = serialize($get_data);
			$set_log_data['sl_api_getdata']  = $serialize_data;
			$set_log_data['sl_api_status']   = $get_data["api_status"];

			$CI->sl->insert_serpslog($set_log_data);

			unset($get_json);
			unset($set_log_data);
			/* end */

			if ($get_data["api_status"] === 'error')
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

			/*
			 * API のログを保存
			 *
			 * 2017.07.18:Query error: Got a packet bigger than 'max_allowed_packet' bytes - Invalid query: INSERT INTO `tb_serpslog`
			 */
			$CI->sl->delete_serpslog();										// 31日削除

			$date = new DateTime();
			$set_log_data['sl_date']         = $date->format('Y-m-d H:i:s');
			$set_log_data['sl_result_id']    = $result_id;
			$set_log_data['sl_api_url']      = $url;
			$set_log_data['sl_api_getjson']  = NULL;
			$set_log_data['sl_api_evidence'] = NULL;
			$set_log_data['sl_api_getdata']  = $err_mess;
			$set_log_data['sl_api_status']   = "";

			$CI->sl->insert_serpslog($set_log_data);

			unset($set_log_data);
			/* end */

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
		 * ・1回目に順位取得に成功しているキーワードは2回目以降はスキップ。
		 *   プラスして1日の取得回数をチェックする。
		 * ・同一キーワードが存在した場合「kw_maxposition」は大きい方を優先。
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

			if ($_get_cnt === 1)
			{

				// 初回実行時は無条件で順位取得（INSERT）する。

			} elseif ($_get_cnt >= 100) {

				// ステータス=errorのリトライ処理

				$get_rk_position = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
				if ((!empty($get_rk_position)) && ($get_rk_position[0]['rk_position'] < 90009))
				{
					continue;
				}

			} else {

				// 二回目以降は、順位が取得されていないデータを再取得する。

				$get_rk_position = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
				if ((!empty($get_rk_position)) && ($get_rk_position[0]['rk_position'] < 9999))
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

				// 検索結果ID を求める
				$_item = $_item_a . $_get_cnt . $_start_date . $_start_time;
				$_se_result_id = md5($_item);

				// 検索データを取得
				list($err_mess, $get_serach_data) = $CI->lib_ranking_data->get_seach_url($_url, $_se_result_id);

				// 検索データセット
				$_set_search_data = array();
				$_set_search_data['se_result_id']     = $_se_result_id;
				$_set_search_data['se_keyword']       = $value['kw_keyword'];
				$_set_search_data['se_searchengine']  = $value['kw_searchengine'];
				$_set_search_data['se_device']        = $value['kw_device'];
				$_set_search_data['se_location_id']   = $value['kw_location_id'];
				$_set_search_data['se_location_name'] = $value['kw_location_name'];
				$_set_search_data['se_maxposition']   = $value['kw_maxposition'];
				$_set_search_data['se_getcnt']        = $_get_cnt;

				$_set_search_data['se_getdate']       = $_start_date;
				$_set_search_data['se_gettime']       = $_start_time;

				if ($err_mess === 'success')
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

						// INSERT
						$CI->sp->insert_seach_data($_set_search_data);

						++$no;
					}
				} else {

					// 'error' 処理はどうしよう？
					// INSERT
					$row_id = $CI->sp->insert_seach_data($_set_search_data);

					// LOG に書き出し
					$set_log['lg_func']   = 'Lib_ranking_data';
					$set_log['lg_detail'] = 'ERROR :: ' . $err_mess . ' <= exec_ranking => row_id = '
							                . $row_id . ' / API_url = ' . $_url . ' / KW;'  . $value['kw_keyword'];
					$CI->sp->insert_log($set_log);

				}

				++$_search_cnt;
			}

			// 順位データセット
			$set_ranking_data = array();
			$set_ranking_data['rk_cl_seq']     = $value['kw_cl_seq'];
			$set_ranking_data['rk_kw_seq']     = $value['kw_seq'];
			$set_ranking_data['rk_kw_old_seq'] = $value['kw_old_seq'];
			$set_ranking_data['rk_getdate']    = $_start_date;
			$set_ranking_data['rk_result_id']  = $_se_result_id;
			if ($err_mess === 'success')
			{
				$set_ranking_data['rk_position'] = 9999;							// 順位データなし。 9999 < 90009
			} else {
				$set_ranking_data['rk_position'] = 90009;							// エラーのため検索データなし
			}

			// 順位データの取得
			$get_rank = $CI->lib_ranking_data->get_ranking($_set_search_data, $value);
			//if (count($get_rank) > 0)
			if (!empty($get_rank))
			{

				$set_ranking_data['rk_se_seq']        = $get_rank[0]['se_seq'];
				$set_ranking_data['rk_result_id']     = $get_rank[0]['se_result_id'];
				$set_ranking_data['rk_position']      = $get_rank[0]['se_position'];
				$set_ranking_data['rk_position_org']  = $get_rank[0]['se_position'];
				$set_ranking_data['rk_ranking_url']   = $get_rank[0]['se_url'];
				$set_ranking_data['rk_ranking_title'] = $get_rank[0]['se_title'];

				if ($_get_cnt === 1)
				{
					// INSERT
					$CI->rk->insert_ranking($set_ranking_data);

				} else {

					// 順位比較のため既存データの読み込み
					$get_rank_kw = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
					//if (count($get_rank_kw) > 0)
					if (!empty($get_rank_kw))
					{

						// 既存順位より上位の場合書き換える
						if ($get_rank[0]['se_position'] < $get_rank_kw[0]['rk_position'])
						{
							// UPDATE
							$set_ranking_data['rk_seq'] = $get_rank_kw[0]['rk_seq'];
							$CI->rk->update_ranking($set_ranking_data);
						}
					} else {

						// INSERT
						$CI->rk->insert_ranking($set_ranking_data);
					}
				}
			} else {

				if ($_get_cnt === 1)
				{
					// INSERT
					$CI->rk->insert_ranking($set_ranking_data);
				} else {

					// 順位比較のため既存データの読み込み
					$get_rank_kw = $CI->rk->get_ranking_kw($value['kw_seq'], $_start_date);
					//if (count($get_rank_kw) === 0)
					if (empty($get_rank_kw))
					{
						// INSERT
						$CI->rk->insert_ranking($set_ranking_data);
					} else {
						// UPDATE
						$set_ranking_data['rk_seq'] = $get_rank_kw[0]['rk_seq'];
						$CI->rk->update_ranking($set_ranking_data);
					}
				}
			}

			unset($get_serach_data);
			unset($get_rank);
			unset($get_rank_kw);
			unset($set_ranking_data);

			/*
			 * 使用メモリのチェック
			 */
			list($max) = sscanf(ini_get('memory_limit'), '%dM');
			$peak = memory_get_peak_usage(true) / 1024 / 1024;
			$used = ((int) $max !== 0)? round((int) $peak / (int) $max * 100, 2): '--';
			if ($used > 80)
			{
				$message = sprintf("[%s] Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n", date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
				log_message('error', $message);

				return array($_get_cnt, $_search_cnt, $_rank_cnt);
			}

			$_item_b = $_item_a;
			++$_rank_cnt;
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

				return $get_serps_data;

				break;

			case 2:
				// ドメイン一致
				/*
				* ホストだけにして完全一致するか比較
				*/

				$get_serps_data = $CI->sp->get_seo_rank2($search_data['se_result_id'], $kw_data['kw_domain']);

				return $get_serps_data;

				break;

			case 0:
				// URL完全一致
				/*
				 * プロトコル・www取り除いた後に完全一致するか否か
				 */

				$get_serps_data = $CI->sp->get_seo_rank0($search_data['se_result_id'], $kw_data['kw_domain'], $kw_data['kw_url']);

				return $get_serps_data;

				break;

			case 1:
				// URL部分一致
				/*
				 * プロトコル・www取り除いた後に前方一致するかどうか
				 */

			default:

				$get_serps_data = $CI->sp->get_seo_rank1($search_data['se_result_id'], $kw_data['kw_domain'], $kw_data['kw_url']);

				return $get_serps_data;

		}

		return FALSE;
	}

	/**
	 * 引継ぎURLを含めて最高順位に書き換え対応
	 *
	 * @param  int
	 * @param  date
	 * @return char
	 */
	public static function top_ranking()
	{

		$CI =& get_instance();

		$date = new DateTime();
		$today_date = $date->format('Y-m-d');

		$get_ranking_data = $CI->rk->get_kw_old_seq($today_date, NULL);

		// 対象順位データから最高順位に書き換え
		foreach ($get_ranking_data as $key => $value)
		{

			$get_top_data = $CI->rk->get_top_rankingdata($value['rk_kw_seq'], $today_date);

			$set_ranking_data['rk_seq']          = $value['rk_seq'];
			$set_ranking_data['rk_se_seq_re']    = $get_top_data[0]['rk_se_seq'];
			$set_ranking_data['rk_result_id_re'] = $get_top_data[0]['rk_result_id'];
			$set_ranking_data['rk_position']     = $get_top_data[0]['rk_position'];
			$CI->rk->update_ranking($set_ranking_data);

		}

		return;
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
	 * 順位データの書換え (イレギュラー処理)
	 *
	 * @param    date : 該当日付
	 * @param    int  : 0:全データ(UPDATE), 1:不足データ(UPDATE), 2:個別, 3:不足データ補完(バッチ=>9009), 9:全データ(INSERT)
	 * @return   array()
	 */
	public static function chg_ranking_data($rk_getdate, $kind)
	{

		$CI =& get_instance();

		$mess = "";

		if ($kind === 9)
		{
			//return;
		} else {
			if ($kind == 3)
			{
				$get_rk_today = $CI->rk->get_rk_getdatelist($rk_getdate, $_SESSION['c_memGrp']=NULL, $kind=1);
			} else {
				$get_rk_today = $CI->rk->get_rk_getdatelist($rk_getdate, $_SESSION['c_memGrp'], $kind);
			}

			if (empty($get_rk_today))
			{
				log_message('info', 'client::[Lib_ranking_data->chg_ranking_data()]順位データの書換え処理 (イレギュラー処理) 該当データなし');
				$mess = "<font color=blue>該当データがありませんでした。</font>";
				return $mess;
			}
		}

		// トランザクション・START
		$CI->db->trans_strict(FALSE);                                     // StrictモードをOFF
		$CI->db->trans_start();                                           // trans_begin

		$date = new DateTime($rk_getdate);
		$_before_date = $date->modify("-1 days")->format('Y-m-d');

		$_kw_list = array();														// 補完されたseq保存用
		if ($kind !== 9)
		{
			foreach ($get_rk_today as $key => $value)
			{
				// 前日データ有無の確認＆取得
				$get_rk_before = $CI->rk->get_rk_getdatelist($_before_date, $_SESSION['c_memGrp'], $kind=2, $value['rk_kw_seq']);

				// データ更新 ←これだと時間がかかるか？
				if (!empty($get_rk_before))
				{
					$set_rk_data               = $get_rk_before[0];
					$set_rk_data['rk_seq']     = $value['rk_seq'];
					$set_rk_data['rk_getdate'] = $value['rk_getdate'];

					$CI->rk->update_ranking($set_rk_data);
				}
			}
		} else {

			// 前日データ有無の確認＆取得
			if ($kind == 3)
			{
				$get_rk_before = $CI->rk->get_rk_getdatelist($_before_date, $_SESSION['c_memGrp']=NULL, $kind=1);
			} else {
				$get_rk_before = $CI->rk->get_rk_getdatelist($_before_date, $_SESSION['c_memGrp'], $kind);
			}

			// INSERT
			foreach ($get_rk_before as $key => $value)
			{
				$set_rk_data['rk_cl_seq']        = $value['rk_cl_seq'];
				$set_rk_data['rk_kw_seq']        = $value['rk_kw_seq'];
				$set_rk_data['rk_se_seq']        = $value['rk_se_seq'];
				$set_rk_data['rk_result_id']     = $value['rk_result_id'];
				$set_rk_data['rk_position']      = $value['rk_position'];
				$set_rk_data['rk_ranking_url']   = $value['rk_ranking_url'];
				$set_rk_data['rk_ranking_title'] = $value['rk_ranking_title'];
				$set_rk_data['rk_getdate']       = $rk_getdate;

				$CI->rk->insert_ranking($set_rk_data);
			}
		}

		// トランザクション・COMMIT
		$CI->db->trans_complete();                                        // trans_rollback & trans_commit
		if ($CI->db->trans_status() === FALSE)
		{
			log_message('error', 'client::[Lib_ranking_data->chg_ranking_data()]順位データの書換え処理 (イレギュラー処理) トランザクションエラー');
			return $mess;
		} else {
			//log_message('info', 'client::[Lib_ranking_data->chg_ranking_data()]順位データの書換え処理 (イレギュラー処理) 更新が完了しました');
			$mess = "<font color=blue>順位データ書換処理が終了しました。</font>";
			return $mess;
		}

	}

	/**
	 * グラフ用順位データ情報の取得
	 *
	 * @param  int
	 * @param  date
	 * @return char
	 */
	public static function create_ranking_graph($kw_list, $cnt_date)
	{

		$CI =& get_instance();

		// グラフ用データ取得
		foreach ($kw_list as $key => $value)
		{
			$CI->lib_ranking_data->get_ranking_graph($value['kw_seq'], $cnt_date);
		}

		// ルートドメイン内ランキング用データ取得（前日比）
		if (!empty($kw_list))
		{
			$CI->lib_ranking_data->get_ranking_kwdomain($kw_list);
		}
	}

	/**
	 * 順位データ集計 （グラフ用/テーブル用）
	 * chart.jp を使用
	 * min:sparkline を使用
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
		$_start_date   = $date->format('Y-m-d');
		$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
		$_end_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

		$get_rk_data = $CI->rk->get_kw_seq($kw_seq, $_start_date, $_end_date);

		$_getdate = $_end_date;
		$_cnt_rk  = 0;														// 順位データの配列カウンター
		$_x_data[$kw_seq] = "x";											// X軸データ（日付）用配列。"x"は接頭語として後で外す。
		$_y_data[$kw_seq] = "y";											// Y軸データ（順位）用配列。"y"は接頭語として後で外す。
		$_y_min_data[$kw_seq] = "y";										// TOP:ミニグラフ用データ
		for ($cnt = $cnt_date; $cnt > 0; $cnt--)
		{

			//$_getdate = $date->modify('+1 days')->format('Y-m-d');
			$_x_data[$kw_seq] .= ',' . $date->format('d');

			if ((!empty($get_rk_data[$_cnt_rk])) && ($get_rk_data[$_cnt_rk]['rk_getdate'] == $_getdate))
			{

				// 順位が300位以内
				if ($get_rk_data[$_cnt_rk]['rk_position'] <= 300)
				{
					$_y_data[$kw_seq]     .=  ',' . $get_rk_data[$_cnt_rk]['rk_position'];
					$_y_min_data[$kw_seq] .=  ',' . (301 - $get_rk_data[$_cnt_rk]['rk_position']);	// Y軸の反転ができないのでとりあえず！
				} else {
					$_y_data[$kw_seq]     .=  ',' . "";
					$_y_min_data[$kw_seq] .=  ',0';
				}

				++$_cnt_rk;

			} else {
				$_y_data[$kw_seq]     .=  ',';
				$_y_min_data[$kw_seq] .=  ',0';
			}

			$_getdate = $date->modify('+1 days')->format('Y-m-d');

		}

		// グラフ用データ
		$_x_data[$kw_seq] = str_replace("x,", "", $_x_data[$kw_seq]);
		$_y_data[$kw_seq] = str_replace("y,", "", $_y_data[$kw_seq]);
		$_y_min_data[$kw_seq] = str_replace("y,", "", $_y_min_data[$kw_seq]);

		$CI->smarty->assign('x_data' . $kw_seq, $_x_data[$kw_seq]);
		$CI->smarty->assign('y_data' . $kw_seq, $_y_data[$kw_seq]);
		$CI->smarty->assign('y_min_data' . $kw_seq, $_y_min_data[$kw_seq]);

		// テーブル用データ
		$_tbl_x_data[$kw_seq] = explode(",", $_x_data[$kw_seq]);
		$_tbl_y_data[$kw_seq] = explode(",", $_y_data[$kw_seq]);
		foreach ($_tbl_y_data[$kw_seq] as $key => $value)
		{
			// 空白を「-」で埋める
			if (empty($value))
			{
				$_tbl_y_data[$kw_seq][$key] = "-";
			}
		}

		$CI->smarty->assign('tbl_x_data' . $kw_seq, $_tbl_x_data[$kw_seq]);
		$CI->smarty->assign('tbl_y_data' . $kw_seq, $_tbl_y_data[$kw_seq]);

		return array($_x_data, $_y_data);

	}

	/**
	 * 順位データ集計 （ルートドメイン内KWランキング用：前日比）
	 * min:sparkline を使用
	 *
	 * @param  array()
	 * @return char
	 */
	public static function get_ranking_kwdomain($kw_list)
	{

		$CI =& get_instance();
		$CI->load->model('Ranking', 'rk', TRUE);

		// 順位データ情報を取得 (前日分)
		$date = new DateTime();
		$_start_date   = $date->format('Y-m-d');
		$_end_date     = $date->modify("-1 days")->format('Y-m-d');

		$_kw_rootdomain = $kw_list[0]['kw_rootdomain'];
		$_pie_data[0] = array(0=>0, 1=>0, 2=>0, 3=>0);						// 円グラフ用 (TOP3/TOP10/TOP100/OUT)
		$i = 0;
		$_ranking_up_cnt[0] = 0;											// 前日比UP件数
		$_ranking_down_cnt[0] = 0;											// 前日比DOWN件数
		foreach ($kw_list as $key => $value)
		{

			if ($kw_list[$key]['kw_rootdomain'] == $_kw_rootdomain)
			{

			} else {

				++$i;

				$_kw_rootdomain = $kw_list[$key]['kw_rootdomain'];
				$_pie_data[$i] = array(0=>0, 1=>0, 2=>0, 3=>0);
				$_ranking_up_cnt[$i] = 0;
				$_ranking_down_cnt[$i] = 0;

			}

			$_ranking_kwdomain[$i][$key] = $value;

			// 順位ランク
			$get_rk_data = $CI->rk->get_kw_seq($kw_list[$key]['kw_seq'], $_start_date, $_end_date);
			if ((isset($get_rk_data[0])) && ($get_rk_data[0]['rk_getdate'] == $_end_date))
			{

				// 前日データが存在する場合
				$_ranking_kwdomain[$i][$key]['end_date'] = $_end_date;
				$_ranking_kwdomain[$i][$key]['end_position'] = intval($get_rk_data[0]['rk_position']);
				if ((isset($get_rk_data[1])) && ($get_rk_data[1]['rk_getdate'] == $_start_date))
				{
					$_ranking_kwdomain[$i][$key]['start_date'] = $_start_date;
					$_ranking_kwdomain[$i][$key]['start_position'] = intval($get_rk_data[1]['rk_position']);
				} else {
					$_ranking_kwdomain[$i][$key]['start_date'] = "";
					$_ranking_kwdomain[$i][$key]['start_position'] = 9999;
				}

				if ($_ranking_kwdomain[$i][$key]['end_position'] > $_ranking_kwdomain[$i][$key]['start_position'])
				{
					$_fugo = "+";
					$_ranking_up_cnt[$i]++;
				} elseif ($_ranking_kwdomain[$i][$key]['end_position'] == $_ranking_kwdomain[$i][$key]['start_position']) {
					$_fugo = "±";
				} else {
					$_fugo = "";
					$_ranking_down_cnt[$i]++;
				}
				$_ranking_kwdomain[$i][$key]['chg_position'] = ($_ranking_kwdomain[$i][$key]['end_position'] - $_ranking_kwdomain[$i][$key]['start_position']);

				// 符号付き変化順位
				$_ranking_kwdomain[$i][$key]['chg_position_fugo'] = $_fugo . ($_ranking_kwdomain[$i][$key]['end_position'] - $_ranking_kwdomain[$i][$key]['start_position']);

			} elseif ((isset($get_rk_data[0])) && ($get_rk_data[0]['rk_getdate'] == $_start_date)) {

				// 前日データがなく、当日データのみ存在する場合
				$_ranking_kwdomain[$i][$key]['end_date'] = "";
				$_ranking_kwdomain[$i][$key]['end_position'] = 9999;
				$_ranking_kwdomain[$i][$key]['start_date'] = $_start_date;
				$_ranking_kwdomain[$i][$key]['start_position'] = intval($get_rk_data[0]['rk_position']);
				$_ranking_kwdomain[$i][$key]['chg_position'] = "-";
				$_ranking_kwdomain[$i][$key]['chg_position_fugo'] = "-";

			} else {
				// データなし
				$_ranking_kwdomain[$i][$key]['end_date'] = "";
				$_ranking_kwdomain[$i][$key]['end_position'] = 9999;
				$_ranking_kwdomain[$i][$key]['start_date'] = "";
				$_ranking_kwdomain[$i][$key]['start_position'] = 9999;
				$_ranking_kwdomain[$i][$key]['chg_position'] = "-";
				$_ranking_kwdomain[$i][$key]['chg_position_fugo'] = "-";

			}

			// **円グラフデータ作成
			/*
			 * 0=>TOP3, 1=>TOP10, 2=>TOP100, 3=>OUT
			 */
			if ($_ranking_kwdomain[$i][$key]['start_position'] <= 3)
			{
				$_pie_data[$i][0] = $_pie_data[$i][0] + 1;
			} elseif (($_ranking_kwdomain[$i][$key]['start_position'] >= 4) && ($_ranking_kwdomain[$i][$key]['start_position'] <= 10)) {
				$_pie_data[$i][1] = $_pie_data[$i][1] + 1;
			} elseif (($_ranking_kwdomain[$i][$key]['start_position'] >= 11) && ($_ranking_kwdomain[$i][$key]['start_position'] <= 100)) {
				$_pie_data[$i][2] = $_pie_data[$i][2] + 1;
			} else {
				$_pie_data[$i][3] = $_pie_data[$i][3] + 1;
			}
		}

		// 円グラフデータを取得 : TOP10
		foreach ($_pie_data as $key => $value)
		{
			$pie_data = $_pie_data[$key][0] . "," . $_pie_data[$key][1] . "," . $_pie_data[$key][2] . "," . $_pie_data[$key][3];
			$CI->smarty->assign('pie_data' . $key, $pie_data);

			$pie_cont = "<font color=\"#3366cc\">TOP3=" . $_pie_data[$key][0] . "</font>" .
						",<font color=\"#109618\">TOP10=" . $_pie_data[$key][1] . "</font>" .
						",<font color=\"#ff9900\">TOP100=" . $_pie_data[$key][2] . "</font>" .
						",<font color=\"#808080\">OUT=" . $_pie_data[$key][3] . "</font>";
			$CI->smarty->assign('pie_cont' . $key, $pie_cont);

			// 前日比UP件数 & 前日比DOWN件数 をここでセット
			$CI->smarty->assign('up_cnt' . $key, $_ranking_up_cnt[$key]);
			$CI->smarty->assign('down_cnt' . $key, $_ranking_down_cnt[$key]);

		}

		// TOPランキングを取得 : TOP10
		foreach ($_ranking_kwdomain as $key => $value)
		{
			array_multisort(array_column($_ranking_kwdomain[$key], 'start_position'), SORT_ASC, $_ranking_kwdomain[$key]);
		}
		$_ranking_top = $_ranking_kwdomain;
		$CI->smarty->assign('ranking_top', $_ranking_top);

		// 上昇ランキングを取得 : IMPROVED POSISIONS
		foreach ($_ranking_kwdomain as $key => $value)
		{
			array_multisort(array_column($_ranking_kwdomain[$key], 'chg_position'), SORT_DESC, $_ranking_kwdomain[$key]);
		}
		$_ranking_up = $_ranking_kwdomain;
		$CI->smarty->assign('ranking_up', $_ranking_up);

		// 下降ランキングを取得 : LOST POSISIONS
		foreach ($_ranking_kwdomain as $key => $value)
		{
			array_multisort(array_column($_ranking_kwdomain[$key], 'chg_position'), SORT_ASC, $_ranking_kwdomain[$key]);
		}
		$_ranking_down = $_ranking_kwdomain;
		$CI->smarty->assign('ranking_down', $_ranking_down);

		return;
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
		$_start_date   = $date->format('Y-m-d');
		$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
		$_end_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

		$get_rk_data = $CI->rk->get_kw_seq($kw_seq, $_start_date, $_end_date);

		$i = 0;
		$_cnt_rk = 0;														// 順位データの配列カウンター
		$_tbl_x_data = array();
		$_tbl_y_data = array();
		$_graph_data = array();
		$_getdate = $_end_date;
		for ($cnt = $cnt_date; $cnt > 0; $cnt--)
		{

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

				++$_cnt_rk;

			} else {
				$_tbl_y_data[$i] = "";
				$_graph_data[$i] = array("date" => $_getdate, "rank" => 301);
			}

			$_getdate = $date->modify('+1 days')->format('Y-m-d');
			++$i;
		}

		// グラフ用データ
		$graph_data_json = json_encode($_graph_data);
		$CI->smarty->assign('graph_data', $graph_data_json);

		// テーブル用データ
		$CI->smarty->assign('tbl_x_data', $_tbl_x_data);
		$CI->smarty->assign('tbl_y_data', $_tbl_y_data);

		$CI->smarty->assign('start_date', $_start_date);
		$CI->smarty->assign('end_date',   $_end_date);
		$CI->smarty->assign('nisuu',      $cnt_date);

	}

	/**
	 * 順位データ集計 （グラフ用/テーブル用）
	 * jqplot.js を使用
	 *
	 * @param  int
	 * @param  array
	 * @return char
	 */
	public static function get_jqplot_graph($kw_seq, $term)
	{

		$CI =& get_instance();
		$CI->load->model('Ranking', 'rk', TRUE);
		$CI->load->model('Keyword', 'kw', TRUE);

		// キーワード設定情報を取得
		$get_kw_data =$CI->kw->get_kw_seq($kw_seq);

		// 順位データ情報の取得期間
		$date = new DateTime();
		$_term = explode("-", $term);
		if ($_term[0] === "0")
		{
			$_start_term = $date->format('Y-m-d');
			$date_end = date_create($get_kw_data[0]['kw_create_date']);
			$_end_term   = date_format($date_end, 'Y-m-d');
		} else {
			$_set_cnt_date = "- " . ($_term[0] - 1) . " months";
			$_end_term   = $date->modify($_set_cnt_date)->format('Y-m-01');
			$_start_term = $date->format('Y-m-t');
		}

		$_start_term_date = new DateTime($_start_term);
		$_end_term_date   = new DateTime($_end_term);
		$diff_date = $_end_term_date->diff($_start_term_date);
		$_date_cnt = $diff_date->format('%a') + 1;

		// グラフ＆テーブルデータの取得
		$set_kw_data['kw_seq']          = $kw_seq;
		$set_kw_data['kw_searchengine'] = $get_kw_data[0]['kw_searchengine'];
		$res = $CI->lib_ranking_data->_jqplot_data($set_kw_data, $_start_term, $_end_term, $_date_cnt);

		// グラフ＆テーブルデータの取得 (Google & Yahoo!) 同時に表示
		if ($_term[1] == 1)
		{
			// 同一キーワードの存在チェック
			$set_kw_data = array();
			$set_kw_data['kw_cl_seq']       = $get_kw_data[0]['kw_cl_seq'];
			$set_kw_data['kw_url']          = $get_kw_data[0]['kw_url'];
			$set_kw_data['kw_keyword']      = $get_kw_data[0]['kw_keyword'];
			$set_kw_data['kw_matchtype']    = $get_kw_data[0]['kw_matchtype'];
			$set_kw_data['kw_device']       = $get_kw_data[0]['kw_device'];
			$set_kw_data['kw_location_id']  = $get_kw_data[0]['kw_location_id'];
			$set_kw_data['kw_searchengine'] = 0;
			if ($get_kw_data[0]['kw_searchengine'] == 0)
			{
				$set_kw_data['kw_searchengine'] = 1;
			}

			$get_kw_row = $CI->kw->get_kw_url($set_kw_data);
			if (!empty($get_kw_row))
			{
				$res = $CI->lib_ranking_data->_jqplot_data($get_kw_row[0], $_start_term, $_end_term, $_date_cnt);
			}
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
	private function _jqplot_data($set_kw_data, $start_date, $end_date, $date_cnt)
	{

		$CI =& get_instance();

		$get_rk_data = $CI->rk->get_kw_seq($set_kw_data['kw_seq'], $start_date, $end_date);

		$date = new DateTime($end_date);
		$_getdate = $end_date;
		$_cnt_rk  = 0;														// 順位データの配列カウンター
		$_plot_data = "[";													// jqPlot グラフデータ
		$_x_data = "x";														// X軸データ（日付）用配列。"x"は接頭語として後で外す。
		$_y_data = "y";														// Y軸データ（順位）用配列。"y"は接頭語として後で外す。
		for ($cnt = ($date_cnt-1); $cnt >= 0; $cnt--)
		{

			$_x_data .= ',' . $date->format('d');
			$_plot_data .= "['" . $date->format('Y-m-d') . " 0:00AM',";

			if ((!empty($get_rk_data[$_cnt_rk])) && ($get_rk_data[$_cnt_rk]['rk_getdate'] == $_getdate))
			{

				// 順位が300位以内
				if ($get_rk_data[$_cnt_rk]['rk_position'] <= 300)
				{
					$_plot_data .= $get_rk_data[$_cnt_rk]['rk_position'] . "],";
					$_y_data    .=  ',' . $get_rk_data[$_cnt_rk]['rk_position'];
				} else {
					$_plot_data .= "300],";
					$_y_data    .=  ',' . "";
				}

				++$_cnt_rk;

			} else {
				$_plot_data .= "300],";
				$_y_data    .=  ',';
			}

			$_getdate = $date->modify('+1 days')->format('Y-m-d');
		}

		// グラフ用データ
		$term = $set_kw_data['kw_searchengine'];
		$_plot_data = rtrim($_plot_data, ",") . "]";

		$CI->smarty->assign('plot_data' . $term, $_plot_data);
		$CI->smarty->assign('plot_start_date', $end_date);
		$CI->smarty->assign('plot_end_date', $start_date);
		$CI->smarty->assign('plot_cnt', $date_cnt);

		$_x_data = str_replace("x,", "", $_x_data);
		$_y_data = str_replace("y,", "", $_y_data);

		// テーブル用データ
		$_tbl_x_data = explode(",", $_x_data);
		$_tbl_y_data = explode(",", $_y_data);

		$CI->smarty->assign('tbl_x_data' . $term, $_tbl_x_data);
		$CI->smarty->assign('tbl_y_data' . $term, $_tbl_y_data);

		return TRUE;
	}

}