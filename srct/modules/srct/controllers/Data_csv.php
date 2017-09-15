<?php

class Data_csv extends MY_Controller
{

    /*
     *  ＣＳＶアップロード ＆ ダウンロード処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('up_mess', NULL);
        $this->smarty->assign('up_mess02', NULL);
        $this->smarty->assign('up_mess03', NULL);
        $this->smarty->assign('dl_mess', NULL);

    }

    // キーワード情報データのCSV取込
    public function toplist_csvup()
    {


    	// バリデーション・チェック
    	$this->_set_validation();

    	// ajax用ダミーページ
    	$this->view('data_csv/index_aj_topcsvup.tpl');

    }

    // キーワード情報データのCSV取込
    public function toplist_csvup_chk()
    {

    	require_once '/var/www/srct/vendor/autoload.php';
    	$validator = new Zend\Validator\Hostname();

    	$input_post = $this->input->post();

    	$up_errflg = FALSE;
    	$up_mess   = '';

    	// **********************************
    	$this->load->helper('form');
    	// **********************************
    	$this->config->load('config_comm');
    	$this->load->library('lib_csvparser');
    	$this->load->library('lib_validator');

    	// CSVファイルのアップロード
    	$this->load->library('upload', $this->config->item('KWLIST_CSV_UPLOAD'));

    	// CSVファイルの保存
    	if ($this->upload->do_upload('kw_csv_data'))
    	{
    		$up_mess .= "<br><font color=blue>>> CSVファイルの読み込みに成功しました。</font><br>";
    		$up_mess .= "<br><font color=blue>>> CSVファイルのバリデーションチェックを開始しました。</font><br><br>";
    		$_upload_data = $this->upload->data();
    	} else {
    		$jsonArray = array(
		    				array(
		    						'title'   => 'up_mess_err',
		    						'message' => 'ERROR:CSVファイルの読み込みに失敗しました。',
		    				),
    		);
    		goto err_label;									// gotoラベル。
    	}

    	try{
    		// CSVファイルの読み込み
    		$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
    		$_csv_data = $this->lib_csvparser->parse();
    	} catch (Exception $e){
    		$jsonArray = array(
		    				array(
		    						'title'   => 'up_mess_err',
		    						'message' => $e->getMessage(),
		    				),
    		);
    		goto err_label;									// gotoラベル。
    	}

    	/*
    	 * アップロード件数を100件に制限
    	 */
    	if (count($_csv_data) >= 101)
    	{
    		$jsonArray = array(
		    				array(
		    						'title'   => 'up_mess_err',
		    						'message' => "ERROR:対象レコード件数が100件を超えています。",
		    				),
    		);
    		goto err_label;									// gotoラベル。
    	}

    	// CSVファイルのバリデーションチェック
    	$err_mess = NULL;
    	$i = 0;
    	$j = 0;
    	foreach ($_csv_data as $key01 => $val01)
    	{
    		foreach ($val01 as $key02 => $val02)
    		{
    			$_line_no = $i + 2;

    			if ($j === 0)	// seq : kw_seq
    			{
    				if ($val02 != "")
    				{
    					// 数字型＆文字列の長さチェック
    					if ($this->lib_validator->checkRange($val02, 0, 4294967295))
    					{
    					} else {
    						$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    					}
    				}
    			}

    			if ($j === 1)	// ステータス : kw_status
    			{
    				// 入力文字のチェック＆変換
    				if ($val02 == "無効")
    				{
    					$_csv_data[$key01]['ステータス(U)'] = 0;
    				} elseif ($val02 == "有効") {
    					$_csv_data[$key01]['ステータス(U)'] = 1;
    				} else {
    					$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で入力文字指定エラー。<br>";
    				}
    			}

    			if ($j === 2)	// 対象URL : kw_url
    			{
    				// 文字列の長さチェック : max510
    				if ($this->lib_validator->checkLength($val02, 1, 510))
    				{
    					// URLチェック
    					if ($this->lib_validator->checkUri($val02))
    					{

    						/*
    						 * 国際化ドメイン対応チェック
    						 * zendframework/zend-validator を使用
    						 */
    						$chk_domain = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $val02);
    						if ($validator->isValid($chk_domain)) {
    							// ホスト名は正しい形式のようです
    						} else {
    							// 不正な形式なので、理由を表示します
    							foreach ($validator->getMessages() as $message) {
    								$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で国際化ドメイン対応エラー。" . $message . "<br>";
    							}
    						}

    					} else {
    						$err_mess .= $_line_no . "行目:「" . $key02 . "」項目でURL形式エラー。<br>";
    					}

    				} else {
    					$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.510)エラー。<br>";
    				}
    			}

