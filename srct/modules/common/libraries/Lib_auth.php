<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 認証用クラス
 */
class Lib_auth
{

//     private $_hash_passwd;
    private $_memSeq;
    private $_memType;
    private $_memGrp;
    private $_memName;
    private $_memKw;
    private $_memGp;
    private $_memTg;

    public function __construct()
    {

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
            $CI->smarty->assign('mem_Kw',    $_SESSION['c_memKw']);
            $CI->smarty->assign('mem_Gp',    $_SESSION['c_memGp']);
            $CI->smarty->assign('mem_Tg',    $_SESSION['c_memTg']);
        } else {

            $CI->smarty->assign('login_chk', FALSE);
            $CI->smarty->assign('mem_Seq',   "");
            $CI->smarty->assign('mem_Type',  "");
            $CI->smarty->assign('mem_Grp',   "");
            $CI->smarty->assign('mem_Name',  "");
            $CI->smarty->assign('mem_Kw',    "");
            $CI->smarty->assign('mem_Gp',    "");
            $CI->smarty->assign('mem_Tg',    "");
            $CI->smarty->assign('err_mess',  '');

            redirect('/login/');
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
                $sql = 'SELECT 	ac_seq,
                				ac_status,
                				ac_type,
                				ac_id,
                				ac_cl_seq,
                				ac_pw,
		                		ac_keyword,
		                		ac_group,
		                		ac_tag,
                				ac_login_cnt,
                				ac_login_lock,
                				ac_login_time
                		'
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
                	$this->insert_log($set_data);

                	$err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                	return $err_mess;
                }

                // クライアント企業のステータスをチェック
                $get_ac_data = $query->result('array');
                $sql = 'SELECT cl_seq, cl_status, cl_company '
                		. 'FROM mt_client '
                		. 'WHERE cl_seq  = ? '
                ;

                $values = array(
                				$get_ac_data[0]['ac_cl_seq']
                );

                $cl_query = $CI->db->query($sql, $values);
                $get_cl_status = $cl_query->result('array');

