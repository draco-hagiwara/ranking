<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 請求書作成用クラス
 */
class Lib_auth
{

    private $_hash_passwd;
    private $_memType;
    private $_memSeq;
    private $_memName;

    public function __construct()
    {

//         $config =& get_config();
//         $CI     =& get_instance();

//         $CI->load->helper('url');
//         $CI->load->library('session');
//         $CI->load->library('user_agent');

    }

    /**
     * セッション状態のチェック
     *
     */
    public function check_session()
    {

        $CI =& get_instance();
        $CI->load->library('smarty');

        if (isset($_SESSION['c_login']) && $_SESSION['c_login'] == TRUE)
        {
            $CI->smarty->assign('login_chk', TRUE);
            $CI->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
            $CI->smarty->assign('mem_Type',  $_SESSION['c_memType']);
            $CI->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
            $CI->smarty->assign('mem_Name',  $_SESSION['c_memName']);
        } else {

            $CI->smarty->assign('login_chk', FALSE);
            $CI->smarty->assign('mem_Seq',   "");
            $CI->smarty->assign('mem_Type',  "");
            $CI->smarty->assign('mem_Grp',   "");
            $CI->smarty->assign('mem_Name',  "");
            $CI->smarty->assign('err_mess',  '');
        }

    }

    /**
     * ログイン・チェック：ログインID（メールアドレス）＆パスワード
     *
     * @param    varchar
     * @param    string
     * @return    string
     */
    public function check_Login($loginid, $password, $login_member, $siteid=FALSE)
    {

        $CI =& get_instance();

        $err_mess = NULL;
        switch ($login_member)
        {
            case 'client':
                $sql = 'SELECT ac_seq, ac_status, ac_type, ac_id, ac_pw, ac_login_cnt, ac_login_lock, ac_login_time, ac_cl_seq '
                        . 'FROM mt_account '
                        . 'WHERE ac_delflg  = 0 '
                        . 'AND   ac_status  = 0 '
                        . 'AND   ac_id      = ? ';

                $values = array(
                        $loginid
                );

                $query = $CI->db->query($sql, $values);

                // レコードチェック
                if ($query->num_rows() == 0)
                {
                    // ログ書き込み
                    $set_data['lg_func']      = 'check_Login';
                    $set_data['lg_detail']    = 'ログインIDエラー：cl_id = ' . $loginid;
                    $CI->insert_log($set_data);

                    $err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                    return $err_mess;
                }

                // 重複チェック
                if ($query->num_rows() >= 2)
                {

                    // ログ書き込み
                    $set_data['lg_func']      = 'check_Login';
                    $set_data['lg_detail']    = 'ログインID重複エラー：cl_id = ' . $loginid;
                    $this->insert_log($set_data);

                    $err_mess = '入力されたログインIDが重複しています。システム管理者にご連絡ください。';
                    return $err_mess;
                }

                // ログインID＆パスワード読み込み
                $arrData = $query->result('array');
                if (is_array($arrData))
                {

                    $CI->config->load('config_comm');

                    // ログイン解除時間＆ロック有無のチェック
                    $tmp_lock_limit    = $CI->config->item('LOGIN_LOCK_LIMITTIME');         // 制限時間(分)
                    $tmp_release_limit = $CI->config->item('LOGIN_LOCK_RELEASETIME');       // 解除時間(分)
                    if (isset($arrData[0]['ac_login_time']))
                    {

                        $_lock_time    = new DateTime($arrData[0]['ac_login_time']);
                        $_release_time = new DateTime($arrData[0]['ac_login_time']);
                        $_now_time     = new DateTime();

                        $_mod_lock_limit = '+' . $tmp_lock_limit . 'minute';                // xx分後：$date->modify('+1 minute');
                        $_lock_time->modify($_mod_lock_limit);

                        $_mod_release_limit = '+' . $tmp_release_limit . 'minute';
                        $_release_time->modify($_mod_release_limit);

                        if ($_lock_time > $_now_time)
                        {

                            if ($arrData[0]['ac_login_lock'] == 1)
                            {
                                if ($_release_time > $_now_time)
                                {
                                    $err_mess = 'このログインIDは現在ロックされています。しばらくしてからログインしていただくかシステム管理者にご連絡ください。';
                                    return $err_mess;
                                } else {
                                    // ログインロック情報をクリア
                                    $this->_login_lock_clear($login_member, $loginid);
                                    $arrData[0]['ac_login_cnt'] = 0;
                                }
                            }
                        } else {
                            // ログインロック情報をクリア
                            $this->_login_lock_clear($login_member, $loginid);
                            $arrData[0]['ac_login_cnt'] = 0;
                        }
                    }

                    // パスワードのチェック
                    $this->_hash_passwd = $arrData[0]['ac_pw'];
                    $res = $this->_check_password($password);
                    if ($res == TRUE)
                    {

                        // ログインエラーのカウント
                        $this->_login_error_cnt($login_member, $loginid, $arrData[0]['ac_login_cnt'], $arrData[0]['ac_login_time']);

                        // ログ書き込み
                        $set_data['lg_func']      = 'check_Login';
                        $set_data['lg_detail']    = 'パスワードエラー：ac_id = ' . $loginid;
                        $this->insert_log($set_data);

                        $err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                        return $err_mess;
                    } else {
                        $this->_hash_passwd = $arrData[0]['ac_pw'];
                        $this->_memType     = $arrData[0]['ac_type'];
                        $this->_memSeq      = $arrData[0]['ac_seq'];
                        $this->_memGrp      = $arrData[0]['ac_cl_seq'];
                        $this->_memName     = "株式会社テミスホールディングス";

                        $this->_update_Session($login_member);

                        // ログインロック情報をクリア
                        $this->_login_lock_clear($login_member, $loginid);

                    }
                }

                break;
            case 'admin':

                $sql = 'SELECT ac_seq, ac_status, ac_type, ac_id, ac_pw, ac_login_cnt, ac_login_lock, ac_login_time '
                        . 'FROM mt_account '
                        . 'WHERE ac_seq     = 1 '
                        . 'AND   ac_status  = 0 '
                        . 'AND   ac_id      = ? ';

                $values = array(
                        $loginid
                );

                $query = $CI->db->query($sql, $values);

                // レコードチェック
                if ($query->num_rows() == 0)
                {

                    // ログ書き込み
                    $set_data['lg_func']      = 'check_Login';
                    $set_data['lg_detail']    = 'ログインIDエラー：ac_id = ' . $loginid;
                    $this->insert_log($set_data);

                    $err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                    return $err_mess;
                }

                // 重複チェック
                if ($query->num_rows() >= 2)
                {

                    // ログ書き込み
                    $set_data['lg_func']      = 'check_Login';
                    $set_data['lg_detail']    = 'ログインID重複エラー：ac_id = ' . $loginid;
                    $this->insert_log($set_data);

                    $err_mess = '入力されたログインIDが重複しています。システム管理者に連絡してください。';
                    return $err_mess;
                }

                // ログインID＆パスワード読み込み
                $arrData = $query->result('array');
                if (is_array($arrData))
                {

                    $CI->config->load('config_comm');

                    // ログイン解除時間＆ロック有無のチェック
                    $tmp_lock_limit    = $CI->config->item('LOGIN_LOCK_LIMITTIME');         // 制限時間(分)
                    $tmp_release_limit = $CI->config->item('LOGIN_LOCK_RELEASETIME');       // 解除時間(分)
                    if (isset($arrData[0]['ac_login_time']))
                    {

                        $_lock_time    = new DateTime($arrData[0]['ac_login_time']);
                        $_release_time = new DateTime($arrData[0]['ac_login_time']);
                        $_now_time     = new DateTime();

                        $_mod_lock_limit = '+' . $tmp_lock_limit . 'minute';                // xx分後：$date->modify('+1 minute');
                        $_lock_time->modify($_mod_lock_limit);

                        $_mod_release_limit = '+' . $tmp_release_limit . 'minute';
                        $_release_time->modify($_mod_release_limit);

                        if ($_lock_time > $_now_time)
                        {

                            if ($arrData[0]['ac_login_lock'] == 1)
                            {
                                if ($_release_time > $_now_time)
                                {
                                    $err_mess = 'このログインIDは現在ロックされています。しばらくしてからログインしていただくかシステム管理者にご連絡ください。';
                                    return $err_mess;
                                } else {
                                    // ログインロック情報をクリア
                                    $this->_login_lock_clear($login_member, $loginid);
                                    $arrData[0]['ac_login_cnt'] = 0;
                                }
                            }
                        } else {
                            // ログインロック情報をクリア
                            $this->_login_lock_clear($login_member, $loginid);
                            $arrData[0]['ac_login_cnt'] = 0;
                        }
                    }

                    // パスワードのチェック
                    $this->_hash_passwd = $arrData[0]['ac_pw'];
                    $res = $this->_check_password($password);
                    if ($res == TRUE)
                    {

                        // ログインエラーのカウント
                        $this->_login_error_cnt($login_member, $loginid, $arrData[0]['ac_login_cnt'], $arrData[0]['ac_login_time']);

                        // ログ書き込み
                        $set_data['lg_func']      = 'check_Login';
                        $set_data['lg_detail']    = 'パスワードエラー：ac_id = ' . $loginid;
                        $this->insert_log($set_data);

                        $err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                        return $err_mess;
                    } else {
                        $this->_hash_passwd = $arrData[0]['ac_pw'];
                        $this->_memType     = $arrData[0]['ac_type'];
                        $this->_memSeq      = $arrData[0]['ac_seq'];

                        $this->_update_Session($login_member);

                        // ログインロック情報をクリア
                        $this->_login_lock_clear($login_member, $loginid);

                    }
                } else {

                    // ログ書き込み
                    $set_data['lg_func']      = 'check_Login';
                    $set_data['lg_detail']    = 'ID & パスワードエラー：ac_id = ' . $loginid;
                    $this->insert_log($set_data);

                    $err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                    return $err_mess;
                }

                break;
            default:
        }

        return $err_mess;

    }

