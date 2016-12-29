<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 受注案件SEQから情報を取得する
     *
     * @param    int
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   bool
     */
    public function get_pj_seq($seq_no, $client_no, $db_name='default')
    {

    	$tb_name = 'tb_project_' . $client_no;
    	$set_where["pj_seq"]    = $seq_no;

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{

    		$query = $this->db->get_where($tb_name, $set_where);

    	} else {

    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->get_where($tb_name, $set_where);

    	}

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 顧客情報SEQから情報を取得する
     *
     * @param    int  : 顧客情報seq
     * @param    int  : 課金方式（固定=0/成果=1/固+成=2）
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @param    bool : 請求書発行有無のチェック
     * @return   bool
     */
    public function get_pj_cm_seq($seq_no, $iv_type, $client_no, $db_name='default', $invo_status = FALSE)
    {

    	$tb_name = 'tb_project_' . $client_no;

    	$sql = 'SELECT
    			  pj_seq,
    			  pj_status,
    			  pj_invoice_status,
    			  pj_start_date,
    			  pj_end_date,
    			  pj_keyword,
    			  pj_url,
    			  pj_accounting,
    			  pj_tax_cal,
    			  pj_billing,
    			  pj_cm_seq,
    			  pj_salesman
    			FROM ' . $tb_name
    	;

    	if ($invo_status == FALSE)
    	{
    		$sql .= ' WHERE pj_cm_seq = ' . $seq_no . ' AND pj_accounting = ' . $iv_type . ' AND pj_invoice_status = 0  AND pj_status = 0 AND pj_delflg = 0'
    				. ' ORDER BY pj_seq ASC';
    	} else {
    		$sql .= ' WHERE pj_cm_seq = ' . $seq_no . ' AND pj_accounting = ' . $iv_type . ' AND pj_status = 0 AND pj_delflg = 0'
    				. ' ORDER BY pj_seq ASC';
    	}

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{

    		$query = $this->db->query($sql);

    	} else {

    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->query($sql);

    	}

    	$projectlist = $query->result('array');

    	return $projectlist;

    }

    /**
     * 顧客情報SEQから情報を取得する : ステータス「解約」への変更に使用
     *
     * @param    int  : 顧客情報seq
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   bool
     */
    public function get_pj_cm_status($seq_no, $client_no, $db_name='default')
    {

    	$tb_name = 'tb_project_' . $client_no;

    	$sql = 'SELECT
    			  pj_seq,
    			  pj_status,
    			  pj_invoice_status,
    			  pj_start_date,
    			  pj_end_date,
    			  pj_keyword,
    			  pj_url,
    			  pj_accounting,
    			  pj_tax_cal,
    			  pj_billing,
    			  pj_cm_seq
    			FROM ' . $tb_name
        			. ' WHERE pj_cm_seq = ' . $seq_no . ' ORDER BY pj_cm_seq ASC';

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{

    		$query = $this->db->query($sql);

    	} else {

    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->query($sql);

    	}

    	$projectlist = $query->result('array');

    	return $projectlist;

    }

    /**
     * 案件情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ（接続先テーブルを切替）
     * @param    char    : 接続先DB
     * @return   array()
     */
    public function get_projectlist($get_post, $tmp_per_page, $tmp_offset=0, $client_no, $db_name='default')
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["pj_seq"]        = $get_post['pj_seq'];
    	$set_select_like["pj_cm_seq"]     = $get_post['pj_cm_seq'];
    	$set_select_like["pj_cm_company"] = $get_post['pj_cm_company'];

    	$set_select["pj_status"]          = $get_post['pj_status'];
    	$set_select["pj_invoice_status"]  = $get_post['pj_invoice_status'];
    	$set_select["pj_accounting"]      = $get_post['pj_accounting'];
    	$set_select["pj_salesman"]        = $get_post['pj_salesman'];

    	// ORDER BY
//     	$set_orderby["pj_cm_seq"] = $get_post['orderstatus'];
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["pj_seq"] = $get_post['orderid'];
    	}else {
    		$set_orderby["pj_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$project_list = $this->_select_projectlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $client_no, $db_name);

    	return $project_list;

    }

    /**
     * 案件情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_projectlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $client_no, $db_name)
    {

    	$tb_name = 'tb_project_' . $client_no;

    	$sql = 'SELECT
    			  pj_seq,
    			  pj_status,
    			  pj_invoice_status,
    			  pj_start_date,
    			  pj_end_date,
    			  pj_keyword,
    			  pj_url,
    			  pj_target,
    			  pj_language,
    			  pj_accounting,
    			  pj_url_match,
    			  pj_billing,
    			  pj_cm_seq,
    			  pj_cm_company,
    			  pj_salesman
    			FROM ' . $tb_name . ' WHERE '
    		;

    	// pj_status 判定
    	if ($set_select["pj_status"] == 2)
    	{
    		$sql .= ' pj_delflg = 1 ';
    	} else {
    		$sql .= ' pj_delflg = 0 ';
    	}

    	if ($set_select["pj_status"] != '')                                     			// 受注案件ステータス
    	{
    		$sql .= ' AND `pj_status`  = ' . $set_select["pj_status"];
    	}
    	if ($set_select["pj_invoice_status"] != '')                                     	// 請求書発行ステータス
    	{
    		$sql .= ' AND `pj_invoice_status`  = ' . $set_select["pj_invoice_status"];
    	}
    	if ($set_select["pj_accounting"] != '')                                     		// 課金方式
    	{
    		$sql .= ' AND `pj_accounting`  = ' . $set_select["pj_accounting"];
    	}
    	if ($set_select["pj_salesman"] != '')                                     			// 担当営業
    	{
    		$sql .= ' AND `pj_salesman`  = ' . $set_select["pj_salesman"];
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
        	$sql .= ' ORDER BY pj_seq DESC';                                    // デフォルト
        }

    	// 対象全件数を取得
        // 接続先DBを選択 ＆ クエリー実行
        if ($db_name == 'default')
        {

        	$query = $this->db->query($sql);
        	$project_countall = $query->num_rows();

        	// LIMIT ＆ OFFSET 値をセット
        	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        	// クエリー実行
        	$query = $this->db->query($sql);
        	$project_list = $query->result('array');

        } else {

        	$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

        	$query = $slave_db->query($sql);
        	$project_countall = $query->num_rows();

        	// LIMIT ＆ OFFSET 値をセット
        	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        	// クエリー実行
        	$query = $slave_db->query($sql);
        	$project_list = $query->result('array');

        }

    	return array($project_list, $project_countall);
    }

    /**
     * 受注案件情報データの件数を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_pj_cnt($pj_status, $client_no, $db_name='default')
    {

    	$tb_name = 'tb_project_' . $client_no;

    	$set_where["pj_status"] = $pj_status;

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{

    		$query = $this->db->get_where($tb_name, $set_where);

    	} else {

    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->get_where($tb_name, $set_where);

    	}

    	$project_count = $query->num_rows();

    	return $project_count;

    }
    /**
     * 案件情報新規登録
     *
     * @param    array()
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   int
     */
    public function insert_project($setdata, $client_no, $db_name='default')
    {

    	$tb_name = 'tb_project_' . $client_no;

    	// pj_status 判定
    	if ($setdata["pj_status"] == 2)
    	{
    		$setdata['pj_delflg'] = 1;
    	}

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{

    		$query  = $this->db->insert($tb_name, $setdata);
    		$_last_sql = $this->db->last_query();
    		$row_id = $this->db->insert_id();										// 挿入した ID 番号を取得

    	} else {

    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$query = $slave_db->insert($tb_name, $setdata);
    		$_last_sql = $slave_db->last_query();
    		$row_id = $slave_db->insert_id();

    	}

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_project';
    	$set_data['lg_detail'] = 'pj_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   bool
     */
    public function update_project($setdata, $client_no, $db_name='default')
    {

    	$tb_name = 'tb_project_' . $client_no;

    	// ステータスの判定
    	if ($setdata["pj_status"] == 2)
    	{
    		$setdata["pj_invoice_status"] = 1;
    		$setdata["pj_delflg"] = 1;
    	} else {
    		$setdata["pj_delflg"] = 0;
    	}

    	$where = array(
    			'pj_seq' => $setdata['pj_seq']
    	);

    	// 接続先DBを選択 ＆ クエリー実行
    	if ($db_name == 'default')
    	{

    		$result = $this->db->update($tb_name, $setdata, $where);
    		$_last_sql = $this->db->last_query();

    	} else {

    		$slave_db = $this->load->database($db_name, TRUE);						// 順位チェックツールDBへ接続

    		$result = $slave_db->update($tb_name, $setdata, $where);
    		$_last_sql = $slave_db->last_query();

    	}

    	// ログ書き込み
    	$set_data['lg_func']      = 'update_project';
    	$set_data['lg_detail']    = 'pj_seq = ' . $setdata['pj_seq'] . ' <= ' . $_last_sql;
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

    	$setData['lg_type'] = 'project.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    }

}