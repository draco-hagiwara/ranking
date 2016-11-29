<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 顧客情報SEQから登録情報を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_cm_seq($seq_no)
    {

    	$set_where["cm_seq"] = $seq_no;

    	$query = $this->db->get_where('mt_customer', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }


    /**
     * 「有効」：顧客情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_invoicelist()
    {

    	$sql = 'SELECT
    			  cm_seq,
    			  cm_status,
    			  cm_company,
    			  cm_department,
    			  cm_person01,
    			  cm_person02,
    			  cm_zip01,
    			  cm_zip02,
    			  cm_pref,
    			  cm_addr01,
    			  cm_addr02,
    			  cm_buil,
    			  cm_memo_iv,
    			  cm_flg_iv,
    			  cm_company_iv,
    			  cm_department_iv,
    			  cm_person01_iv,
    			  cm_person02_iv,
    			  cm_zip01_iv,
    			  cm_zip02_iv,
    			  cm_pref_iv,
    			  cm_addr01_iv,
    			  cm_addr02_iv,
    			  cm_buil_iv,
    			  cm_bank_cd,
    			  cm_bank_nm,
    			  cm_branch_cd,
    			  cm_branch_nm,
    			  cm_kind,
    			  cm_account_no,
    			  cm_account_nm
    			FROM mt_customer WHERE cm_delflg = 0 AND cm_status = 0 ORDER BY cm_seq ASC';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$invoicelist = $query->result('array');

    	return $invoicelist;

    }




    /**
     * クライアントメンバーの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_customerlist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select["cm_status"]  = $get_post['cm_status'];
    	$set_select["cm_company"] = $get_post['cm_company'];

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["cm_seq"] = $get_post['orderid'];
    	}else {
    		$set_orderby["cm_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$customer_list = $this->_select_customerlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $customer_list;

    }

    /**
     * 対象クライアントメンバーの取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_customerlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
    			  cm_seq,
    			  cm_status,
    			  cm_company,
    			  cm_person01,
    			  cm_person02,
    			  cm_tel01,
    			  cm_tel02,
    			  cm_mail,
    			  cm_mailsub
    			FROM mt_customer WHERE ';

    	// cm_delflg 判定
    	if ($set_select["cm_status"] == 2)
    	{
    		$sql .= ' cm_delflg = 1 ';
    	} else {
    		$sql .= ' cm_delflg = 0 ';
    	}

    	// WHERE文 作成
    	if ($set_select["cm_status"] != '')
    	{
    		$sql .= ' AND cm_status = ' . $set_select["cm_status"];
    	}
    	if ($set_select["cm_company"] != '')
    	{
    		$sql .= ' AND cm_company LIKE \'%' . $this->db->escape_like_str($set_select['cm_company']) . '%\'';
    	}

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
    	$customer_countall = $query->num_rows();

    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$customer_list = $query->result('array');

    	return array($customer_list, $customer_countall);
    }

    /**
     * 顧客情報データの件数を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_cm_cnt($cm_status)
    {

    	$set_where["cm_status"] = $cm_status;

    	$query = $this->db->get_where('mt_customer', $set_where);

    	$customer_count = $query->num_rows();

    	return $customer_count;

    }

    /**
     * クライアント新規会員登録
     *
     * @param    array()
     * @param    bool : パスワード設定有無(空PWは危険なので一応初期登録でも入れておく)
     * @return   int
     */
    public function insert_customer($setdata)
    {

    	// 請求書の別住所
    	if (isset($setdata['chkinvoice']))
    	{
    		$setdata['cm_flg_iv'] = 1;
    		unset($setdata['chkinvoice']) ;
    	} else {
    		$setdata['cm_flg_iv'] = 0;
    	}

    	// データ追加
    	$query = $this->db->insert('mt_customer', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_customer';
    	$set_data['lg_detail']    = 'cm_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_customer($setdata)
    {

    	// ステータスの判定
    	if ($setdata["cm_status"] == 2)
    	{
    		$setdata["cm_delflg"] = 1;
    	} else {
    		$setdata["cm_delflg"] = 0;
    	}

    	// 請求書の別住所
    	if (isset($setdata['chkinvoice']))
    	{
    		$setdata['cm_flg_iv'] = 1;
    		unset($setdata['chkinvoice']) ;
    	} else {
    		$setdata['cm_flg_iv'] = 0;
    	}

    	$where = array(
    		'cm_seq' => $setdata['cm_seq']
    	);

    	$result = $this->db->update('mt_customer', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']      = 'update_customer';
    	$set_data['lg_detail']    = 'cm_seq = ' . $setdata['cm_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $result;
    }


//     /**
//      * 会社一覧を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_cl_company()
//     {

//     	$sql = 'SELECT
//     			  cl_seq,
//     			  cl_status,
//     			  cl_company
//     			FROM mt_client WHERE cl_delflg = 0 ORDER BY cl_seq DESC';

//     	// クエリー実行
//     	$query = $this->db->query($sql);
//     	$get_data = $query->result('array');

//     	return $get_data;

//     }



//     /**
//      * 重複データのチェック：ログインID
//      *
//      * @param    char
//      * @param    bool         :: FALSE => 新規登録時。 TRUE => 更新時使用。
//      * @return   bool
//      */
//     public function check_loginid($cl_id, $seq = FALSE)
//     {

//     	if ($seq == FALSE)
//     	{
//     		$sql = 'SELECT cl_id FROM `mt_client` '
//     				. 'WHERE `cl_id` = ? ';

//     		$values = array(
//     						$cl_id,
//     		);
//     	} else {
//     		$sql = 'SELECT cl_id FROM `mt_client` '
//         			. 'WHERE `cl_seq` != ? AND `cl_id` = ? ';

//         	$values = array(
//         					$seq,
//         					$cl_id,
//         	);
//     	}

//         $query = $this->db->query($sql, $values);

//         if ($query->num_rows() > 0) {
//         	return TRUE;
//         } else {
//         	return FALSE;
//         }
//     }



//     /**
//      * ログイン日時の更新
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function update_Logindate($cl_seq, $user_type=2)
//     {

//     	$time = time();
//     	$setData = array(
//     			'cl_lastlogin' => date("Y-m-d H:i:s", $time)
//     	);
//     	$where = array(
//     			'cl_seq' => $cl_seq
//     	);
//     	$result = $this->db->update('mt_client', $setData, $where);

//     	// ログ書き込み
//     	$set_data['lg_func']      = 'update_Logindate';
//     	$set_data['lg_detail']    = 'cl_seq = ' . $cl_seq;
//     	$this->insert_log($set_data);

//     	return $result;
//     }


//     /**
//      * クライアントIDから登録情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_cl_id($cl_id)
//     {

//     	$set_where["cl_id"]    = $cl_id;

//     	$query = $this->db->get_where('mb_client', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * クライアントサイトIDから登録情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_cl_siteid($cl_siteid)
//     {

//     	$set_where["cl_siteid"]    = $cl_siteid;

//     	$query = $this->db->get_where('mb_client', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * クライアントSEQから営業＆編集者情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_clac_seq($cl_seq, $cl_id)
//     {

//     	if (isset($cl_seq))
//     	{
//     		$set_where["cl_seq"]    = $cl_seq;
//     	} else {
//     		$set_where["cl_id"]    = $cl_id;
//     	}

//     	$query = $this->db->get_where('vw_a_clientlist', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }


//     /**
//      * 重複データのチェック：サイトID(URL名)
//      *
//      * @param    char
//      * @param    bool         :: FALSE => 新規登録時。 TRUE => 更新時使用。
//      * @return   bool
//      */
//     public function check_siteid($seq = FALSE, $cl_siteid)
//     {

//     	if ($seq == FALSE)
//     	{
//     		$sql = 'SELECT * FROM `mb_client` '
//     				. 'WHERE `cl_siteid` = ? ';

//     		$values = array(
//     				$cl_siteid,
//     		);
//     	} else {
// 	    	$sql = 'SELECT * FROM `mb_client` '
// 	    			. 'WHERE `cl_seq` != ? AND `cl_siteid` = ? ';

// 	    	$values = array(
// 	    			$seq,
// 	    			$cl_siteid,
// 	    	);
//     	}

//     	$query = $this->db->query($sql, $values);

//     	if ($query->num_rows() > 0) {
//     		return TRUE;
//     	} else {
//     		return FALSE;
//     	}
//     }



//     /**
//      * 重複データのチェック：メールアドレス
//      *
//      * @param    int
//      * @param    varchar
//      * @return   bool
//      */
//     public function check_mailaddr($seq, $mail)
//     {

//     	$sql = 'SELECT * FROM `mb_client` '
//     			. 'WHERE `cl_seq` != ? AND `cl_mail` = ? ';

//     	$values = array(
//     			$seq,
//     			$mail,
//     	);

//     	$query = $this->db->query($sql, $values);

//     	if ($query->num_rows() > 0) {
//     		return TRUE;
//     	} else {
//     		return FALSE;
//     	}
//     }

//     /**
//      * クライアントSEQから登録情報を取得する
//      *
//      * @param    int
//      * @return   int
//      */
//     public function check_statusno($seq_no)
//     {

//     	$sql = 'SELECT cl_status FROM `mb_client` '
//     			. 'WHERE `cl_seq` = ' . $seq_no;

//     	$query = $this->db->query($sql);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }




//     /**
//      * ログインIDによる更新
//      *
//      * @param    array()
//      * @return   bool
//      */
//     public function update_client_id($setData, $user_type=2)
//     {

//     	$where = array(
//     			'cl_id' => $setData['cl_id']
//     	);

//     	$result = $this->db->update('mb_client', $setData, $where);

// //     	// ログ書き込み
// //     	$set_data['lg_user_type'] = "";
// //     	$set_data['lg_type']      = 'client_update';
// //     	$set_data['lg_func']      = 'update_client_id';
// //     	$set_data['lg_detail']    = 'cl_id = ' . $setData['cl_id'];
// //     	$this->insert_log($set_data);

//     	return $result;
//     }

    /**
     * ログ書き込み
     *
     * @param    array()
     * @return   int
     */
    public function insert_log($setData)
    {

    	if (isset($_SESSION['c_memSeq'])) {
    		$setData['lg_user_id']   = $_SESSION['c_memSeq'];
    	} else {
    		$setData['lg_user_id']   = "";
    	}

    	$setData['lg_type'] = 'Customer.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);
    }

}