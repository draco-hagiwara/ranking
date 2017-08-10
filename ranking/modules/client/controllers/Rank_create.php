<?php

class Rank_create extends MY_Controller
{

    /*
     *  手動順位データ取得処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess',   FALSE);
        $this->smarty->assign('mess01', FALSE);

    }

    // 順位データ取得画面
    public function index()
    {

    	/*
    	 * メモリ使用量チェック
    	 */
    	print "\n".'<pre style="text-align:left;">'."\n";
    	$mem     = number_format(memory_get_usage());
    	$peakmem = number_format(memory_get_peak_usage());
    	print("Memory:{$mem} / Peak Memory:{$peakmem}");
    	print "\n</pre>\n";


        // バリデーション・チェック
        $this->_set_validation();

        $this->view('rank_create/index.tpl');

    }

    // 検索データの取得
    public function manual()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->load->model('Keyword',    'kw', TRUE);
    	$this->load->model('Monitoring', 'mn', TRUE);
    	$this->load->model('Serps',      'sp', TRUE);
    	$this->load->model('Ranking',    'rk', TRUE);
    	$this->load->library('lib_ranking_data');
    	$this->load->library('lib_rootdomain');
    	$this->config->load('config_status');

    	// 取得状況を監視チェックする : 本日の実行回数を取得
    	$date = new DateTime();
    	$_mn_date = $date->format('Y-m-d');

    	$opt_monitoring = $this->config->item('MONITORING_STATUS');

    	$get_mn_data = $this->mn->get_mn_date($_mn_date);
    	//if (count($get_mn_data) > 0)
    	if (!empty($get_mn_data))
    	{

    		if ($get_mn_data[0]['mn_status'] != $opt_monitoring['neutral'])
    		{
    			$mess = "<font color=red>ERROR::現在データを取得中です。しばらくしてから実行してください。</font>";

    			$this->smarty->assign('mess', $mess);
    			$this->view('rank_create/index.tpl');

    			return ;
    		}

    		$_get_cnt = $get_mn_data[0]['mn_cnt'];
    		if ($_get_cnt >= 3)
    		{
    			$mess = "<font color=red>ERROR::１日の順位データ取得回数をオーバーしています。</font>";

    			$this->smarty->assign('mess', $mess);
    			$this->view('rank_create/index.tpl');

    			return ;
    		}

    	} else {
    		$_get_cnt = 0;
    	}

		// 検索対象となるKEYWORDデータを抽出
    	$get_kw_data = $this->kw->get_keyword_data($_get_cnt);

    	// 監視テーブルに開始時刻を書き込む
    	$_set_mn_data['mn_date']   = $_mn_date;
    	$_set_mn_data['mn_status'] = $opt_monitoring['start'];
    	$_set_mn_data['mn_cnt']    = $_get_cnt;
    	if ($_get_cnt === 0)
    	{
    		$this->mn->insert_monitoring($_set_mn_data, $_get_cnt);
    	} else {
    		$this->mn->update_monitoring($_set_mn_data, $_get_cnt);
    	}

    	//if (count($get_kw_data) == 0)
    	if (empty($get_kw_data))
    	{
    		$mess = "<font color=red>対象となる検索キーワードが存在しません。</font>";
    		$this->smarty->assign('mess', $mess);
    		$this->view('rank_create/index.tpl');

    		// 監視テーブルに検索終了を書き込む
    		$_get_cnt = $_get_cnt + 1;
    		$_set_mn_data['mn_date']               = $_mn_date;
    		$_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
    		$_set_mn_data['mn_cnt']                = $_get_cnt;
    		$_no = $_get_cnt - 1;
    		$_set_mn_data['mn_search_cnt' .  $_no] = 9999999;
    		$_set_mn_data['mn_ranking_cnt' . $_no] = 9999999;

    		$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

    		return;
    	}

    	// ** 検索＆順位取得を実行する **
    	list($_get_cnt, $_search_cnt, $_rank_cnt) = $this->lib_ranking_data->exec_ranking($get_kw_data, $_get_cnt);
    	log_message('info', '検索＆順位取得が実行されました。検索データ数=' . $_search_cnt . ', 順位取得データ数=' . $_rank_cnt);

    	// ** 引継ぎURLを含めて最高順位に書き換え対応
    	$this->lib_ranking_data->top_ranking();






    	/*
    	 * ここからは、上の処理で順位を取得できなかったKWの再取得を行う！？
    	 *
    	 * 再取得を2回にセット。
    	 */
    	for ($i = 100; $i <= 101; $i++)
    	{

    		// ** 検索＆順位取得を実行する **
    		list($r_get_cnt, $r_search_cnt, $r_rank_cnt) = $this->lib_ranking_data->exec_ranking($get_kw_data, $i);
    		log_message('info', '取得リトライが実行されました。検索データ数=' . $r_search_cnt . ', 順位取得データ数=' . $r_rank_cnt);

    		// ** 引継ぎURLを含めて最高順位に書き換え対応
    		$this->lib_ranking_data->top_ranking();

    	}
    	/* end */












    	// 監視テーブルに検索終了を書き込む
    	$_set_mn_data['mn_date']               = $_mn_date;
    	$_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
    	$_set_mn_data['mn_cnt']                = $_get_cnt;
    	$_no = $_get_cnt - 1;
    	$_set_mn_data['mn_search_cnt' .  $_no] = $_search_cnt;
    	$_set_mn_data['mn_ranking_cnt' . $_no] = $_rank_cnt;

    	$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

    	$mess = "<font color=blue>順位データ取得処理が終了しました。</font>";
    	$this->smarty->assign('mess', $mess);

    	$this->view('rank_create/index.tpl');

    }

    // 検索データの取得 (イレギュラー処理)
    public function irregular()
    {

    	$input_post = $this->input->post();

    	$mess = "";
    	$kind = "";

    	// バリデーション・チェック
    	$this->_set_validation01();
    	if ($this->form_validation->run() == TRUE)
    	{
    		$this->load->model('Keyword',    'kw', TRUE);
    		$this->load->model('Monitoring', 'mn', TRUE);
    		$this->load->model('Ranking',    'rk', TRUE);
    		$this->load->library('lib_ranking_data');
    		$this->config->load('config_status');

     		// 取得状況を監視チェックする
    		$date = new DateTime();
    		$_today_date = $date->format('Y-m-d');

    		$date = new DateTime($input_post['rk_getdate']);
    		$_mn_date = $date->format('Y-m-d');
    		$opt_monitoring = $this->config->item('MONITORING_STATUS');
    		$_set_mn_data['mn_date']   = $_mn_date;
    		$_set_mn_data['mn_status'] = $opt_monitoring['start'];

    		// 過去順位取得日チェック
    		if ($input_post['rk_getdate'] > $_today_date)
    		{
    			$mess = "<font color=red>ERROR::書換日の指定エラー。「" . $_today_date . "」 より過去の日付を指定してください。</font>";
    			$this->smarty->assign('mess01', $mess);
    			$this->view('rank_create/index.tpl');

    			return ;
    		}

    		$get_mn_data = $this->mn->get_mn_date($_mn_date);
    		if (!empty($get_mn_data))
    		{
    			if ($get_mn_data[0]['mn_status'] != $opt_monitoring['neutral'])
    			{
    				$mess = "<font color=red>ERROR::現在データを取得中です。しばらくしてから実行してください。</font>";
    				$this->smarty->assign('mess01', $mess);
    				$this->view('rank_create/index.tpl');

    				return ;
    			}

    			// 監視テーブルに開始時刻を書き込む
    			$this->mn->update_monitoring($_set_mn_data, 9);
    		} else {
    			$mess = "<font color=red>ERROR::該当する書換日のデータ取得がありません。</font>";
    			$this->smarty->assign('mess01', $mess);
    			$this->view('rank_create/index.tpl');

    			//return ;

    			// ここは運用を見て!?
    			$this->mn->insert_monitoring($_set_mn_data, 9);

    			// 全順位データ作成 (INSERT)
    			$kind = 9;
    			$this->lib_ranking_data->chg_ranking_data($input_post['rk_getdate'], $kind);
    			//$this->_chg_rank_data($input_post['rk_getdate'], $kind);
    		}

    		// 対象順位データを取得 ( 0:全件, 1:一部, 2:個別 )
    		if ($input_post['submit'] == 'chg_all')
    		{
    			// 全順位データ書換(UPDATE)
    			$kind = 0;
    			//$this->_chg_rank_data($input_post['rk_getdate'], $kind);

    		} else {
    			// 不足分の順位データ書換(UPDATE)
    			/*
    			 * ここでは、errorステータス(=9009)のみを対象とする
    			 */
    			$kind = 1;
    			//$this->_chg_rank_data($input_post['rk_getdate'], $kind);

    		}
    		$result = $this->lib_ranking_data->chg_ranking_data($input_post['rk_getdate'], $kind);

    		if ($result == "")
    		{
    			$mess = "<font color=red>順位データ書換処理が失敗しました。</font>";
    		} else {
   				$mess = $result;
    		}

    		// 監視テーブルに検索終了を書き込む
    		$_set_mn_data['mn_date']               = $_mn_date;
    		$_set_mn_data['mn_status']             = $opt_monitoring['neutral'];

    		$this->mn->update_monitoring($_set_mn_data, 9);

    	}

    	$this->smarty->assign('mess01', $mess);

    	$this->view('rank_create/index.tpl');

    }

