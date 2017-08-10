<?php

class System extends MY_Controller
{

    /*
     *  システム関連
     *
     *    > DB & System バックアップ
     *    > セッション情報削除
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

//         $this->smarty->assign('up_mess',   NULL);
//         $this->smarty->assign('up_mess02', NULL);
        $this->smarty->assign('up_mess03', NULL);
//         $this->smarty->assign('dl_mess',   NULL);

    }

    // 初期表示
    public function index()
    {

        $this->view('system/index.tpl');

    }

    // データCSVのアップロード処理TOP
    public function rank_csvup()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->view('system/rank_csvup.tpl');

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
    		$this->view('system/rank_csvup.tpl');
    		return;
    	}

    	try{
    		// CSVファイルの読み込み
    		$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
    		$_csv_data = $this->lib_csvparser->parse();
    	} catch (Exception $e){
    		$up_mess .= "<font color=red>エラー発生:" . $e->getMessage() . '</font><br><br>';

    		$this->smarty->assign('up_mess03', $up_mess);
    		$this->view('system/rank_csvup.tpl');
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

    	$this->view('system/rank_csvup.tpl');

    }

    // DB & System バックアップ
    public function backup()
    {

        // sh に記述
        $arg = 1;                                                    // 判定用に引数を渡す

        // DBのバックアップ
        $app_path = "/var/www/ranking/backup/";
        $strCommand = $app_path . 'backup4mysql.sh  "'.$arg.'"';
        //$strCommand = $app_path . 'backup4mysql.sh';
        exec( $strCommand );

        // システムのバックアップ (手動データ作成)
        $app_path = "/var/www/ranking/backup/";
        $strCommand = $app_path . 'backup4pg.sh  "'.$arg.'"';
        //$strCommand = $app_path . 'backup4pg.sh';
        exec( $strCommand );

        $this->view('system/index.tpl');

    }

    // セッション情報削除 (一か月前)
    public function sess_destroy()
    {

        // 一か月前のセッションを削除
        $now_time = time();
//      $del_time = strtotime('-1 month' , $now_time);
        $del_time = strtotime('-1 hour' , $now_time);

        $this->load->model('Ci_sessions', 'sess', TRUE);
        $this->sess->destroy_session($del_time);

        $this->view('system/index.tpl');

    }

    // グラフの キャッシュ (Memcached) を削除する
    public function memcached_delete()
    {

        $this->load->driver('cache', array('adapter' => 'memcached'));

        if ($this->cache->is_supported('memcached'))
        {
            $this->cache->delete('key_salesman');                               // 月次売上グラフ：全体
            $this->cache->delete('key_monthly');                                // 月次売上グラフ：担当者別
            $this->cache->delete('key_accounting');                             // 月次売上グラフ：課金方式別


//          $this->cache->clean();                                              // 全てのキャッシュデータを削除

//          if ($this->cache->delete('key_salesman')) {
//              // キャッシュデータを削除しました。
//              echo var_export($this->cache->get_metadata('key_salesman'), TRUE);
//              // false
//          }
//          else {
//              // キャッシュデータの削除に失敗しました。
//          }

        }

        $this->view('system/index.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}
