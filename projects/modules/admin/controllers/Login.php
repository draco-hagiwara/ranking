<?php

class Login extends MY_Controller
{

    /*
     * ADMIN管理者 LOGINページ
    */
    public function __construct()
    {
        parent::__construct();

        if (isset($_SESSION['a_login']) && $_SESSION['a_login'] == TRUE)
        {
        	$this->smarty->assign('login_chk', TRUE);
        	$this->smarty->assign('mem_type',  $_SESSION['a_memType']);
        	$this->smarty->assign('mem_Seq',   $_SESSION['a_memSeq']);

        	$this->view('top/index.tpl');
        } else {
        	$this->smarty->assign('login_chk', FALSE);
        	$this->smarty->assign('mem_type',  "");
        	$this->smarty->assign('mem_Seq',   "");
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
            $login_member = $this->config->item('LOGIN_ADMIN');

            // ログインID＆パスワードチェック
            $this->load->model('comm_auth', 'auth', TRUE);

            $loginid  = $this->input->post('ac_id');
            $password = $this->input->post('ac_pw');

            $err_mess = $this->auth->check_Login($loginid, $password, $login_member);
            if (isset($err_mess)) {
                // 入力エラー
                $this->smarty->assign('err_mess', $err_mess);
                $this->view('login/index.tpl');
            } else {
                // 認証OK
                // ログイン日時 更新
                $this->load->model('Account', 'ac', TRUE);
                $this->ac->update_Logindate($_SESSION['a_memSeq']);

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
        $this->auth->logout('admin');

        // TOPへリダイレクト
        redirect(base_url());
    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
                array(
                        'field'   => 'ac_id',
                        'label'   => 'ログインID　（メールアドレス）',
                        'rules'   => 'trim|required|valid_email|max_length[50]'
                ),
                array(
                        'field'   => 'ac_pw',
                        'label'   => 'パスワード',
                        'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|max_length[50]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}