//     /**
//      * 順位データの書換え
//      *
//      * @param    date : 該当日付
//      * @param    int  : 0:全データ(UPDATE), 1:不足データ(UPDATE), 9:全データ(INSERT)
//      * @return   array()
//      */
//     private function _chg_rank_data($rk_getdate, $kind)
//     {

//     	if ($kind === 9)
//     	{
//     		//return;
//     	} else {
// 	    	$get_rk_today = $this->rk->get_rk_getdatelist($rk_getdate, $_SESSION['c_memGrp'], $kind);
// 	    	if (empty($get_rk_today))
// 	    	{
// 	    		return;
// 	    	}
//     	}

//     	// トランザクション・START
//     	$this->db->trans_strict(FALSE);                                     // StrictモードをOFF
//     	$this->db->trans_start();                                           // trans_begin

//     		$date = new DateTime($rk_getdate);
//     		$_before_date = $date->modify("-1 days")->format('Y-m-d');

//     		if ($kind !== 9)
//     		{
// 		    	foreach ($get_rk_today as $key => $value)
// 		    	{
// 		    		// 前日データ有無の確認＆取得
// 		    		$get_rk_before = $this->rk->get_rk_getdatelist($_before_date, $_SESSION['c_memGrp'], $kind=2, $value['rk_kw_seq']);

