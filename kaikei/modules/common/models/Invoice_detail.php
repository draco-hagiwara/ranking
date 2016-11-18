<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice_detail extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }




    /**
     * 顧客情報SEQからデータの有無を判定
     *
     * @param    int
     * @param    char
     * @return   bool
     */
    public function get_iv_seq($seq_no, $issue_yymm)
    {

    	$set_where = '`ivd_iv_seq` = ' . $seq_no  . ' AND `ivd_iv_issue_yymm` = ' . $issue_yymm;
//     	$set_where = '`ivd_status` = 0 AND `ivd_iv_seq` = ' . $seq_no  . ' AND `ivd_iv_issue_yymm` = ' . $issue_yymm;

		$query = $this->db->get_where('tb_invoice_detail', $set_where);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 履歴データの取得
     *
     * @param    int
     * @param    char
     * @return   bool
     */
    public function get_ivd_history($seq_no, $seq_suffix)
    {

    	$set_where = '`ivd_iv_seq` = ' . $seq_no  . ' AND `ivd_seq_suffix` = ' . $seq_suffix;

    	$query = $this->db->get_where('tb_invoice_detail', $set_where);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 受注案件SEQから情報を取得する
     *
     * @param    int
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   bool
     */
    public function get_iv_id($id1, $id2, $id3)
    {

    	$set_where["ivd_seq_suffix"] = $id1;
    	$set_where["ivd_iv_seq"]     = $id2;
    	$set_where["ivd_pj_seq"]     = $id3;

    	// 接続先DBを選択 ＆ クエリー実行
		$query = $this->db->get_where('tb_invoice_detail', $set_where);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 請求書データ新規登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_invoice_detail($setdata)
    {

    	// データ追加
    	$query = $this->db->insert('tb_invoice_detail', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_invoice_detail';
    	$set_data['lg_detail'] = 'ivd_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $row_id;
    }

    /**
     * 請求書データ新規登録 << 履歴ファイル
     *
     * @param    array()
     * @return   int
     */
    public function insert_invoice_detail_history($setdata)
    {

    	// データ追加
    	$query = $this->db->insert('tb_invoice_detail_h', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_invoice_detail_history';
    	$set_data['lg_detail'] = 'ivd_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_invoice_detail($setdata)
    {

    	$where = array(
    		'ivd_seq' => $setdata['ivd_seq']
    	);

    	$result = $this->db->update('tb_invoice_detail', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']      = 'update_invoice_detail';
    	$set_data['lg_detail']    = 'ivd_seq = ' . $setdata['ivd_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $result;
    }


//     /**
//      * 受注案件SEQから情報を取得する
//      *
//      * @param    int
//      * @param    int  : クライアントSEQ（接続先テーブルを切替）
//      * @param    char : 接続先DB
//      * @return   bool
//      */
//     public function get_pj_seq($seq_no, $client_no, $db_name='default')
//     {

//     	$tb_name = 'tb_project_' . $client_no;
//     	$set_where["pj_seq"]    = $seq_no;

//     	// 接続先DBを選択 ＆ クエリー実行
//     	if ($db_name == 'default')
//     	{

//     		$query = $this->db->get_where($tb_name, $set_where);

//     	} else {

//     		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

//     		$query = $slave_db->get_where($tb_name, $set_where);

//     	}

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * 顧客情報SEQから情報を取得する
//      *
//      * @param    int  : 顧客情報seq
//      * @param    int  : 課金方式（固定=0/成果=1/固+成=2）
//      * @param    int  : クライアントSEQ（接続先テーブルを切替）
//      * @param    char : 接続先DB
//      * @return   bool
//      */
//     public function get_pj_cm_seq($seq_no, $iv_type, $client_no, $db_name='default')
//     {

//     	$tb_name = 'tb_project_' . $client_no;

//     	$sql = 'SELECT
//     			  pj_seq,
//     			  pj_status,
//     			  pj_invoice_status,
//     			  pj_start_date,
//     			  pj_end_date,
//     			  pj_keyword,
//     			  pj_url,
//     			  pj_accounting,
//     			  pj_tax_cal,
//     			  pj_collect,
//     			  pj_billing,
//     			  pj_cm_seq
//     			FROM ' . $tb_name
//     			. ' WHERE pj_cm_seq = ' . $seq_no . ' AND pj_accounting = ' . $iv_type . ' AND pj_invoice_status = 0  AND pj_status = 0 AND pj_delflg = 0'
//     			. ' ORDER BY pj_seq ASC';

//     	// 接続先DBを選択 ＆ クエリー実行
//     	if ($db_name == 'default')
//     	{

//     		$query = $this->db->query($sql);

//     	} else {

//     		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

//     		$query = $slave_db->query($sql);

//     	}

//     	$projectlist = $query->result('array');

//     	return $projectlist;

//     }

//     /**
//      * 案件情報の取得
//      *
//      * @param    array() : 検索項目値
//      * @param    int     : 1ページ当たりの表示件数(LIMIT値)
//      * @param    int     : オフセット値(ページ番号)
//      * @param    int     : クライアントSEQ（接続先テーブルを切替）
//      * @param    char    : 接続先DB
//      * @return   array()
//      */
//     public function get_projectlist($get_post, $tmp_per_page, $tmp_offset=0, $client_no, $db_name='default')
//     {

//     	// 各SQL項目へセット
//     	// WHERE
//     	$set_select_like["pj_seq"]        = $get_post['pj_seq'];
//     	$set_select_like["pj_cm_seq"]     = $get_post['pj_cm_seq'];
//     	$set_select_like["pj_cm_company"] = $get_post['pj_cm_company'];

//     	$set_select["pj_status"]          = $get_post['pj_status'];
//     	$set_select["pj_invoice_status"]  = $get_post['pj_invoice_status'];
//     	$set_select["pj_accounting"]      = $get_post['pj_accounting'];
//     	$set_select["pj_salesman"]        = $get_post['pj_salesman'];

//     	// ORDER BY
// //     	$set_orderby["pj_cm_seq"] = $get_post['orderstatus'];
//     	if ($get_post['orderid'] == 'ASC')
//     	{
//     		$set_orderby["pj_seq"] = $get_post['orderid'];
//     	}else {
//     		$set_orderby["pj_seq"] = 'DESC';
//     	}

//     	// 対象クアカウントメンバーの取得
//     	$project_list = $this->_select_projectlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $client_no, $db_name);

//     	return $project_list;

//     }

//     /**
//      * 案件情報の取得
//      *
//      * @param    array() : WHERE句項目
//      * @param    array() : ORDER BY句項目
//      * @param    int     : 1ページ当たりの表示件数
//      * @param    int     : オフセット値(ページ番号)
//      * @return   array()
//      */
//     public function _select_projectlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $client_no, $db_name)
//     {

//     	$tb_name = 'tb_project_' . $client_no;

//     	$sql = 'SELECT
//     			  pj_seq,
//     			  pj_status,
//     			  pj_invoice_status,
//     			  pj_start_date,
//     			  pj_end_date,
//     			  pj_keyword,
//     			  pj_url,
//     			  pj_target,
//     			  pj_language,
//     			  pj_accounting,
//     			  pj_url_match,
//     			  pj_billing,
//     			  pj_cm_seq,
//     			  pj_cm_company,
//     			  pj_salesman
//     			FROM ' . $tb_name . ' WHERE '
//     		;

//     	// pj_status 判定
//     	if ($set_select["pj_status"] == 2)
//     	{
//     		$sql .= ' pj_delflg = 1 ';
//     	} else {
//     		$sql .= ' pj_delflg = 0 ';
//     	}

//     	if ($set_select["pj_status"] != '')                                     			// 受注案件ステータス
//     	{
//     		$sql .= ' AND `pj_status`  = ' . $set_select["pj_status"];
//     	}
//     	if ($set_select["pj_invoice_status"] != '')                                     	// 請求書発行ステータス
//     	{
//     		$sql .= ' AND `pj_invoice_status`  = ' . $set_select["pj_invoice_status"];
//     	}
//     	if ($set_select["pj_accounting"] != '')                                     		// 課金方式
//     	{
//     		$sql .= ' AND `pj_accounting`  = ' . $set_select["pj_accounting"];
//     	}
//     	if ($set_select["pj_salesman"] != '')                                     			// 担当営業
//     	{
//     		$sql .= ' AND `pj_salesman`  = ' . $set_select["pj_salesman"];
//     	}

//         // WHERE文 作成
//         foreach ($set_select_like as $key => $val)
//         {
//             if (isset($val) && $val != '')
//             {
//             	$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
//             }
//         }

//     	// ORDER BY文 作成
//         $tmp_firstitem = FALSE;
//         foreach ($set_orderby as $key => $val)
//         {
//         	if (isset($val) && $val != '')
//         	{
//         		if ($tmp_firstitem == FALSE)
//         		{
//         			$sql .= ' ORDER BY ' . $key . ' ' . $val;
//         			$tmp_firstitem = TRUE;
//         		} else {
//         			$sql .= ' , ' . $key . ' ' . $val;
//         		}
//         	}
//         }
//         if ($tmp_firstitem == FALSE)
//         {
//         	$sql .= ' ORDER BY pj_seq DESC';                                    // デフォルト
//         }

//     	// 対象全件数を取得
//         // 接続先DBを選択 ＆ クエリー実行
//         if ($db_name == 'default')
//         {

//         	$query = $this->db->query($sql);
//         	$project_countall = $query->num_rows();

//         	// LIMIT ＆ OFFSET 値をセット
//         	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

//         	// クエリー実行
//         	$query = $this->db->query($sql);
//         	$project_list = $query->result('array');

//         } else {

//         	$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

//         	$query = $slave_db->query($sql);
//         	$project_countall = $query->num_rows();

//         	// LIMIT ＆ OFFSET 値をセット
//         	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

//         	// クエリー実行
//         	$query = $slave_db->query($sql);
//         	$project_list = $query->result('array');

//         }

//     	return array($project_list, $project_countall);
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
//     			FROM mt_account WHERE ac_delflg = 0 AND ac_cl_seq = ' . $set_select['ac_cl_seq'];

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

//         // ログ書き込み
//         $set_data['lg_type']      = 'account.php';
//         $set_data['lg_func']      = 'update_account';
//         $set_data['lg_detail']    = 'ac_seq = ' . $setData['ac_seq'];
//         $this->insert_log($set_data);

//     	return $result;
//     }

//         /**
//          * ログイン日時の更新
//          *
//          * @param    int
//          * @return   bool
//          */
//         public function update_Logindate($ac_seq)
//         {

//         	$time = time();
//         	$setData = array(
//         			'ac_lastlogin' => date("Y-m-d H:i:s", $time)
//         	);
//         	$where = array(
//         			'ac_seq' => $ac_seq
//         	);
//         	$result = $this->db->update('mt_account', $setData, $where);

//             // ログ書き込み
//         	$set_data['lg_type']      = 'account.php';
//         	$set_data['lg_func']      = 'update_Logindate';
//             $set_data['lg_detail']    = 'ac_seq = ' . $ac_seq;
//             $this->insert_log($set_data);

//         	return $result;
//         }

//         /**
//          * 1レコード更新
//          *
//          * @param    array()
//          * @return   bool
//          */
//         public function update_account($setData, $pw = FALSE)
//         {

//         	// パスワード更新有無
//         	if ($pw == TRUE)
//         	{
//         		$_hash_pw = password_hash($setData["ac_pw"], PASSWORD_DEFAULT);
//         		$setData["ac_pw"] = $_hash_pw;
//         	} else {
//         		unset($setData["ac_pw"]) ;
//         	}

//         	$where = array(
//         			'ac_seq' => $setData['ac_seq']
//         	);

//         	$result = $this->db->update('mt_account', $setData, $where);

//         	// ログ書き込み
//         	$set_data['lg_type']      = 'account.php';
//         	$set_data['lg_func']      = 'update_account';
//         	$set_data['lg_detail']    = 'ac_seq = ' . $setData['ac_seq'];
//         	$this->insert_log($set_data);

//         	return $result;
//         }








//     /**
//      * 編集者アカウント情報を1件取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_ac_editor_limit()
//     {

//     	$sql = 'SELECT
//     			  ac_seq,
//     			  ac_status,
//     			  ac_type,
//     			  ac_name01,
//     			  ac_name02,
//     			  ac_mail
//     			FROM mb_account ';

//     	$sql .= 'where ac_type = 0 AND ac_status = 1 ORDER BY ac_seq DESC LIMIT 1';

//     	$query = $this->db->query($sql);

//     	$get_data = $query->result('array');

//     	return $get_data;

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

//     	$sql = 'SELECT * FROM `mb_account` '
//     			. 'WHERE `ac_seq` != ? AND `ac_mail` = ? ';

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
//      * 営業or編集者担当者リスト作成
//      *
//      * @param    int : 0=>編集, 1=>営業, 2=>管理者
//      * @return   bool
//      */
//     public function get_contact($person)
//     {

//     	$sql = 'SELECT
//     			  ac_seq,
//     			  ac_name01,
//     			  ac_name02
//     			FROM mb_account ';

//     	// WHERE文 作成
//     	if ($person == 0)
//     	{
//     		$sql .= ' WHERE `ac_status` = \'1\' AND `ac_type` = \'0\' ORDER BY `ac_seq` DESC ';
//     	} elseif ($person == 1) {
//     		$sql .= ' WHERE `ac_status` = \'1\' AND `ac_type` = \'1\' ORDER BY `ac_seq` DESC ';
//     	} else {
//     		$sql .= ' WHERE `ac_status` = \'1\' AND `ac_type` = \'2\' ORDER BY `ac_seq` DESC ';
//     	}

//     	// クエリー実行
//     	$query = $this->db->query($sql);
//     	$contact_list = $query->result('array');

//     	return $contact_list;

//     }

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

    	$setData['lg_type'] = 'invoice_detail.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    }

}