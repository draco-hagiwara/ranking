<?php

class Batch extends MY_Controller
{

    /*
     *  ＣＲＯＮバッチ処理
     *
     *    > DB & PG のバックアップ処理
     *    > セッション情報削除
     *    > 売上データの集計
     */

    public function __construct()
    {

        parent::__construct();

        // CLI実行かのチェック :: URIからの直接実行拒否
        if (!is_cli()) {
            log_message('error', 'CLI以外からのアクセスがありました。');
            show_404();
        }
    }

    /**
     *  「分」間隔バッチのメイン処理
     */
    public function minute_bat()
    {


    }

    /**
     *  「時::夜間(02:10)」バッチのメイン処理
     */
    public function hour_bat()
    {

        $_st_day = date("Y-m-d H:i:s", time());

        // DB & PG のバックアップ処理
        $this->_system_backup();

        // セッション情報削除 (一か月前)
        $this->_sess_destroy();

        $_ed_day = date("Y-m-d H:i:s", time());
        log_message('info', 'hour_bat::** 夜間バッチ ** ' . $_st_day . ' => ' . $_ed_day);
    }

    /**
     *  「日」間隔バッチのメイン処理
     */
    public function day_bat()
    {

    }

    /**
     *  「月」間隔バッチのメイン処理
     */
    public function month_bat()
    {

        $_st_day = date("Y-m-d H:i:s", time());


        $_ed_day = date("Y-m-d H:i:s", time());
        log_message('info', 'month_bat::** 夜間バッチ ** ' . $_st_day . ' => ' . $_ed_day);
    }

