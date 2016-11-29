<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 請求書データ新規登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_sales($setdata)
    {

    	// データ追加
    	$query = $this->db->insert('tb_sales', $setdata);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_sales';
    	$set_data['lg_detail'] = 'sa_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $row_id;
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

    	$setData['lg_type'] = 'Sales.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    }

}