                if ($get_cl_status[0]['cl_status'] != 0)
                {
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
                    $res = $this->_check_password($password, $arrData[0]['ac_pw']);
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
                        $this->_memName     = $get_cl_status[0]['cl_company'];

                        $this->_memKw       = $arrData[0]['ac_keyword'];
                        $this->_memGp       = $arrData[0]['ac_group'];
                        $this->_memTg       = $arrData[0]['ac_tag'];

                        $this->_update_Session($login_member);

                        // ログインロック情報をクリア
                        $this->_login_lock_clear($login_member, $loginid);

                    }
                }

                break;
            case 'admin':

            	// 管理者のみログインを許可
            	/*
            	 * 「cl_status:99,info@seo.com.dev」のみを許可中
            	 */
            	$sql = 'SELECT cl_seq, cl_status, cl_id, cl_pw, cl_login_cnt, cl_login_lock, cl_login_time '
            			. 'FROM mt_client '
            			. 'WHERE cl_status  = 99 '
            			. 'AND   cl_id      = ? ';

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
                    $this->insert_log($set_data);

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
                    if (isset($arrData[0]['cl_login_time']))
                    {

                        $_lock_time    = new DateTime($arrData[0]['cl_login_time']);
                        $_release_time = new DateTime($arrData[0]['cl_login_time']);
                        $_now_time     = new DateTime();

                        $_mod_lock_limit = '+' . $tmp_lock_limit . 'minute';                // xx分後：$date->modify('+1 minute');
                        $_lock_time->modify($_mod_lock_limit);

                        $_mod_release_limit = '+' . $tmp_release_limit . 'minute';
                        $_release_time->modify($_mod_release_limit);

                        if ($_lock_time > $_now_time)
                        {

                            if ($arrData[0]['cl_login_lock'] == 1)
                            {
                                if ($_release_time > $_now_time)
                                {
                                    $err_mess = 'このログインIDは現在ロックされています。しばらくしてからログインしていただくかシステム管理者にご連絡ください。';
                                    return $err_mess;
                                } else {
                                    // ログインロック情報をクリア
                                    $this->_login_lock_clear($login_member, $loginid);
                                    $arrData[0]['cl_login_cnt'] = 0;
                                }
                            }
                        } else {
                            // ログインロック情報をクリア
                            $this->_login_lock_clear($login_member, $loginid);
                            $arrData[0]['cl_login_cnt'] = 0;
                        }
                    }

                    // パスワードのチェック
                    $res = $this->_check_password($password, $arrData[0]['cl_pw']);
                    if ($res == TRUE)
                    {

                        // ログインエラーのカウント
                        $this->_login_error_cnt($login_member, $loginid, $arrData[0]['cl_login_cnt'], $arrData[0]['cl_login_time']);

                        // ログ書き込み
                        $set_data['lg_func']      = 'check_Login';
                        $set_data['lg_detail']    = 'パスワードエラー：cl_id = ' . $loginid;
                        $this->insert_log($set_data);

                        $err_mess = '入力されたログインID（メールアドレス）またはパスワードが間違っています。';
                        return $err_mess;
                    } else {
                        $this->_hash_passwd = $arrData[0]['cl_pw'];
                        $this->_memSeq      = $arrData[0]['cl_seq'];

                        $this->_update_Session($login_member);

                        // ログインロック情報をクリア
                        $this->_login_lock_clear($login_member, $loginid);

                    }
                } else {

                    // ログ書き込み
                    $set_data['lg_func']      = 'check_Login';
                    $set_data['lg_detail']    = 'ID & パスワードエラー：cl_id = ' . $loginid;
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
                $set_data['cl_login_cnt']  = 1;
                $set_data['cl_login_lock'] = 0;
                $set_data['cl_login_time'] = date('Y-m-d H:i:s');

            } elseif ($login_cnt == 9) {

                // ロック回数カウント(10)＆ロックオン(1)＆ロック制限時間セット
                $set_data['cl_login_cnt']  = 10;
                $set_data['cl_login_lock'] = 1;
                $set_data['cl_login_time'] = date('Y-m-d H:i:s');

            } else {

                // ロック回数カウントセット
                $set_data['cl_login_cnt']  = $login_cnt + 1;
                $set_data['cl_login_lock'] = 0;

            }

            $set_data['cl_id'] = $loginid;

            $CI->load->model('Client', 'cl', TRUE);
            $CI->cl->update_client_id($set_data);

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

            $set_data['cl_id']         = $loginid;
            $set_data['cl_login_cnt']  = 0;
            $set_data['cl_login_lock'] = 0;
            $set_data['cl_login_time'] = NULL;

            $CI->load->model('Client', 'cl', TRUE);
            $CI->cl->update_client_id($set_data);

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


    	var_dump($login_member);


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
            }
        }

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

                $_SESSION['c_memKw']   = $this->_memKw;      // キーワード付与権限
                $_SESSION['c_memGp']   = $this->_memGp;      // グループ付与権限
                $_SESSION['c_memTg']   = $this->_memTg;      // タグ付与権限

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

                    $backup_c_memKw   = $_SESSION['c_memKw'];
                    $backup_c_memGp   = $_SESSION['c_memGp'];
                    $backup_c_memTg   = $_SESSION['c_memTg'];

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

                    $_SESSION['c_memKw']   = $backup_c_memKw;         // キーワード付与権限
                    $_SESSION['c_memGp']   = $backup_c_memGp;         // グループ付与権限
                    $_SESSION['c_memTg']   = $backup_c_memTg;         // タグ付与権限

                } else {
                    $this->logout($login_member);
                    redirect('/login/');
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
    public function _check_password($password, $hash_passwd)
    {
        // パスワードハッシュ認証チェック
        if (password_verify($password, $hash_passwd)) {
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
        $setData['lg_type']    = 'Lib_auth.php';
        $setData['lg_ip'] = $CI->input->ip_address();

        // データ追加
        $query = $CI->db->insert('tb_log', $setData);

        //      // 挿入した ID 番号を取得
        //      $row_id = $CI->db->insert_id();
        //      return $row_id;
    }

}