    /**
     * ログイン・ロックエラーのカウント
     *
     * @param    varchar
     * @param    varchar
     * @param    int
     * @param    timestamp
     */
    private function _login_error_cnt($login_member, $loginid, $login_cnt, $login_time)
    {

        $CI =& get_instance();

        // 各メンバー毎にDB更新
        if ($login_member == 'client')
        {

            if ($login_cnt == 0)
            {
                // ロック回数カウント(1)＆ロック制限時間セット
                $set_data['ac_login_cnt']  = 1;
                $set_data['ac_login_lock'] = 0;
                $set_data['ac_login_time'] = date('Y-m-d H:i:s');

            } elseif ($login_cnt == 9) {

                // ロック回数カウント(10)＆ロックオン(1)＆ロック制限時間セット
                $set_data['ac_login_cnt']  = 10;
                $set_data['ac_login_lock'] = 1;
                $set_data['ac_login_time'] = date('Y-m-d H:i:s');

            } else {

                // ロック回数カウントセット
                $set_data['ac_login_cnt']  = $login_cnt + 1;
                $set_data['ac_login_lock'] = 0;

            }

            $set_data['ac_id'] = $loginid;

            $CI->load->model('Account', 'ac', TRUE);
            $CI->ac->update_account_id($set_data, 2);

        } elseif ($login_member == 'admin') {

            if ($login_cnt == 0)
            {
                // ロック回数カウント(1)＆ロック制限時間セット
                $set_data['ac_login_cnt']  = 1;
                $set_data['ac_login_lock'] = 0;
                $set_data['ac_login_time'] = date('Y-m-d H:i:s');

            } elseif ($login_cnt == 9) {

                // ロック回数カウント(10)＆ロックオン(1)＆ロック制限時間セット
                $set_data['ac_login_cnt']  = 10;
                $set_data['ac_login_lock'] = 1;
                $set_data['ac_login_time'] = date('Y-m-d H:i:s');

            } else {

                // ロック回数カウントセット
                $set_data['ac_login_cnt']  = $login_cnt + 1;
                $set_data['ac_login_lock'] = 0;

            }

            $set_data['ac_id'] = $loginid;

            $CI->load->model('Account', 'ac', TRUE);
            $CI->ac->update_account_id($set_data, 2);

        }

    }

