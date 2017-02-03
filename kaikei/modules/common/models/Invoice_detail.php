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
    public function get_iv_seq($seq_no, $issue_yymm, $seq_suffix)
    {

    	$set_where = '`ivd_iv_seq` = ' . $seq_no  . ' AND `ivd_iv_issue_yymm` = ' . $issue_yymm  . ' AND `ivd_seq_suffix` = ' . $seq_suffix;
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

    	$query = $this->db->get_where('tb_invoice_detail_h', $set_where);
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
//     	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

//     	// ログ書き込み
//     	$set_data['lg_func']   = 'insert_invoice_detail_history';
//     	$set_data['lg_detail'] = 'ivd_seq = ' . $row_id . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

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
    	$set_data['lg_func']   = 'update_invoice_detail';
    	$set_data['lg_detail'] = 'ivd_seq = ' . $setdata['ivd_seq'] . ' <= ' . $_last_sql;
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

    	$setData['lg_type'] = 'invoice_detail.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    }

}