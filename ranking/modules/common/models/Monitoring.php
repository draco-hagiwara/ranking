<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitoring extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * SEQから情報を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_mn_seq($mn_seq)
    {

    	$set_where["mn_seq"] = $mn_seq;

    	$query = $this->db->get_where('tb_monitoring', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 日付から該当情報を取得する
     *
     * @param    date(Y-m-d)
     * @return   bool
     */
    public function get_mn_date($mn_date)
    {

    	$set_where["mn_date"] = $mn_date;

    	$query = $this->db->get_where('tb_monitoring', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 監視テーブルから現状のステータスを取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_status($mn_seq)
    {

    	$set_where["mn_seq"] = $mn_seq;

    	$query = $this->db->get_where('tb_monitoring', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }




    /**
     * 1レコード登録
     *
     * @param    array()
     * @return   bool
     */
    public function insert_monitoring($setdata, $no)
    {

    	$time = time();
    	$setdata['mn_start' . $no] = date("Y-m-d H:i:s", $time);

    	// データ追加
    	$query = $this->db->insert('tb_monitoring', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_monitoring';
    	$set_data['lg_detail']    = 'mn_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * 順位データの更新
     *
     * @param    array()
     * @return   int
     */
    public function update_monitoring($setdata, $no)
    {

    	$time = time();

    	if ($setdata['mn_status'] == 0)
    	{
    		$setdata['mn_end' . $no] = date("Y-m-d H:i:s", $time);
    	} else {
    		$setdata['mn_start' . $no] = date("Y-m-d H:i:s", $time);
    		$setdata['mn_end' . $no] = NULL;
    	}

    	$where = array(
    			'mn_date' => $setdata['mn_date']
    	);

    	// UPDATE
    	$result = $this->db->update('tb_monitoring', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_monitoring';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    }




//     /**
//      * 1レコード更新
//      *
//      * @param    int : 固定=1
//      * @param    int
//      * @param    int : 1日のデータ取得回数
//      * @param    int : 取得件数
//      * @return   bool
//      */
//     public function update_status($seq, $status, $get_cnt, $cnt)
//     {

//     	$time = time();

//     	if ($status == 1)
//     	{
// 	    	$setData = array(
// 	    			'mn_status'       => $status,
// 	    			'mn_search_start' => date("Y-m-d H:i:s", $time),
// 	    			'mn_search_end'   => NULL,
// 	    			'mn_search_cnt'   => $cnt,
// 	    	);
//     	} elseif ($status == 2) {
//     		$setData = array(
//     				'mn_status'       => $status,
//     				'mn_cnt'          => $get_cnt,
//     				'mn_search_end'   => date("Y-m-d H:i:s", $time),
//     				'mn_search_cnt'   => $cnt,
//     		);
//     	} elseif ($status == 3) {
//     		$setData = array(
//     				'mn_status'        => $status,
//     				'mn_getrank_start' => date("Y-m-d H:i:s", $time),
//     				'mn_getrank_end'   => NULL,
//     		);
//     	} elseif ($status == 4) {
//     		$setData = array(
//     				'mn_status'       => $status,
//     				'mn_getrank_end'  => date("Y-m-d H:i:s", $time),
//     		);
//     	} else {
//     		$setData = array(
//     				'mn_status'       => $status,
//     		);
//     	}

//     	$where = array(
//     				'mn_seq'          => $seq,
//     	);

//     	$result = $this->db->update('tb_monitoring', $setData, $where);
//     	$_last_sql = $this->db->last_query();

//     	// ログ書き込み
//     	$set_data['lg_type']    = 'Monitoring.php';
//     	$set_data['lg_func']    = 'update_status';
//     	$set_data['lg_detail']  = 'mn_seq = ' . $seq . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     	return $result;
//     }


























//     /**
//      * 重複データのチェック：メールアドレス
//      *
//      * @param    varchar
//      * @return   bool
//      */
//     public function check_loginid($id)
//     {

//     	$sql = 'SELECT ac_id FROM `mt_account` '
//     			. 'WHERE `ac_id` = ? ';

//     	$values = array(
//     					$id,
//     	);

//     	$query = $this->db->query($sql, $values);

//     	if ($query->num_rows() > 0) {
//     		return TRUE;
//     	} else {
//     		return FALSE;
//     	}
//     }


//     /**
//      * アカウントメンバーの取得
//      *
//      * @param    array() : 検索項目値
//      * @param    int     : 1ページ当たりの表示件数(LIMIT値)
//      * @param    int     : オフセット値(ページ番号)
//      * @return   array()
//      */
//     public function get_accountlist($arr_post, $tmp_per_page, $tmp_offset=0)
//     {

//     	// 各SQL項目へセット
//     	// WHERE
//     	$set_select["ac_name01"]  = $arr_post['ac_name'];
//     	$set_select["ac_name02"]  = $arr_post['ac_name'];
// //     	$set_select["cl_company"] = $arr_post['cl_company'];
//     	$set_select["ac_cl_seq"]  = $arr_post['ac_cl_seq'];

//     	// ORDER BY
//     	if ($arr_post['orderid'] == 'ASC')
//     	{
//     		$set_orderby["ac_seq"] = $arr_post['orderid'];
//     	}else {
//     		$set_orderby["ac_seq"] = 'DESC';
//     	}

//     	// 対象クアカウントメンバーの取得
//     	$account_list = $this->_select_accoountlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset);

//     	return $account_list;

//     }

//     /**
//      * 対象アカウントメンバーの取得
//      *
//      * @param    array() : WHERE句項目
//      * @param    array() : ORDER BY句項目
//      * @param    int     : 1ページ当たりの表示件数
//      * @param    int     : オフセット値(ページ番号)
//      * @return   array()
//      */
//     public function _select_accoountlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset=0)
//     {

//     	$sql = 'SELECT
//     			  ac_seq,
//     			  ac_status,
//     			  ac_type,
//     			  ac_name01,
//     			  ac_name02,
//     			  ac_mail,
//     			  ac_lastlogin
//     			FROM mt_account WHERE ac_delflg = 0 AND ac_type != 9 AND ac_cl_seq = ' . $set_select['ac_cl_seq'];

//     	// WHERE文 作成
// //     	$sql .= ' AND ( ac_name01 LIKE \'%' .     $this->db->escape_like_str($set_select['ac_name01']) . '%\'' .
// //         		' OR  ac_name02 LIKE \'%' . $this->db->escape_like_str($set_select['ac_name02']) . '%\' )';

//     	// ORDER BY文 作成
//     	$tmp_firstitem = FALSE;
//     	foreach ($set_orderby as $key => $val)
//     	{
//     		if (isset($val))
//     		{
//     			if ($tmp_firstitem == FALSE)
//     			{
//     				$sql .= ' ORDER BY ' . $key . ' ' . $val;
//     				$tmp_firstitem = TRUE;
//     			} else {
//     				$sql .= ' , ' . $key . ' ' . $val;
//     			}
//     		}
//     	}

//     	// 対象全件数を取得
//     	$query = $this->db->query($sql);
//     	$account_countall = $query->num_rows();

//     	// LIMIT ＆ OFFSET 値をセット
//     	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

//     	// クエリー実行
//     	$query = $this->db->query($sql);
//     	$account_list = $query->result('array');

//     	return array($account_list, $account_countall);
//     }



//     /**
//      * 管理者新規会員登録
//      *
//      * @param    array()
//      * @param    bool : パスワード設定有無(空PWは危険なので一応初期登録でも入れておく)
//      * @return   int
//      */
//     public function insert_account($setdata)
//     {

//     	// ID ⇒ MAIL に挿入
//     	$setdata["ac_mail"] = $setdata["ac_id"];

//     	// パスワード作成
//     	$_hash_pw = password_hash($setdata["ac_pw"], PASSWORD_DEFAULT);
//     	$setdata["ac_pw"] = $_hash_pw;

//     	// データ追加
//     	$query = $this->db->insert('mt_account', $setdata);
//     	$_last_sql = $this->db->last_query();

//     	// 挿入した ID 番号を取得
//     	$row_id = $this->db->insert_id();

//     	// ログ書き込み
//     	$set_data['lg_func']      = 'insert_account';
//     	$set_data['lg_detail']    = 'ac_seq = ' . $row_id . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     	return $row_id;
//     }

//     /**
//      * 1レコード更新
//      *
//      * @param    array()
//      * @return   bool
//      */
//     public function update_account($setData, $pw = FALSE)
//     {

//     	// パスワード更新有無
//     	if ($pw == TRUE)
//     	{
//     		$_hash_pw = password_hash($setData["ac_pw"], PASSWORD_DEFAULT);
//     		$setData["ac_pw"] = $_hash_pw;
//     	} else {
//     		unset($setData["ac_pw"]) ;
//     	}

//     	$where = array(
//     			'ac_seq' => $setData['ac_seq']
//     	);

//     	$result = $this->db->update('mt_account', $setData, $where);
//     	$_last_sql = $this->db->last_query();

//     	// ログ書き込み
//     	$set_data['lg_type']      = 'account.php';
//     	$set_data['lg_func']      = 'update_account';
//     	$set_data['lg_detail']    = 'ac_seq = ' . $setData['ac_seq'] . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     	return $result;
//     }

//     /**
//      * ログインIDによる更新
//      *
//      * @param    array()
//      * @return   bool
//      */
//     public function update_account_id($setData)
//     {

//     	$where = array(
//     			'ac_id' => $setData['ac_id']
//     	);

//     	$result = $this->db->update('mt_account', $setData, $where);
//     	$_last_sql = $this->db->last_query();

//         // ログ書き込み
//         $set_data['lg_type']      = 'account.php';
//         $set_data['lg_func']      = 'update_account_id';
//         $set_data['lg_detail']    = 'ac_id = ' . $setData['ac_id'] . ' <= ' . $_last_sql;
//         $this->insert_log($set_data);

//     	return $result;
//     }

//     /**
//      * ログイン日時の更新
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function update_Logindate($ac_seq)
//     {

//     	$time = time();
//     	$setData = array(
//     			'ac_lastlogin' => date("Y-m-d H:i:s", $time)
//         );
//         $where = array(
//         		'ac_seq' => $ac_seq
//         );
//         $result = $this->db->update('mt_account', $setData, $where);
//         $_last_sql = $this->db->last_query();

//         // ログ書き込み
//         $set_data['lg_type']      = 'account.php';
//         $set_data['lg_func']      = 'update_Logindate';
//         $set_data['lg_detail']    = 'ac_seq = ' . $ac_seq . ' <= ' . $_last_sql;
//         $this->insert_log($set_data);

//         return $result;
//     }









// //     /**
// //      * 編集者アカウント情報を1件取得する
// //      *
// //      * @param    int
// //      * @return   bool
// //      */
// //     public function get_ac_editor_limit()
// //     {

// //     	$sql = 'SELECT
// //     			  ac_seq,
// //     			  ac_status,
// //     			  ac_type,
// //     			  ac_name01,
// //     			  ac_name02,
// //     			  ac_mail
// //     			FROM mb_account ';

// //     	$sql .= 'where ac_type = 0 AND ac_status = 1 ORDER BY ac_seq DESC LIMIT 1';

// //     	$query = $this->db->query($sql);

// //     	$get_data = $query->result('array');

// //     	return $get_data;

// //     }



// //     /**
// //      * 重複データのチェック：メールアドレス
// //      *
// //      * @param    int
// //      * @param    varchar
// //      * @return   bool
// //      */
// //     public function check_mailaddr($seq, $mail)
// //     {

// //     	$sql = 'SELECT * FROM `mb_account` '
// //     			. 'WHERE `ac_seq` != ? AND `ac_mail` = ? ';

// //     	$values = array(
// //     			$seq,
// //     			$mail,
// //     	);

// //     	$query = $this->db->query($sql, $values);

// //     	if ($query->num_rows() > 0) {
// //     		return TRUE;
// //     	} else {
// //     		return FALSE;
// //     	}
// //     }





// //     /**
// //      * 営業or編集者担当者リスト作成
// //      *
// //      * @param    int : 0=>編集, 1=>営業, 2=>管理者
// //      * @return   bool
// //      */
// //     public function get_contact($person)
// //     {

// //     	$sql = 'SELECT
// //     			  ac_seq,
// //     			  ac_name01,
// //     			  ac_name02
// //     			FROM mb_account ';

// //     	// WHERE文 作成
// //     	if ($person == 0)
// //     	{
// //     		$sql .= ' WHERE `ac_status` = \'1\' AND `ac_type` = \'0\' ORDER BY `ac_seq` DESC ';
// //     	} elseif ($person == 1) {
// //     		$sql .= ' WHERE `ac_status` = \'1\' AND `ac_type` = \'1\' ORDER BY `ac_seq` DESC ';
// //     	} else {
// //     		$sql .= ' WHERE `ac_status` = \'1\' AND `ac_type` = \'2\' ORDER BY `ac_seq` DESC ';
// //     	}

// //     	// クエリー実行
// //     	$query = $this->db->query($sql);
// //     	$contact_list = $query->result('array');

// //     	return $contact_list;

// //     }

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

    	$setData['lg_type']   = 'Monitoring.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}