    /**
     * ログイン・ロックの解除
     *
     * @param    varchar
     * @param    varchar
     */
    private function _login_lock_clear($login_member, $loginid)
    {

        $CI =& get_instance();

        // 各メンバー毎にDB更新
        if ($login_member == 'client')
        {

            $set_data['ac_id']         = $loginid;
            $set_data['ac_login_cnt']  = 0;
            $set_data['ac_login_lock'] = 0;
            $set_data['ac_login_time'] = NULL;

            $CI->load->model('Account', 'ac', TRUE);
            $CI->ac->update_account_id($set_data, 2);

        } elseif ($login_member == 'admin') {

            $set_data['ac_id']         = $loginid;
            $set_data['ac_login_cnt']  = 0;
            $set_data['ac_login_lock'] = 0;
            $set_data['ac_login_time'] = NULL;

            $CI->load->model('Account', 'ac', TRUE);
            $CI->ac->update_account_id($set_data, 2);

        }
    }


    /**
     * LOGOUT ＆ SESSIONクリア
     *
     * @param    varchar
     * @return   bool
     */
    public function logout($login_member)
    {

        $CI =& get_instance();
        $CI->load->library('session');

        // 特定のセッションユーザデータを削除
        switch ($login_member)
        {
            case 'client':
                $seach_key = 'c';
                break;
            case 'admin':
                $seach_key = 'a';
                break;
            default:
        }

        $get_data = $CI->session->all_userdata();

        $unset_data = array();
        foreach ($get_data as $key => $val)
        {
            if (substr($key, 0, 1) == $seach_key)
            {
                unset($_SESSION[$key]);
//                 $unset_data[$key] = '';
            }
        }

//         $CI->session->unset_userdata($unset_data);                               // セッションデータ削除

        // ログイン解除
        switch ($login_member)
        {
            case 'client':
                $setData = array('c_login' => FALSE);
                break;
            case 'admin':
                $setData = array('a_login' => FALSE);
                break;
            default:
        }

        $CI->session->set_userdata($setData);                                     // ログイン解除
        //$CI->session->sess_destroy();                                           // 全セッションデータ削除

    }

