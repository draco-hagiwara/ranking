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
     * @return   array()
     */
    public function get_cm_seq($seq_no)
    {

    	$set_where["cm_seq"] = $seq_no;

    	$query = $this->db->get_where('mt_customer', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 固定報酬：対象顧客情報の取得
     *
     * @param    none
     * @return   array()
     */
    public function get_ivlist($timing, $cmseq = FALSE)
    {

    	$sql = 'SELECT
    			  cm_seq,
    			  cm_status,
    			  cm_agency_flg,
    			  cm_agency_seq,
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
    			  cm_collect,
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
    			  cm_account_nm,
    			  cm_invo_timing,
    			cm_salesman
    			FROM mt_customer WHERE cm_delflg = 0 AND cm_status = 0 AND cm_invo_timing = ' . $timing
    	;

    	// 個別発行に対応
    	if ($cmseq != FALSE)
    	{
    		$sql .= ' AND cm_seq = ' . $cmseq;
    	}

    	// 代理店対象顧客を抽出
    	if ($timing == 2)
    	{
    		$sql .= ' AND cm_agency_flg = 1';
    	}

    	$sql .= ' ORDER BY cm_seq ASC';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$invoicelist = $query->result('array');

    	return $invoicelist;

    }

    /**
     * 代理店を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_cm_agency()
    {

    	$set_where["cm_status"]     = 0;
    	$set_where["cm_agency_flg"] = 1;

    	$this->db->order_by('cm_seq', 'DESC');
    	$query = $this->db->get_where('mt_customer', $set_where);

//     	$_last_sql = $this->db->last_query();
//     	print_r($_last_sql);

    	$get_data = $query->result('array');

    	return $get_data;

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
    			  cm_agency_flg,
    			  cm_agency_seq,
    			  cm_company,
    			  cm_person01,
    			  cm_person02,
    			  cm_tel01,
    			  cm_tel02,
    			  cm_mail,
    			  cm_mailsub,
    			  cm_collect,
    			  cm_invo_timing
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
     * 口座名義カナの重複チェック
     *
     * @param    int
     * @return   bool
     */
    public function get_cm_account_nm($account_nm, $cm_seq=NULL)
    {

    	if ($cm_seq == NULL)
    	{
    		$set_where["cm_account_nm"] = $account_nm;
    	} else {
    		$set_where = '`cm_seq` <> ' . $cm_seq . ' AND `cm_account_nm` = \'' . $account_nm . '\'';
    	}

    	$query = $this->db->get_where('mt_customer', $set_where);

    	$account_nm_count = $query->num_rows();

    	return $account_nm_count;

    }

    /**
     * 請求書作成：顧客情報SEQから成功報酬情報を取得する
     *
     * @param    int  : 顧客情報seq
     * @param    boolen
     * @return   array()
     */
    public function get_ivlist_result($seq_no, $invo_status = FALSE)
    {



    	/*
    	 * pj_invoice_status = 0
    	 * 一括作成時には発行有無をチェックしている
    	 */



    	$sql = 'SELECT *
    			FROM vw_pjlist_result '
        		;

        if ($invo_status == FALSE)
        {
        	$sql .= ' WHERE cm_seq = ' . $seq_no . ' AND pj_invoice_status = 0'
        			;
        } else {
        	$sql .= ' WHERE cm_seq = ' . $seq_no
        			;
        }

        // 接続先DBを選択 ＆ クエリー実行
       	$query = $this->db->query($sql);

        $invoice_list = $query->result('array');

        return $invoice_list;

    }

    /**
     * 請求書作成：顧客情報SEQから代理店情報を取得する
     *
     * @param    int  : 顧客情報seq
     * @param    boolen
     * @return   array()
     */
    public function get_ivlist_agency($seq_no, $invo_status = FALSE)
    {



    	/*
    	 * pj_invoice_status = 0
    	 * 一括作成時には発行有無をチェックしている
    	 */



    	$sql = 'SELECT *
    			FROM vw_pjlist_agency '
    			;

    			if ($invo_status == FALSE)
    			{
    				$sql .= ' WHERE (cm_seq = ' . $seq_no . ' OR cm_agency_seq = ' . $seq_no . ') AND pj_invoice_status = 0'
    						;
    			} else {
    				$sql .= ' WHERE (cm_seq = ' . $seq_no . ' OR cm_agency_seq = ' . $seq_no . ')'
    						;
    			}

    			// 接続先DBを選択 ＆ クエリー実行
    			$query = $this->db->query($sql);

    			$invoice_list = $query->result('array');

    			return $invoice_list;

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
    		if (isset($setdata['cm_flg_iv']))
    		{
    			$setdata['cm_flg_iv'] = $setdata['cm_flg_iv'];
    		} else {
    			$setdata['cm_flg_iv'] = 0;
    		}
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