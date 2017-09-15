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
    		//$set_where["ac_status"] = 1;						// ステータス=有効
    	} else {
    		$set_where["ac_status"] = 0;						// ステータス=登録中
    	}
    	$set_where["ac_seq"]    = $seq_no;

    	$query = $this->db->get_where('mt_account', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * アカウントメンバーの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_accountlist($arr_item, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select["ac_seq"]    = $arr_item['ac_seq'];
    	$set_select["ac_cl_seq"] = $arr_item['ac_cl_seq'];

    	// ORDER BY
    	if ($arr_item['orderid'] == 'ASC')
    	{
    		$set_orderby["ac_seq"] = $arr_item['orderid'];
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
    			  ac_id,
    			  ac_name01,
    			  ac_name02,
    			  ac_lastlogin,
    			  ac_create_date
    			FROM mt_account WHERE ac_delflg = 0 AND ac_type != 9 AND ac_cl_seq = ' . $set_select['ac_cl_seq']
        			;

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
    	//$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$account_list = $query->result('array');

    	return array($account_list, $account_countall);
    }

    /**
     * 重複データのチェック：メールアドレス
     *
     * @param    varchar
     * @return   bool
     */
    public function check_loginid($id)
    {

    	$sql = 'SELECT ac_id FROM `mt_account` WHERE `ac_id` = ? ';

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
     * 管理者新規会員登録
     *
     * @param    array()
     * @param    bool : パスワード設定有無(空PWは危険なので一応初期登録でも入れておく)
     * @return   int
     */
    public function insert_account($setdata)
    {

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