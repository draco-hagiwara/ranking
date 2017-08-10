<?php

class Dashboard extends MY_Controller
{

    /*
     *  ＣＳＶアップロード処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

    }

    // ダッシュボードTOP
    public function index()
    {

    	// バリデーション・チェック
    	$this->_set_validation();                                                       // バリデーション設定
    	$this->form_validation->run();

    	$this->load->model('Comm_dashboard', 'dd', TRUE);

    	// 登録キーワード数カウント
    	$set_data['cl_seq'] = $_SESSION['c_memGrp'];
    	list($get_kw_cnt, $get_g_cnt, $get_y_cnt) = $this->dd->get_db_kwcount($set_data);

    	$this->smarty->assign('keyword_cnt', $get_kw_cnt);
    	$this->smarty->assign('google_cnt', $get_g_cnt);
    	$this->smarty->assign('yahoo_cnt', $get_y_cnt);

    	// 登録ルートドメイン数カウント
    	$get_rdcnt = $this->dd->get_db_rdcount($set_data);

    	$this->smarty->assign('rootdomain_cnt', $get_rdcnt);

    	// 月次キーワード登録数の推移：グラフデータ作成
    	$get_kwtran = $this->dd->get_db_kwtran();

    	$_kwtran_x_data = "";
    	$_kwtran_y_data = "";
    	 foreach ($get_kwtran as $key => $value)
    	{
    		$_kwtran_x_data .= '"' . $value['regist_time'] . '",';
    		$_kwtran_y_data .= '"' . $value['count'] . '",';
    	}

    	$this->smarty->assign('kwtran_x_data', $_kwtran_x_data);
    	$this->smarty->assign('kwtran_y_data', $_kwtran_y_data);





    	$this->view('dashboard/index.tpl');

    }


    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}