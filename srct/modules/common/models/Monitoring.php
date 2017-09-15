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