// 		    		// データ更新 ←これだと時間がかかるか？
// 		    		if (!empty($get_rk_before))
// 		    		{
// 		    			$set_rk_data               = $get_rk_before[0];
// 		    			$set_rk_data['rk_seq']     = $value['rk_seq'];
// 		    			$set_rk_data['rk_getdate'] = $value['rk_getdate'];

// 		    			$this->rk->update_ranking($set_rk_data);
// 		    		}
// 		    	}
//     		} else {

//     			// 前日データ有無の確認＆取得
//     			$get_rk_before = $this->rk->get_rk_getdatelist($_before_date, $_SESSION['c_memGrp'], $kind);

//     			// INSERT
//     			foreach ($get_rk_before as $key => $value)
//     			{
//     				$set_rk_data['rk_cl_seq']        = $value['rk_cl_seq'];
//     				$set_rk_data['rk_kw_seq']        = $value['rk_kw_seq'];
//     				$set_rk_data['rk_se_seq']        = $value['rk_se_seq'];
//     				$set_rk_data['rk_result_id']     = $value['rk_result_id'];
//     				$set_rk_data['rk_position']      = $value['rk_position'];
//     				$set_rk_data['rk_ranking_url']   = $value['rk_ranking_url'];
//     				$set_rk_data['rk_ranking_title'] = $value['rk_ranking_title'];
//     				$set_rk_data['rk_getdate']       = $rk_getdate;

//     				$this->rk->insert_ranking($set_rk_data);
//     			}
//     		}

//     	// トランザクション・COMMIT
//     	$this->db->trans_complete();                                        // trans_rollback & trans_commit
//     	if ($this->db->trans_status() === FALSE)
//     	{
//     		//$this->smarty->assign('mess',  "トランザクションエラーが発生しました。");
//     		log_message('error', 'client::[Rank_create->_chg_rank_data()]順位データの書換え処理 トランザクションエラー');

//     		redirect('/rank_create/');
//     	} else {
//     		//$this->smarty->assign('mess',  "更新が完了しました。");
//     	}

