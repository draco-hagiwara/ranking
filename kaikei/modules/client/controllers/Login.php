<?php

class Login extends MY_Controller
{

    /*
     * ADMIN管理者 LOGINページ
    */

    public function __construct()
    {
        parent::__construct();

//         if ($_SESSION['c_login'] == TRUE)
        if (isset($_SESSION['c_login']) && $_SESSION['c_login'] == TRUE)
        {
        	$this->smarty->assign('login_chk', TRUE);
            $this->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
            $this->smarty->assign('mem_Type',  $_SESSION['c_memType']);
            $this->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
            $this->smarty->assign('mem_Name',  $_SESSION['c_memName']);
        	$this->view('top/index.tpl');
        } else {

        	$this->smarty->assign('login_chk', FALSE);
        	$this->smarty->assign('mem_Seq',   "");
        	$this->smarty->assign('mem_Type',  "");
        	$this->smarty->assign('mem_Grp',   "");
        	$this->smarty->assign('mem_Name',  "");
        	$this->smarty->assign('err_mess',  '');
        	$this->view('login/index.tpl');
        }

    }

    // ログイン 初期表示
    public function index()
    {

    	$this->_set_validation();												// バリデーション設定

    }

    // ログインID＆パスワード チェック
    public function check()
    {

       // バリデーション・チェック
    	$this->_set_validation();												// バリデーション設定
        if ($this->form_validation->run() == FALSE) {
            $this->smarty->assign('err_mess', '');
            $this->view('login/index.tpl');
        } else {
            // ログインメンバーの読み込み
            $this->config->load('config_comm');
            $login_member = $this->config->item('LOGIN_CLIENT');

            // ログインID＆パスワードチェック
            $this->load->model('comm_auth', 'auth', TRUE);

            $loginid  = $this->input->post('cl_id');
            $password = $this->input->post('cl_pw');

            $err_mess = $this->auth->check_Login($loginid, $password, $login_member);
            if (isset($err_mess)) {
                // 入力エラー
                $this->smarty->assign('err_mess', $err_mess);
                $this->view('login/index.tpl');
            } else {
                // 認証OK
                // ログイン日時 更新
                $this->load->model('Account', 'ac', TRUE);
                $this->ac->update_Logindate($_SESSION['c_memSeq']);

                // 管理・マイページ画面TOPへ
                redirect('/top/');
            }
        }
    }

    // ログアウト チェック
    public function logout()
    {
        // SESSION クリア
        $this->load->model('comm_auth', 'auth', TRUE);
        $this->auth->logout('client');

        // TOPへリダイレクト
        redirect(base_url());
    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
                array(
                        'field'   => 'cl_id',
                        'label'   => 'ログインID',
                        'rules'   => 'trim|required|max_length[50]'
                ),
                array(
                        'field'   => 'cl_pw',
                        'label'   => 'パスワード',
                        'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|max_length[50]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}
