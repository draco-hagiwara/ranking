<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO検索用クラス
 */
class Lib_ranking_data
{

	/**************************************************************************/
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

	/**************************************************************************/
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

		//「$http_response_header」の初期化
		$http_response_header = array();

		//file_get_contents関数でデータを取得
		if ($get_json = @file_get_contents($url))
		{

			// データ取得が成功
			$json = mb_convert_encoding($get_json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

			unset($get_data);
			$get_data = array();

			$get_data = json_decode($json, true);

			/*
			 * API のログを保存
			 *
			 * ここはPHPメモリ不足に要注意！！
			 */

			$date = new DateTime();
			$set_log_data   = array();
			$serialize_data = NULL;

			$set_log_data['sl_date']         = $date->format('Y-m-d H:i:s');
			$set_log_data['sl_result_id']    = $result_id;
			$set_log_data['sl_api_url']      = $url;
			//$set_log_data['sl_api_getjson']  = $get_json;
			$set_log_data['sl_api_getjson']  = NULL;
			//$set_log_data['sl_api_evidence'] = implode("", $get_data["result"]["evidence"]);
			$set_log_data['sl_api_evidence'] = NULL;
			$serialize_data = serialize($get_data);
			$set_log_data['sl_api_getdata']  = $serialize_data;
			//$set_log_data['sl_api_getdata']  = NULL;
			$set_log_data['sl_api_status']   = $get_data["api_status"];

			$CI->sl->insert_serpslog($set_log_data);

			unset($get_json);
			unset($json);
			unset($set_log_data);
			unset($serialize_data);
			/* end */

			if ($get_data["api_status"] === 'error')
			{
				unset($get_data);

				// 取得失敗
				$err_mess = "get-data-error";
				return array($err_mess, "");
			}

			if ($get_data === NULL)
			{
				unset($get_data);

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

			$date = new DateTime();
			$set_log_data = array();
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

	/**************************************************************************/
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

						unset($get_serach_data[$key01]);

						++$no;
					}

					unset($val);

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

				unset($get_serach_data);

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

			unset($get_rank);
			unset($get_rank_kw);
			unset($set_ranking_data);

			unset($get_kw_data[$key]);
			unset($value);

			/*
			 * 使用メモリのチェック
			 */
			list($max) = sscanf(ini_get('memory_limit'), '%dM');
			$peak = memory_get_peak_usage(true) / 1024 / 1024;
			$used = ((int) $max !== 0)? round((int) $peak / (int) $max * 100, 2): '--';
			if ($used > 80)
			{
				$message = sprintf("[%s] 【exec_ranking】Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n", date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
				log_message('error', $message);

				return array($_get_cnt, $_search_cnt, $_rank_cnt);
			}

			if (($_rank_cnt%50) == 0)
			{
				$message = sprintf("[%s] 【***】Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n", date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
				log_message('INFO', $message);
			}

			$_item_b = $_item_a;
			++$_rank_cnt;
		}

		$sth = null;
		$dbh = null;

		unset($get_kw_data);
		return array($_get_cnt, $_search_cnt, $_rank_cnt);
	}


	/**************************************************************************/
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

	/**************************************************************************/
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

			unset($get_ranking_data[$key]);

		}

		unset($get_ranking_data);
		unset($get_top_data);

		return;
	}

	/**************************************************************************/
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

				unset($get_rk_today[$key]);
				unset($get_rk_before);

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

				unset($get_rk_before[$key]);
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

}