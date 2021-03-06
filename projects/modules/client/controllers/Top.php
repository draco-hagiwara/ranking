<?php

class Top extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($_SESSION['c_login'] == TRUE)
        {
            $this->smarty->assign('login_chk', TRUE);
            $this->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
            $this->smarty->assign('mem_Type',  $_SESSION['c_memType']);
            $this->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
            $this->smarty->assign('mem_Name',  $_SESSION['c_memName']);
        } else {
            $this->smarty->assign('login_chk', FALSE);
            $this->smarty->assign('mem_Seq',   "");
            $this->smarty->assign('mem_Type',  "");
            $this->smarty->assign('mem_Grp',   "");
            $this->smarty->assign('mem_Name',  "");

            redirect('/login/');
        }

    }

    // ログイン 初期表示
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->model('comm_auth', 'comm_auth', TRUE);
		$this->comm_auth->delete_session('client');

    	$this->_set_validation();

//     	// クライアントデータを取得
// 		$this->load->model('Client', 'cl', TRUE);
//     	$cl_data = $this->cl->get_cl_seq($_SESSION['c_memSeq'], TRUE);

//     	$this->smarty->assign('list', $cl_data[0]);

//     	// アカウントデータを取得
//     	$this->load->model('Account', 'ac', TRUE);
//     	$ac_data = $this->ac->get_ac_seq($_SESSION['c_memSeq'], TRUE);

//     	$this->smarty->assign('list', $ac_data[0]);

        $this->view('top/index.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {
    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}
