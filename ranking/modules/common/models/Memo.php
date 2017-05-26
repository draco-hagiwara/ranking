<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Memo extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * キーワードSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_me_seq($seq_no)
    {

    	$set_where["me_seq"] = $seq_no;

    	$this->db->order_by('me_seq', 'DESC');
    	$query = $this->db->get_where('tb_memo', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワードSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_me_kwseq($seq_no)
    {

    	$set_where["me_kw_seq"] = $seq_no;

    	$this->db->order_by('me_seq', 'DESC');
    	$query = $this->db->get_where('tb_memo', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * メモ情報の書き込み
     *
     * @param    int
     * @param    text
     * @param    int
     * @param    int
     * @return   int
     */
    public function insert_kw_memo($kw_seq, $kw_memo, $cl_seq, $ac_seq)
    {


    	// データ追加
    	$setData['me_kw_seq'] = $kw_seq;
    	$setData['me_memo']   = $kw_memo;
    	$setData['me_cl_seq'] = $cl_seq;
    	$setData['me_ac_seq'] = $ac_seq;

    	$query = $this->db->insert('tb_memo', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_client';
    	$set_data['lg_detail']    = 'cl_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * メモ情報削除 (同一kw_seq)
     *
     * @param    array()
     * @return   int
     */
    public function delete_memo($kw_seq, $cl_seq)
    {

    	$set_where["me_kw_seq"] = $kw_seq;
    	$set_where["me_cl_seq"] = $cl_seq;

    	$result = $this->db->delete('tb_memo', $set_where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_memo';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * メモ情報削除 (個別me_seq)
     *
     * @param    array()
     * @return   int
     */
    public function delete_me_seq($me_seq)
    {

    	$set_where["me_seq"] = $me_seq;

    	$result = $this->db->delete('tb_memo', $set_where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_memo';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

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

    	$setData['lg_type']   = 'Memo.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}