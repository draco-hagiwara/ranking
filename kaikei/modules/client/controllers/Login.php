<?php

class Login extends MY_Controller
{

    /*
     * ADMIN管理者 LOGINページ
    */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

    }

    // ログイン 初期表示
    public function index()
    {

        $this->_set_validation();                                               // バリデーション設定

        $this->smarty->assign('err_mess', '');
        $this->view('login/index.tpl');

    }

    // ログインID＆パスワード チェック
    public function check()
    {

       // バリデーション・チェック
        $this->_set_validation();                                               // バリデーション設定
        if ($this->form_validation->run() == FALSE) {
            $this->smarty->assign('err_mess', '');
            $this->view('login/index.tpl');
        } else {
            // ログインメンバーの読み込み
            $this->config->load('config_comm');
            $login_member = $this->config->item('LOGIN_CLIENT');

            // ログインID＆パスワードチェック
            $this->load->library('lib_auth');
//             $this->load->model('comm_auth', 'auth', TRUE);

            $loginid  = $this->input->post('cl_id');
            $password = $this->input->post('cl_pw');

            $err_mess = $this->lib_auth->check_Login($loginid, $password, $login_member);
//             $err_mess = $this->auth->check_Login($loginid, $password, $login_member);
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
        $this->load->library('lib_auth');
        $this->lib_auth->logout('client');

        // ログイン画面へリダイレクト
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
