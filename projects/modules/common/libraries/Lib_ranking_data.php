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
	public static function create_seach_url($list, $engine)
	{

		$CI =& get_instance();
		$CI->config->load('config_comm');

// 		print_r($CI);
// 		print("<br><br>");

		$_url  = $CI->config->item('API_URL');
		$_code = $CI->config->item('API_CODE');

		$_keyword = mb_convert_encoding($list['pj_keyword'], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');;

		if ($engine == "g")
		{
			$_search_engine = "google";
		} else {
			$_search_engine = "yahoo";
		}

		$_device = $CI->config->item('API_DEVICE');
		$_device = $_device[$list['pj_target']];

		$_location = $list['pj_location_name'];

		$_page = $CI->config->item('API_PAGE');
		$_debug = $CI->config->item('API_DEBAG');



		$_api_url = $_url
					. '?code=' . $_code
					. '&keyword=' . urlencode($_keyword)
					. '&search_engine=' . $_search_engine
					. '&device=' . $_device
					. '&location=' . urlencode($_location)
					. '&page=' . $_page
					. '&debag=' . $_debug
					//. '&debag=1'
		;



// 		print($_api_url);
// 		print("<br><br>");
// 		exit;


		return $_api_url;

	}


	/**
	 * 検索結果のデータを取得する
	 *
	 * @param  char
	 * @return array()
	 */
	public static function get_seach_url($url, $domain, $pj_url_match)
	{

		$CI =& get_instance();
		$CI->load->library('lib_ranking_data');

		//「$http_response_header」の初期化
		$http_response_header = array();

		//file_get_contents関数でデータを取得
		if ($get_json = @file_get_contents($url))
		{




			/*
			 * API先でユニコード・エスケープしないようにしてもらう！
			 *
			 * $json = json_encode( $array , JSON_UNESCAPED_UNICODE ) ;
			 *
			 */




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


				$cnt = 1;
				$rank = 0;
 				foreach ($get_data["result"]["organic"] as $key => $val)
 				{

 					// SEO順位を取得する
 					$get_rank = $CI->lib_ranking_data->get_seo_rank($val['url'], $domain, $pj_url_match);
 					if ($get_rank == TRUE)
 					{
 						$rank = $key;
 						break;
 					}

					$cnt++;

 				}

				$err_mess = $get_data["api_status"];
				return array($err_mess, $get_json, $rank);							// ←ここでは元データを保存させます

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
	 * SEO順位を取得する
	 *
	 * @param  char
	 * @param  char
	 * @param  int
	 * @return int
	 */
	public static function get_seo_rank($url, $target_domain, $pj_url_match)
	{

		$CI =& get_instance();

		// URLマッチタイプ別に処理を分ける
		switch( $pj_url_match )
		{
			case 0:
				// ドメイン一致
				/*
				 * ホストだけにして完全一致するか比較
				 */

				// 検索結果URLからドメインを抜き出す
				$serach_domain = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $url);


// 				print($serach_domain);
// 				print(" == ");
// 				print($target_domain);
// 				print("<br>");


				if ($serach_domain === $target_domain)
				{

// 					print("<br>");


					return TRUE;
				}

				break;

			case 1:
				// ルートドメイン一致（サブドメイン含む）
				/*
				 * rootdomainと完全一致するか否か
				 */

				// URLを分解して比較する
				$serach_domain = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $url);
				$serach_domain = array_reverse(explode('.', $serach_domain));

				$target_domain = array_reverse(explode('.', $target_domain));

				// 配列をリバースして比較
				foreach ($target_domain as $key  => $value)
				{
					if ($value != $serach_domain[$key])
					{
						return FALSE;
					}
				}




// 				print_r($serach_domain);
// 				print(" *==* ");
// 				print_r($target_domain);
// 				print("<br>");
// 				print("<br>");





				return TRUE;

				break;
			case 2:
				// URL完全一致
				/*
				 * プロトコル・www取り除いた後に完全一致するか否か
				 */

				// 検索結果URLからドメインを抜き出す
				// 右端の「/」を除いて比較
				$serach_domain = rtrim(preg_replace("/^https?:\/\/(www\.)?/i", "", $url), "/");

				$target_domain = rtrim($target_domain, "/");



// 								print($serach_domain);
// 								print(" x==x ");
// 								print($target_domain);
// 								print("<br>");


				if ($serach_domain === $target_domain)
				{

// 								print("<br>");


					return TRUE;
				}

				break;
			case 3:
				// URL部分一致
				/*
				 * プロトコル・www取り除いた後に前方一致するかどうか
				 */


			default:

				// 検索結果URLからドメインを抜き出す
				// 右端の「/」を除いて比較
				$serach_domain = rtrim(preg_replace("/^https?:\/\/(www\.)?/i", "", $url), "/");

				$target_domain = rtrim($target_domain, "/");




// 				print($serach_domain);
// 				print(" y==y ");
// 				print($target_domain);
// 				print("<br>");



				if (strpos($serach_domain, $target_domain, 0) === 0)
				{

// 					print("<br>");


					return TRUE;
				}
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




}