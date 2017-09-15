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
     *  「分(*時*分10秒)」間隔バッチのメイン処理
     */
    public function minute_bat()
    {

    	$_st_day = date("Y-m-d H:i:s", time());



    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'minute_bat::** 分間隔バッチ ** ' . $_st_day . ' => ' . $_ed_day);

    }

    /**
     *  「時(*時45分00秒)」バッチのメイン処理
     */
    public function hour_bat()
    {

    	log_message('info', '** 時間バッチ ** ');

    	$_st_day = date("Y-m-d H:i:s", time());

    	// 順位データerrorステータスのリトライ
    	$this->_get_ranking_retry();

    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'hour_bat::** 時間バッチ ** ' . $_st_day . ' => ' . $_ed_day);

    }

    /**
     *  「日::夜間(03時30分)」間隔バッチのメイン処理
     */
    public function day_bat()
    {

    	$_st_day = date("Y-m-d H:i:s", time());

    	// DB & PG のバックアップ処理
    	$this->_system_backup();

    	// セッション情報削除 (一か月前)
    	$this->_sess_destroy();

    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'day_bat::** 日次バッチ ** ' . $_st_day . ' => ' . $_ed_day);

    }

    /**
     *  「日::夜間(0時05分)」間隔バッチのメイン処理：順位データ補完処理
     */
    public function day_bat01()
    {

    	log_message('info', 'day_bat::** 日次バッチ ** ');

    	$_st_day = date("Y-m-d H:i:s", time());

    	// 順位データ補完処理
    	$this->_rank_complement();

    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'day_bat::** 日次バッチ ** ' . $_st_day . ' => ' . $_ed_day);

    }

    /**
     *  「月」間隔バッチのメイン処理
     */
    public function month_bat()
    {

        $_st_day = date("Y-m-d H:i:s", time());


        $_ed_day = date("Y-m-d H:i:s", time());
        log_message('info', 'month_bat::** 月次バッチ ** ' . $_st_day . ' => ' . $_ed_day);
    }

    /**
     *  時間指定バッチのメイン処理
     *  順位データ取得
     */
    public function ranking_bat()
    {

    	$_st_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'ranking_bat::** 順位取得バッチ ** ' . $_st_day);


    	// 順位データ取得
    	$this->_get_ranking();


    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'ranking_bat::** 順位取得バッチ ** ' . $_st_day . ' => ' . $_ed_day);
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

    	/*
    	 * /opt/lampp/etc/php.ini
    	 *   memory_limit=1024M
    	 */
    	ini_set('memory_limit', '256M');


        $date = new DateTime();
        $_set_time = $date->format('Y-m-d H:i:s');

        $this->load->model('Keyword',    'kw', TRUE);
        $this->load->model('Monitoring', 'mn', TRUE);
        $this->load->model('Serps',      'sp', TRUE);
        $this->load->model('Ranking',    'rk', TRUE);
        $this->load->library('lib_ranking_data');
        $this->load->library('lib_rootdomain');
        $this->config->load('config_status');

        /*
         * 検索生データのログを削除
         */
        $this->load->model('Serpslog',   'sl', TRUE);
        $this->sl->delete_serpslog();										// 31日削除


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
        		log_message('error', 'Batch->_get_ranking::順位データを取得中です。' . $_set_time);
        		return ;
        	}

        	$_get_cnt = $get_mn_data[0]['mn_cnt'];
        	if ($_get_cnt >= 3)
        	{
        		log_message('error', 'Batch->_get_ranking::１日の順位データ取得回数をオーバーしています。' . $_set_time);
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

        /*
         * 現在仕様変更により、1日の取得回数が 3 → 1回 となっている。
         */
        if (empty($get_kw_data))
        {
        	// 監視テーブルに検索終了を書き込む
        	$_get_cnt = $_get_cnt + 1;
        	$_set_mn_data['mn_date']               = $_mn_date;
        	$_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
        	$_set_mn_data['mn_cnt']                = $_get_cnt;
        	$_no = $_get_cnt - 1;
        	$_set_mn_data['mn_search_cnt' . $_no]  = 9999999;
        	$_set_mn_data['mn_ranking_cnt' . $_no] = 9999999;

        	log_message('info', 'Batch->_get_ranking::対象キーワードが存在しません。' . $_set_time);
        	$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

        	return;
        }


        /*
         * 手動によるデータベース接続
         *
         * Codeigniterのバグか分からないが検索データの大量データをinsertすると
         * PHPメモリを大量に消費してメモリ不足になる。
         *
         * その回避策として、この部分のみ手動接続とする
         *
         */
        $date = new DateTime();
        $_set_time = $date->format('Y-m-d H:i:s');

        try {
        	$this->dbh = new PDO('mysql:host=localhost;dbname=seorank', 'seorank', 'db!mp', array(
        								PDO::ATTR_PERSISTENT => true,
        								PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        	));
        	log_message('info', '_get_ranking::手動DB接続に成功しました。' . $_set_time);

        } catch ( PDOException $e ) {
        	log_message('error', '_get_ranking::手動DB接続エラー。' . $_set_time);

        	// 監視テーブルに検索終了を書き込む
        	$_get_cnt = $_get_cnt + 1;
        	$_set_mn_data['mn_date']               = $_mn_date;
        	$_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
        	$_set_mn_data['mn_cnt']                = $_get_cnt;
        	$_no = $_get_cnt - 1;
        	$_set_mn_data['mn_search_cnt' . $_no]  = 9999999;
        	$_set_mn_data['mn_ranking_cnt' . $_no] = 9999999;

        	$this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

        	die();
        }


        // ** 検索＆順位取得を実行する **
        list($_get_cnt, $_search_cnt, $_rank_cnt) = $this->lib_ranking_data->exec_ranking($get_kw_data, $_get_cnt);


        // PDO切断
        $this->dbh = null;


        // ** 引継ぎURLを含めて最高順位に書き換え対応
        $this->lib_ranking_data->top_ranking();

        /*
         * ここからは、上の処理で順位を取得できなかったKWの再取得を行う！？
         *
         * 再取得を5回にセット。
         */
        //for ($i = 100; $i <= 104;$i++)
        for ($i = 100; $i <= 100; $i++)
        {

        	// ** 検索＆順位取得を実行する **
        	$this->lib_ranking_data->exec_ranking($get_kw_data, $i);

        	// ** 引継ぎURLを含めて最高順位に書き換え対応
        	$this->lib_ranking_data->top_ranking();

        }
        /* end */

        // 監視テーブルに検索終了を書き込む
        $_set_mn_data['mn_date']               = $_mn_date;
        $_set_mn_data['mn_status']             = $opt_monitoring['neutral'];
        $_set_mn_data['mn_cnt']                = $_get_cnt;
        $_no = $_get_cnt - 1;
        $_set_mn_data['mn_search_cnt' . $_no]  = $_search_cnt;
        $_set_mn_data['mn_ranking_cnt' . $_no] = $_rank_cnt;

        $this->mn->update_monitoring($_set_mn_data, $_get_cnt-1);

    }

    /**
     *  毎時：順位データ取得（エラーステータスのみ）
     *  ・errorステータスがなくなるまで実行
     */
    private function _get_ranking_retry()
    {

    	/*
    	 * /opt/lampp/etc/php.ini
    	 *   memory_limit=1024M
    	 */
    	ini_set('memory_limit', '256M');


    	$date = new DateTime();
    	$_set_time = $date->format('Y-m-d H:i:s');

    	$this->load->model('Keyword',    'kw', TRUE);
    	$this->load->model('Monitoring', 'mn', TRUE);
    	$this->load->model('Serps',      'sp', TRUE);
    	$this->load->model('Serpslog',   'sl', TRUE);
    	$this->load->model('Ranking',    'rk', TRUE);
    	$this->load->library('lib_ranking_data');
    	$this->load->library('lib_rootdomain');
    	$this->config->load('config_status');

    	// 取得状況を監視チェックする : 本日の実行回数を取得
    	$date = new DateTime();
    	$_mn_date = $date->format('Y-m-d');

    	$opt_monitoring = $this->config->item('MONITORING_STATUS');

    	$get_mn_data = $this->mn->get_mn_date($_mn_date);
    	if (empty($get_mn_data))
    	{
    		log_message('error', 'Batch->_get_ranking_retry::順位データ取得が実行されていません。' . $_set_time);
    		return ;
    	} else {

    		if ($get_mn_data[0]['mn_status'] != $opt_monitoring['neutral'])
    		{
    			log_message('error', 'Batch->_get_ranking_retry::順位データを取得中です。' . $_set_time);
    			return ;
    		}

    	}

    	// 検索対象となるKEYWORDデータを抽出
    	$get_kw_data = $this->kw->get_keyword_data(0);

    	if (empty($get_kw_data))
    	{
    		log_message('info', 'Batch->_get_ranking_retry::対象キーワードが存在しません。' . $_set_time);
    		return;
    	}

    	/*
    	 * ここからは、上の処理で順位を取得できなかったKWの再取得を行う！？
    	 *
    	 * 再取得を5回にセット。
    	 * 最初の取得で全滅の場合、ここを実行すると1日で終わらなくなる可能性あり。
    	 *
    	 */
    	for ($i = 100; $i <= 104;$i++)
    	{

    		$date = new DateTime();
    		$_now_time = $date->format('H:i');

    		if ($_now_time <= '17:30')
    		{
	    		// ** 検索＆順位取得を実行する **
	    		list($_get_cnt, $_search_cnt, $_rank_cnt) = $this->lib_ranking_data->exec_ranking($get_kw_data, $i);
	    		log_message('info', 'Batch->_get_ranking_retry::順位データ取得リトライが実行されました。検索データ数=' . $_search_cnt . ', 順位取得データ数=' . $_rank_cnt);

	    		// ** 引継ぎURLを含めて最高順位に書き換え対応
	    		$this->lib_ranking_data->top_ranking();
    		}

    	}
    	/* end */

    	// ログ出力
    	$date = new DateTime();
    	$_ed_time = $date->format('Y-m-d H:i:s');
    	log_message('info', 'Batch->_get_ranking_retry::順位データ取得リトライが終了しました。' . $_set_time . ' => ' . $_ed_time);

    }

    /**
     *  日次：順位データ補完
     *  ・error(9009)ステータスが存在する場合、前々日データを補完する
     */
    private function _rank_complement()
    {

    	$this->load->model('Keyword',    'kw', TRUE);
    	$this->load->model('Monitoring', 'mn', TRUE);
    	$this->load->model('Ranking',    'rk', TRUE);
    	$this->load->library('lib_ranking_data');
    	$this->config->load('config_status');

    	$mess = NULL;

    	// 前日の順位取得状況を監視チェックする
    	$date = new DateTime();
    	$_today = $date->format('Y-m-d');

    	$_yesterday = $date->modify('-1 days')->format('Y-m-d');
    	$opt_monitoring = $this->config->item('MONITORING_STATUS');
    	//$_set_mn_data['mn_date']   = $_mn_date;
    	//$_set_mn_data['mn_status'] = $opt_monitoring['start'];

    	$get_mn_data = $this->mn->get_mn_date($_yesterday);
    	if (!empty($get_mn_data))
    	{
    		if ($get_mn_data[0]['mn_status'] != $opt_monitoring['neutral'])
    		{
    			$mess = "ERROR::現在データを取得中です。";
    			$result = $this->_sendmail($mail_no=1, $mess);

    			log_message('error', 'Batch->_rank_complement::日次：順位データ補完処理　現在データを取得中。');

    			return ;
    		}
    	}

    	// 順位データ補完(UPDATE)
    	/*
    	 * 各クライアント毎に、件数やメール配信となると面倒になる！
    	 * 現行確認するには、tb_rankingのupdate_date項目にて該当日次(00:05)を確認
    	 */
    	$kind = 3;
    	$result = $this->lib_ranking_data->chg_ranking_data($_yesterday, $kind);
    	if ($result == "")
    	{
    		$result = $this->_sendmail($mail_no=2, $mess);
    		log_message('error', 'Batch->_rank_complement::日次：順位データ補完処理　処理が失敗しました。');
    	} else {
    		$result = $this->_sendmail($mail_no=3, $mess);
    		log_message('info', 'Batch->_rank_complement::日次：順位データ補完処理　処理が終了しました。');
    	}
    }

    // メール送信処理
    private function _sendmail($mail_no, $mess)
    {

    	if ($mail_no == 1)
    	{
    		$mail['subject']   = "日次：順位データ補完処理エラー";
    		$mail['body']      = "順位データ取得処理が実行中または不具合の為、処理が失敗しました。";
    	} elseif($mail_no == 2) {
    		$mail['subject']   = "日次：順位データ補完処理エラー";
    		$mail['body']      = "データベース処理において、トランザクションエラーが発生して処理が失敗しました。";
    	} else {
    		$mail['subject']   = "日次：順位データ補完処理 終了";
    		$mail['body']      = "順位データ取得処理が終了しました。";
    	}

    	// メール送信先設定
    	$this->config->load('config_comm');
    	$mail['from']      = $this->config->item('MAIL_SEND_ADDR');;
    	$mail['from_name'] = "ランキングサーバ管理者";
    	$mail['to']        = $this->config->item('MAIL_ADMIN_ADDR');;
    	$mail['cc']        = "";
    	$mail['bcc']       = "";


    	$this->load->library('email');                                        // メール送信クラス読み込み

    	$from_name = mb_encode_mimeheader($mail['from_name'], 'ISO-2022-JP', 'UTF-8');
    	$subject   = mb_convert_encoding ($mail['subject'],   'SJIS-win',    'UTF-8');
    	$body      = mb_convert_encoding ($mail['body'],      'SJIS-win',    'UTF-8');
    	//$subject   = mb_convert_encoding ($mail['subject'], 'ISO-2022-JP-MS', 'UTF-8');        // 一部で文字化けが発生！
    	//$body      = mb_convert_encoding ($mail['body'],    'ISO-2022-JP-MS', 'UTF-8');

    	$this->email->clear();
    	$this->email->reply_to('autoreply@ranking.dev.local', 'Ranking');
    	$this->email->from($mail['from'] , $from_name);
    	$this->email->to($mail['to']);
    	$this->email->cc($mail['cc']);
    	$this->email->bcc($mail['bcc']);
    	$this->email->subject($subject);
    	$this->email->message($body);

    	if ($this->email->send()) {
    		return TRUE;
    	} else {
    		return FALSE;
    	}

    	echo $this->email->print_debugger();

    }

}