    /**
     *  時間指定バッチのメイン処理
     */
    public function ranking_bat()
    {

    	$_st_day = date("Y-m-d H:i:s", time());


    	// 順位データ取得
    	$this->_get_ranking();


    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'month_bat::** 夜間バッチ ** ' . $_st_day . ' => ' . $_ed_day);
    }



    /**
     *  テストデータ作成BAT
     */
    public function test_data_bat()
    {

    	set_time_limit(0);
    	/*
    	 * /opt/lampp/etc/php.ini
    	 *   memory_limit=1024M
    	 */
    	ini_set('memory_limit', '1024M');


    	$_st_day = date("Y-m-d H:i:s", time());


    	// テストデータ作成
    	$this->load->model('Keyword', 'kw', TRUE);
    	$this->load->model('Ranking', 'rk', TRUE);

    	// KW数
    	$word = 20000;

//     	for ($i=1; $i<=$word; $i++)
//     	{

//     		// KW作成
//     		$set_kw_data['kw_old_seq']       = NULL;
//     		$set_kw_data['kw_status']        = 1;
//     		$set_kw_data['kw_url']           = "http://www.sub1.sample" . $i . ".com/";
//     		$set_kw_data['kw_domain']        = "sub1.sample" . $i . ".com";
//     		$set_kw_data['kw_rootdomain']    = "sample" . $i . ".com";
//     		$set_kw_data['kw_keyword']       = "kw-" . $i;
//     		$set_kw_data['kw_matchtype']     = 3;
//     		$set_kw_data['kw_searchengine']  = 0;
//     		$set_kw_data['kw_device']        = 0;
//     		$set_kw_data['kw_location_id']   = 2392;
//     		$set_kw_data['kw_location_name'] = "Japan";
//     		$set_kw_data['kw_maxposition']   = 0;
//     		$set_kw_data['kw_trytimes']      = 0;
//     		$set_kw_data['kw_group']         = NULL;
//     		$set_kw_data['kw_tag']           = NULL;
//     		$set_kw_data['kw_cl_seq']        = 3;
//     		$set_kw_data['kw_ac_seq']        = 11;

//     		$this->kw->insert_keyword($set_kw_data);
//     	}





// 		// Rankingデータ作成
// 		$set_rk_data['rk_cl_seq']        = 3;
// 		$set_rk_data['rk_kw_old_seq']    = NULL;
// 		$set_rk_data['rk_se_seq']        = 0;
// 		$set_rk_data['rk_result_id']     = "xxxxx";
// 		$set_rk_data['rk_se_seq_re']     = NULL;
// 		$set_rk_data['rk_result_id_re']  = NULL;
// 		$set_rk_data['rk_position_org']  = NULL;
// 		$set_rk_data['rk_ranking_url']   = NULL;
// 		$set_rk_data['rk_ranking_title'] = NULL;


//     	for ($i=300; $i>=0; $i--)								// 31日*7ヶ月 = 217日
//     	{
//     		$date = new DateTime();
//     		$_today_date   = $date->format('Y-m-d');
//     		$_set_cnt_date = "- " . $i . " days";
//     		$_set_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

//     		for ($j=1; $j<=$word; $j++)
//     		{

//     			// Rankingデータ作成
//     			$set_rk_data['rk_kw_seq']        = $j;
//     			$set_rk_data['rk_position']      = $i % 300;
//     			$set_rk_data['rk_getdate']       = $_set_date;

//     			//$this->rk->insert_ranking($set_rk_data);
//     			$query = $this->db->insert('tb_ranking', $set_rk_data);

//     		}
//     	}






		/*
		 * 約2時間+
		 */
    	for ($i=365; $i>=0; $i--)								// 31日*7ヶ月 = 217日
    	{
    		$date = new DateTime();
    		$_today_date   = $date->format('Y-m-d');
    		$_set_cnt_date = "- " . $i . " days";
    		$_set_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

    		$set_rk_data = array();
    		for ($j=1; $j<=$word; $j++)
    		{

    			// Rankingデータ作成
    			$set_rk_data[] = array(
				    					'rk_cl_seq'        => 3,
    									'rk_kw_old_seq'    => NULL,
				    					'rk_se_seq'        => 0,
				    					'rk_result_id'     => "xxxxx",
				    					'rk_se_seq_re'     => NULL,
				    					'rk_result_id_re'  => NULL,
				    					'rk_position_org'  => NULL,
				    					'rk_ranking_url'   => NULL,
				    					'rk_ranking_title' => NULL,
				    					'rk_kw_seq'        => $j,
				    					'rk_position'      => (($i % 300) + 1),
				    					'rk_getdate'       => $_set_date
    			);

    		}

    		//$this->rk->insert_ranking($set_rk_data);
    		//$query = $this->db->insert('tb_ranking', $set_rk_data);
    		$query = $this->db->insert_batch('tb_ranking', $set_rk_data);			// 一括バッチ

    		unset($set_rk_data[]);													// メモリ解放？

    	}


    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'test_data_bat::** テストデータ作成バッチ ** ' . $_st_day . ' => ' . $_ed_day);
    }



    /**
     *  日次：DB & PG のシステムバックアップ処理
     */
    private function _system_backup()
    {

        $date = new DateTime();
        $_set_time = $date->format('Y-m-d H:i:s');

        // インストールパスを取得 :: /var/www/ranking/backup/
        $this->load->helper('path');
        $root_path = '../';
        $base_path = set_realpath($root_path);

        // sh に記述
        $strCommand = $base_path . 'backup/backup4mysql.sh';
        exec( $strCommand );

        $strCommand = $base_path . 'backup/backup4pg.sh';
        exec( $strCommand );

        // ログ出力
        $date = new DateTime();
        $_ed_time = $date->format('Y-m-d H:i:s');
        log_message('info', 'bat::バックアップ処理が実行されました。' . $_set_time . ' => ' . $_ed_time);

    }

    /**
     *  日次：セッション情報削除 (一か月前)
     */
    private function _sess_destroy()
    {

        $date = new DateTime();
        $_set_time = $date->format('Y-m-d H:i:s');

        // 一か月前のセッションを削除
        $del_time = $date->modify('-1 days')->format('U');
//      $del_time = $date->modify('-1 months')->format('U');

        $this->load->model('Ci_sessions', 'sess', TRUE);
        $this->sess->destroy_session($del_time);

        // ログ出力
        $date = new DateTime();
        $_ed_time = $date->format('Y-m-d H:i:s');
        log_message('info', 'bat::セッション情報削除が実行されました。' . $_set_time . ' => ' . $_ed_time);

    }

    /**
     *  時間指定：順位データ取得
     */
    private function _get_ranking()
    {

        $date = new DateTime();
        $_set_time = $date->format('Y-m-d H:i:s');

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

        	//if ($get_mn_data[0]['mn_status'] != $opt_monitoring['neutral'])
        	//{
        	//	$mess = "<font color=red>ERROR::現在、データを取得中です。しばらくしてから実行してください。</font>";
			//
        	//	$this->smarty->assign('mess', $mess);
        	//	$this->view('rank_create/index.tpl');
			//
        	//	return ;
        	//}

        	$_get_cnt = $get_mn_data[0]['mn_cnt'];
        	//if ($_get_cnt >= 3)
        	//{
        	//	$mess = "<font color=red>ERROR::１日の順位データ取得回数をオーバーしています。</font>";
        	//
        	//	$this->smarty->assign('mess', $mess);
        	//	$this->view('rank_create/index.tpl');
        	//
        	//	return ;
        	//}

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
        	$_set_mn_data['mn_search_cnt' . $_no]  = 9999999;
        	$_set_mn_data['mn_ranking_cnt' . $_no] = 9999999;

        	$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

        	return;
        }

        // ** 検索＆順位取得を実行する **
        list($_get_cnt, $_search_cnt, $_rank_cnt) = $this->lib_ranking_data->exec_ranking($get_kw_data, $_get_cnt);

        // ** 引継ぎURLを含めて最高順位に書き換え対応
        $this->lib_ranking_data->top_ranking();

        // 監視テーブルに検索終了を書き込む
        $_set_mn_data['mn_date']               = $_mn_date;
        $_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
        $_set_mn_data['mn_cnt']                = $_get_cnt;
        $_no = $_get_cnt - 1;
        $_set_mn_data['mn_search_cnt' . $_no]  = $_search_cnt;
        $_set_mn_data['mn_ranking_cnt' . $_no] = $_rank_cnt;

        $this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

        // ログ出力
        $date = new DateTime();
        $_ed_time = $date->format('Y-m-d H:i:s');
        log_message('info', 'bat::順位データ取得が実行されました。' . $_set_time . ' => ' . $_ed_time);

    }

}