    			if ($j === 3)	// 検索キーワード : kw_keyword
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 1, 100))
    				{
    				} else {
    					$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.100)エラー。<br>";
    				}
    			}

    			if ($j === 4)	// URL一致方式 : kw_matchtype
    			{
    				// 入力文字のチェック＆変換
    				if ($val02 == "完全一致")
    				{
    					$_csv_data[$key01]['URL一致方式(U)'] = 0;
    				} elseif ($val02 == "前方一致") {
    					$_csv_data[$key01]['URL一致方式(U)'] = 1;
    				} elseif ($val02 == "ドメイン一致") {
    					$_csv_data[$key01]['URL一致方式(U)'] = 2;
    				} elseif ($val02 == "ルートドメイン一致") {
    					$_csv_data[$key01]['URL一致方式(U)'] = 3;
    				} else {
    					$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で入力文字指定エラー。<br>";
    				}
    			}

    			if ($j === 5)	// Canonical Name : kw_location_name
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 1, 100))
    				{
    				} else {
    					$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.100)エラー。<br>";
    				}
    			}

    			if ($j === 6)	// 設定グループ : kw_group
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 50))
    				{
    				} else {
    					$err_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.50)エラー。<br>";
    				}
    			}

    			if ($j >= 7)	// 項目オーバー
    			{
   					$err_mess .= $_line_no . "行目:設定項目が多すぎます。<br>";
    			}

    			$j++;
    		}
    		$i++;
    		$j = 0;
    	}

    	if (!empty($err_mess))
    	{
    		$err_mess .= "<br><font color=red>>> ERROR:CSVファイルのバリデーションチェックに失敗しました。</font><br>";

    		$jsonArray = array(
		    				array(
		    						'title'   => 'up_mess_err',
		    						'message' => $err_mess,
		    				),
    		);
    		goto err_label;									// gotoラベル。
    	} else {
    		$up_mess .= "<font color=blue>>> CSVファイルのバリデーションチェックに成功しました。</font><br>";
    	}

    	// CSVファイルでのUPDATE
    	$this->load->model('Location',  'lc', TRUE);
    	$this->load->model('Keyword',   'kw', TRUE);
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$this->load->library('lib_keyword');
    	$this->load->library('lib_rootdomain');

    	$err_mess = NULL;
    	$cnt = 0;
    	$line_cnt = 2;
    	foreach ($_csv_data as $key => $value)
    	{
    		$set_kw_data = array();
    		$result = TRUE;

    		// 対象URL + 補正
    		preg_match_all("/\//", $value['対象URL(U)'], $cnt_slash) ;
    		if (count($cnt_slash[0]) == 2)
    		{
    			$set_kw_data['kw_url'] = $value['対象URL(U)'] . "/";
    		} else {
    			$set_kw_data['kw_url'] = $value['対象URL(U)'];
    		}

    		// 対象URL からドメインを自動作成
    		$set_kw_data['kw_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $set_kw_data['kw_url']);
    		$_rootdomain = $this->lib_rootdomain->get_rootdomain($set_kw_data['kw_url']);
    		$set_kw_data['kw_rootdomain'] = $_rootdomain['rootdomain'];

    		$set_kw_data['kw_status']    = $value['ステータス(U)'];
    		$set_kw_data['kw_matchtype'] = $value['URL一致方式(U)'];
    		$set_kw_data['kw_group']     = $value['設定グループ(U)'];

    		$set_kw_data['kw_cl_seq']    = $_SESSION['c_memGrp'];
    		$set_kw_data['kw_ac_seq']    = $_SESSION['c_memSeq'];

    		// 新規作成か更新処理か選択
    		if ($value['ID(U)'] == "")
    		{

    			$get_location_data = $this->lc->get_location_name($value['Canonical Name']);
    			if (empty($get_location_data))
    			{
    				$err_mess .= "<br><font color=red>>> ERROR:ロケーション名が見つかりませんでした。 　：　" . $line_cnt . "行目 => " . $value['Canonical Name'] . "</font>";
    				++$line_cnt;
    				continue;
    			} else {
    				$set_kw_data['kw_location_id']   = $get_location_data[0]['lo_criteria_id'];
    				$set_kw_data['kw_location_name'] = $get_location_data[0]['lo_canonical_name'];
    			}

    			$_tmp_csv_data['kw_keyword'][0]  = $value['検索キーワード'];
    			$_tmp_csv_data['kw_location'][0] = $set_kw_data['kw_location_id'];

    			// 新規作成
    			$result = $this->lib_keyword->create_kw_data($_tmp_csv_data, $set_kw_data);

    		} else {

    			// 更新可能な項目
    			/*
    			 * /controllers/Topdetail.php => index_aj_kwupdate_chk()
    			 * 「キーワード編集」処理に準ずる！
    			 *
    			 * --> "ID(U)","ステータス(U)","対象URL(U)","URL一致方式(U)","設定グループ(U)"
    			 */

    			// *** 一つのseqから仲間 3人を見つける！
    			$_arr_kw_seq = array();

    			$get_kw_pair = $this->kw->get_kw_seq($value['ID(U)']);
    			if ($get_kw_pair[0]['kw_status'] == 0)
    			{
    				$err_mess .= "<br><font color=red>>> ERROR:指定されたIDが見つかりません。 　：　" . $line_cnt . "行目 => " . $get_kw_pair[0]['kw_seq'] . "</font>";
    				++$line_cnt;
    				continue;
    			}

    			// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
    			$set_kw_pair['kw_cl_seq']      = $get_kw_pair[0]['kw_cl_seq'];
    			$set_kw_pair['kw_url']         = $get_kw_pair[0]['kw_url'];
    			$set_kw_pair['kw_keyword']     = $get_kw_pair[0]['kw_keyword'];
    			$set_kw_pair['kw_matchtype']   = $get_kw_pair[0]['kw_matchtype'];
    			$set_kw_pair['kw_location_id'] = $get_kw_pair[0]['kw_location_id'];

    			$arr_kw_pair =$this->kw->get_kw_info($set_kw_pair);
    			if (empty($arr_kw_pair))
    			{
    				$err_mess .= "<br><font color=red>>> ERROR:指定された情報が見つかりません。 　：　" . $line_cnt . "行目</font>";
    				++$line_cnt;
    				continue;
    			}

    			// *** ここから個別にキーワード情報のチェックを行う
    			foreach ($arr_kw_pair as $key1 => $val1)
    			{

    				// 不足分のキーワード情報を取得
    				$get_kw_data = $this->kw->get_kw_seq($val1['kw_seq']);

    				$set_kw_data['kw_keyword']      = $get_kw_data[0]['kw_keyword'];
    				$set_kw_data['kw_searchengine'] = $get_kw_data[0]['kw_searchengine'];
    				$set_kw_data['kw_device']       = $get_kw_data[0]['kw_device'];
    				$set_kw_data['kw_location_id']  = $get_kw_data[0]['kw_location_id'];
    				$set_kw_data['kw_cl_seq']       = $get_kw_data[0]['kw_cl_seq'];

    				$set_kw_data['kw_seq']          = $get_kw_data[0]['kw_seq'];

    				// ** 旧URL情報を別レコードとして保存するかチェック
    				$get_old_kw_data =$this->kw->get_kw_seq($set_kw_data['kw_seq']);

    				if (($set_kw_data['kw_url'] != $get_old_kw_data[0]['kw_url'])
    						|| ($set_kw_data['kw_matchtype'] != $get_old_kw_data[0]['kw_matchtype']))
    				{

    					// 同一URLのチェック
    					$get_kw_check = $this->kw->check_keyword($set_kw_data, $old_seq=NULL, $status=1);

    					if (count($get_kw_check) >= 1)
    					{
    						foreach ($get_kw_check as $key => $value)
    						{
    							if (($value['kw_old_seq'] == NULL) && ($value['kw_status'] == 1))
    							{
    								$err_mess .= "<br><font color=red>>> ERROR:同一URLの設定が存在します。 　：　" . $line_cnt . "行目 => " . $set_kw_data['kw_url'] . "</font>";
    								++$line_cnt;
    								continue 3;
    							}
    						}
    					}
    				}

    				// トランザクション・START
    				$this->db->trans_strict(FALSE);                                     // StrictモードをOFF
    				$this->db->trans_start();                                           // trans_begin

    				// 「有効」と「無効」で処理を分けるか？
    				//if ($input_post['kw_status'] == 0)

    				/*
    				 * URL書き換えは、基本303(または301)の場合以外の使用は順位データがおかしくなる可能性あり？
    				 * 順位データの引継ぎする？
    				 */

    				// ** 旧URL情報を別レコードとして保存
    				$get_old_kw_data[0]['kw_old_seq'] = $get_old_kw_data[0]['kw_seq'];
    				$get_old_kw_data[0]['kw_group']   = NULL;
    				$get_old_kw_data[0]['kw_tag']     = NULL;

    				if (($set_kw_data['kw_url'] == $get_old_kw_data[0]['kw_url'])
    						&& ($set_kw_data['kw_matchtype'] != $get_old_kw_data[0]['kw_matchtype']))
    				{

    					// URLマッチタイプのみ変更は、UPDATE。
    					$set_matchtype_data['kw_seq']       = $set_kw_data['kw_seq'];
    					$set_matchtype_data['kw_matchtype'] = $set_kw_data['kw_matchtype'];
    					$this->kw->update_keyword($set_matchtype_data);

    				} elseif ($set_kw_data['kw_url'] != $get_old_kw_data[0]['kw_url']) {

    					// 対象URLが変更された場合は、旧URLレコードを作成する。 INSERT。
    					$get_old_kw_data[0]['kw_status'] = 1;
    					unset($get_old_kw_data[0]['kw_seq']);

    					$this->kw->insert_keyword($get_old_kw_data[0]);
    				}

    				// 旧URLの重複チェック
    				$get_url_check = $this->kw->check_url($set_kw_data, $set_kw_data['kw_seq'], $status=1);
    				if (count($get_url_check) >= 1)
    				{
    					// status を書き換え
    					foreach ($get_url_check as $key => $val02)
    					{
    						$get_url_check[$key]['kw_status'] = 0;
    						$this->kw->update_keyword($get_url_check[$key]);
    					}
    				}

    				// ** 設定内容の反映範囲：
    				//   「他キーワードへの反映」選択 → 仕様変更でとりあえず「0：反映させない」固定とする
    				$this->lib_keyword->update_reflection($set_kw_data, 0);

    				// トランザクション・COMMIT
    				$this->db->trans_complete();                                        // trans_rollback & trans_commit
    				if ($this->db->trans_status() === FALSE)
    				{
    					log_message('error', 'client::[keywordlist->chg_comp()]キーワード編集処理 トランザクションエラー');

    					$jsonArray = array(
			    							array(
			    									'title'   => 'up_mess_err',
			    									'message' => 'ERROR:キーワードcsv処理 トランザクションエラー。',
			    							),
    					);
    					goto err_label;													// gotoラベル。ここは後で考えよう！
    				} else {
    					//$this->smarty->assign('mess',  "更新が完了しました。");
    				}

    				// ルートドメイン数のカウント＆更新
    				$get_kw_info = $this->kw->get_kw_seq($set_kw_data['kw_seq']);
    				$this->lib_rootdomain->get_rootdomain_chg($get_kw_info[0]['kw_cl_seq'], $get_kw_info[0]['kw_rootdomain']);

    				if (!empty($get_old_kw_data))
    				{
    					// ルートドメインの削除有無
    					$this->lib_rootdomain->get_rootdomain_del($get_old_kw_data[0]['kw_cl_seq'], $get_old_kw_data[0]['kw_rootdomain']);
    				}

    				/*
    				 * ここは変えた方がいいかも？
    				 * ロジック？ or 仕様？
    				 */
    				// 新規に追加された設定グループをレコード追加
    				//if ($input_post['kw_group'][0] != "")
    				if (!empty($set_kw_data['kw_group']))
    				{
    					$get_gt_name = $this->gt->get_gt_name($set_kw_data['kw_group'], $set_kw_data['kw_cl_seq'], 0);

   						if (count($get_gt_name) == 0)
   						{
   							$set_gt_data['gt_name']   = $set_kw_data['kw_group'];
   							$set_gt_data['gt_cl_seq'] = $set_kw_data['kw_cl_seq'];
   							$set_gt_data['gt_type']   = 0;

   							// INSERT
   							$this->gt->insert_group_tag($set_gt_data);
   						}
   					}

   					// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
   					$this->lib_keyword->update_group_info_all($set_kw_data['kw_cl_seq'], 0);

   				}
    		}

    		if ($result == FALSE)
    		{
    			$err_mess .= "<br><font color=red>>> ERROR:データの追加または更新に失敗しました。 　：　" . $line_cnt . "行目 => " . $set_csv_data['kw_url'] . "</font>";
    			++$line_cnt;
    			continue;
    		} else {
    			++$cnt;
    			++$line_cnt;
    		}

    		// ルートドメイン数のカウント＆更新
    		$this->lib_rootdomain->get_rootdomain_chg($set_kw_data['kw_cl_seq'], $set_kw_data['kw_rootdomain']);
    		if (!empty($get_old_kw_data))
    		{
    			// ルートドメインの削除有無
    			$this->lib_rootdomain->get_rootdomain_del($get_old_kw_data[0]['kw_cl_seq'], $get_old_kw_data[0]['kw_rootdomain']);
    		}

    		/*
    		 * ここは変えた方がいいかも？
    		 * ロジック？ or 仕様？
    		 */
    		// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		if ($set_kw_data['kw_group'] != "")
    		{
    			$get_gt_name = $this->gt->get_gt_name($set_kw_data['kw_group'], $set_kw_data['kw_cl_seq'], 0);

    			if (count($get_gt_name) == 0)
    			{
    				$set_gt_data['gt_name']   = $set_kw_data['kw_group'];
    				$set_gt_data['gt_cl_seq'] = $set_kw_data['kw_cl_seq'];
    				$set_gt_data['gt_type']   = 0;

    				// INSERT
    				$this->gt->insert_group_tag($set_gt_data);
    			}
    		}
    		// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		$this->lib_keyword->update_group_info_all($set_kw_data['kw_cl_seq'], 0);

    		// 512M のように M で指定されている前提なのでアレでごめんなさい
    		list($max) = sscanf(ini_get('memory_limit'), '%dM');
    		$peak = memory_get_peak_usage(true) / 1024 / 1024;
    		$used = ((int) $max !== 0)? round((int) $peak / (int) $max * 100, 2): '--';
    		if ($used > 80) {
    			$err_mess .= "<br><font color=red>>> ERROR:PHP メモリエラー。管理者に連絡をしてください。</font>";

    			$message = sprintf("[%s] Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n", date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
    			log_message('error', $message);
    			break;
    		}
    	}

    	$up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt .  " 件</font>";
    	log_message('info', 'client::[Data_csv->kwlist_csvup()]キーワード情報データのCSV取込 CSVファイルによる更新が完了しました');

    	$jsonArray = array(
			    			array(
			    					'title'   => 'up_mess',
			    					'message' => $err_mess . $up_mess,
			    			),
    	);


err_label:																// gotoラベル


    	$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
    	$this->smarty->assign('mess', $_tmp_json);

    	// ajax用ダミーページ
    	$this->view('data_csv/index_aj_topcsvup_chk.tpl');

    }

    // TOPキーワード一覧情報CSV ダウンロード
    public function toplist_csvdown()
    {

    	$input_post = $this->input->post();

    	$arr_kwseq = explode(",", $input_post['kw_seq']);

    	// 件数(max1000件)を取得。とりあえず制限をかけておきます
    	$tmp_offset   = 0;
    	$tmp_per_page = 1000;

    	// キーワード情報の取得
    	$this->load->model('Keyword', 'kw', TRUE);
    	$query = $this->kw->get_csvdl_top_kwlist($arr_kwseq, $tmp_per_page=0, $tmp_offset=0, $_SESSION['c_memGrp']);

    	// 作成したヘルパーを読み込む
    	$this->load->helper(array('download', 'csvdata'));

    	// ヘルパーに追加した関数を呼び出し、CSVデータ取得
    	$get_dl_csv = csv_toplist_result($query);

    	$file_name = 'dlcsv_toplist_' . date('YmdHis') . '.csv';

    	// S-JIS変換を行う場合
    	$get_sjis_csv = mb_convert_encoding($get_dl_csv,"SJIS", "UTF-8");
    	force_download($file_name, $get_sjis_csv);

    }

    // レポート：キーワード情報CSV ダウンロード（複数キーワード指定に対応）
    public function report_csvdown()
    {

    	$input_post = $this->input->post();

    	$this->load->model('Keyword', 'kw', TRUE);

    	// kw_seq & 表示期間を呼び出し
    	$report_kwseq = $_SESSION['c_report_kwseq'];
    	$_term        = $_SESSION['c_report_term'];

    	$_tmp_kw_seq = explode(",", $report_kwseq);

    	// *** 一つのseqから仲間 3人を見つける！
    	$_arr_kw_seq = array();
    	foreach ($_tmp_kw_seq as $key => $value)
    	{
    		$get_kw_pair = $this->kw->get_kw_seq($value);

    		// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
    		$set_kw_pair['kw_cl_seq']      = $get_kw_pair[0]['kw_cl_seq'];
    		$set_kw_pair['kw_url']         = $get_kw_pair[0]['kw_url'];
    		$set_kw_pair['kw_keyword']     = $get_kw_pair[0]['kw_keyword'];
    		$set_kw_pair['kw_matchtype']   = $get_kw_pair[0]['kw_matchtype'];
    		$set_kw_pair['kw_location_id'] = $get_kw_pair[0]['kw_location_id'];

    		$arr_kw_pair[$key] =$this->kw->get_kw_info($set_kw_pair);
    	}

    	$_start_date     = $input_post['start_date'];
    	$_end_date       = $input_post['end_date'];

    	// キーワード情報の取得
    	$query = $this->kw->get_csvdl_report($arr_kw_pair, $_start_date, $_end_date);

    	// 作成したヘルパーを読み込む
    	$this->load->helper(array('download', 'csvdata'));

    	// ヘルパーに追加した関数を呼び出し、CSVデータ取得
    	$get_dl_csv = csv_report_result($query);
    	//$get_dl_csv = csv_from_result($query);

    	// CSVファイル名
    	$_url = preg_replace("/^https?:\/\/(www\.)?|/i", "", $arr_kw_pair[0][0]['kw_url']);
    	$_url = str_replace("/", "_", $_url);
    	$_url = str_replace(".", "_", $_url);

    	$_tmp_location_name =  explode(",", $arr_kw_pair[0][0]['kw_location_name']);

    	$file_name = 'dlreport_' . $arr_kw_pair[0][0]['kw_keyword'] . '_' . $_url . '_' . $_tmp_location_name[0] . '_' . date('YmdHis') . '.csv';
    	force_download($file_name, $get_dl_csv);									// ここは連続でできない！

    	redirect('/topdetail/report/' . $_term);

    }

   	// Location criteria情報データのCSV取込
   	public function criteria_csvup()
   	{

   		/*
   		 * 基準ファイル
   		 * https://developers.google.com/adwords/api/docs/appendix/geotargeting
   		 */

   		$input_post = $this->input->post();

   		$up_errflg = FALSE;
   		$up_mess   = '';

   		// **********************************
   		$this->load->helper('form');
   		// **********************************
   		$this->config->load('config_comm');
   		$this->load->library('lib_csvparser');
   		$this->load->library('lib_validator');

   		// CSVファイルのアップロード
   		$this->load->library('upload', $this->config->item('CRITERIA_CSV_UPLOAD'));

   		// CSVファイルの保存
   		if ($this->upload->do_upload('criteria_data'))
   		{
   			$up_mess .= "<br><font color=blue>>> CSVファイルの読み込みに成功しました。</font><br>";
   			$up_mess .= "<br><font color=blue>>> CSVファイルのバリデーションチェックを開始しました。</font><br><br>";
   			$_upload_data = $this->upload->data();
   		} else {
   			$up_mess .= "<br><font color=red>>> CSVファイルの読み込みに失敗しました。</font><br><br>";
   			$up_mess .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

   			$this->smarty->assign('up_mess03', $up_mess);
   			$this->view('data_csv/project_csvup.tpl');
   			return;
   		}

   		try{
   			// CSVファイルの読み込み
   			$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
   			$_csv_data = $this->lib_csvparser->parse();
   		} catch (Exception $e){
   			$up_mess .= "<font color=red>エラー発生:" . $e->getMessage() . '</font><br><br>';

   			$this->smarty->assign('up_mess03', $up_mess);
   			$this->view('data_csv/project_csvup.tpl');
   			return;
   		}

   		/*
   		 * ここではバリデーションチェックは行わない
   		 */

   		// CSVファイルでのUPDATE
   		$this->load->model('Location',  'lc', TRUE);

   		$cnt = 0;
   		$line_cnt = 1;
   		foreach ($_csv_data as $key => $value)
   		{
   			$set_csv_data = array();

   			$set_csv_data['lo_criteria_id']    = $value['Criteria ID'];
   			$set_csv_data['lo_name']           = $value['Name'];
   			$set_csv_data['lo_canonical_name'] = $value['Canonical Name'];
   			$set_csv_data['lo_parent_id']      = $value['Parent ID'];
   			$set_csv_data['lo_country_code']   = $value['Country Code'];
   			$set_csv_data['lo_target_type']    = $value['Target Type'];
   			$set_csv_data['lo_status']         = $value['Status'];

			$result = $this->lc->up_insert_criteria($set_csv_data, "");
   			if ($result == FALSE)
   			{
   				$up_mess .= "<br><font color=red>>> データの追加または更新に失敗しました。 　：　" . $line_cnt . " => " . $set_csv_data['kw_url'] . "</font>";
   				++$line_cnt;
   				continue;
   			} else {
   				++$cnt;
   				++$line_cnt;
   			}

   			unset($set_csv_data);
   		}

   		$up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt .  " 件</font>";
   		log_message('info', 'client::[Data_csv->criteria_csvup()]Location criteria情報データのCSV取込 CSVファイルによる更新が完了しました');

   		// バリデーション・チェック
   		$this->_set_validation();

   		$this->smarty->assign('up_mess03', $up_mess);

   		$this->view('data_csv/project_csvup.tpl');

   	}

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}