<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 請求書情報SEQから情報を取得する
     *
     * @param    int
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   bool
     */
    public function get_iv_seq($seq_no)
    {

    	$set_where["iv_seq"] = $seq_no;

    	// クエリー実行
    	$query = $this->db->get_where('tb_invoice', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 顧客情報SEQからデータの有無を判定
     *
     * @param    int
     * @param    char
     * @return   bool
     */
    public function get_iv_cm_seq($seq_no, $issue_yymm)
    {

//     	$set_where["iv_status"]     = 0;
//     	$set_where["iv_cm_seq"]     = $seq_no;
//     	$set_where["iv_issue_yymm"] = $issue_yymm;

    	$set_where = '`iv_cm_seq` = ' . $seq_no . ' AND `iv_issue_yymm` = ' . $issue_yymm . ' AND `iv_status` <> 9';

		$query = $this->db->get_where('tb_invoice', $set_where);
//     	$_num_rows = $query->num_rows();
		$get_data = $query->result('array');

//     	return $_num_rows;
    	return $get_data;

    }

    /**
     * 請求書情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_invoicelist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["iv_slip_no"]    = $get_post['iv_slip_no'];
    	$set_select_like["iv_cm_seq"]     = $get_post['iv_cm_seq'];
    	$set_select_like["iv_company_cm"] = $get_post['iv_company'];

    	$set_select["iv_status"]       = $get_post['iv_status'];
    	$set_select["iv_issue_yymm"]   = $get_post['iv_issue_yymm'];

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["iv_cm_seq"]     = $get_post['orderid'];
    	}elseif ($get_post['orderid'] == 'DESC') {
    		$set_orderby["iv_cm_seq"]     = $get_post['orderid'];
    	}else {
    		$set_orderby["iv_issue_date"] = 'DESC';
    		$set_orderby["iv_status"]     = 'ASC';
    		$set_orderby["iv_cm_seq"]     = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$invoice_list = $this->_select_invoicelist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $invoice_list;

    }

    /**
     * 請求書情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_invoicelist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
    			  iv_seq,
    			  iv_seq_suffix,
    			  iv_status,
    			  iv_cm_seq,
    			  iv_accounting,
    			  iv_agency_flg,
    			  iv_issue_yymm,
    			  iv_slip_no,
    			  iv_total,
    			  iv_issue_date,
    			  iv_pay_date,
    			  iv_company_cm,
    			  iv_company
    			FROM tb_invoice WHERE ';

    	// pj_status 判定
    	if ($set_select["iv_status"] == 9)
    	{
    		$sql .= ' iv_delflg = 1 ';
    	} else {
    		$sql .= ' iv_delflg = 0 ';
    	}

    	if ($set_select["iv_status"] != '')
    	{
    		$sql .= ' AND `iv_status`  = ' . $set_select["iv_status"];
    	}
    	if ($set_select["iv_issue_yymm"] != '')
    	{
    		$sql .= ' AND `iv_issue_yymm`  = ' . $set_select["iv_issue_yymm"];
    	}

    	// WHERE文 作成
    	foreach ($set_select_like as $key => $val)
    	{
    		if (isset($val) && $val != '')
    		{
    			$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
    		}
    	}

    	// ORDER BY文 作成
    	$tmp_firstitem = FALSE;
    	foreach ($set_orderby as $key => $val)
    	{
    		if (isset($val) && $val != '')
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
    	if ($tmp_firstitem == FALSE)
    	{
    		$sql .= ' ORDER BY iv_issue_date DESC, iv_status ASC, iv_seq DESC';						// デフォルト
    	}

    	// 対象全件数を取得
    	$query = $this->db->query($sql);
    	$invoice_countall = $query->num_rows();

    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$invoice_list = $query->result('array');

    	return array($invoice_list, $invoice_countall);

    }

    /**
     * 履歴情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ（接続先テーブルを切替）
     * @param    char    : 接続先DB
     * @return   array()
     */
    public function get_historylist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
					T1.iv_seq,
					T1.iv_seq_suffix,
					T1.iv_status,
					T1.iv_cm_seq,
					T1.iv_accounting,
					T1.iv_method,
    				T1.iv_method,
					T1.iv_salse_yymm,
					T1.iv_slip_no,
					T1.iv_subtotal,
					T1.iv_tax,
					T1.iv_total,
					T1.iv_issue_date,
					T1.iv_pay_date,
					T1.iv_reissue,
					T1.iv_company,
					T1.iv_department,
					T1.iv_person01,
					T1.iv_person02,
					T1.iv_zip01,
					T1.iv_zip02,
					T1.iv_pref,
					T1.iv_addr01,
					T1.iv_addr02,
					T1.iv_buil,
					T1.iv_remark,
					T1.iv_memo,
					T1.iv_create_date

					from tb_invoice_h AS T1
					WHERE T1.iv_seq = ? AND T1.iv_issue_yymm = ?
					  ORDER BY T1.iv_seq_suffix DESC
				';

    	$_set_values = array(
    			$get_post['iv_seq'],
    			$get_post['iv_issue_yymm'],
    	);

    	// 対象全件数を取得
    	$query = $this->db->query($sql, $_set_values);
    	$history_countall = $query->num_rows();

    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql, $_set_values);
    	$history_list = $query->result('array');

    	return array($history_list[0], $history_countall);

    }

    /**
     * 請求書情報データの件数を取得する
     *
     * @param    int
     * @param    date
     * @return   int
     */
    public function get_iv_cnt($iv_status, $date_ym)
    {

    	$set_where["iv_status"]     = $iv_status;
    	$set_where["iv_issue_yymm"] = $date_ym;

    	$query = $this->db->get_where('tb_invoice', $set_where);
    	$invoice_count = $query->num_rows();

    	return $invoice_count;

    }

    /**
     * 前日日付の請求書情報データを取得する
     *
     * @param    date
     * @return   array
     */
    public function get_iv_sales($sasles_date)
    {

    	// 課金方式が「8:前受」以外
    	$set_where = '`iv_status` = 1 AND iv_sales_date` = \'' . $sasles_date . '\' AND `iv_accounting` != 8';

    	$query = $this->db->get_where('tb_invoice', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 日付範囲指定時の請求書情報データを取得する
     *
     * @param    date
     * @param    date
     * @return   array
     */
    public function get_iv_sales2($sasles_date1, $sasles_date2)
    {

    	$set_where = '`iv_status` = 1 AND `iv_sales_date` BETWEEN \'' . $sasles_date1 . '\' AND \'' . $sasles_date2 . '\' ORDER BY iv_cm_seq ASC';

    	$query = $this->db->get_where('tb_invoice', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 請求書データ新規登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_invoice($setdata)
    {

    	// データ追加
    	$query = $this->db->insert('tb_invoice', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_invoice';
    	$set_data['lg_detail'] = 'iv_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $row_id;
    }

    /**
     * 請求書データ新規登録 << 履歴ファイル
     *
     * @param    array()
     * @return   int
     */
    public function insert_invoice_history($setdata)
    {

    	// データ追加
    	$query = $this->db->insert('tb_invoice_h', $setdata);
//     	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

//     	// ログ書き込み
//     	$set_data['lg_func']   = 'insert_invoice_history';
//     	$set_data['lg_detail'] = 'iv_seq = ' . $row_id . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

    	return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_invoice($setdata)
    {

    	$where = array(
    		'iv_seq' => $setdata['iv_seq']
    	);

    	$result = $this->db->update('tb_invoice', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_invoice';
    	$set_data['lg_detail'] = 'iv_seq = ' . $setdata['iv_seq'] . ' <= ' . $_last_sql;
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
    		$setData['lg_user_id'] = $_SESSION['a_memSeq'];
    	} elseif (isset($_SESSION['c_memSeq'])) {
    		$setData['lg_user_id'] = $_SESSION['c_memSeq'];
    	} else {
    		$setData['lg_user_id'] = "";
    	}

    	$setData['lg_type'] = 'Invoice.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    }

}