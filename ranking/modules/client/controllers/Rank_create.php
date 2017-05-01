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

        $this->smarty->assign('mess', FALSE);

    }

    // 順位データ取得画面
    public function index()
    {

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
    	if (count($get_mn_data) > 0)
    	{

    		if ($get_mn_data[0]['mn_status'] != $opt_monitoring['neutral'])
    		{
    			$mess = "<font color=red>ERROR::現在、データを取得中です。しばらくしてから実行してください。</font>";

    			$this->smarty->assign('mess', $mess);
    			$this->view('rank_create/index.tpl');

    			return ;
    		}

    		$_get_cnt = $get_mn_data[0]['mn_cnt'];
    	} else {
    		$_get_cnt = 0;
    	}

		// 検索対象となるKEYWORDデータを抽出
    	$get_kw_data = $this->kw->get_keyword_data($_get_cnt);

    	// 監視テーブルに開始時刻を書き込む
    	$_set_mn_data['mn_date']   = $_mn_date;
    	$_set_mn_data['mn_status'] = $opt_monitoring['start'];
    	$_set_mn_data['mn_cnt']    = $_get_cnt;
    	if ($_get_cnt == 0)
    	{
    		$this->mn->insert_monitoring($_set_mn_data, $_get_cnt);
    	} else {
    		$this->mn->update_monitoring($_set_mn_data, $_get_cnt);
    	}

    	if (count($get_kw_data) == 0)
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
    		$_set_mn_data['mn_search_cnt' . $_no]  = 9999999;
    		$_set_mn_data['mn_ranking_cnt' . $_no] = 9999999;

    		$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

    		return;
    	}

    	// ** 検索＆順位取得を実行する **
    	list($_get_cnt, $_search_cnt, $_rank_cnt) = $this->lib_ranking_data->exec_ranking($get_kw_data, $_get_cnt);

    	// 監視テーブルに検索終了を書き込む
    	$_set_mn_data['mn_date']               = $_mn_date;
    	$_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
    	$_set_mn_data['mn_cnt']                = $_get_cnt;
    	$_no = $_get_cnt - 1;
    	$_set_mn_data['mn_search_cnt' . $_no]  = $_search_cnt;
    	$_set_mn_data['mn_ranking_cnt' . $_no] = $_rank_cnt;

    	$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

    	$mess = "<font color=blue>順位データ取得処理が終了しました。</font>";
    	$this->smarty->assign('mess', $mess);

    	$this->view('rank_create/index.tpl');

    }















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

}

