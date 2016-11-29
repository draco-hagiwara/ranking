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

    	// 顧客データ数
    	$this->load->model('Customer', 'cm', TRUE);
    	$_cm_cnt_enable  = $this->cm->get_cm_cnt(0);
    	$_cm_cnt_pause   = $this->cm->get_cm_cnt(1);
    	$_cm_cnt_disable = $this->cm->get_cm_cnt(2);

    	$this->smarty->assign('cm_cnt_enable',  $_cm_cnt_enable);
    	$this->smarty->assign('cm_cnt_pause',   $_cm_cnt_pause);
    	$this->smarty->assign('cm_cnt_disable', $_cm_cnt_disable);


    	// 受注案件データ数
    	$this->load->model('Project', 'pj', TRUE);
    	$_pj_cnt_enable  = $this->pj->get_pj_cnt(0, $_SESSION['c_memGrp'], 'seorank');
    	$_pj_cnt_pause   = $this->pj->get_pj_cnt(1, $_SESSION['c_memGrp'], 'seorank');
    	$_pj_cnt_disable = $this->pj->get_pj_cnt(2, $_SESSION['c_memGrp'], 'seorank');

    	$this->smarty->assign('pj_cnt_enable',  $_pj_cnt_enable);
    	$this->smarty->assign('pj_cnt_pause',   $_pj_cnt_pause);
    	$this->smarty->assign('pj_cnt_disable', $_pj_cnt_disable);


    	// 請求書データ数
    	$this->load->model('Invoice', 'iv',  TRUE);

    	// 固定請求年月のセット（過去一年分）
    	$date = new DateTime();
    	$_date_ym = $date->format('Ym');
    	$_date_fix = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';

    	$_iv_cnt_enable  = $this->iv->get_iv_cnt(0, $_date_ym);
    	$_iv_cnt_pause   = $this->iv->get_iv_cnt(1, $_date_ym);
    	$_iv_cnt_disable = $this->iv->get_iv_cnt(2, $_date_ym);

    	$this->smarty->assign('iv_date_fix',    $_date_fix);
    	$this->smarty->assign('iv_cnt_enable',  $_iv_cnt_enable);
    	$this->smarty->assign('iv_cnt_pause',   $_iv_cnt_pause);
    	$this->smarty->assign('iv_cnt_disable', $_iv_cnt_disable);


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