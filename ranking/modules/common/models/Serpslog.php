<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Serpslog extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * API のログを保存
     *
     * @param    array()
     * @return   int
     */
    public function insert_serpslog($set_data)
    {

    	// データ追加
    	$query = $this->db->insert('tb_serpslog', $set_data);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    }

    /**
     * API のログ情報削除 (31日保持)
     *
     * @param    array()
     * @return   int
     */
    public function delete_serpslog()
    {

    	$date = new DateTime();
    	//$del_date = $date->modify('-31 days')->format('Y-m-d');
    	$del_date = $date->modify('-7 days')->format('Y-m-d');

    	$sql = 'DELETE FROM `tb_serpslog`
    				WHERE `sl_date` < "' . $del_date . '"'
    	;

    	// クエリー実行
    	$result = $this->db->query($sql);

    	return $result;

    }

}