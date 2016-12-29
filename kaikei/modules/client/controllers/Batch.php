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

    	// 売上データの集計
    	$this->_sales_summary();

    	$_ed_day = date("Y-m-d H:i:s", time());
    	log_message('info', 'bat::** 夜間バッチ ** ' . $_st_day . ' => ' . $_ed_day);
    }

    /**
     *  「日」間隔バッチのメイン処理
     */
    public function day_bat()
    {

    }


    /**
     *  日次：DB & PG のシステムバックアップ処理
     */
    private function _system_backup()
    {

    	$date = new DateTime();
    	$_set_time = $date->format('Y-m-d H:i:s');

        // インストールパスを取得 :: /var/www/kaikei/backup/
        $this->load->helper('path');
        $root_path = '../';
        $base_path = set_realpath($root_path);

		// sh に記述
        $strCommand = $base_path . 'backup/backup4mysql.sh';
    	exec( $strCommand );

    	$strCommand = $base_path . 'backup/backup4pg.sh';
    	exec( $strCommand );

    	// ログ出力
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
//     	$del_time = $date->modify('-1 months')->format('U');

    	$this->load->model('Ci_sessions', 'sess', TRUE);
    	$this->sess->destroy_session($del_time);

    	// ログ出力
    	$_ed_time = $date->format('Y-m-d H:i:s');
    	log_message('info', 'bat::セッション情報削除が実行されました。' . $_set_time . ' => ' . $_ed_time);

    }

    /**
     *  日次：売上データの集計
     */
    private function _sales_summary()
    {

    	$date = new DateTime();
    	$_set_time = $date->format('Y-m-d H:i:s');

    	$this->load->model('Invoice', 'iv',  TRUE);
    	$this->load->model('Sales',   'sa',  TRUE);

    	// 前日日付の請求書データを抽出
    	$get_sales_data = $this->iv->get_iv_sales($date->modify('-1 days')->format('Y-m-d'));

    	if (count($get_sales_data) >= 0)
    	{

    		// トランザクション・START
    		$this->db->trans_strict(FALSE);                                         // StrictモードをOFF
    		$this->db->trans_start();                                               // trans_begin

			foreach($get_sales_data as $key => $value)
			{

				// 売上月の指定：売上月度から指定月の売上に振り分ける
				$set_sales['sa_sales_date']  = substr($value['iv_salse_yymm'], 0, 4) . '-' . substr($value['iv_salse_yymm'], 4, 2) . '-01';
// 				$set_sales['sa_sales_date']  = $value['iv_sales_date'];

				$set_sales['sa_cm_seq']      = $value['iv_cm_seq'];
				$set_sales['sa_iv_seq']      = $value['iv_seq'];
				$set_sales['sa_slip_no']     = $value['iv_slip_no'];
				$set_sales['sa_tax']         = $value['iv_tax'];
				$set_sales['sa_total']       = $value['iv_total'];
				$set_sales['sa_company']     = $value['iv_company_cm'];
				$set_sales['sa_collect']     = $value['iv_collect'];
				$set_sales['sa_salesman']    = $value['iv_salesman'];
				$set_sales['sa_salesman_id'] = $value['iv_salesman_id'];
				$set_sales['sa_memo']        = $value['iv_memo'];

				$this->sa->insert_sales($set_sales);
			}

			// トランザクション・COMMIT
			$this->db->trans_complete();                                            // trans_rollback & trans_commit
			if ($this->db->trans_status() === FALSE)
			{
				log_message('error', 'CLIENT::[Batch -> _sales_summary()]：売上データ作成バッチ処理 トランザクションエラー');
			}

    	}

    	// ログ出力
    	$_ed_time = $date->format('Y-m-d H:i:s');
    	log_message('info', 'bat::売上データの集計が実行されました。' . $_set_time . ' => ' . $_ed_time);

    }

}