    /**
     * SESSION 書き込み
     *
     * @param    varchar
     */
    private function _update_Session($login_member)
    {

        switch ($login_member)
        {
            case 'client':
                $_SESSION['c_login']   = TRUE;               // ログイン有無
                $_SESSION['c_memSeq']  = $this->_memSeq;     // メンバーseq
                $_SESSION['c_memType'] = $this->_memType;    // 0:一般,1:管理
                $_SESSION['c_memGrp']  = $this->_memGrp;     // 親クライアントNO
                $_SESSION['c_memName'] = $this->_memName;    // 会社名

                break;
            case 'admin':
                $_SESSION['a_login']   = TRUE;                 // ログイン有無
                $_SESSION['a_memType'] = $this->_memType;      // 0:editor,1:sales,2:admin
                $_SESSION['a_memSeq']  = $this->_memSeq;       // メンバーseq

                break;
            default:
        }
    }

    /**
     * 不要なセッションデータの削除
     *
     * @param    string
     * @return    bool
     */
    public function delete_session($login_member)
    {

        $CI =& get_instance();
        $CI->load->library('session');

//      if ((!isset($_SESSION['c_login'])) || ($_SESSION['c_login'] == FALSE))
//      {
//          redirect('/login/');
//      }

        switch ($login_member)
        {
            case 'client':
                if ((isset($_SESSION['c_login'])) && ($_SESSION['c_login'] == TRUE))
                {
                    $backup_c_login   = $_SESSION['c_login'];
                    $backup_c_memSeq  = $_SESSION['c_memSeq'];
                    $backup_c_memType = $_SESSION['c_memType'];
                    $backup_c_memGrp  = $_SESSION['c_memGrp'];
                    $backup_c_memName = $_SESSION['c_memName'];

                    $get_data = $CI->session->all_userdata();
                    foreach ($get_data as $key => $val)
                    {
                        if (substr($key, 0, 2) == 'c_')
                        {
                           $CI->session->unset_userdata($key);
                        }
                    }

                    $_SESSION['c_login']   = $backup_c_login;         // ログイン有無
                    $_SESSION['c_memSeq']  = $backup_c_memSeq;        // メンバーID
                    $_SESSION['c_memType'] = $backup_c_memType;       // 0:一般,1:管理
                    $_SESSION['c_memGrp']  = $backup_c_memGrp;        // 親クライアントNO
                    $_SESSION['c_memName'] = $backup_c_memName;       // メンバー名前
                } else {
                    $this->logout($login_member);
                    redirect('/login/');
//                  redirect(base_url());
                }

                break;
            case 'admin':
                if (isset($_SESSION['a_login']))
                {
                    $backup_a_login   = $_SESSION['a_login'];
                    $backup_a_memType = $_SESSION['a_memType'];
                    $backup_a_memSeq  = $_SESSION['a_memSeq'];

                    $get_data = $CI->session->all_userdata();
                    foreach ($get_data as $key => $val)
                    {
                        if (substr($key, 0, 2) == 'a_')
                        {
                           $CI->session->unset_userdata($key);
                        }
                    }

                    $_SESSION['a_login']   = $backup_a_login;         // ログイン有無
                    $_SESSION['a_memType'] = $backup_a_memType;       // メンバーID
                    $_SESSION['a_memSeq']  = $backup_a_memSeq;        // メンバーID
                } else {
                    $this->logout($login_member);
                    redirect(base_url());
                }

                break;
            default:
        }

    }

    /**
     * パスワードチェック
     *
     * @param    varchar
     * @param    varchar
     * @return    string
     */
     private function _check_password($password)
    {
        // パスワードハッシュ認証チェック
        if (password_verify($password, $this->_hash_passwd)) {
            $result = FALSE;
        } else {
            $result = TRUE;
        }

        return $result;
    }

    /**
     * ログ書き込み
     *
     * @param    array()
     * @return   int
     */
    public function insert_log($setData)
    {

        $CI =& get_instance();

        $setData['lg_user_id'] = "";
        $setData['lg_type']    = 'Comm_auth.php';
        $setData['lg_ip'] = $CI->input->ip_address();

        // データ追加
        $query = $CI->db->insert('tb_log', $setData);

        //      // 挿入した ID 番号を取得
        //      $row_id = $CI->db->insert_id();
        //      return $row_id;
    }

}
