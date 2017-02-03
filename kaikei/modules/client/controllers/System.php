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

    }

    // 初期表示
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

        $this->view('system/index.tpl');

    }

    // DB & System バックアップ
    public function backup()
    {

    	// sh に記述

    	// DBのバックアップ
    	$app_path = "/var/www/kaikei/backup/";
    	$strCommand = $app_path . 'backup4mysql.sh';
    	exec( $strCommand );

    	// システムのバックアップ
    	$app_path = "/var/www/kaikei/backup/";
    	$strCommand = $app_path . 'backup4pg.sh';
    	exec( $strCommand );

    	$this->view('system/index.tpl');

    }

    // セッション情報削除 (一か月前)
    public function sess_destroy()
    {

    	// 一か月前のセッションを削除
    	$now_time = time();
//     	$del_time = strtotime('-1 month' , $now_time);
    	$del_time = strtotime('-1 hour' , $now_time);

    	$this->load->model('Ci_sessions', 'sess', TRUE);
    	$this->sess->destroy_session($del_time);

    	$this->view('system/index.tpl');

    }

}
