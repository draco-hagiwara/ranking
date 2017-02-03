<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 管理者SEQから管理者アカウント情報を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_ac_seq($seq_no, $status = FALSE)
    {

    	if ($status == TRUE)
    	{
    		// 			$set_where["ac_status"] = 1;						// ステータス=有効
    	} else {
    		$set_where["ac_status"] = 0;									// ステータス=登録中
    	}
    	$set_where["ac_seq"]    = $seq_no;

    	$query = $this->db->get_where('mt_account', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 重複データのチェック：メールアドレス
     *
     * @param    varchar
     * @return   bool
     */
    public function check_loginid($id)
    {

    	$sql = 'SELECT ac_id FROM `mt_account` '
    			. 'WHERE `ac_id` = ? ';

    	$values = array(
    					$id,
    	);

    	$query = $this->db->query($sql, $values);

    	if ($query->num_rows() > 0) {
    		return TRUE;
    	} else {
    		return FALSE;
    	}
    }

    /**
     * 営業担当者リスト作成
     *
     * @param    int
     * @param    char : 接続先DB
     * @return   bool
     */
    public function get_salesman($cl_seq, $db_name='default')
    {

    	$sql = 'SELECT
    			  ac_seq,
    			  ac_name01,
    			  ac_name02
    			FROM mt_account WHERE ac_cl_seq = ' . $cl_seq;

    	// WHERE文 作成
    	$sql .= ' AND `ac_status` = 0 AND `ac_delflg` = 0 ORDER BY `ac_seq` ASC ';

    	// 接続先DBを選択 ＆ クエリー実行
		if ($db_name == 'default')
    	{
    		$query = $this->db->query($sql);
    	} else {
    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->query($sql);
    	}

    	$salesman_list = $query->result('array');
    	return $salesman_list;

    }

    /**
     * アカウントSEQから担当営業を取得する
     *
     * @param    int  : ac_seq
     * @param    char : 接続先DB
     * @return   bool
     */
    public function get_pj_salesman($seq_no, $db_name='default')
    {

    	$sql = 'SELECT
    			  ac_seq,
    			  ac_status,
    			  ac_type,
    			  ac_department,
    			  ac_name01,
    			  ac_name02
    			FROM mt_account WHERE ac_seq = ' . $seq_no;

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{
    		$query = $this->db->query($sql);
    	} else {
    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->query($sql);
    	}

    	$get_salesman = $query->result('array');

    	return $get_salesman;

    }

    /**
     * アカウントメンバーの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_accountlist($arr_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select["ac_name01"]  = $arr_post['ac_name'];
    	$set_select["ac_name02"]  = $arr_post['ac_name'];
//     	$set_select["cl_company"] = $arr_post['cl_company'];
    	$set_select["ac_cl_seq"]  = $arr_post['ac_cl_seq'];

    	// ORDER BY
    	if ($arr_post['orderid'] == 'ASC')
    	{
    		$set_orderby["ac_seq"] = $arr_post['orderid'];
    	}else {
    		$set_orderby["ac_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$account_list = $this->_select_accoountlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $account_list;

    }

    /**
     * 対象アカウントメンバーの取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_accoountlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
    			  ac_seq,
    			  ac_status,
    			  ac_type,
    			  ac_name01,
    			  ac_name02,
    			  ac_mail,
    			  ac_lastlogin
    			FROM mt_account WHERE ac_delflg = 0 AND ac_cl_seq = ' . $set_select['ac_cl_seq'];

    	// WHERE文 作成
//     	$sql .= ' AND ( ac_name01 LIKE \'%' .     $this->db->escape_like_str($set_select['ac_name01']) . '%\'' .
//         		' OR  ac_name02 LIKE \'%' . $this->db->escape_like_str($set_select['ac_name02']) . '%\' )';

    	// ORDER BY文 作成
    	$tmp_firstitem = FALSE;
    	foreach ($set_orderby as $key => $val)
    	{
    		if (isset($val))
    		{
    			if ($tmp_firstitem == FALSE)
    			{
    				$sql .= ' ORDER BY ' . $key . ' ' . $val;
    				$tmp_firstitem = TRUE;
    			} else {
    				$sql .= ' , ' . $key . ' ' . $val;
    			}
    		}
    	}

    	// 対象全件数を取得
    	$query = $this->db->query($sql);
    	$account_countall = $query->num_rows();

    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$account_list = $query->result('array');

    	return array($account_list, $account_countall);
    }

    /**
     * 管理者新規会員登録
     *
     * @param    array()
     * @param    bool : パスワード設定有無(空PWは危険なので一応初期登録でも入れておく)
     * @return   int
     */
    public function insert_account($setdata)
    {

    	// ID ⇒ MAIL に挿入
    	$setdata["ac_mail"] = $setdata["ac_id"];

    	// パスワード作成
    	$_hash_pw = password_hash($setdata["ac_pw"], PASSWORD_DEFAULT);
    	$setdata["ac_pw"] = $_hash_pw;

    	// データ追加
    	$query = $this->db->insert('mt_account', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_account';
    	$set_data['lg_detail']    = 'ac_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_account($setData, $pw = FALSE)
    {

    	// パスワード更新有無
    	if ($pw == TRUE)
    	{
    		$_hash_pw = password_hash($setData["ac_pw"], PASSWORD_DEFAULT);
    		$setData["ac_pw"] = $_hash_pw;
    	} else {
    		unset($setData["ac_pw"]) ;
    	}

    	$where = array(
    			'ac_seq' => $setData['ac_seq']
    	);

    	$result = $this->db->update('mt_account', $setData, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_type']      = 'account.php';
    	$set_data['lg_func']      = 'update_account';
    	$set_data['lg_detail']    = 'ac_seq = ' . $setData['ac_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $result;
    }

    /**
     * ログインIDによる更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_account_id($setData)
    {

    	$where = array(
    			'ac_id' => $setData['ac_id']
    	);

    	$result = $this->db->update('mt_account', $setData, $where);
    	$_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_type']      = 'account.php';
        $set_data['lg_func']      = 'update_account_id';
        $set_data['lg_detail']    = 'ac_id = ' . $setData['ac_id'] . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

    	return $result;
    }

    /**
     * ログイン日時の更新
     *
     * @param    int
     * @return   bool
     */
    public function update_Logindate($ac_seq)
    {

        $time = time();
        $setData = array(
        		'ac_lastlogin' => date("Y-m-d H:i:s", $time)
        );
        $where = array(
        		'ac_seq' => $ac_seq
        );
        $result = $this->db->update('mt_account', $setData, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_type']      = 'account.php';
        $set_data['lg_func']      = 'update_Logindate';
        $set_data['lg_detail']    = 'ac_seq = ' . $ac_seq . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

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

        if (isset($_SESSION['a_memSeq'])) {
    		$setData['lg_user_id']   = $_SESSION['a_memSeq'];
    	} elseif (isset($_SESSION['c_memSeq'])) {
    		$setData['lg_user_id']   = $_SESSION['c_memSeq'];
    	} else {
    		$setData['lg_user_id']   = "";
    	}

    	$set_data['lg_type']   = 'account.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}