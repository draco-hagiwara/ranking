<?php

class System extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

            if ($_SESSION['c_login'] == TRUE)
        {
            $this->smarty->assign('login_chk', TRUE);
            $this->smarty->assign('mem_Type',  $_SESSION['c_memType']);
            $this->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
            $this->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
            $this->smarty->assign('mem_Name',  $_SESSION['c_memName']);
        } else {
            $this->smarty->assign('login_chk', FALSE);
            $this->smarty->assign('mem_Type',  "");
            $this->smarty->assign('mem_Seq',   "");
            $this->smarty->assign('mem_Grp',   "");

            redirect('/login/');
        }
    }

    // 初期表示
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->model('comm_auth', 'comm_auth', TRUE);
    	$this->comm_auth->delete_session('client');

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

//     // Pagination 設定
//     private function _get_Pagination($cate_countall, $tmp_per_page)
//     {

//     	$config['base_url']       = base_url() . '/system/categroup_search/';	// ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
//     	$config['per_page']       = $tmp_per_page;								// 1ページ当たりの表示件数。
//     	$config['total_rows']     = $cate_countall;								// 総件数。where指定するか？
//     	//$config['uri_segment']    = 4;										// オフセット値がURIパスの何セグメント目とするか設定
//     	$config['num_links']      = 5;											//現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
//     	$config['full_tag_open']  = '<p class="pagination">';					// ページネーションリンク全体を階層化するHTMLタグの先頭タグ文字列を指定
//     	$config['full_tag_close'] = '</p>';										// ページネーションリンク全体を階層化するHTMLタグの閉じタグ文字列を指定
//     	$config['first_link']     = '最初へ';									// 最初のページを表すテキスト。
//     	$config['last_link']      = '最後へ';									// 最後のページを表すテキスト。
//     	$config['prev_link']      = '前へ';										// 前のページへのリンクを表わす文字列を指定
//     	$config['next_link']      = '次へ';										// 次のページへのリンクを表わす文字列を指定

//     	$this->load->library('pagination', $config);							// Paginationクラス読み込み
//     	$set_page['page_link'] = $this->pagination->create_links();

//     	return $set_page;

//     }

//     // 初期値セット
//     private function _init_set()
//     {

//     	// 各項目を初期化
//     	$mailtpl_info['mt_id']        = 1;
//     	$mailtpl_info['mt_subject']   = NULL;
//     	$mailtpl_info['mt_body']      = NULL;
//     	$mailtpl_info['mt_from']      = NULL;
//     	$mailtpl_info['mt_from_name'] = NULL;
//     	$mailtpl_info['mt_to']        = NULL;
//     	$mailtpl_info['mt_cc']        = NULL;
//     	$mailtpl_info['mt_bcc']       = NULL;

//     	$this->smarty->assign('mailtpl_info', $mailtpl_info);

//     }

//     // フォーム・バリデーションチェック
//     private function _set_validation()
//     {

//     	$rule_set = array(
//     			array(
//     					'field'   => 'mt_id',
//     					'label'   => 'テンプレタイトル',
//     					'rules'   => 'trim|required|numeric'
//     			),
//     			array(
//     					'field'   => 'mt_subject',
//     					'label'   => 'メール件名',
//     					'rules'   => 'trim|required|max_length[100]'
//     			),
//     			array(
//     					'field'   => 'mt_body',
//     					'label'   => 'メール本文',
//     					'rules'   => 'trim|required|max_length[1000]'
//     			),
//     			array(
//     					'field'   => 'mt_from',
//     					'label'   => 'メールfrom',
//     					'rules'   => 'trim|required|valid_email|max_length[50]'
//     			),
//     			array(
//     					'field'   => 'mt_from_name',
//     					'label'   => 'メールfrom名称',
//     					'rules'   => 'trim|required|max_length[50]'
//     			),
//     			array(
//     					'field'   => 'mt_to',
//     					'label'   => 'メールto',
//     					'rules'   => 'trim|max_length[100]'
//     			),
//     			array(
//     					'field'   => 'mt_cc',
//     					'label'   => 'メールcc',
//     					'rules'   => 'trim|max_length[100]'
//     			),
//     			array(
//     					'field'   => 'mt_bcc',
//     					'label'   => 'メールbcc',
//     					'rules'   => 'trim|max_length[100]'
//     			),
//     	);

//     	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

//     }

//     // フォーム・バリデーションチェック
//     private function _set_validation02()
//     {

//     	$rule_set = array(
//     	);

//     	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

//     }

//     // フォーム・バリデーションチェック
//     private function _set_validation03()
//     {

//     	$rule_set = array(
//     	);

//     	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

//     }

}