//     }









    // 順位データの取得
    public function manual_test()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->load->model('Monitoring', 'mn', TRUE);
    	$this->load->library('lib_ranking_data');
    	$this->config->load('config_status');


    	// 取得状況を監視チェックする
    	$opt_monitoring = $this->config->item('MONITORING_STATUS');

    	$get_monitoring = $this->mn->get_status(1);
    	if ($get_monitoring[0]['mn_status'] != $opt_monitoring['neutral'])
    	{
    		if ($get_monitoring[0]['mn_status'] == $opt_monitoring['get_start_g'])
    		{
    			$mess = "<font color=red>ERROR::現在、データを取得中(G)です。しばらくしてから実行してください。</font>";
    		} elseif ($get_monitoring[0]['mn_status'] == $opt_monitoring['get_end_g']) {
    			$mess = "<font color=red>ERROR::現在、順位を取得中(G)です。しばらくしてから実行してください。</font>";
    		} elseif ($get_monitoring[0]['mn_status'] == $opt_monitoring['get_start_y']) {
    			$mess = "<font color=red>ERROR::現在、順位を取得中(Y)です。しばらくしてから実行してください。</font>";
    		} elseif ($get_monitoring[0]['mn_status'] == $opt_monitoring['get_end_y']) {
    			$mess = "<font color=red>ERROR::現在、順位を取得中(Y)です。しばらくしてから実行してください。</font>";
    		} elseif ($get_monitoring[0]['mn_status'] == $opt_monitoring['rank_start']) {
    			$mess = "<font color=red>ERROR::現在、順位を書込み中です。しばらくしてから実行してください。</font>";
    		} elseif ($get_monitoring[0]['mn_status'] == $opt_monitoring['rank_end']) {
    			$mess = "<font color=red>ERROR::現在、順位を書込み中です。しばらくしてから実行してください。</font>";
    		}
    	} else {

    		$this->load->model('Project',     'pj', TRUE);
    		$this->load->model('Search_data', 'sd', TRUE);


    		// 監視テーブルに検索開始(G)を書き込む
      		$this->mn->update_status(1, $opt_monitoring['get_start_g'], 0);

    		// 対象受注案件情報を取得（SEO固定＆SEO成功）
    		$get_pj_list = $this->pj->get_search_data($_SESSION['c_memGrp']);

    		// 検索結果の取得 : Google用
    		$cnt = 0;
    		$_engine = "g";
    		foreach ($get_pj_list as $key => $value)
    		{

    			if (($value['pj_engine'] == "10") || ($value['pj_engine'] == "11"))
    			{
    				// URL整形
    				$_url = $this->lib_ranking_data->create_seach_url($value, $_engine);

    				// 検索データを取得
    				list($err_mess, $get_serach_data, $rank) = $this->lib_ranking_data->get_seach_url(
    																									$_url,
    																									$value['pj_compare_domain'],
    																									$value['pj_url_match']
    															);

    				// 検索データを書き込み
    				$value['sd_ranking'] = $rank;
    				$this->sd->insert_seach_data($get_serach_data, $err_mess, $value, $_engine);

    				$cnt++;
    			}

    		}

    		// 監視テーブルに検索終了(G)を書き込む
    		$this->mn->update_status(1, $opt_monitoring['get_end_g'], $cnt);

    		$mess = "Google の検索データ ; " . $cnt . " 件<br>";

    		// 監視テーブルに検索開始(Y)を書き込む
    		$this->mn->update_status(1, $opt_monitoring['get_start_y'], 0);

    		// 整形 : Yahoo用
    		$cnt = 0;
    		$_engine = "y";
    		foreach ($get_pj_list as $key => $value)
    		{

    			if (($value['pj_engine'] == "01") || ($value['pj_engine'] == "11"))
    			{
    				// URL整形
    				$_url = $this->lib_ranking_data->create_seach_url($value, $_engine);

    				// 検索データを取得
    				list($err_mess, $get_serach_data, $rank) = $this->lib_ranking_data->get_seach_url(
																			    						$_url,
																			    						$value['pj_compare_domain'],
																			    						$value['pj_url_match']
    														);

    				// 検索データを書き込み
    				$value['sd_ranking'] = $rank;
    				$this->sd->insert_seach_data($get_serach_data, $err_mess, $value, $_engine);

    				$cnt++;
    			}

    		}

    		$mess .= "Yahoo! の検索データ ; " . $cnt . " 件<br>";
    		$mess .= "<br><font color=blue>検索データ の取得が完了しました。</font>";

    		// 監視テーブルに検索終了(Y)を書き込む
    		$this->mn->update_status(1, $opt_monitoring['get_end_y'], $cnt);


    	}


    	$this->smarty->assign('mess', $mess);

    	$this->view('rank_create/index.tpl');

    }







    // BLOB順位データの取得　　テスト
    public function manual_blob()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->load->model('Search_data', 'sd', TRUE);


    	// BLOB（検索結果）データを取得
    	$get_search_data = $this->sd->get_sd_seq($sd_seq=7);


    	$json = mb_convert_encoding($get_search_data[0]['sd_data'], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    	$get_search_data_json = json_decode($json, true);



//     	print_r($get_search_data_json);
//     	print("<br><br>");


//     	print_r($get_search_data_json['result']['organic']);
//     	print("<br><br>");

//     	print_r($get_search_data_json['result']['organic'][1]['title']);
//     	exit;



    	$this->smarty->assign('mess', "");

    	$this->view('rank_create/index.tpl');

    }








    // フォーム・バリデーションチェック : クライアント追加
    private function _set_validation()
    {
        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    // フォーム・バリデーションチェック : クライアント追加
    private function _set_validation01()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'rk_getdate',
					    'label'   => '書換日',
					    'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

